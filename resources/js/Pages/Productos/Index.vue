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
        },
        // constante para manejar la seleccion automatica del producto
        guardarProductoId(producto_id) {
            // Guardar el producto_id en localStorage
            localStorage.setItem('producto_id', producto_id);
        },
        redirectToSalidas(productoId) {
            // Redirigir a la página de salidas con el mismo enlace que el botón de visualización de salidas
            window.location.href = this.route('salidas.index', { producto_id: productoId });
        },
    }
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
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

const formatDate = (dateString) => {
    const options = { month: 'short', day: '2-digit', year: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', options);
};

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
                        <Link :href="route('productos.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center" v-if="$page.props.user.permissions.includes('Crear Producto')">
                            <i class="bi bi-clipboard-plus mx-1"></i>Registrar Producto
                        </Link>
                        <Link :href="route('salidas.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fa solid fa-list-ul mx-1"></i>Listar Salidas
                        </Link>
                        <Link :href="route('entradas.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-list-check mx-1"></i>Listar Entradas
                        </Link>
                    </div>
                    <div class="md:mt-2 mt-4">
                        <div class="font-semibold text-center dark:text-white">Categoria || {{ filteredProductos.length > 0 ? filteredProductos[0].name : 'Sin Productos' }}</div>
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
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar el producto">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Codigo</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Foto</th>
                                        <th scope="col" class="px-6 py-3 text-left dark:border-white border-b-2">Producto</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Comprador</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Categoria</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Marca</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Stock</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad Ultima Entrada</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Fecha</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Comentario</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Aproximado</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white border-b cursor-pointer" @dblclick="redirectToSalidas(producto.producto_id); guardarProductoId(producto.producto_id)" v-for="producto in filteredProductos">
                                        <td class="px-6 py-4 text-center">{{ producto.producto_id }}</td>
                                        <td class="px-0 py-0 font-semibold text-center">
                                            <img @click="openModal('/img/productos/' + producto.imagen_producto)" :src="'/img/productos/' + producto.imagen_producto" alt="Foto del Producto" style="width: 70px; height: 70px; cursor: pointer; object-fit: cover;" class="rounded-md py-1 mx-auto">
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-left">{{ producto.insumo }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.comprador }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.cantidad }}   {{ producto.unidad_medida }}</td>
                                        <!-- <td class="px-6 py-4 text-left">{{ producto?.name }}</td> -->
                                        <td class="px-6 py-4 text-center">{{ producto.marca }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.modelo }}</td>
                                        <td class="px-6 py-4 font-semibold text-center">{{ parseInt(producto.stock) + parseInt(producto.total_entradas) + parseInt(producto.total_devolucion) - parseInt(producto.total_salidas) }}    {{ producto.unidad_medida }}</td>
                                        <!-- <td class="px-6 py-4 text-center">{{ producto.ultima_cantidad_entrada == "0" ? producto.ultima_entrada : producto.ultima_cantidad_entrada }}</td> -->
                                        <td class="px-6 py-4">{{ formatDate(producto.fecha) }}</td>
                                        <td class="px-6 py-4 text-center">S/. {{ parseFloat(producto.precio).toFixed(2) }}</td>
                                        <!-- <td class="px-6 py-4 text-left">{{ producto.comentario }}</td> -->
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <!-- <Link class="py-1.5 px-3.5 text-black font-semibold bg-yellow-400 rounded-lg border-solid border-2 hover:bg-yellow-500" :href="route('salidas.index', { producto_id: producto.producto_id })" @click="guardarProductoId(producto.producto_id)">
                                                <i class="bi bi-eye"><label class="ml-2">Salidas</label></i>
                                            </Link> -->
                                            <Link class="py-2 px-3 rounded-lg text-white bg-green-600 hover:bg-green-700" :href="route('productos.edit', producto.producto_id)" v-if="$page.props.user.permissions.includes('Acciones Productos')"><i class="bi bi-pencil-square"></i></Link>
                                            <ButtonDelete @click="$event => deleteProducto(producto.producto_id,producto.insumo)" v-if="$page.props.user.permissions.includes('Acciones Productos')">
                                                <i class="bi bi-trash3 py-2 px-3 rounded-lg text-white bg-red-600 hover:bg-red-700"></i>
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
                    <div class="flex justify-between mt-4">
                        <Link v-if="productos.current_page > 1" :href="productos.prev_page_url" class="flex items-center justify-center px-3 h-8 me-3 text-sm font-medium text-white bg-blue-600 border rounded-lg hover:text-white hover:bg-blue-700">
                            <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                            </svg>
                            Anterior
                        </Link>
                        <Link v-if="productos.current_page < productos.last_page" :href="productos.next_page_url" class="flex items-center justify-center px-3 h-8 me-3 text-sm font-medium text-white bg-blue-600 border rounded-lg hover:text-white hover:bg-blue-700">
                            Siguiente
                            <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed right-0 top-[110px] bottom-[-60px] md:left-[50px] left-[-2px] overflow-y-auto z-[10000] bg-gray-200/40" v-if="modalOpen">
            <div class="flex justify-center items-center flex-col h-full max-h-[90%] py-10 m-auto rounded-lg" style="backdrop-filter: blur(5px);" @click="modalOpen = false">
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