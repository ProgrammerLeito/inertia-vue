<script>
export default {
    name: 'SalidasIndex',
    data() {
        return {
        searchQuery: '',
        };
    },
    computed: {
        filteredSalidas() {
        // Filtrar categorías según searchQuery
        // Considere usar una expresión regular para un filtrado de nombres y números más sólido
        return this.salidas.data.filter(salida => {
            const normalizedQuery = this.searchQuery.toLowerCase();
            return salida.empresa.toLowerCase().includes(normalizedQuery) ||
                salida.name.toLowerCase().includes(normalizedQuery) ||
                salida.id.toString().includes(normalizedQuery); // Suponiendo que 'id' es un número
        });
        },
    },
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineProps({
    salidas: {
        type : Object,
        required: true
    },
    productos: {
        type : Object,
        required: true
    }
});

const form = useForm ({
    empresa: '',
    unidad_salida: '',
    comentario_salida: '',
    tecnico: '',
    fecha: '',
    producto_id: '',
    devolucion: '0',
})

const deleteSalidas = (id, empresa) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar la salida de productos para la empresa ' +empresa+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('salidas.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Salida de productos eliminado exitosamente'
                    });
                }
            });
        }
    });
}

</script>

<template>
    <AppLayout title="Salidas">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Salidas de Productos</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('categories.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-arrow-left-short mx-1"></i>Regresar
                        </Link>
                        <Link :href="route('salidas.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-clipboard-x mx-1"></i>Ingresar Salida
                        </Link>
                    </div>
                    <div class="md:mt-0 mt-4">
                        <div class="font-semibold text-center dark:text-white">Producto || {{ filteredSalidas.length > 0 ? filteredSalidas[0].insumo : 'Sin salidas' }}</div>
                    </div>
                    <div class="mt-4">
                        <div class="pb-4 bg-white dark:bg-gray-800">
                            <label for="table-search" class="sr-only">Buscar</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar la salida">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Empresa</th>
                                        <!-- <th scope="col" class="px-6 py-3">Producto</th> -->
                                        <th scope="col" class="px-6 py-3">Unidad de Salida</th>
                                        <th scope="col" class="px-6 py-3">Comentario</th>
                                        <th scope="col" class="px-6 py-3">Tecnico</th>
                                        <th scope="col" class="px-6 py-3">Fecha</th>
                                        <th scope="col" class="px-6 py-3">Cantidad de Devolucion</th>
                                        <th scope="col" class="hidden px-6 py-3">Devolucion</th>
                                        <th scope="col" class="text-center px-6 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- <tr class="bg-white text-black border-b dark:bg-gray-700 dark:border-gray-400 dark:text-white" v-for="salida in filteredSalidas" :class="{ 'bg-gray-300 dark:bg-amber-400': salida.devolucion == 1 }"> -->
                                    <tr class="bg-white text-black border-b dark:bg-gray-700 dark:border-gray-400 dark:text-white" v-for="salida in filteredSalidas">
                                        <td class="px-6 py-4 font-semibold">{{ salida.empresa }}</td>
                                        <!-- <td class="px-6 py-4">{{ salida.insumo }}</td> -->
                                        <td class="px-6 py-4">{{ salida.unidad_salida }}</td>
                                        <td class="px-6 py-4">{{ salida.comentario_salida }}</td>
                                        <td class="px-6 py-4">{{ salida.name }}</td>
                                        <td class="px-6 py-4">{{ salida.fecha }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                {{ salida.devolucion != 1 ? 'Sin Devolucion' : (salida.unidad_devolucion === null ? 'Esperando devolución' : salida.unidad_devolucion) }}
                                                <img v-if="salida.devolucion == 1 && salida.unidad_devolucion === null" src="img/advertencia.png" class="pulsoAdvertencia ml-5 h-6 drop-shadow-[0_0_5px_rgba(255,255,255,0.80)]" alt="Advertencia">
                                            </div>
                                        </td>
                                        <td class="hidden px-6 py-4">{{ salida.devolucion }}</td>
                                        <td class="p-3 border-b text-center dark:border-gray-400">
                                            <Link class="py-2 px-4 text-green-500" :href="route('salidas.edit', salida.id)"><i class="bi bi-pencil-square"></i></Link>
                                            <ButtonDelete @click="$event => deleteSalidas(salida.id,salida.empresa)" class="ml-1">
                                                <i class="bi bi-trash3 ml-2 text-red-500"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredSalidas.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <Link v-if="salidas.current_page > 1" :href="salidas.prev_page_url" class="flex items-center justify-center px-3 h-8 me-3 text-sm font-medium text-white bg-blue-600 border rounded-lg hover:text-white hover:bg-blue-700">
                            <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                            </svg>
                            Anterior
                        </Link>
                        <Link v-if="salidas.current_page < salidas.last_page" :href="salidas.next_page_url" class="flex items-center justify-center px-3 h-8 me-3 text-sm font-medium text-white bg-blue-600 border rounded-lg hover:text-white hover:bg-blue-700">
                            Siguiente
                            <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>