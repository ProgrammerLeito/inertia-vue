<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';
import {nextTick, ref, watchEffect } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const searchQuery = ref('');
const filteredServicio = ref([]);
const groupedServicios = ref({});

const props = defineProps({
    servicios: {
        type : Object,
        required: true
    },
    users:{
        type : Object
    },
    datos:{
        type:Object
    }
});
const form = useForm({
    id:''
})
//filtro
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

//eliminar
const deleteServicio= (id, n_informe) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar ' +n_informe+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('servicios.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Servicio eliminado exitosamente',
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


</script>
<template>
    <AppLayout title="Servicios" >
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Hojas de Servicio</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 uppercase text-sm justify-between">
                        <Link :href="route('servicios.create')" class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fa fa-plus-circle mx-2"></i>agregar hoja tecnica
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
                        <div class="relative overflow-x-auto shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N° informe</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">cliente</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">descripcion</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">fecha</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">R.transporte</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-xs">
                                    <template v-for="(filteredServicio, key) in groupedServicios" :key="key">
                                        <tr class="bg-gray-300 dark:bg-gray-900">
                                            <td colspan="7" class="px-6 py-3 text-start font-bold uppercase dark:text-white text-black"><strong><b>{{ key }}</b></strong></td>
                                        </tr>
                                        <tr v-for="(servicio, i) in filteredServicio" :key="servicio.id"  class="bg-white text-gray-900 font-bold dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 hover:text-white cursor-pointer">
                                            <td class="px-6 py-4 text-center">
                                                <div :class="{
                                                    'bg-blue-600': servicio.estado === 'Visitado',
                                                    'bg-green-600': servicio.estado === 'Cotizado',
                                                    'bg-yellow-600': servicio.estado === 'Pendiente',
                                                    'bg-red-600': servicio.estado === 'Finalizado'
                                                }" class="estadoco inline-block px-2 py-1 rounded font-bold text-white">
                                                    {{ servicio.estado }}
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.n_informe }}</td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.cliente ? servicio.cliente.razonSocial : 'Sin marca' }}</td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.descripcion }}</td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ formatDate(servicio.fecha) }} a las {{ formatTime(servicio.hora) }}</td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ servicio.user ? servicio.user.name : 'Sin usuario' }}</td>
                                            <td class="p-3 text-center">
                                                <Link  :href="route('hservicios.index', { servicio_id: servicio.id })">
                                                    <i class="fas fa-arrow-right fa-beat text-yellow-400 mx-2"></i>
                                                </Link>
                                                <Link :href="route('servicios.edit',  servicio.id)">
                                                    <i class="bi bi-pencil-square text-green-500 mx-2"></i>
                                                </Link>
                                                <ButtonDelete @click="$event => deleteServicio(servicio.id,servicio.n_informe)" class="ml-1">
                                                    <i class="bi bi-trash3 text-red-500"></i>
                                                </ButtonDelete>
                                            </td>
                                            <td class="hidden">{{ servicio.id }}</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
$(document).ready(function() {
    const changeState = async (servicioId, newState) => {
        try {
            await Inertia.post(route('servicios.cambiar_estado'), {
                servicio_id: servicioId,
                estado: newState
            });
            const servicio = filteredServicio.value.find(servicio => servicio.id === servicioId);
            if (servicio) {
                servicio.estado = newState;
            }
        } catch (error) {
            console.error(error);
        }
    };
    // Selecciona todos los elementos con la clase ".estadoco"
    $('.estadoco').on('contextmenu', function(event) {
        // Evita el comportamiento por defecto del menú contextual
        event.preventDefault();
        let fila = $(this).closest('tr');
        let estadoco = fila.find('td:eq(0)').text();
        let servicioid = fila.find('td:eq(7)').text();
        console.log(estadoco);
        console.log(servicioid);
        // console.log(event.target);
        if (estadoco == "Visitado") {
            changeState(servicioid, 'Cotizado')
        }
        if (estadoco == "Cotizado") {
            changeState(servicioid, 'Pendiente')
        }
        if (estadoco == "Pendiente") {
            changeState(servicioid, 'Finalizado')
        }
        if (estadoco == "Finalizado") {
            changeState(servicioid, 'Visitado')
        }
    });
});
</script>
