<script setup>
import { ref, watchEffect } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import ButtonEdit from '@/Components/ButtonEdit.vue';
import Swal from 'sweetalert2';
import {useForm} from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
 
const filteredTbproductos = ref([]);
const { props } = usePage();
const searchQuery = ref('');
 
defineProps({
    tbproductos: {
        type : Object,
        required: true
    },
    tbsubcategorias: {
        type : Object,
        required: true
    },
    tbcategorias: {
        type : Object,
        required: true
    },
    tbmarcas: {
        type : Object,
        required: true
    }
});
const form = useForm ({
    tbcategoria_id: '',
    tbsubcategoria_id: '',
    tbmarca_id: '',
    modelo: '',
    medida: '',
    moneda: '',
    precio: 0,
    descuento: 0,
    stock: 0,
    codigo: '',
    estado: '',
    capacidades: '',
    especificaciones: '',
    foto: '',
});
 
const deleteUser = async (tbproductoId) => {
    const confirmed = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Estás a punto de eliminar permanentemente este producto. Esta acción no se puede deshacer.',
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
            await form.delete(`/delete_tbproducto_deletePermanently/${tbproductoId}`);
       
            Swal.fire('producto eliminado', 'El producto ha sido eliminado permanentemente.', 'success');
            location.reload();
        } catch (error) {
            console.error('Error al eliminar el usuario:', error);
            Swal.fire('Error', 'Hubo un error al eliminar el producto.', 'error');
        }
    }
};
 
watchEffect(() => {
  filteredTbproductos.value = props.tbproductos.data.filter(tbproducto => {
    return tbproducto.modelo.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           tbproducto.id.toString().includes(searchQuery.value.toLowerCase());
  });
});
 
// console.log(props.tbproductos)
</script>
 
<template>
    <AppLayout title="Catalago Productos">
        <template #header>
            <h1 class="font-semibold text-gray-800 text-xl leading-tight dark:text-white">Historial de Productos eliminados</h1>
        </template>
        <div>
            <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
                <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                        <div class="flex flex-wrap gap-2 py-2 justify-between">
                            <Link :href="route('tbproductos.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                <i class="fas fa-arrow-left mx-1"></i> Regresar Producto
                            </Link>
                        </div>
                        <div>
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                                    <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Foto</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Marca</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Capacidades</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Especificaciones</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Codigo</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(tbproducto, i) in filteredTbproductos" :key="tbproducto.id"  class="bg-white text-black dark:bg-gray-700 dark:text-white">
                                            <td class="px-6 py-4 text-center"><b>{{ i + 1 }}</b></td>
                                            <img @click="openModal('/img/catalogo/' + tbproducto.foto)" :src="'/img/catalogo/' + tbproducto.foto" alt="Foto" style="width: 70px; height: 70px; cursor: pointer; object-fit: cover;" class="rounded-md py-1">
                                            <td class="px-6 py-4 text-center">{{ tbproducto.modelo }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.capacidades }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.especificaciones }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.precio }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.codigo }}</td>
                                            <td class="p-3 text-center">
                                                <Link :href="route('tbproductos.restore', { id: tbproducto.id })" class="text-xs hover:bg-yellow-500 bg-yellow-300 rounded-full py-1 mx-2  hover:text-green-500">
                                                    <i class="fa-solid fa-edit fa-sm">restaurar </i>
                                                </Link>
                                                <Link @click="deleteUser(tbproducto.id)" >
                                                    <i class="fa-solid fa-trash mr-1 fa-sm"></i>
                                                </Link>  
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div v-if="filteredTbproductos.length === 0" class="text-center py-2 dark:text-white">
                                    No se encontraron datos.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed right-0 top-[110px] bottom-[-60px] left-[50px] overflow-y-auto z-[10000] bg-gray-200/40" v-if="modalOpen">
            <div class="flex justify-center items-center flex-col h-full max-h-[90%] py-10 m-auto rounded-lg" @click="modalOpen = false">
                <div class="w-full max-w-sm max-h-[90%] h-full bg-gray-50 rounded-t-lg">
                    <img :src="modalImageUrl" alt="Imagen ampliada" class="max-w-sm w-full h-full object-contain">
                </div>
                <!-- Botón de cierre del modal -->
                <div class="bg-gray-50 p-2 w-full max-w-sm flex justify-end rounded-b-lg">
                    <button @click="modalOpen = false" type="button" class="w-full justify-center rounded-md border border-transparent shadow-sm px-14 py-0 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
 
<script>
export default {
    data() {
        return {
            modalOpen: false, // Estado del modal
            modalImageUrl: '' // URL de la imagen para mostrar en el modal
        };
    },
    methods: {
        // Método para abrir el modal con la imagen seleccionada
        openModal(imageUrl) {
            this.modalImageUrl = imageUrl; // Establece la URL de la imagen
            this.modalOpen = true; // Abre el modal
        }
    }
}
</script>