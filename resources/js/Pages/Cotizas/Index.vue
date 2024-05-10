<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import Create from '@/Pages/Cotizas/Create.vue';
 
const props=defineProps({
    cventas: {
        type : Object,
        required: true
    },
    clientes:{
        type:Object,
        required:true
    }
});
const form = useForm({
    id:''
})
 
const deleteCotizacion = (id, cliente_id) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });
 
    alerta.fire({
        title: '¿Estás seguro de eliminar ' +cliente_id+ '?',
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
                        text: 'cotizacion eliminado exitosamente',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
}
</script>
 
<template>
    <AppLayout title="Cotizaciones">
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
                    <div class="flex flex-nowrap justify-normal py-1">
                        <div class="flex items-center me-4">
                            <input checked id="red-checkbox" type="checkbox" value="" class="w-4 h-4 text-cyan-600 bg-gray-100 border-gray-300 rounded focus:ring-cyan-500 dark:focus:ring-cyan-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-red-600 dark:border-gray-600">
                            <label for="red-checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Por enviar</label>
                        </div>
                        <div class="flex items-center me-4">
                            <input checked id="green-checkbox" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-green-600 dark:border-gray-600">
                            <label for="green-checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Enviado</label>
                        </div>
                        <div class="flex items-center me-4">
                            <input checked id="yellow-checkbox" type="checkbox" value="" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-600 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-yellow-500 dark:border-gray-600">
                            <label for="yellow-checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Aceptado</label>
                        </div>
                        <div class="flex items-center me-4">
                            <input checked id="orange-checkbox" type="checkbox" value="" class="w-4 h-4 text-red-500 bg-gray-100 border-gray-300 rounded focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-orange-500 dark:border-gray-600">
                            <label for="orange-checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Rechazado</label>
                        </div>
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
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cliente</th>
                                        <th scope="col" class="px-6 py-3 text-left dark:border-white border-b-2">fecha</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Moneda</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">garantia</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">F_pago</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">D_entrega</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">subtotal</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Igv</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Total</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(cventa, index) in cventas" :key="cventa.id" class="bg-white text-black dark:bg-gray-700 dark:text-white">
                                        <td class="px-1 py-4 text-center">{{ index + 1 }}</td>
                                        <td class="text-xs px-6">
                                            <div class="max-h-[70px] overflow-y-auto">
                                                <td class="px-6 py-4 text-center">{{ cventa.cliente ? cventa.cliente.razonSocial : 'Sin cliente' }}</td>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">{{ cventa.fecha }}</td>
                                        <td class="px-6 py-4 text-center">{{ cventa.moneda }}</td>
                                        <td class="px-2 py-4 text-center">{{ cventa.garantia }}</td>
                                        <td class="px-2 py-4 text-center">{{ cventa.forma_pago }}</td>
                                        <td class="px-2 py-4 text-center">{{ cventa.dias_entrega }}-dias</td>
                                        <td class="px-3 py-4 text-center">{{ cventa.subtotal }}</td>
                                        <td class="px-2 py-4 text-center">{{ cventa.igv }}</td>
                                        <td class="px-3 py-4 text-center">{{ cventa.total }}</td>
                                        <td class="p-3 text-center flex flex-wrap items-center justify-center">
                                            <ButtonDelete @click="$event => deleteCotizacion(cventa.id, cventa.cliente_id)" class="ml-1 text-red-500">
                                                <i class="bi bi-trash3"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>