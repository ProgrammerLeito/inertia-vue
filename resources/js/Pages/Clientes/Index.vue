<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from 'sweetalert2';
import { useForm, Link } from '@inertiajs/vue3';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import { computed, ref } from 'vue';

const searchQuery = ref('');
const isDropdownOpen = ref(false);
 

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

// Función para seleccionar una provincia
const selectProvincia = (id) => {
    selectedProvincia.value = id;
    toggleDropdown(); // Cierra el dropdown después de seleccionar una provincia
};

const props = defineProps({
    clientes: {
        type : Object,
        required: true
    },
    tbprovincias: {
        type : Object,
        required: true
    }
});
 
const form = useForm({
  id : ''
});
 
const formPage = useForm({});
 
const onPageClick = (event) => {
    formPage.get(route('clientes.index', { page: event }));
};
 
//Constante para filtrar clientes por diferentes campos
const selectedProvincia = ref(''); // Para almacenar el ID de la provincia seleccionada

const filteredClients = computed(() => {
  const normalizedQuery = searchQuery.value.toLowerCase().trim();
  return props.clientes.data.filter(cliente => {
    return (!normalizedQuery || cliente.numeroDocumento.toLowerCase().includes(normalizedQuery) ||
            cliente.razonSocial.toLowerCase().includes(normalizedQuery) ||
            cliente.direccion.toLowerCase().includes(normalizedQuery)) &&
           (selectedProvincia.value === '' || cliente.tbprovincia_id === selectedProvincia.value);
  });
});

const deleteCliente = (id, razonSocial) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });
 
    alerta.fire({
        title: '¿Estás seguro de eliminar ' +razonSocial+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('clientes.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Cliente eliminado exitosamente',
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
    <AppLayout title="Clientes">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Lista de Clientes</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('clientes.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-person-plus mx-1"></i> Registrar Cliente
                        </Link>
                        <Link :href="route('clientes.trashed')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fas fa-trash-alt mx-1"></i> Clientes Eliminados
                        </Link>
                    </div>
                    <div>
                        <div class="py-2 sm:py-2">
                            <div class="md:max-w-lg">
                                <div class="flex">
                                    <div class="relative w-full">
                                        <input v-model="searchQuery" type="text" id="table-search" class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-l-lg border-s-gray-200 border-s-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:border-s-gray-700 dark:border-gray-600 placeholder-gray-700 dark:text-black dark:focus:border-blue-500" placeholder="Buscar cliente" required />
                                    </div>
                                    <div class="relative">
                                        <button id="dropdown-button" data-dropdown-toggle="dropdown" @click="toggleDropdown" class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-r-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600" type="button">
                                            {{ selectedProvincia ? tbprovincias.find(p => p.id === selectedProvincia).prov_nombre : 'Todas' }}
                                            <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </button>
                                        <div id="dropdown" :class="{ 'hidden': !isDropdownOpen }" class="z-20 absolute right-0 mt-2 w-44 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 font-bold">
                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdown-button">
                                                <li>
                                                    <button @click="selectProvincia('')" type="button" class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white font-bold">Todas</button>
                                                </li>
                                                <li v-for="tbprovincia in tbprovincias" :key="tbprovincia.id">
                                                    <button @click="selectProvincia(tbprovincia.id)" type="button" class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white font-bold">{{ tbprovincia.prov_nombre }}</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
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
                                    <tr v-for="(cliente, i) in filteredClients" :key="cliente.id"  class="bg-white text-black dark:bg-gray-700 dark:text-white">
                                        <td class="px-6 py-4 text-center"><b>{{ i + 1 }}</b></td>
                                        <td class="px-6 py-4 text-center">{{ cliente.numeroDocumento }}</td>
                                        <td class="px-6 py-4 text-left font-semibold">{{ cliente.razonSocial }}</td>
                                        <td class="px-6 py-4 text-center"><b>{{ cliente.direccion }}</b></td>
                                        <td class="px-6 py-4 text-center">{{ cliente.tbprovincia ? cliente.tbprovincia.prov_nombre : 'Sin ciudad' }}</td>
 
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <Link class="py-0.5 px-2.5 text-xs text-black font-semibold bg-yellow-300 rounded-lg border-solid border-2 hover:bg-yellow-400" :href="route('datos.index', { cliente_id: cliente.id })">
                                                <i class='bi bi-eye'><label class="ml-2">Cartera</label></i>
                                            </Link>
                                            <Link class="py-2 px-4 text-green-500 hover:text-green-600" :href="route('clientes.edit', { cliente: cliente.id })"><i class="bi bi-pencil-square"></i></Link>
                                            <ButtonDelete  @click="$event => deleteCliente(cliente.id,cliente.razonSocial)" class="ml-1">
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