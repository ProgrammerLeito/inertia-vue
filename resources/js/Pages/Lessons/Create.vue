<script>
    export default {
        name: 'LessonsCreate'
    }
</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import LessonForm from '@/Components/Lessons/Form.vue';

defineProps({
    categories:{
        type: Object,
        required: true,
    },
    levels:{
        type: Object,
        required: true,
    }
})

const form = useForm ({
    name: ''
})
</script>

<template>
    <AppLayout title="Create Lesson">
        <template #header>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Crear Lecciones</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                            <LessonForm :form="form" :categories="categories" :levels="levels" @submit="form.post(route('lessons.store'))"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>