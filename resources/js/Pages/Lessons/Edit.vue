<script>
export default {
    name: 'CategoryEdit'
}

</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CategoryForm from '@/Components/Categories/Form.vue';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    }
})

const form = useForm({
    name: props.category.name
})

</script>

<template>
    <AppLayout title="Edit Category">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Editar Categoria</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"></div>
                <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                    <CategoryForm :updating="true" :form="form" @submit="form.put(route('categories.update', category.id))"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>