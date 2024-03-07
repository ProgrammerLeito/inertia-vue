<script>
    export default {
        name: 'EntradasCreate'
    }
</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import EntradasForm from '@/Components/Entradas/Form.vue';
import { onMounted } from 'vue';

defineProps({
    entradas: {
        type : Object,
        required: true
    },
    productos: {
        type : Object,
        required: true
    }
})

const form = useForm ({
    cantidad: '',
    fecha: '',
    producto_id: '',
})

onMounted(() => {
    const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual y la formatea como yyyy-mm-dd
    form.fecha = today; // Asigna la fecha actual al modelo de datos del formulario
});

</script>

<template>
    <AppLayout title="Crear Entrada">
        <template #header>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Ingresar Entrada</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                            <EntradasForm :form="form" :productos="productos" :entradas="entradas" @submit="form.post(route('entradas.store'))"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>