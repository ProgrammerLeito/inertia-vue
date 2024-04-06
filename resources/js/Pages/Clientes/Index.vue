<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';
import { ref } from 'vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import ButtonEdit from '@/Components/ButtonEdit.vue';
import { computed } from 'vue';

const searchQuery = ref('');

const props = defineProps({
    clientes: {
        type : Object,
        // required: true
    }
});

const form = useForm({
  numeroDocumento: '',
  razonSocial: '',
  direccion: '',
  distrito: '',
  provincia: '',
  departamento:'',
  estado:'',
  cli_direccion2:'',
  cli_observacion:'',
  // Agregar otros campos según sea necesario
});
 
const formPage = useForm({});
 
const onPageClick = (event) => {
    formPage.get(route('clientes.index', { page: event }));
};

//Constante para filtrar clientes por diferentes campos
const filteredClients = computed(() => {
    const normalizedQuery = searchQuery.value.toLowerCase().trim();
    if (!normalizedQuery) {
        return props.clientes.data;
    } else {
        return props.clientes.data.filter(cliente => {
            return cliente.numeroDocumento.toLowerCase().includes(normalizedQuery) ||
                   cliente.razonSocial.toLowerCase().includes(normalizedQuery) ||
                   cliente.direccion.toLowerCase().includes(normalizedQuery) ||
                   cliente.provincia.toLowerCase().includes(normalizedQuery);
        });
    }
});
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
                        <Link :href="route('clientes.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Registrar Empresa
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
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar Clientes">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500 mt-2">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-900 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Ruc</th>
                                        <th scope="col" class="px-6 py-3 text-left dark:border-white border-b-2">Razon social</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Direccion</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Ciudad</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(cliente, i) in filteredClients" :key="cliente.id">
                                        <td class="px-6 py-4 text-center">{{ i + 1 }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.numeroDocumento }}</td>
                                        <td class="px-6 py-4 text-left font-semibold">{{ cliente.razonSocial }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.direccion }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.provincia }}</td>
                                        <td class="p-3 text-center">
                                            <ButtonEdit @click="$event => openModal(2,category.name,category.id)">
                                                <i class="bi bi-pencil-square text-green-500"></i>
                                            </ButtonEdit>
                                            <ButtonDelete>
                                                <i class="bi bi-trash3 text-red-500"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredClients.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>