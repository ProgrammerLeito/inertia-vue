<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import ModalResponsive from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';
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
        // Filtramos los servicios basándonos en si la cadena de búsqueda está incluida en el ID o el número de informe
        const serviciosFiltrados = props.servicios.data.filter(servicio => {
            return servicio.id.toString().includes(searchLowerCase) || servicio.n_informe.includes(searchLowerCase);
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
    <AppLayout title="Hojas de Servicio no Cotizados">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Hojas Servicio no Cotizados</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 uppercase text-sm justify-between">
                        <Link :href="route('c_servicos.index')" class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fas fa-arrow-left mx-2"></i>Lista de cotizaciones
                        </Link>
                    </div>
                    <div class="w-full mt-4 p-4 bg-gray-200 border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <div class="mb-1 font-normal text-dark dark:text-white">
                            Lista de las hojas de servicio que aun no se han cotizado.
                            <b>Seleccione y luego Haga doble Click en el informe a Cotizar</b>:
                        </div>
                    </div>
                    <div class="flex flex-col items-start py-2">
                        <InputLabel for="table-search" class="block text-md font-medium text-gray-700">Buscar
                        </InputLabel>
                        <div class="relative mt-1">
                            <div
                                class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input v-model="searchQuery" type="text" id="table-search"
                                class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Buscar el id o n_informe">
                        </div>
                    </div>
                    <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N° Informe</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cliente</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Descripcion</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">T. Bal</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-xs">
                                <template v-for="(filteredServicio, key) in groupedServicios" :key="key">
                                    <tr class="bg-gray-300 dark:bg-gray-900">
                                        <td colspan="7" class="px-12 py-2 uppercase text-start font-bold text-gray-900 dark:text-white">
                                            <strong>{{ key }}</strong>
                                        </td>
                                    </tr>
                                    <tr v-for="(servicio, i) in filteredServicio" :key="servicio.id" class="bg-white text-black border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                        <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.n_informe }}</td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.cliente ?
                                            servicio.cliente.razonSocial : 'Sin marca' }}</td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.descripcion }}</td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <span
                                                class=" text-gray-800 dark:text-white bg-yellow-500 text-sm font-medium me-2 px-2.5 py-0.5 rounded  dark:bg-yellow-500">{{
                                                    $page.props.totalHservicio[servicio.id] ?? 'N/R' }}</span>
                                        </td>
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <button @click="redirectToCreate(servicio)"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded">
                                                <i class="fas fa-arrow-right fa-beat hover:text-white text-yellow-400 mx-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                        <div class="hidden sm:block">
                            <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                                <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                                <p class="text-gray-700 dark:text-white font-semibold">Total de Servicios no Cotizados: {{ totalCount }}</p>
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
            this.$inertia.visit(route('requiere.cotiza', { servicio_id: servicio.id }));
        }
    }
};
</script>
