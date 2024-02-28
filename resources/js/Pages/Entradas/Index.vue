<script>
export default {
    name: 'EntradasIndex',
    data() {
        return {
        searchQuery: '',
        };
    },
    computed: {
        filteredEntradas() {
        // Filtrar categorías según searchQuery
        // Considere usar una expresión regular para un filtrado de nombres y números más sólido
        return this.entradas.data.filter(entrada => {
            const normalizedQuery = this.searchQuery.toLowerCase();
            return entrada.producto?.insumo.toLowerCase().includes(normalizedQuery) ||
                entrada.id.toString().includes(normalizedQuery); // Suponiendo que 'id' es un número
        });
        },
    },
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'

defineProps({
    entradas: {
        type : Object,
        required: true
    },
    productos: {
        type : Object,
        required: true
    }
});

const deleteEntradas = id =>{
    if (confirm('Are you sure?')){
        Inertia.delete(route('entradas.destroy', id))
    }
}

</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">Entradas de Productos</h1>
        </template>

        <div class="py-2 md:py-4">
            <div class=" mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-600">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('categories.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Regresar
                        </Link>
                        <Link :href="route('entradas.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Ingresar Entrada
                        </Link>
                    </div>
                    <div class="mt-4">
                        <div class="pb-4 bg-white dark:bg-white">
                            <label for="table-search" class="sr-only">Buscar</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar entradas">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Producto</th>
                                        <th scope="col" class="px-6 py-3">Cantidad</th>
                                        <th scope="col" class="px-6 py-3">fecha</th>
                                        <th scope="col" class="px-6 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black border-b dark:bg-gray-200 dark:border-gray-400 dark:text-black" v-for="entrada in filteredEntradas">
                                        <td class="px-6 py-4 font-semibold">{{ entrada.producto?.insumo }}</td>
                                        <td class="px-6 py-4">{{ entrada.cantidad }}</td>
                                        <td class="px-6 py-4">{{ entrada.fecha }}</td>
                                        <td class="p-3 border-b text-right dark:border-gray-400">
                                            <Link class="py-2 px-4 text-green-500" :href="route('entradas.edit', entrada.id)"><i class="bi bi-pencil-square"></i></Link>
                                            <Link class="py-2 px-4 text-red-500" @click="deleteEntradas(entrada.id)"><i class="bi bi-trash3"></i></Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredEntradas.length === 0" class="text-center py-2">
                                No se encontraron datos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>