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


//eliminar
const deleteHservicio= (id, modelo) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar ' +modelo+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('hservicios.destroy', id), {
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
                        text: "Requerimientos de servicio  eliminado exitosamente"
                    });
                }
            });
        }
    });
}

//formateo de fecha y hora
const formatDate = (dateString) => {
    const options = { month: 'short', day: '2-digit', year: 'numeric' };
    const offset = new Date().getTimezoneOffset();
    const offsetMilliseconds = offset * 60 * 1000;
    const date = new Date(new Date(dateString).getTime() + offsetMilliseconds);
    return date.toLocaleDateString('es-ES', options);
};

//llamando cliente_id razonSocial local datos
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
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Hojas Servicio</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 uppercase text-sm justify-between">
                        <Link @click="guardarRequerimientoId(idHojasServicio)" :href="route('hservicios.create')" class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fa fa-plus-circle mx-2"></i>Requerimientos Servicio
                        </Link>
                        <Link :href="route('servicios.index')" class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                        <i class="fas fa-arrow-left mx-2"></i>Servicios
                    </Link>
                    </div>
                    <div class="md:mt-0 mt-4">
                        <div class="w-full my-2 text-center font-bold flex justify-center text-black dark:border-gray-700">
                           <p class="py-2 text-black dark:text-white rounded ml-1">N° Informe | {{ nInforme }}</p> <p class="text-center mx-2 ml-1 py-2 dark:text-white"> - {{ razonSocial }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <InputLabel for="table-search" class="block text-md font-medium text-gray-700">Buscar</InputLabel>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                            <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar Hoja de Servico">
                            </div>
                        </div>
                    <div>
                        <div class="flex flex-wrap gap-4 my-6">
                            <div class="dark:text-white uppercase text-xs font-bold">
                                <span class="bg-indigo-800 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-indigo-800 dark:text-white">{{ $page.props.totalHservicio }}</span>
                                Equipos en total
                            </div>
                            <div v-for="(count, requiere) in $page.props.countByRequiere" :key="requiere" class="dark:text-white text-xs font-bold">
                                <span :class="getBadgeClass(requiere)" class="text-xs font-medium px-2.5 py-0.5 rounded text-white me-2">{{ count }}</span>
                                {{ requiere }}
                            </div>
                        </div>
                        <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">marca</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">modelo</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">capacidad</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">serie</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">div</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">plataforma</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">diagnostico</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">requiere</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">registro</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">fecha</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-xs">
                                    <tr v-for="hservicio in filteredHservicios" :key="hservicio.id" class="bg-white text-black border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                        <!-- Mostrar los datos de cada hservicio -->
                                        <td class="px-6 py-4 text-center">{{ hservicio.hmarca ? hservicio.hmarca.nombre : 'Sin marca' }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.modelo }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.capacidad }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.serie }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.div }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.plataforma }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.diagnostico }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.requiere }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ hservicio.tecnico }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ formatDate(hservicio.fecha) }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">
                                            <Link :href="route('hservicios.edit',  hservicio.id)" class="mx-2 px-1 py-1 bg-green-700 text-white rounded hover:bg-green-500">
                                                <i class="bi bi-pencil-square"></i>
                                            </Link>
                                            <Button @click="$event => deleteHservicio(hservicio.id,hservicio.modelo)" class=" ml-1 px-1 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                                <i class="bi bi-trash3"></i>
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="filteredHservicios.length === 0" class="text-center py-2 dark:text-white">
                            No se encontraron datos.
                        </div>
                    </div>
                    <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                        <div class="hidden sm:block">
                            <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                                <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                                <p class="text-gray-700 dark:text-white font-semibold">Total de Hojas de Servicios: {{ totalCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:text-end text-center">
                            <nav aria-label="Page navigation example mt-4">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <li>
                                        <button @click="previousPage" :disabled="!hservicios.prev_page_url"
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
                                        <button @click="nextPage" :disabled="!hservicios.next_page_url"
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
    name: 'CategoriesIndex',
    methods: {
        guardarRequerimientoId(idHojasServicio) {
            // Guardar el producto_id en localStorage
            localStorage.setItem('idrequerimiento', idHojasServicio);
        },
    }
};
</script>
