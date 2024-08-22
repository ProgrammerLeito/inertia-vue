<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref, watchEffect } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const searchQuery = ref('');
const filteredServicio = ref([]);
const groupedServicios = ref({});

const props = defineProps({
    servicios: {
        type: Object,
        required: true
    },
    users: {
        type: Object
    },
    datos: {
        type: Object
    }
});
const form = useForm({
    id: ''
})
watchEffect(() => {
    if (props.servicios.data) {
        const searchLowerCase = searchQuery.value.toLowerCase().trim();

        // Filtramos los servicios basándonos en si la cadena de búsqueda está incluida en varios campos
        const serviciosFiltrados = props.servicios.data.filter(servicio => {
            const idMatch = servicio.id.toString().includes(searchLowerCase);
            const nInformeMatch = servicio.n_informe.includes(searchLowerCase);
            const descripcionMatch = servicio.descripcion.toLowerCase().includes(searchLowerCase);
            const razonSocialMatch = servicio.cliente ? servicio.cliente.razonSocial.toLowerCase().includes(searchLowerCase) : false;
            const fecht = servicio.fecha.includes(searchLowerCase);
            const jomar= servicio.estado.includes(searchLowerCase);
            const cruz= servicio.e_servicio.includes(searchLowerCase);
            const valencia = servicio.user ? servicio.user.name.toLowerCase().includes(searchLowerCase) : false;

            // Retorna true si alguna de las condiciones se cumple
            return idMatch || nInformeMatch || descripcionMatch || razonSocialMatch || fecht || jomar || cruz || valencia;
        });

        groupedServicios.value = groupByMonthAndYear(serviciosFiltrados);
    }
});

// Función para agrupar por mes y año
function groupByMonthAndYear(servicios) {
    const grouped = {};
    servicios.forEach(servicio => {
        const date = new Date(servicio.fecha);
        const month = date.toLocaleString('default', { month: 'long' });
        const year = date.getFullYear();
        const key = `${month} ${year}`;
        if (!grouped[key]) {
            grouped[key] = [];
        }
        grouped[key].push(servicio);
    });
    return grouped;
}

