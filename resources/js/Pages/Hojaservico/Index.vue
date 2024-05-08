<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import Swal from 'sweetalert2';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';
import {computed, nextTick, ref, watchEffect } from 'vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';

const searchQuery = ref('');
const{servicios,hmarcas}=defineProps({
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
    countByRequiere:{type: Object}
});
const form = useForm({
    id:''
})


const selectedServicio = computed(() => {
    return servicios.find(servicio => servicio.id === form.servicio_id);
});

const deleteHservicio= (id, marca) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar ' +marca+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('hservicios.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'requerimientos de servicio  eliminado exitosamente',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false
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

const  getBadgeClass=(requiere)=> {
            // Asignar diferentes clases de color según el valor de "requiere"
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
                    return 'bg-gray-500'; // Color predeterminado para otras categorías
            }
        }
</script>
<template>
    <AppLayout title="Requerimientos de Servicio">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Requerimientos del Servicio</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 uppercase text-sm justify-between">
                        <Link :href="route('hservicios.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fa fa-plus-circle fa-beat mx-2"></i>Requerimientos-hoja-servicio
                        </Link>
                        <Link :href="route('servicios.index')" class="text-white uppercase text-xs bg-indigo-700 hover:bg-indigo-800 py-2 px-2 rounded md:w-min whitespace-nowrap w-full text-center">
                        <i class="fas fa-arrow-left mx-2"></i> servicios
                    </Link>
                    </div>
                    <div class="flex flex-col">
                        <InputLabel for="table-search" class="block text-md font-medium text-gray-700">Buscar</InputLabel>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                            <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar el id o n_informe">
                            </div>
                        </div>
                    <div>
                        <div class="font-semibold text-center dark:text-white my-6">
                            <template v-if="selectedServicio && selectedServicio.n_informe">
                            n| informe | {{ selectedServicio.n_informe }}
                            </template>
                            <template v-else>
                            Sin n| informe
                            </template>
                        </div>
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
                        <div class="relative overflow-x-auto shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
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
                                    <tr v-for="hservicio in hservicios" :key="hservicio.id"  class="bg-white text-black border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
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
                                            <Link :href="route('hservicios.edit',  hservicio.id)">
                                                    <i class="bi bi-pencil-square text-green-500 mx-2"></i>
                                                </Link>
                                            <ButtonDelete @click="$event => deleteHservicio(hservicio.id,hservicio.marca)" class="ml-1">
                                                <i class="bi bi-trash3 text-red-500"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-white grid v-screen dark:bg-gray-800  dark:text-gray-200 place-items-center mt-4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
