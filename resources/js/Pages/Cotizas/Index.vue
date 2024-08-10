<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
 
const props = defineProps({
    cventas: {
        type: Object,
        required: true
    },
    clientes: {
        type: Object,
        required: true
    }
});
const form = useForm({
    id: ''
})
 
const deleteCotizacion = (id, cliente_id, cventa) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    // Verificar si cventa y cventa.cliente están definidos antes de acceder a razonSocial
    const razonSocial = cventa && cventa.cliente ? cventa.cliente.razonSocial : 'Cliente desconocido';
    
    alerta.fire({
        title: `¿Estás seguro de eliminar la cotización de ${razonSocial}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('cventas.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Cotización eliminada exitosamente',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                },
                onError: () => {
                    alerta.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al eliminar la cotización',
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
 
const openCtgModal = async (cventa) => {
    const modalTitle = `Cotizacion del cliente: ${cventa.cliente ? cventa.cliente.razonSocial : 'Sin cliente'}`;
 
    const options = {
        title: modalTitle,
        input: 'select',
        inputOptions: {
            'Por Enviar': 'Por Enviar',
            'Enviado': 'Enviado',
            'Aceptado': 'Aceptado',
            'Rechazado': 'Rechazado',
            'Finalizado': 'Finalizado',
        },
        customClass: {
            title: 'text-2xl font-bold tracking-widest ',
            input: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
        inputPlaceholder: 'Selecciona una opcion',
        showCancelButton: true,
        confirmButtonColor: '#009846',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Asignar',
        showLoaderOnConfirm: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debes seleccionar un tipo de estado de envio';
            }
        },
        preConfirm: async (value) => {
            const response = await Inertia.post(route('cventas.cambiar_estado'), { cventa_id: cventa.id, estado: value });
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
 
const openModal = (cventa) => {
    if (cventa) {
        Swal.fire({
            title: 'DETALLES DE LA COTIZACION POR VENTA',
            width: 800,
            html: `<hr/><br/>
            <div style="text-align: left;" class="text-justify p-1 uppercase">
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>N° COTIZACION</strong>: ${cventa.n_cotizacion}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>ESTADO</strong>: ${cventa.estado}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>CLIENTE</strong>: ${cventa.cliente ? cventa.cliente.razonSocial : 'Sin cliente'} </p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>FECHA REGISTRO</strong>: ${cventa.fecha}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>REGISTRADO POR</strong>: ${cventa.tecnico}</p>
            </div><br/><hr/>
            `,
            confirmButtonText: 'Cerrar',
            customClass: {
                title: 'text-sm sm:text-xs lg:text-2xl  xl:text-2xl font-extrabold bg-green-600  overflow-hidden text-white rounded-t-md  py-3 tracking-widest ',
                content: 'text-xs sm:text-xs lg:text-base xl:text-base  tracking-widest ',
                confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
            },
        });
    }
};
 
const formPage = useForm({});
const onPageClick = (event) => {
    formPage.get(route('cventas.index', { page: event }));
}
const previousPage = () => {
    const prevPage = props.cventas.current_page - 1;
    formPage.get(route('cventas.index', { page: prevPage }));
};
 
const nextPage = () => {
    const nextPage = props.cventas.current_page + 1;
    formPage.get(route('cventas.index', { page: nextPage }));
};
 
const goToPage = (page) => {
    formPage.get(route('cventas.index', { page }));
};
 
const total_pages = props.cventas.last_page;
const current_page = props.cventas.current_page;
const countPerPage = props.cventas.data.length;
const totalCount = props.cventas.total;
</script>
 
<template>
    <AppLayout title="Cotizaciones por Venta">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Cotizaciones</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('cventas.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-folder-plus mx-1"></i> Registrar cotizacion
                        </Link>
                    </div>
                    <div>
                        <div class="py-1">
                            <div class="grid grid-cols-1 gap-y-2 sm:grid-cols-1 sm:gap-x-2 mb-1">
                                <div class="flex flex-col">
                                    <InputLabel for="table-search" class="block text-md font-medium text-gray-700 dark:text-white">Buscar</InputLabel>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                            </svg>
                                        </div>
                                        <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar cotizacion">
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md md:rounded-lg rounded-md shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">C | Factura</th>
                                        <th scope="col" class="px-6 py-3 text-left dark:border-white border-b-2">Cliente</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Referencia</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Emision</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Neto</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Total</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-xs">
                                    <tr v-for="(cventa, index) in cventas.data" :key="cventa.id" class="bg-white text-black border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-300 cursor-pointer">
                                        <td class="px-1 py-4 text-center">{{ cventa.n_cotizacion }}</td>
                                        <td class="px-1 py-4 text-center fa-fade font-semibold">s|codigo</td>
                                        <td class="px-1 py-4 text-center">
                                            {{ cventa.cliente ? cventa.cliente.razonSocial : 'Sin cliente' }}
                                        </td>
                                        <!-- <td class="px-6 py-4 text-center">{{ cventa.tenor ? cventa.tenor.name : 'Sin codigo' }}
                                        </td> -->
                                        <td class="px-6 py-4 text-center">{{ cventa.fecha }}</td>
                                        <td class="px-6 py-4 text-center">{{ cventa.moneda == "dolares $" ? "$" : "S/" }} {{ cventa.subtotal }}</td>
                                        <td class="px-6 py-4 text-center">{{ cventa.moneda == "dolares $" ? "$" : "S/" }} {{ cventa.total }}</td>
                                        <td class="px-6 py-4 text-center text-white">
                                            <div :class="{
                                                'bg-blue-600': cventa.estado === 'Por Enviar',
                                                'bg-yellow-600': cventa.estado === 'Enviado',
                                                'bg-green-600': cventa.estado === 'Aceptado',
                                                'bg-red-600': cventa.estado === 'Rechazado',
                                                'bg-indigo-600': cventa.estado === 'Finalizado',
                                            }" class="inline-block px-2 py-1 rounded">
                                                <b>{{ cventa.estado }}</b>
                                            </div>
                                        </td>
                                        <td class="py-4 text-center whitespace-nowrap">
                                            <button @click="openCtgModal(cventa)"
                                                class="text-center mx-1 text-white bg-blue-500 hover:bg-blue-600 py-1 px-2 dark:hover:bg-white dark:hover:text-blue-600 rounded-md"><i
                                                    class="fas fa-star"></i></button>
                                            <Link :href="route('cventas.edit', { cventa: cventa.id })"
                                                class=" bg-green-500 p-1 dark:hover:bg-white py-1 px-2  dark:hover:text-green-500 rounded">
                                            <i class="bi bi-pencil-square"></i>
                                            </Link>
                                            <Button @click="$event => deleteCotizacion(cventa.id, cventa.cliente_id, cventa)"
                                                class="ml-1 bg-red-600 dark:hover:bg-white dark:hover:text-red-600 py-1 px-2 font-extrabold dark:text-white rounded cursor-pointer text-white">
                                                <i class="bi bi-trash3"></i>
                                            </Button>
                                            <button @click="openModal(cventa)"
                                                class="text-center ml-1 text-white bg-blue-500 hover:bg-blue-600 py-1 px-2 rounded-md"><i
                                                    class="bi bi-eye"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex flex-wrap justify-between">
                            <div class="mt-4 text-star">
                                <p class="text-gray-700 dark:text-white">Registros por página: {{ countPerPage }}
                                    Total de
                                    registros: {{
                                        totalCount }}</p>
                            </div>
                            <div class="mt-4 text-end">
                                <nav aria-label="Page navigation example mt-4">
                                    <ul class="inline-flex -space-x-px text-sm">
                                        <li>
                                            <button @click="previousPage" :disabled="!cventas.prev_page_url"
                                                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                Previous
                                            </button>
                                        </li>
                                        <li v-for="page in total_pages" :key="page">
                                            <button @click="goToPage(page)"
                                                :class="{ 'text-blue-600 border-blue-300 dark:text-gray-800 bg-blue-50 hover:bg-blue-100 hover:text-blue-700': page === current_page, 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': page !== current_page }"
                                                class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                {{ page }}
                                            </button>
                                        </li>
                                        <li>
                                            <button @click="nextPage" :disabled="!cventas.next_page_url"
                                                class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
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