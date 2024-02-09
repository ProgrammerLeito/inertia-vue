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
                        <Link :href="route('productos.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded">
                            Ingresar Producto
                        </Link>
                    </div>
                    <div class="mt-4">
                        <ul role="list" class="divide-y divide-gray-100">
                            <li class="flex justify-between gap-x-6 py-5" v-for="producto in productos.data">
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ producto.insumo }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ producto.category.name }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ producto.marca }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ producto.modelo }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ producto.cantidad }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ producto.unidad_medida }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ producto.fecha }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ producto.empresa }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ producto.comentario }}</p>
                                    </div>
                                </div>
                                <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                    <p class="text-md leading-6 text-gray-900">
                                        <Link class="py-2 px-4 text-green-500" :href="route('productos.edit', producto.id)"><i class="bi bi-pencil-square"></i></Link>
                                        <Link class="py-2 px-4 text-red-500" @click="deleteProducto(producto.id)"><i class="bi bi-trash3"></i></Link>
                                    </p>
                                </div>
                            </li>
                        </ul>
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