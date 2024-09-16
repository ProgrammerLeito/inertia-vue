<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
 
const searchQuery = ref('');
const filteredClients = ref([]);
const selectedProvincia = ref('');
const tecnicoQuery = ref('');

const props = defineProps({
    clientes: {
        type: Array,
        required: true
    },
    tbprovincias: {
        type: Object,
        required: true
    }
});
const form = useForm({
    id: ''
})

const deleteCliente = (id, razonSocial) => {
    show_confirmacion(`¿Estás seguro de eliminar al cliente: ${razonSocial}, ¡Este cliente se eliminará definitivamente de la base de datos. Esta acción no se puede deshacer!`)
    .then((result) => {
        if (result.isConfirmed) {
                    form.delete(route('clientes.destroy', id), {
                        onSuccess: () => {
                            show_alerta('Cliente eliminado exitosamente de la base de datos.', 'success')
                        }
                    });
                }
            });
        }

watchEffect(() => {
    if (props.clientes.data) {
        const leonardo = searchQuery.value.toLowerCase().trim();

        filteredClients.value = props.clientes.data.filter(cliente => {
            const primero = cliente.id.toString().includes(leonardo);
            const segundo = cliente.ctg.toLowerCase().includes(leonardo);
            const tercero = cliente.razonSocial.toLowerCase().includes(leonardo);
            const cuarto = cliente.numeroDocumento.toLowerCase().includes(leonardo);

            const searchMatch = primero || segundo || tercero || cuarto;
            const provinciaMatch = selectedProvincia.value === '' || cliente.tbprovincia_id === selectedProvincia.value;
            const tecnicoMatch = tecnicoQuery.value === '' || cliente.asesor.toLowerCase().includes(tecnicoQuery.value.toLowerCase());

            return searchMatch && provinciaMatch && tecnicoMatch;
        });
    }
});


const openCtgModal = async (cliente) => {
    const { value: ctg } = await Swal.fire({
        title: `Calificación del cliente: ${cliente.razonSocial}`,
        input: 'select',
        inputOptions: {
            'Vip': 'Vip',
            'Potencial': 'Potencial',
            'Regular': 'Regular',
            'Sin Informacion': 'Sin Informacion',
        },
        customClass: {
            title: 'text-2xl font-bold tracking-widest ',
            input: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
        inputPlaceholder: 'Selecciona una opción ⏬',
        showCancelButton: true,
        confirmButtonText: 'Asignar',
        showLoaderOnConfirm: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debes seleccionar un tipo de calificación';
            }
        },
    });

    if (ctg) {
        try {
            form.post(route('clientes.updateCtg', cliente.id));
            const response = await axios.post(route('clientes.updateCtg'), {
                cliente_id: cliente.id,
                ctg: ctg
            });
            if (response && response.status === 200) {
                show_alerta('Calificación asignada exitosamente.', 'success')
            } else {
                throw new Error(response ? response.data.message : 'Error desconocido');
            }
        } catch (error) {
            console.error('Error al actualizar la calificación:', error);
            show_alerta('Hubo un problema al actualizar la calificación del cliente.', 'error')
        }
    }
};

const formPage = useForm({});
const onPageClick = (event) => {
    formPage.get(route('clientes.index', { page: event }));
}
const previousPage = () => {
    const prevPage = props.clientes.current_page - 1;
    formPage.get(route('clientes.index', { page: prevPage }));
};
 
const nextPage = () => {
    const nextPage = props.clientes.current_page + 1;
    formPage.get(route('clientes.index', { page: nextPage }));
};
 
const goToPage = (page) => {
    formPage.get(route('clientes.index', { page }));
};
 
const total_pages = props.clientes.last_page;
const current_page = props.clientes.current_page;
const countPerPage = props.clientes.data.length;
const totalCount = props.clientes.total;