//eliminar
const deleteServicio = (id, n_informe) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar todos los datos del N° Informe : ' + n_informe + '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('servicios.destroy', id), {
                onSuccess: () => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "bottom-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: "success",
                        title: 'Éxito',
                        text: "Servicio eliminado exitosamente",
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
//cambio de estado
const openCtgModal = async (servicio) => {
    const modalTitle = `Asigna el Estado del cliente: ${servicio.cliente.razonSocial}`;

    const options = {
        title: modalTitle,
        input: 'select',
        inputOptions: {
            'atendido': 'atendido',
            'no atendido': 'no atendido',
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
            const response = await Inertia.post(route('servicios.cambiar_estado'), { servicio_id: servicio.id, estado: value });
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

//formateo de fecha y hora
const formatDate = (dateString) => {
    const options = { month: 'short', day: '2-digit', year: 'numeric' };

    // Obtener la diferencia de zona horaria en minutos
    const offset = new Date().getTimezoneOffset();
    // Convertir minutos a milisegundos
    const offsetMilliseconds = offset * 60 * 1000;

    // Crear el objeto Date en la zona horaria local sumando el offset
    const date = new Date(new Date(dateString).getTime() + offsetMilliseconds);
    return date.toLocaleDateString('es-ES', options);
};
const formatTime = (timeString) => {
    const time = new Date("2000-01-01T" + timeString);
    return time.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
};



const openModal = (servicio) => {
    if (servicio) {
        Swal.fire({
            title: 'DETALLES DEL SERVICIO',
            width: 800,
            html: `<hr/><br/>
            <div style="text-align: left;" class="text-justify p-1 uppercase">
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>ESTADO</strong>: ${servicio.estado}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>CLIENTE</strong>: ${servicio.cliente ? servicio.cliente.razonSocial : 'Sin marca'}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>N° INFORME</strong>: ${servicio.n_informe}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>DESCRIPCION</strong>: ${servicio.descripcion}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>FECHA REGISTRO</strong>: ${servicio.fecha}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>REGISTRADO POR</strong>: ${servicio.tecnico}</p>
            </div><br/><hr/>
            `,
            confirmButtonText: 'Cerrar',
            customClass: {
                title: 'text-2xl rounded-t-md text-white font-bold bg-green-600 py-3  tracking-widest ',
                content: 'text-base tracking-widest font-bold',
                confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
            },
        });
    }
};

const formPage = useForm({});
const onPageClick = (event) => {
    formPage.get(route('servicios.index', { page: event }));
}
const previousPage = () => {
    const prevPage = props.servicios.current_page - 1;
    formPage.get(route('servicios.index', { page: prevPage }));
};

const nextPage = () => {
    const nextPage = props.servicios.current_page + 1;
    formPage.get(route('servicios.index', { page: nextPage }));
};

const goToPage = (page) => {
    formPage.get(route('servicios.index', { page }));
};

const total_pages = props.servicios.last_page;
const current_page = props.servicios.current_page;
const countPerPage = props.servicios.data.length;
const totalCount = props.servicios.total;
</script>
<template>
    <AppLayout title="Servicios">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">
                Hojas De Servicios De Clientes
            </h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 uppercase text-sm justify-between">
                        <Link :href="route('servicios.create')" class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fa fa-plus-circle mx-2"></i>Agregar hoja tecnica
                        </Link>
                    </div>
                    <div class="flex flex-col">
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                            <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar Hoja de Servicio">
                        </div>
                    </div>
                    <div>
                        <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-600 mt-2">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N° informe</th>
                                        <th scope="col" class="px-6 py-3 text-left dark:border-white border-b-2">Cliente</th>
                                        <th scope="col" class="px-6 py-3 text-left dark:border-white border-b-2">Descripcion</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">F. Atencion Cliente</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">R. Transporte</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">R. Servicio</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-xs">
                                    <template v-for="(filteredServicio, key) in groupedServicios" :key="key">
                                        <tr class="bg-gray-300 dark:bg-gray-900">
                                            <td colspan="8" class="px-12 py-2 uppercase text-start font-bold text-gray-900 dark:text-white">
                                                <strong>{{ key }}</strong></td>
                                        </tr>
                                        <tr v-for="(servicio, i) in filteredServicio" :key="servicio.id" class="bg-white text-gray-900 border-b border-gray-400 dark:border-white font-bold dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 hover:text-white cursor-pointer">
                                            <td class="px-6 py-4 text-center capitalize text-white">
                                                <div :class="{
                                                    'bg-red-600': servicio.estado === 'no atendido',
                                                    'bg-green-600': servicio.estado === 'atendido'
                                                }" class="inline-block px-2 py-1 rounded">
                                                    <b>{{ servicio.estado }}</b>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.n_informe }}</td>
                                            <td class="px-6 py-4 text-left whitespace-normal">{{ servicio.cliente ? servicio.cliente.razonSocial : 'Sin marca' }}</td>
                                            <td class="px-6 py-4 text-left whitespace-nowrap">{{ servicio.descripcion }}</td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ formatDate(servicio.fecha) }} a las {{ formatTime(servicio.hora) }}</td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.user ? `${servicio.user.name}  ${servicio.user.apellidopat}` : 'Por Asignar' }}</td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.e_servicio ? `${servicio.e_servicio}` : 'Por Asignar' }}</td>
                                            <td class="p-3 text-center whitespace-nowrap">
                                                <button @click="redirectToCreate(servicio)"
                                                    class="bg-blue-600 hover:bg-blue-400 text-white px-2 py-1 rounded mx-1">
                                                    <i class="fas fa-arrow-right fa-beat"></i>
                                                </button>

                                                <Link :href="route('servicios.edit', servicio.id)" class="py-1 px-2 bg-green-600 rounded hover:bg-green-600 dark:hover:bg-white dark:hover:text-green-600">
                                                    <i class="bi bi-pencil-square"></i>
                                                </Link>
                                                <Button
                                                    @click="$event => deleteServicio(servicio.id, servicio.n_informe)"
                                                    class="ml-1 py-1 px-2 bg-red-600 rounded  hover:bg-red-600  dark:hover:bg-white dark:hover:text-red-600">
                                                    <i class="bi bi-trash3"></i>
                                                </Button>

                                                <button @click="openModal(servicio)"
                                                    class="text-center ml-1 text-white bg-blue-500 rounded-md  hover:bg-blue-600 py-1 px-2 dark:hover:bg-white dark:hover:text-blue-600"><i
                                                        class="bi bi-eye"></i>
                                                </button>
                                                <button @click="openCtgModal(servicio)"
                                                    class="text-center ml-1 text-white bg-blue-500 hover:bg-blue-600 py-1 px-2 dark:hover:bg-white dark:hover:text-blue-600 rounded-md"><i
                                                        class="fas fa-star"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6 grid grid-cols-1 gap-y-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 sm:gap-x-8 mb-3">
                            <div class="flex w-auto md:max-w-sm p-2 text-xs font-bold bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 items-center md:justify-center justify-start whitespace-break-spaces">
                                <span class="bg-blue-600 text-white me-2 px-2.5 py-0.5 rounded">Visitado</span>
                                <p class="text-gray-700 ml-2 dark:text-gray-100">
                                    Equipo o instrumento ya verificado por servicio tecnico
                                </p>
                            </div>
                            <div class="flex w-auto md:max-w-sm p-2 text-xs font-bold bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 items-center md:justify-center justify-start whitespace-break-spaces">
                                <span class="bg-green-600 text-white me-2 px-2.5 py-0.5 rounded">Cotizado</span>
                                <p class="text-gray-700 ml-2 dark:text-gray-100">
                                    A la espera de una confirmacion positiva
                                </p>
                            </div>
                            <div class="flex w-auto md:max-w-sm p-2 bg-white border text-xs font-bold border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 items-center md:justify-center justify-start whitespace-break-spaces">
                                <span class="bg-yellow-600 text-white me-2 px-2.5 py-0.5 rounded">Pendiente</span>
                                <p class="text-gray-700 ml-2 dark:text-gray-100">
                                    Equipo o instrumento aun no revisado por servicio tecnico
                                </p>
                            </div>
                            <div class="flex w-auto md:max-w-sm p-2 bg-white border text-xs font-bold border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 items-center md:justify-center justify-start whitespace-break-spaces">
                                <span class="bg-red-600 text-white me-2 px-2.5 py-0.5 rounded">Finalizado</span>
                                <p class="text-gray-700 ml-2 dark:text-gray-100">
                                    Concluida y aceptada por el cliente
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                        <div class="hidden sm:block">
                            <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                                <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                                <p class="text-gray-700 dark:text-white font-semibold">Total de Servicios: {{ totalCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:text-end text-center">
                            <nav aria-label="Page navigation example mt-4">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <li>
                                        <button @click="previousPage" :disabled="!servicios.prev_page_url"
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
                                        <button @click="nextPage" :disabled="!servicios.next_page_url"
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

    </AppLayout>
</template>
<script>
export default {
    methods: {
        redirectToCreate(servicio) {
            localStorage.setItem('servicio_id', servicio.id);
            localStorage.setItem('razonSocial', servicio.cliente ? servicio.cliente.razonSocial : 'Sin marca');
            localStorage.setItem('n_informe', servicio.n_informe);
            this.$inertia.visit(route('hservicios.index', { servicio_id: servicio.id }));
        }
    }
};
</script>
