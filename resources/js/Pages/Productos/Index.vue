<script>
export default {
    name: 'ProductosIndex',
    data() {
        return {
            searchQuery: '',
            modalOpen: false, // Estado del modal
            modalImageUrl: '', // URL de la imagen para mostrar en el modal
            nombreCategoria: '',
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
    mounted() {
        // Recuperar el nombre de la categoría desde localStorage al montar el componente
        this.nombreCategoria = localStorage.getItem('nombre_categoria') || 'No definido';
    },
    methods: {
        // Método para abrir el modal con la imagen seleccionada
        openModal(imageUrl) {
            this.modalImageUrl = imageUrl; // Establece la URL de la imagen
            this.modalOpen = true; // Abre el modal
        },
        // Constante para manejar la selección automática del producto
        guardarProductoId(producto_id, name) {
            // Guardar el producto_id en localStorage
            localStorage.setItem('producto_id', producto_id);
            localStorage.setItem('nombre_categoria', name);
        },
        redirectToSalidas(productoId) {
            // Redirigir a la página de salidas con el mismo enlace que el botón de visualización de salidas
            this.$inertia.visit(route('salidas.index', { producto_id: productoId }));
        },
        redirectToCreate(producto) {
            localStorage.setItem('productoData', JSON.stringify(producto));
            this.$inertia.visit(route('carritos.index'));
        }
    }
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import Swal from 'sweetalert2';
import {useForm} from '@inertiajs/vue3';

const props = defineProps({
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

    // Obtener la diferencia de zona horaria en minutos
    const offset = new Date().getTimezoneOffset();
    // Convertir minutos a milisegundos
    const offsetMilliseconds = offset * 60 * 1000;

    // Crear el objeto Date en la zona horaria local sumando el offset
    const date = new Date(new Date(dateString).getTime() + offsetMilliseconds);
    return date.toLocaleDateString('es-ES', options);
};

const formPage = useForm({});
 
const onPageClick = (event) => {
    formPage.get(route('productos.index', { page: event }));
};
 
 
const previousPage = () => {
    const prevPage = props.productos.current_page - 1;
    formPage.get(route('productos.index', { page: prevPage }));
};
 
const nextPage = () => {
    const nextPage = props.productos.current_page + 1;
    formPage.get(route('productos.index', { page: nextPage }));
};
 
const goToPage = (page) => {
    formPage.get(route('productos.index', { page }));
};
 
const total_pages = props.productos.last_page;
const current_page = props.productos.current_page;
const countPerPage = props.productos.data.length;
const totalCount = props.productos.total;

</script>

<template>
    <AppLayout title="Inventario de Productos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Productos</h1>
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
                        <div class="font-semibold text-center dark:text-white">Categoria || {{ nombreCategoria }}</div>
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
                        <div class="relative overflow-x-auto shadow-md md:rounded-lg rounded-md shadow-gray-200 dark:shadow-gray-500">
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
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white border-b dark:hover:bg-gray-900 hover:bg-gray-300 cursor-pointer" @dblclick="redirectToSalidas(producto.producto_id); guardarProductoId(producto.producto_id)" v-for="producto in filteredProductos">
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
                                        <td class="px-6 py-4 text-center">{{ formatDate(producto.entrada_maxima == null ? producto.fecha : producto.entrada_maxima) }}</td>
                                        <td class="px-6 py-4 text-center">S/. {{ parseFloat(producto.precio).toFixed(2) }}</td>
                                        <!-- <td class="px-6 py-4 text-left">{{ producto.comentario }}</td> -->
                                        <!-- <td class="text-center whitespace-nowrap">
                                            <button @click="redirectToCreate(producto)" class="bg-blue-600 hover:bg-blue-400 text-white px-2.5 py-[7px] rounded-lg mx-4"><i class="fas fa-shopping-cart"></i></button>
                                            <Link class="py-2 px-3 rounded-lg text-white bg-green-600 hover:bg-green-700" :href="route('productos.edit', producto.producto_id)" v-if="$page.props.user.permissions.includes('Acciones Productos')"><i class="bi bi-pencil-square"></i></Link>
                                            <ButtonDelete @click="$event => deleteProducto(producto.producto_id,producto.insumo)" v-if="$page.props.user.permissions.includes('Acciones Productos')">
                                                <i class="bi bi-trash3 py-2 px-3 rounded-lg text-white bg-red-600 hover:bg-red-700"></i>
                                            </ButtonDelete>
                                        </td> -->
                                        <td class="px-2 py-5 text-center whitespace-nowrap" style="display: flex; justify-content: center; align-items: center;">
                                            <button @click="redirectToCreate(producto)" class="inline-flex items-center justify-center mx-2 bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded-md">
                                                <i class='fas fa-shopping-cart text-sm text-white'></i>
                                            </button>
                                            <Link :href="route('productos.edit', producto.producto_id)" v-if="$page.props.user.permissions.includes('Acciones Productos')" class="inline-flex items-center justify-center bg-amber-400 hover:bg-amber-500 px-2 py-[0.1px] rounded-md mx-2">
                                                <i class='bx bxs-edit text-lg text-white'></i>
                                            </Link>
                                            <button @click="$event => deleteProducto(producto.producto_id,producto.insumo)" v-if="$page.props.user.permissions.includes('Acciones Productos')" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 px-2 py-[0.1px] rounded-md mx-2">
                                                <i class='bx bxs-trash text-lg text-white'></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredProductos.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                        <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                            <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                            <p class="text-gray-700 dark:text-white font-semibold">Total de Productos: {{ totalCount }}</p>
                        </div>
                        <div class="mt-4 sm:text-end text-center">
                            <nav aria-label="Page navigation example mt-4">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <li>
                                        <button @click="previousPage" :disabled="!productos.prev_page_url"
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-700 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            Prev
                                        </button>
                                    </li>
                                    <li v-for="page in total_pages" :key="page">
                                        <button @click="goToPage(page)"
                                            :class="{ 'text-blue-600 border-blue-300 dark:text-gray-900 bg-blue-50 hover:bg-blue-100 hover:text-blue-700': page === current_page, 'text-gray-900 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': page !== current_page }"
                                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            {{ page }}
                                        </button>
                                    </li>
                                    <li>
                                        <button @click="nextPage" :disabled="!productos.next_page_url"
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-700 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            Next
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
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