const printPDF = async () => {
    const doc = new jsPDF({
        orientation: 'p',
        unit: 'mm',
        format: 'a4',
        compress: true,
    });

    // Definir el contenido del PDF
    const headerText = 'LISTA DE CLIENTES:';
    const tableHeaders = ['N°', 'RUC', 'RAZÓN SOCIAL', 'DIRECCIÓN', 'CIUDAD', 'ASESOR'];
    const tableData = filteredClients.value.map((cliente, i) => [
        i + 1,
        cliente.numeroDocumento || '',
        cliente.razonSocial || '',
        cliente.direccion || '',
        cliente.tbprovincia ? cliente.tbprovincia.prov_nombre : 'Sin ciudad',
        cliente.asesor || '',
    ]);

    var eje_y = 10;

    // Añadir contenido al PDF
    doc.setFontSize(14);
    doc.setFont('courier', 'bold');//estilos de texto
    doc.text(headerText, 15, eje_y += 7); // Alineado a la izquierda y más abajo

    // Usar autoTable para generar la tabla
    doc.autoTable({
        head: [tableHeaders],
        body: tableData,
        startY: eje_y += 4, // Posición más abajo
        theme: 'grid',
        styles: {
            fontSize: 8,
            cellPadding: 2,
            valign: 'middle',
            halign: 'center',
            textColor: '#ffffff', // Color de texto para toda la tabla
        },
        headStyles: {
            fillColor: '#1423BC', // Color de fondo del encabezado
            textColor: '#ffffff', // Color de texto del encabezado
        },
        bodyStyles: {
            fillColor: '#d5d7d8', // Color de fondo del cuerpo
            textColor: '#000000', // Color de texto del cuerpo
        },
        columnStyles: {
            2: { fontStyle: 'bold' }, // Aplicar negrita a la primera columna (N°2)
        },
    });
    const blob = doc.output('blob');
    const url = URL.createObjectURL(blob);
    window.open(url);
};
</script>

