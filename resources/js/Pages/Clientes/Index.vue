<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import ButtonEdit from '@/Components/ButtonEdit.vue';
import { computed } from 'vue';
 
const searchQuery = ref('');
const statusesFilter = ref(new Set());
 
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
                            <i class="fa fa-plus-circle mx-1"></i> Registrar Empresa
                        </Link>
                        <Link :href="route('clientes.trashed')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fas fa-trash-alt mx-1"></i> Clientes Eliminados
                        </Link>
                    </div>
                    <div>
                        <div class="py-1">
                            <!-- grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-2 mb-3 -->
                            <div class="grid grid-cols-1 gap-y-2 sm:grid-cols-2 sm:gap-x-2 mb-1">
                                <div class="flex flex-col">
                                    <InputLabel for="table-search" class="block text-md font-medium text-gray-700 dark:text-white">Buscar</InputLabel>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                            </svg>
                                        </div>
                                        <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar cliente">
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <InputLabel class="block text-md font-medium text-gray-700 dark:text-white">Ciudad</InputLabel>
                                    <select v-model="selectedProvincia" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full h-10 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una Ciudad</option>
                                        <option v-for="tbprovincia in tbprovincias" :key="tbprovincia.id" :value="tbprovincia.id">{{ tbprovincia.prov_nombre }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
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
                                            <Link class="py-2 px-4 text-green-500" :href="route('clientes.edit', { cliente: cliente.id })"><i class="bi bi-pencil-square"></i></Link>
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