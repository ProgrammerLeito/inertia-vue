<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
 
const searchQuery = ref('');
const filteredClients = ref([]);
const selectedProvincia = ref('');
const isDropdownOpen = ref(false);
const tecnicoQuery = ref('');

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

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
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar al cliente: ' + razonSocial + '?' + '¡Este cliente se eliminará definitivamente de la base de datos. Esta acción no se puede deshacer.!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
        customClass: {
            title: 'text-xl font-bold tracking-widest ',
            cancelButton: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
    }).then((result) => {
        if (result.isConfirmed) {
                    form.delete(route('clientes.destroy', id), {
                        onSuccess: () => {
                            Swal.fire({
                                icon: "success",
                                title: 'Éxito',
                                text: "Cliente eliminado exitosamente.",
                                customClass: {
                                    title: 'text-2xl font-bold tracking-widest ',
                                    icon: 'text-base font-bold tracking-widest ',
                                    text: 'bg-red-500 hover:bg-red-600 tracking-widest ',
                                },
                            });
                        }
                    });
                }
            });
        }

watchEffect(() => {
    if (props.clientes.data) {
        const jomar = searchQuery.value.toLowerCase().trim();

        filteredClients.value = props.clientes.data.filter(cliente => {
            const primero = cliente.id.toString().includes(jomar);
            const segundo = cliente.ctg.toLowerCase().includes(jomar);
            const tercero = cliente.razonSocial.toLowerCase().includes(jomar);
            const cuarto = cliente.numeroDocumento.toLowerCase().includes(jomar);

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
        inputPlaceholder: 'Selecciona una opción 🔽',
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
                const Toast = Swal.mixin({
                        toast: true,
                        position: "bottom-end",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: "success",
                        title: 'Éxito',
                        text: "Calificación asignada exitosamente.",
                        customClass: {
                            title: 'text-2xl font-bold tracking-widest ',
                            icon: 'text-base font-bold tracking-widest ',
                            text: 'bg-red-500 hover:bg-red-600 tracking-widest ',
                        },
                    });
            } else {
                throw new Error(response ? response.data.message : 'Error desconocido');
            }
        } catch (error) {
            console.error('Error al actualizar la calificación:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al actualizar la calificación del cliente',
            });
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

    const backgroundImage = '/storage/profile-photos/plantillaclientes.png';

    // Agregar la imagen de fondo (simulado, jsPDF no admite imágenes de fondo directamente)
    doc.addImage(backgroundImage, 'PNG', 0, 0, 210, 297);
    // A4: 210 x 297 mm

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

    var eje_y = 40;

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
    // Abrir el PDF en una nueva ventana para imprimir
    doc.output('dataurlnewwindow');
};
</script>

<template>
    <AppLayout title="Clientes">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">
                Lista de Clientes
                ({{ $page.props.totalClientes }})</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('clientes.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-person-plus mx-1"></i> Registrar Cliente
                        </Link>
                    </div>
                    <div>
                        <div class="py-1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-y-3 sm:gap-x-6 py-2">
                                <div class="flex flex-col ">
                                    <InputLabel for="table-search"
                                        class="block text-md font-medium text-gray-700 dark:text-white">Buscar
                                    </InputLabel>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-900 dark:text-black" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input v-model="searchQuery" type="text" id="table-search"
                                            class="block pt-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Buscar cliente">
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <InputLabel class="block text-md font-medium text-gray-700 dark:text-white">Ciudad
                                    </InputLabel>
                                    <select v-model="selectedProvincia"
                                        class="block pt-2.5 ps-10 text-sm tracking-widest text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Buscar cliente">
                                        <option value="" selected disabled>Seleccione por Ciudad</option>
                                        <option v-for="tbprovincia in tbprovincias" :key="tbprovincia.id"
                                            :value="tbprovincia.id">{{ tbprovincia.prov_nombre }}</option>
                                    </select>
                                </div>
                                <div class="flex flex-col">
                                    <InputLabel class="block text-md font-medium text-gray-700 dark:text-white">Asesor
                                    </InputLabel>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-800 dark:text-black" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input v-model="tecnicoQuery" type="text"
                                            class="block pt-2.5 ps-10 text-sm tracking-widest text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Buscar por asesor">
                                    </div>
                                </div>
                                <div class="flex flex-col 2xl:mt-5 mt-1">
                                    <button @click="printPDF"
                                        class="text-white bg-indigo-700 hover:bg-indigo-800 mt-0.5 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                        <i class="fas fa-print mx-2"></i> Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Ruc</th>
                                        <th scope="col" class="px-6 py-3 text-left dark:border-white border-b-2">Razon social</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Direccion</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Ciudad</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Asesor</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Ctg</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-xs">
                                    <tr @dblclick="redirectToClient(cliente.id); guardarClienteId(cliente.id, cliente.razonSocial)" v-for="(cliente, i) in filteredClients" :key="cliente.id" class="bg-white border-b-2 dark:border-white border-gray-400 dark:hover:bg-gray-900 cursor-pointer hover:bg-gray-500 hover:text-white text-black dark:bg-gray-700 dark:text-white">
                                        <td class="px-6 py-4 text-center">{{ cliente.id }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.numeroDocumento }}</td>
                                        <td class="px-6 py-4 text-left font-semibold">{{ cliente.razonSocial }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.direccion }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.tbprovincia ? cliente.tbprovincia.prov_nombre : 'Sin ciudad' }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.asesor }}</td>
                                        <td class="py-4 text-center">
                                            <div :class="{
                                                'bg-blue-600 text-white': cliente.ctg === 'Vip',
                                                'bg-yellow-600 text-white': cliente.ctg === 'Regular',
                                                'bg-red-600 text-white': cliente.ctg === 'Sin Informacion',
                                                'bg-green-600 text-white': cliente.ctg === 'Potencial'
                                            }" class="inline-block px-2 py-1 rounded">
                                                <b>{{ cliente.ctg }}</b>
                                            </div>
                                        </td>
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <Link class="text-center text-white bg-green-500 hover:bg-green-600 py-1.5 px-2 rounded-md" :href="route('clientes.edit', cliente.id)" v-if="$page.props.user.permissions.includes('Acciones Administrador')">
                                                <i class="bi bi-pencil-square"></i>
                                            </Link>
                                            <button @click="$event => deleteCliente(cliente.id, cliente.razonSocial)" class="text-center ml-1 text-white bg-red-500 hover:bg-red-600 py-1 px-2 rounded-md" v-if="$page.props.user.permissions.includes('Acciones Administrador')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                            <button @click="openCtgModal(cliente)" class="text-center ml-1 text-white bg-blue-500 hover:bg-blue-600 py-1 px-2 rounded-md">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredClients.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                        <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                            <div class="hidden sm:block">
                                <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                                    <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                                    <p class="text-gray-700 dark:text-white font-semibold">Total de Clientes: {{ totalCount }}</p>
                                </div>
                            </div>
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