<template>
    <AppLayout title="Clientes">
        <template #header>
            <div class="flex justify-between w-full pr sm:pr-14 ">
                <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">
                    Lista de Clientes
                </h1>
            </div>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('clientes.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-person-plus mx-1"></i> Registrar Cliente
                        </Link>
                    </div>
                    <div>
                        <div class="py-1">
                            <div class="flex lg:flex-row flex-col md:gap-4 gap-3 md:my-0 my-2 w-full py-1.5">
                                <div class="flex flex-col justify-end w-full">
                                    <div class="flex w-full">
                                        <span
                                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                            <i class='fa-solid fa-users text-md'></i>
                                        </span>
                                        <input v-model="searchQuery" type="text" id="table-search"
                                            class="w-full outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-primary-600 focus:border-primary-600 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            autocomplete="off"
                                            placeholder="Ingrese Nombre de Cliente">
                                    </div>
                                </div>
                                <div class="flex flex-col justify-end w-full">
                                    <div class="flex flex-col md:flex-row md:items-center w-full md:h-9">
                                        <div class="h-10 text-sm flex items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-[#111B22] rounded-t-lg md:rounded-none md:rounded-l-lg">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max px-2">Filtrar Ciudad: </h4>
                                        </div>
                                        <select v-model="selectedProvincia" class="w-full h-10 outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-b-lg md:rounded-none md:rounded-r-lg">
                                            <option value="" selected disabled>Seleccione la Ciudad</option>
                                            <option v-for="tbprovincia in tbprovincias" :key="tbprovincia.id"
                                                :value="tbprovincia.id">{{ tbprovincia.prov_nombre }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex flex-col justify-end w-full">
                                    <div class="flex w-full">
                                        <span
                                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                            <i class='bx bxs-user-circle text-xl'></i>
                                        </span>
                                        <input v-model="tecnicoQuery"
                                            class="w-full outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-primary-600 focus:border-primary-600 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="text" autocomplete="off"
                                            placeholder="Ingrese Nombre de Asesor">
                                    </div>
                                </div>
                                <div class="flex flex-col justify-end w-full">
                                    <button @click="printPDF"
                                        class="text-white bg-indigo-700 hover:bg-indigo-800 mt-0.5 py-2 px-4 rounded lg:w-min w-full whitespace-nowrap text-center">
                                        <i class="fas fa-print mx-2"></i> Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="relative scroll-dataTableLEO overflow-x-auto shadow-md md:rounded-lg rounded-md shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                                <caption class="p-3.5 text-base font-semibold text-left rtl:text-right bg-gray-200 dark:bg-gray-700">
                                    <p class="mx-2 items-start text-black dark:text-white">Para acceder a los contactos de un cliente, por favor haga doble clic sobre el nombre del cliente en la lista. Esto le permitirá visualizar los detalles de sus contactos.</p>
                                </caption>
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 hidden">ID</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Ruc</th>
                                        <th scope="col" class="px-6 py-3 text-left dark:border-white border-b-2">Razon social</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Direccion</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Ciudad</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Asesor</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Ctg</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Facturacion</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2" v-if="$page.props.user.permissions.includes('Acciones Administrador')">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-xs">
                                    <tr @dblclick="redirectToClient(cliente.id); guardarClienteId(cliente.id, cliente.razonSocial)" v-for="(cliente , i) in filteredClients" :key="cliente.id" class="bg-white border-b-2 dark:border-white border-gray-400 dark:hover:bg-gray-900 cursor-pointer hover:bg-gray-500 hover:text-white text-black dark:bg-gray-700 dark:text-white">
                                        <td class="px-6 py-4 text-center hidden">{{ cliente.id }}</td>
                                        <td class="px-6 py-4 text-center">{{ i + 1 }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.numeroDocumento }}</td>
                                        <td class="px-6 py-4 text-left font-semibold">{{ cliente.razonSocial }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.direccion }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.tbprovincia ? cliente.tbprovincia.prov_nombre : 'Sin ciudad' }}</td>
                                        <td class="px-6 py-4 text-center uppercase">{{ cliente.asesor }}</td>
                                        <td class="py-4 px-2 text-center" @contextmenu.prevent="openCtgModal(cliente)">
                                            <div :class="{
                                                'bg-blue-600 text-white': cliente.ctg === 'Vip',
                                                'bg-yellow-600 text-white': cliente.ctg === 'Regular',
                                                'bg-red-600 text-white': cliente.ctg === 'Sin Informacion',
                                                'bg-green-600 text-white': cliente.ctg === 'Potencial'
                                            }" class="inline-block px-2 py-1 rounded">
                                                <b>{{ cliente.ctg }}</b>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">{{ (cliente.fechafactura ? cliente.fechafactura : 'Esperando fecha') + ' || ' + (cliente.codigofactura ? cliente.codigofactura : 'Esperando Codigo') }}</td>
                                        <td class="p-3 text-center whitespace-nowrap" v-if="$page.props.user.permissions.includes('Acciones Administrador')">
                                            <Link :href="route('clientes.edit', cliente.id)" class="transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-amber-400 hover:bg-amber-500 px-1.5 py-0.5 rounded-md mr-2">
                                                <i class='bx bxs-edit text-base text-white'></i>
                                            </Link>
                                            <button @click="$event => deleteCliente(cliente.id, cliente.razonSocial)" class="transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-red-600 hover:bg-red-700 px-1.5 py-0.5 rounded-md">
                                                <i class='bx bxs-trash text-base text-white'></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredClients.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                        <div class="flex flex-wrap justify-end">
                            <!-- <div class="hidden sm:block">
                                <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                                    <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                                    <p class="text-gray-700 dark:text-white font-semibold">Total de Clientes: {{ totalCount }}</p>
                                </div>
                            </div> -->
                            <div class="mt-4 sm:text-end text-center">
                                <nav aria-label="Page navigation example mt-4">
                                    <ul class="inline-flex -space-x-px text-sm">
                                        <li>
                                            <button @click="previousPage" :disabled="!clientes.prev_page_url"
                                                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-700 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                Prev
                                            </button>
                                        </li>
                                        <li v-for="page in total_pages" :key="page">
                                            <button @click="goToPage(page)"
                                                :class="{ 'text-blue-600 border-blue-300 dark:text-gray-900 bg-blue-50 hover:bg-blue-100 hover:text-blue-700': page === current_page, 'text-gray-900 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': page !== current_page }"
                                                class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                {{ page }}
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="nextPage" :disabled="!clientes.next_page_url"
                                                class="flex items-center justify-center px-3 h-8 leading-tight text-gray-700 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                Next
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    methods: {
        redirectToClient(cliente_id) {
            // Redirigir a la página de detalles usando el mismo enlace que el botón de visualización
            // window.location.href = this.route('datos.index', { cliente_id: cliente_id });
            this.$inertia.visit(route('datos.index', { cliente_id: cliente_id }));
        },
        guardarClienteId(cliente_id, razonSocial) {
            // Guardar el cliente_id y razonSocial en localStorage
            localStorage.setItem('cliente_id', cliente_id);
            localStorage.setItem('razonSocial', razonSocial);
        },
    }
};
</script>
