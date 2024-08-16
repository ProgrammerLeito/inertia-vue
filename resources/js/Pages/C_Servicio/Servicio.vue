<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import Swal from 'sweetalert2';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';
import {computed, nextTick, ref, watchEffect, onMounted } from 'vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';

const searchQuery = ref('');
const filteredHservicios = ref([]);

const{hservicios,servicios,hmarcas}=defineProps({
    hservicios:{
        type : Array,
        required:true
    },
    servicios:{
        type:Object
    },
    hmarcas:{
        type:Object
    },
    countByRequiere:{type: Object},
});
const form = useForm({
    id:''
})

//filtros
watchEffect(() => {
    filteredHservicios.value = hservicios.data.filter(hservicio => {
        const searchLower = searchQuery.value.toLowerCase();
        return (hservicio.modelo && hservicio.modelo.toLowerCase().includes(searchLower)) ||
               (hservicio.serie && hservicio.serie.toLowerCase().includes(searchLower)) ||
               hservicio.id.toString().includes(searchLower);
    });
});

//no se que hace
const selectedServicio = computed(() => {
    return servicios.find(servicio => servicio.id === form.servicio_id);
});


//formateo de fecha y hora
const formatDate = (dateString) => {
    const options = { month: 'short', day: '2-digit', year: 'numeric' };
    const offset = new Date().getTimezoneOffset();
    const offsetMilliseconds = offset * 60 * 1000;
    const date = new Date(new Date(dateString).getTime() + offsetMilliseconds);
    return date.toLocaleDateString('es-ES', options);
};

//llamando cliente_id razonSocial
const idHojasServicio = ref('');
const razonSocial = ref('');
const nInforme = ref('');

onMounted(() => {
    idHojasServicio.value = localStorage.getItem('servicio_id');
    razonSocial.value = localStorage.getItem('razonSocial');
    nInforme.value = localStorage.getItem('n_informe');
});
//estilos
const getBadgeClass=(requiere)=> {
    switch (requiere) {
        case 'REQUIERE MANTENIMIENTO':
            return 'bg-blue-400';
        case 'REQUIERE REPARACION':
            return 'bg-red-500';
        case 'POR REVISAR':
            return 'bg-yellow-400';
        case 'CERTIFICACION':
            return 'bg-green-500';
        case 'GARANTIA':
            return 'bg-purple-500';
        case 'IMPLEMENTACION':
            return 'bg-indigo-500';
        case 'CALIBRACION':
            return 'bg-pink-500';
        default:
            return 'bg-gray-500';
    }
};
//paginacion
const previousPage = () => {
    const prevPage = hservicios.current_page - 1;
    formPage.get(route('hservicios.index', { page: prevPage }));
};
const nextPage = () => {
    const nextPage = hservicios.current_page + 1;
    formPage.get(route('hservicios.index', { page: nextPage }));
};
const goToPage = (page) => {
    formPage.get(route('hservicios.index', { page }));
};

const total_pages = hservicios.last_page;
const current_page = hservicios.current_page;
const countPerPage = hservicios.data.length;
const totalCount = hservicios.total;
</script>

<template>
    <AppLayout title="Requerimientos de Servicio">
        <template #header>
            <h1 class="font-semibold tracking-widest text-base uppercase text-gray-800 leading-tight dark:text-white">requerimientos</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 uppercase text-sm justify-between">

                    </div>
                    <div class="md:mt-0 mt-4">
                        <div class="w-full p-6 bg-green-600 border border-green-700 tracking-widest text-white font-extralight rounded-lg shadow dark:bg-green-800 dark:border-gray-700">
                            <p class="text-center underline  dark:text-white">COTIZACION PARA:  {{ razonSocial }}</p>
                            <div class="font-bold text-center dark:text-white">
                                Informe N°|| {{ nInforme }}
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4  my-6">
                        <div class="dark:text-white uppercase text-xs font-bold">
                                <span class="bg-indigo-800 text-white tracking-widest text-xs py-2 uppercase  font-bold me-2 px-2 rounded dark:bg-indigo-800 dark:text-white">{{ $page.props.totalHservicio }} Equipos en total</span>
                            </div>
                            <div v-for="(count, requiere) in $page.props.countByRequiere" :key="requiere" class="dark:text-white text-xs font-bold">
                                <span :class="getBadgeClass(requiere)" class="text-xs font-bold px-2 py-2 rounded text-white me-2">{{ count }}  {{ requiere }}</span>
                            </div>
                        </div>


                    <div class="flex flex-col">
                        <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 tracking-widest dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">#</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">marca</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">modelo</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">capacidad</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">serie</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">div</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">plataforma</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">requiere</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cotizar</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-xs">
                                    <tr v-for="(hservicio,i) in filteredHservicios" :key="hservicio.id"  class="bg-white dark:hover:text-yellow-600 hover:text-white text-black border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                        <!-- Mostrar los datos de cada hservicio -->
                                        <td class="px-6 py-4 text-center">{{ i + 1 }}</td>
                                        <td class="px-6 py-4 text-center">{{ hservicio.hmarca ? hservicio.hmarca.nombre : 'Sin marca' }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.modelo }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.capacidad }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.serie }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.div }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.plataforma }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.requiere }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">

                                                <button @click="redirectToCreate(hservicio) "  class="bg-blue-600 hover:bg-blue-400 text-white px-2 py-1 rounded">Cotizar</button>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="filteredHservicios.length === 0" class="text-center py-2 dark:text-white">
                            No se encontraron datos.
                        </div>
                    </div>
                    <div class="flex flex-wrap tracking-widest justify-between">
                            <div class="mt-4 text-star">
                                <p class="text-gray-700 dark:text-white">Registros por página: {{ countPerPage }}
                                    Total de
                                    registros: {{
                                        totalCount }}</p>
                            </div>
                            <div class="mt-4 text-end">
                                <nav aria-label="Page navigation example mt-4">
                                    <ul class="inline-flex -space-x-px text-sm ">
                                        <li>
                                            <button @click="previousPage" :disabled="!hservicios.prev_page_url"
                                                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight tracking-widest text-gray-500 cursor-pointer bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
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
                                            <button @click="nextPage" :disabled="!hservicios.next_page_url"
                                                class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 tracking-widest cursor-pointer bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
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
    name: 'CategoriesIndex',
    methods: {
        redirectToCreate(hservicio) {
            localStorage.setItem('hservicioData', JSON.stringify(hservicio));
            this.$inertia.visit(route('c_servicos.create'));
        }
    }
};
</script>
