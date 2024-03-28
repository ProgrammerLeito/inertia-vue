<script>
export default {
    name: 'ProductosIndex',
    data() {
        return {
            searchQuery: '',
            modalOpen: false, // Estado del modal
            modalImageUrl: '' // URL de la imagen para mostrar en el modal
        };
    },
    computed: {
        filteredProductos() {
            // Filtrar productos según la búsqueda
            return this.productos.data.filter(producto => {
                const normalizedQuery = this.searchQuery.toLowerCase();
                return producto.insumo.toLowerCase().includes(normalizedQuery) ||
                    producto.producto_id.toString().includes(normalizedQuery); // Suponiendo que 'id' es un número
            });
        },
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

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import Swal from 'sweetalert2';
import {useForm} from '@inertiajs/vue3';

defineProps({
    productos: {
        type : Object,
        required: true
    }
});

const form = useForm ({
    insumo: '',
    marca: '',
    modelo: '',
    cantidad: '',
    unidad_medida: '',
    fecha: '',
    comprador: '',
    comentario: '',
    stock: '',
    ultima_entrada: '',
    precio: '',
    category_id: '',
});

const deleteProducto = (id, insumo) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar ' +insumo+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('productos.destroy', id), {
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

</script>

<template>
    <AppLayout title="Productos">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Productos</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('productos.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Registrar Producto
                        </Link>
                        <Link :href="route('entradas.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Listar Entradas
                        </Link>
                        <!-- <Link :href="route('salidas.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Listar Salidas
                        </Link> -->
                    </div>
                    <div class="font-semibold text-center dark:text-white">Categoria || {{ filteredProductos.length > 0 ? filteredProductos[0].name : 'Sin Productos' }}</div>
                    <div class="mt-4 overflow-auto">
                        <div class="pb-4 bg-white dark:bg-gray-800">
                            <label for="table-search" class="sr-only">Buscar</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar el producto">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Codigo</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Foto</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Producto</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Stock</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Categoria</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Marca</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad Ultima Entrada</th> -->
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Fecha</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Comprador</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Comentario</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Aproximado</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white border-b" v-for="producto in filteredProductos">
                                        <td class="px-6 py-4 text-center">{{ producto.producto_id }}</td>
                                        <img @click="openModal('/img/productos/' + producto.imagen_producto)" :src="'/img/productos/' + producto.imagen_producto" alt="Foto del Producto" style="max-width: 100px; cursor: pointer" class="rounded-md py-1">
                                        <td class="px-6 py-4 font-semibold text-left">{{ producto.insumo }}</td>
                                        <td class="px-6 py-4 font-semibold text-center">{{ parseInt(producto.stock) + parseInt(producto.total_entradas) + parseInt(producto.total_devolucion) - parseInt(producto.total_salidas) }}</td>
                                        <!-- <td class="px-6 py-4 text-left">{{ producto?.name }}</td> -->
                                        <td class="px-6 py-4 text-center">{{ producto.marca }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.modelo }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.cantidad }}   {{ producto.unidad_medida }}</td>
                                        <!-- <td class="px-6 py-4 text-center">{{ producto.ultima_cantidad_entrada == "0" ? producto.ultima_entrada : producto.ultima_cantidad_entrada }}</td> -->
                                        <!-- <td class="px-6 py-4 text-center">{{ producto.fecha }}</td> -->
                                        <td class="px-6 py-4 text-center">{{ producto.comprador }}</td>
                                        <td class="px-6 py-4 text-center">S/. {{ parseFloat(producto.precio).toFixed(2) }}</td>
                                        <!-- <td class="px-6 py-4 text-left">{{ producto.comentario }}</td> -->
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <Link class="py-1.5 px-3.5 text-black font-semibold bg-yellow-400 rounded-lg border-solid border-2 hover:bg-yellow-500" :href="route('salidas.index', { producto_id: producto.producto_id })"><i class="bi bi-eye"><label class="ml-2">Salidas</label></i></Link>
                                            <Link class="py-2 px-4 text-green-500" :href="route('productos.edit', producto.producto_id)"><i class="bi bi-pencil-square"></i></Link>
                                            <!-- <Link class="py-2 px-4 text-red-500" @click="deleteProducto(producto.producto_id)"><i class="bi bi-trash3"></i></Link> -->
                                            <ButtonDelete @click="$event => deleteProducto(producto.producto_id,producto.insumo)">
                                                <i class="bi bi-trash3 text-red-500"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredProductos.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-2">
                        <Link v-if="productos.current_page > 1" :href="productos.prev_page_url" class="py-2 px-4 rounded">
                            Prev
                        </Link>
                        <div v-else></div>
                        <Link v-if="productos.current_page < productos.last_page" :href="productos.next_page_url" class="py-2 px-4 rounded">
                            Next
                        </Link>
                        <div v-else></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed inset-0 overflow-y-auto" v-if="modalOpen">
            <div class="flex items-center justify-center min-h-screen pt-1 px-1 pb-20 text-center sm:block w-full sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <!-- Contenedor del modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <!-- Contenido del modal -->
                    <div class="bg-white px-1 pb-1 sm:p-1 sm:pb-1">
                        <div class="sm:flex sm:items-start">
                            <!-- Imagen -->
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-100 w-100 rounded-lg bg-green-100 sm:mx-0 sm:h-100 sm:w-full">
                                <!-- Aquí se mostrará la imagen -->
                                <img :src="modalImageUrl" alt="Imagen ampliada" class="h-auto w-full">
                            </div>

                        </div>
                    </div>
                    <!-- Botón de cierre del modal -->
                    <div class="bg-gray-50 px-1 w-full py-1 sm:px-1 sm:flex sm:flex-row-reverse">
                        <button @click="modalOpen = false" type="button" class="w-full justify-center rounded-md border border-transparent shadow-sm px-14 py-0 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>