<script>
export default {
    name: 'CategoriesIndex'
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'

defineProps({
    categories: {
        type : Object,
        required: true
    }
});

const deleteCategory = id =>{
    if (confirm('¿Desea eliminar la categoria?')){
        Inertia.delete(route('categories.destroy', id))
    }
}

</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">Categorias</h1>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-600">
                    <div class="flex justify-between" v-if="$page.props.user.permissions.includes('create categories')">
                        <Link :href="route('categories.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded" v-if="$page.props.user.permissions.includes('create categories')">
                            Crear Categoria
                        </Link>
                        <Link :href="route('productos.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded">
                            Ingresar Producto
                        </Link>
                    </div>
                    <div class="mt-4">
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="category in categories.data">
                                    <td class="p-3 border">{{ category.name }}</td>
                                    <td class="p-3 border text-right">
                                        <Link class="py-2 px-4 text-yellow-500" :href="route('productos.index', { category_id: category.id })"><i class="bi bi-eye"></i></Link>
                                        <Link class="py-2 px-4 text-green-500" :href="route('categories.edit', category.id)" v-if="$page.props.user.permissions.includes('update categories')"><i class="bi bi-pencil-square"></i></Link>
                                        <Link class="py-2 px-4 text-red-500" @click="deleteCategory(category.id)" v-if="$page.props.user.permissions.includes('delete categories')"><i class="bi bi-trash3"></i></Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-between mt-2">
                        <Link v-if="categories.current_page > 1" :href="categories.prev_page_url" class="py-2 px-4 rounded">
                            Prev
                        </Link>
                        <div v-else></div>
                        <Link v-if="categories.current_page < categories.last_page" :href="categories.next_page_url" class="py-2 px-4 rounded">
                            Next
                        </Link>
                        <div v-else></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>