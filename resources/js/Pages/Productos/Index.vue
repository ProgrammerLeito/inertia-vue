<script>
export default {
    name: 'ProductosIndex'
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

const deleteProducto = id =>{
    if (confirm('Are you sure?')){
        Inertia.delete(route('productos.destroy', id))
    }
}

</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">Insumos</h1>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-600">
                    <div class="flex justify-between">
                        <Link :href="route('categories.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded">
                            Regresar
                        </Link>
                        <Link :href="route('entradas.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded">
                            Ingresar Entrada
                        </Link>
                        <Link :href="route('productos.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded">
                            Ingresar Producto
                        </Link>
                    </div>
                    <div class="mt-4 overflow-auto">
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th>Insumo</th>
                                    <th>Categoria</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Cantidad</th>
                                    <th>Cantidad Ultima Entrada</th>
                                    <th>Medida</th>
                                    <th>Fecha</th>
                                    <th>Empresa</th>
                                    <th>Comentario</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="producto in productos.data">
                                    <td class="p-3 border">{{ producto.insumo }}</td>
                                    <td class="p-3 border">{{ producto.category?.name }}</td>
                                    <td class="p-3 border">{{ producto.marca }}</td>
                                    <td class="p-3 border">{{ producto.modelo }}</td>
                                    <td class="p-3 border">{{ producto.cantidad }}</td>
                                    <td class="p-3 border">{{ producto.ultima_entrada }}</td>
                                    <td class="p-3 border">{{ producto.unidad_medida }}</td>
                                    <td class="p-3 border">{{ producto.fecha }}</td>
                                    <td class="p-3 border">{{ producto.empresa }}</td>
                                    <td class="p-3 border">{{ producto.comentario }}</td>
                                    <td class="p-3 border">{{ producto.stock }}</td>
                                    <td class="p-3 border text-right">
                                        <Link class="py-2 px-4 text-yellow-500" :href="route('salidas.index', { producto_id: producto.id })"><i class="bi bi-eye"></i></Link>
                                        <Link class="py-2 px-4 text-green-500" :href="route('productos.edit', producto.id)"><i class="bi bi-pencil-square"></i></Link>
                                        <Link class="py-2 px-4 text-red-500" @click="deleteProducto(producto.id)"><i class="bi bi-trash3"></i></Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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