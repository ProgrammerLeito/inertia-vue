<script>
export default {
    name: 'ProductosEdit'
}

</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductosForm from '@/Components/Productos/Form.vue';

const props = defineProps({
    productos: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
})

const form = useForm({
    insumo: props.productos.insumo,
    marca: props.productos.marca,
    modelo: props.productos.modelo,
    cantidad: props.productos.cantidad,
    unidad_medida: props.productos.unidad_medida,
    fecha: props.productos.fecha,
    empresa: props.productos.empresa,
    comentario: props.productos.comentario,
    stock: props.productos.stock,
    ultima_entrada: props.productos.ultima_entrada,
    category_id: props.productos.category_id
})

</script>

<template>
    <AppLayout title="Editar Producto">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">Editar Producto</h1>
        </template>

        <div class="py-2 md:py-4">
            <div class=" mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"></div>
                <div class="p-6 bg-white border-b border-gray-800">
                    <ProductosForm :updating="true" :form="form" :categories="categories" :productos="productos" @submit="form.put(route('productos.update', productos.id))"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>