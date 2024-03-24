<script>
export default {
    name: 'ProductosIndex',
    data() {
        return {
        searchQuery: '',
        };
    },
    computed: {
        filteredProductos() {
        // Filtrar categorías según searchQuery
        // Considere usar una expresión regular para un filtrado de nombres y números más sólido
        return this.productos.data.filter(producto => {
            const normalizedQuery = this.searchQuery.toLowerCase();
            return producto.insumo.toLowerCase().includes(normalizedQuery) ||
                producto.producto_id.toString().includes(normalizedQuery); // Suponiendo que 'id' es un número
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
    productos: {
        type : Object,
        required: true
    }
});

const deleteProducto = (producto_id) =>{
    if (confirm('Are you sure?')){
        Inertia.delete(route('productos.destroy', producto_id))
    }
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
                        <Link :href="route('categories.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Regresar
                        </Link>
                        <Link :href="route('entradas.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Ingresar Entrada
                        </Link>
                        <Link :href="route('productos.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Registrar Producto
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
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Categoria</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Marca</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad Ultima Entrada</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Medida</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Comprador</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Comentario</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Aproximado</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white border-b" v-for="producto in filteredProductos">
                                        <td class="px-6 py-4 text-center">{{ producto.producto_id }}</td>
                                        <td class="px-6 py-4 text-center"><img :src="'/img/productos/' + producto.imagen_producto" alt="Foto del Producto" class="px-6 py- object-cover"></td>
                                        <td class="px-6 py-4 font-semibold text-left">{{ producto.insumo }}</td>
                                        <td class="px-6 py-4 font-semibold text-center">{{ parseInt(producto.stock) + parseInt(producto.total_entradas) - parseInt(producto.total_salidas) }}</td>
                                        <td class="px-6 py-4 text-left">{{ producto?.name }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.marca }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.modelo }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.cantidad }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.ultima_cantidad_entrada == "0" ? producto.ultima_entrada : producto.ultima_cantidad_entrada }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.unidad_medida }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.fecha }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.comprador }}</td>
                                        <td class="px-6 py-4 text-center">S/. {{ parseFloat(producto.precio).toFixed(2) }}</td>
                                        <!-- <td class="px-6 py-4 text-left">{{ producto.comentario }}</td> -->
                                        <td class="p-3 text-center">
                                            <Link class="py-2 px-4 text-yellow-500" :href="route('salidas.index', { producto_id: producto.producto_id })"><i class="bi bi-eye"></i></Link>
                                            <Link class="py-2 px-4 text-green-500" :href="route('productos.edit', producto.producto_id)"><i class="bi bi-pencil-square"></i></Link>
                                            <Link class="py-2 px-4 text-red-500" @click="deleteProducto(producto.producto_id)"><i class="bi bi-trash3"></i></Link>
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
    </AppLayout>
</template>