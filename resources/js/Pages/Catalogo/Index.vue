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
 
const deleteTbproducto = (id, modelo) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });
 
    alerta.fire({
        title: '¿Estás seguro de eliminar ' +modelo+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('tbproductos.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Producto eliminado exitosamente'
                    });
                }
            });
        }
    });
}
 
watchEffect(() => {
  filteredTbproductos.value = props.tbproductos.data.filter(tbproducto => {
    return tbproducto.modelo.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           tbproducto.id.toString().includes(searchQuery.value.toLowerCase());
  });
});
 
// console.log(props.tbproductos)
</script>
 
<template>
    <AppLayout title="Categorias">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Catalago de Productos</h1>
        </template>
        <div>
            <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
                <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                        <div class="flex flex-wrap gap-2 justify-between">
                            <Link :href="route('tbproductos.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                Registrar Producto
                            </Link>
                        </div>
                        <div class="mt-4 overflow-auto">
                            <div class="py-1 uppercase">
                                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                    <div class="flex flex-col ">
                                        <InputLabel class="block text-md font-medium text-gray-700">Categoría</InputLabel>
                                        <select v-model="selectedtbcategoria" @change="filtertbsubcategorias" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="">Seleccione una Categoría</option>
                                            <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id" :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                        </select>
                                    </div>
    
                                    <div class="flex flex-col ">
                                        <InputLabel class="block text-md font-medium text-gray-700">Subcategoría</InputLabel>
                                        <select v-model="selectedtbsubcategoria" @change="filtertbproductos"  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="">Seleccione una Subcategoría</option>
                                            <option v-for="tbsubcategoria in tbsubcategorias" :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <InputLabel for="table-search" class="block text-md font-medium text-gray-700">Buscar</InputLabel>
                                        <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <!-- <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                            </svg> -->
                                        </div>
                                        <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar el producto">
                                    </div>
                                </div>
                            </div>

                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500">
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
                                        <tr class="bg-white text-black dark:bg-gray-700 dark:text-white border-b" v-for="(tbproducto, i) in filteredTbproductos" :key="tbproducto.id">
                                            <td class="px-6 py-4 text-center">{{ i + 1 }}</td>
                                            <img @click="openModal('/img/catalogo/' + tbproducto.foto)" :src="'/img/catalogo/' + tbproducto.foto" alt="Foto" style="width: 100px; height: 100px; cursor: pointer; object-fit: cover;" class="rounded-md py-1">
                                            <td class="px-6 py-4 text-center">{{ tbproducto.modelo }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.capacidades }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.especificaciones }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.precio }}</td>
                                            <td class="px-6 py-4 text-center">{{ tbproducto.codigo }}</td>
                                            <td class="p-3 text-center">
                                                <ButtonEdit >
                                                    <i class="bi bi-pencil-square text-green-500"></i>
                                                </ButtonEdit>
                                                <ButtonDelete @click="$event => deleteTbproducto(tbproducto.id,tbproducto.modelo)" class="ml-1">
                                                    <i class="bi bi-trash3 text-red-500"></i>
                                                </ButtonDelete>
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