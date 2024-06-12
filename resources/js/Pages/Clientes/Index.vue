<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';
import { ref, watchEffect } from 'vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import { Inertia } from '@inertiajs/inertia';

const searchQuery = ref('');
const filteredClients = ref([]);
const selectedProvincia = ref('');
const isDropdownOpen = ref(false);

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

// Función para seleccionar una provincia
const selectProvincia = (id) => {
    selectedProvincia.value = id;
    toggleDropdown(); // Cierra el dropdown después de seleccionar una provincia
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
            // Mostrar el segundo modal para ingresar la contraseña
            Swal.fire({
                title: "Ingrese su Contraseña para confirmar la eliminación",
                input: "password",
                inputAttributes: {
                    autocapitalize: "off"
                },
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                showLoaderOnConfirm: true,
                cancelButtonText: "Cancelar",
                preConfirm: (password) => {
                    return fetch('/comprobarEliminacionCli', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Para protección CSRF
                        },
                        body: JSON.stringify({ passwordconfirmacion: password })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.siexisteusuario) {
                            throw new Error('Contraseña incorrecta');
                        }
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Error: ${error}`
                        );
                    });
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
    });
}


watchEffect(() => {
    filteredClients.value = props.clientes.data.filter(cliente => {
        return (cliente.modelo && cliente.modelo.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
            cliente.id.toString().includes(searchQuery.value.toLowerCase()) &&
            (selectedProvincia.value === '' || cliente.tbprovincia_id === selectedProvincia.value);
    });
});

const openCtgModal = async (cliente) => {
    const modalTitle = `Calificación del cliente: ${cliente.razonSocial}`;

    const options = {
        title: modalTitle,
        input: 'select',
        inputOptions: {
            '': 'Seleccione una opción 🔽',
            'Vip': 'Vip',
            'Potencial': 'Potencial',
            'Regular': 'Regular',
            'Sin Informacion': 'Sin Informacion',
        },
        customClass: {
            title: 'text-2xl font-bold tracking-widest',
            input: 'text-base tracking-widest',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest',
        },
        showCancelButton: true,
        confirmButtonText: 'Asignar',
        showLoaderOnConfirm: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debes seleccionar un tipo de calificación';
            }
        },
        preConfirm: async (value) => {
            const response = await Inertia.post(route('clientes.updateCtg'), { cliente_id: cliente.id, ctg: value });
            if (response && response.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Calificación asignada exitosamente',
                    timer: 1000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            } else {
                throw new Error(response ? response.statusText : 'Error desconocido');
            }
        }
    };

    const result = await Swal.fire(options);
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
                        <div class="py-2 sm:py-2">
                            <div class="md:max-w-lg">
                                <div class="flex">
                                    <div class="relative w-full">
                                        <input v-model="searchQuery" type="text" id="table-search" class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-l-lg border-s-gray-200 border-s-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:border-s-gray-700 dark:border-gray-600 placeholder-gray-700 dark:text-black dark:focus:border-blue-500" placeholder="Buscar cliente" required />
                                    </div>
                                    <div class="relative">
                                        <button id="dropdown-button" data-dropdown-toggle="dropdown" @click="toggleDropdown" class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-r-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600" type="button">
                                            {{ selectedProvincia ? tbprovincias.find(p => p.id === selectedProvincia).prov_nombre : 'Ciudades' }}
                                            <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </button>
                                        <div id="dropdown" :class="{ 'hidden': !isDropdownOpen }" class="z-20 absolute right-0 mt-2 w-44 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 font-bold">
                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdown-button">
                                                <li>
                                                    <button @click="selectProvincia('')" type="button" class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white font-bold">Todas</button>
                                                </li>
                                                <li v-for="tbprovincia in tbprovincias" :key="tbprovincia.id">
                                                    <button @click="selectProvincia(tbprovincia.id)" type="button" class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white font-bold">{{ tbprovincia.prov_nombre }}</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
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
                                <tbody  class="text-center">
                                    <tr @dblclick="redirectToClient(cliente.id); guardarClienteId(cliente.id)" v-for="(cliente, i) in filteredClients" :key="cliente.id" class="bg-white dark:hover:bg-gray-900 cursor-pointer hover:bg-gray-500 hover:text-white text-black dark:bg-gray-700 dark:text-white">
                                        <td class="px-6 py-4 text-center">{{ cliente.id }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.numeroDocumento }}</td>
                                        <td class="px-6 py-4 text-left font-semibold">{{ cliente.razonSocial }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.direccion }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.tbprovincia ? cliente.tbprovincia.prov_nombre : 'Sin ciudad' }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.asesor }}</td>
                                        <td class="py-4 text-center">
                                            <div :class="{
                                                'bg-blue-600': cliente.ctg === 'Vip',
                                                'bg-yellow-600': cliente.ctg === 'Regular',
                                                'bg-red-600': cliente.ctg === 'Sin Informacion',
                                                'bg-green-600': cliente.ctg === 'Potencial'
                                            }" class="inline-block px-2 py-1 rounded">
                                                <b>{{ cliente.ctg }}</b>
                                            </div>
                                        </td>
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <Link class="text-center text-white bg-green-500 hover:bg-green-600 py-1.5 px-2 rounded-md" :href="route('clientes.edit', cliente.id)">
                                                <i class="bi bi-pencil-square"></i>
                                            </Link>
                                            <button @click="$event => deleteCliente(cliente.id, cliente.razonSocial)" class="text-center ml-1 text-white bg-red-500 hover:bg-red-600 py-1 px-2 rounded-md">
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
            window.location.href = this.route('datos.index', { cliente_id: cliente_id });
        },
        guardarClienteId(cliente_id) {
            // Guardar el producto_id en localStorage
            localStorage.setItem('cliente_id', cliente_id);
            localStorage.setItem('razonSocial', cliente.razonSocial);
        },
    }
};
</script>
