<script>
export default {
    name: 'ClientesIndex',
    data() {
        return {
        searchQuery: '',
        };
    },
    computed: {
        filteredClientes() {
        return this.clientes.data.filter(cliente => {
            const normalizedQuery = this.searchQuery.toLowerCase();
            return cliente.cli_ruc.toLowerCase().includes(normalizedQuery) ||
                cliente.idCliente.toString().includes(normalizedQuery); // Suponiendo que 'id' es un número
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
    clientes: {
        type : Object,
        required: true
    }
});

const deleteCliente = idCliente =>{
    if (confirm('Are you sure?')){
        Inertia.delete(route('clientes.destroy', idCliente))
    }
}

</script>

<template>
    <AppLayout title="Clientes">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Listar Clientes</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('clientes.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Regresar
                        </Link>
                        <Link :href="route('clientes.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Ingresar Clientes
                        </Link>
                    </div>
                    <div class="mt-4 overflow-auto">
                        <div class="pb-4 bg-white dark:bg-gray-800">
                            <label for="table-search" class="sr-only">Buscar</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar el cliente">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Ruc</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Razon Social</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Direccion Legal</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Observacion</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white border-b" v-for="cliente in clientes.data">
                                        <td class="px-6 py-4 text-center">{{ cliente.idCliente }}</td>
                                        <td class="px-6 py-4 text-left">{{ cliente.cli_razonSocial }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.cli_ruc }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.cli_direccionlegal }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.cli_observacion }}</td>
                                        <td class="p-3 text-center">
                                            <Link class="py-2 px-4 text-yellow-500" :href="route('clientes.index')"><i class="bi bi-eye"></i></Link>
                                            <Link class="py-2 px-4 text-green-500" :href="route('clientes.edit', cliente.idCliente)"><i class="bi bi-pencil-square"></i></Link>
                                            <Link class="py-2 px-4 text-red-500" @click="deleteCliente(cliente.idCliente)"><i class="bi bi-trash3"></i></Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredClientes.length === 0" class="text-center py-2">
                                No se encontraron datos.
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-2">
                        <Link v-if="clientes.current_page > 1" :href="clientes.prev_page_url" class="py-2 px-4 rounded">
                            Prev
                        </Link>
                        <div v-else></div>
                        <Link v-if="clientes.current_page < clientes.last_page" :href="clientes.next_page_url" class="py-2 px-4 rounded">
                            Next
                        </Link>
                        <div v-else></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>