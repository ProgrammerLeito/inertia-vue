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
    },
    tbprovincias: {
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
  tbprovincia_id: '',
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
                   cliente.prov_cliente.toLowerCase().includes(normalizedQuery);
        });
    }
});


const deleteCliente = async (clienteId) => {
    const confirmed = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Estás a punto de eliminar permanentemente este cliente. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (confirmed.isConfirmed) {
        try {
            // Realiza una solicitud HTTP DELETE para eliminar permanentemente el usuario
            await form.delete(`/delete_cliente_deletePermanently/${clienteId}`);

            Swal.fire('cliente eliminado', 'El producto ha sido eliminado permanentemente.', 'success');
            location.reload();
        } catch (error) {
            console.error('Error al eliminar el cliente:', error);
            Swal.fire('Error', 'Hubo un error al eliminar el cliente.', 'error');
        }
    }
};
</script>

<template>
    <AppLayout title="Clientes Eliminados">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Historial de Clientes Eliminados</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('clientes.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fas fa-arrow-left mx-1"></i> Regresar
                        </Link>
                    </div>
                    <div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
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
                                    <tr v-for="(cliente, i) in filteredClients" :key="cliente.id" class="bg-white text-black dark:bg-gray-700 dark:text-white">
                                        <td class="px-6 py-4 text-center">{{ i + 1 }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.numeroDocumento }}</td>
                                        <td class="px-6 py-4 text-left font-semibold">{{ cliente.razonSocial }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.direccion }}</td>
                                        <td class="px-6 py-4 text-center">{{ cliente.tbprovincia ? cliente.tbprovincia.prov_nombre : 'Sin ciudad' }}</td>
                                        <td class="p-3 text-center">
                                            <Link class="py-0.5 px-2.5 text-xs text-black font-semibold bg-yellow-400 rounded-lg border-solid border-2 hover:bg-yellow-500" :href="route('clientes.restore', { id: cliente.id })">
                                                <i class='bx bxs-share'><label class="ml-2">Restaurar</label></i>
                                            </Link>
                                            <Link @click="deleteCliente(cliente.id)" class="text-center ml-1 text-white bg-red-500 hover:bg-red-600 py-1 px-2 rounded-md">
                                                <i class="bi bi-trash3 text-white"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredClients.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos eliminados.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
