<script>
    export default {
        name: 'ProductosCreate'
    }
</script>

<script setup>

import { watch } from 'vue'

import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductosForm from '@/Components/Productos/Form.vue';
import { onMounted } from 'vue';

defineProps({
    categories: {
        type : Object,
        required: true
    },
    productos: {
        type : Object,
        required: true
    }
})

const form = useForm ({
    insumo: '',
    marca: '',
    modelo: '',
    cantidad: '',
    unidad_medida: '',
    fecha: '',
    empresa: '',
    comentario: '',
    stock: '',
    ultima_entrada: '',
    category_id: '',
})

// Importante para duplicar
watch(
    () => form.cantidad,
    (newValue) => {
        form.stock = newValue;
        form.ultima_entrada = newValue;
    }
);

onMounted(() => {
    const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual y la formatea como yyyy-mm-dd
    form.fecha = today; // Asigna la fecha actual al modelo de datos del formulario
});

</script>

<template>
    <AppLayout title="Crear Producto">
        <template #header>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">Ingresar Producto</h1>
        </template>

        <div class="py-2 md:py-4">
            <div class=" mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <ProductosForm :form="form" :categories="categories" :productos="productos" @submit="form.post(route('productos.store'))"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>