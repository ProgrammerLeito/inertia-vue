<script>
    export default {
        name: 'ProductosCreate'
    }
</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductosForm from '@/Components/Productos/Form.vue';

const form = useForm ({
    insumo: '',
    marca: '',
    modelo: '',
    cantidad: '',
    unidad_medida: '',
    fecha: '',
    empresa: '',
    comentario: '',
})
</script>

<template>
    <AppLayout title="Crear Producto">
        <template #header>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">Productos</h1>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <ProductosForm :form="form" @submit="form.post(route('productos.store'))"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>