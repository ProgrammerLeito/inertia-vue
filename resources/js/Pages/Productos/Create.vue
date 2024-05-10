<script>
    export default {
        name: 'ProductosCreate'
    }
</script>

<script setup>

import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductosForm from '@/Components/Productos/Form.vue';
import { onMounted, watch } from 'vue';
import Swal from 'sweetalert2';

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
    comprador: '',
    comentario: '',
    stock: '',
    ultima_entrada: '',
    precio: '',
    category_id: '',
    imagen_producto: '',
})
const handleSubmit = async () => {
    if (!form.insumo || !form.marca || !form.modelo || !form.cantidad || !form.unidad_medida || !form.fecha || !form.comprador || !form.comentario || !form.stock || !form.ultima_entrada || !form.precio || !form.category_id || !form.imagen_producto) {
        await Swal.fire({
            title: 'Campos Incompletos',
            text: 'Por favor, completa todos los campos antes de guardar el producto.',
            icon: 'error',
        });
        return; 
    }

    const { value } = await Swal.fire({
        title: '¿Estás seguro?',
        text: '¡Estás a punto de guardar este producto!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    });

    if (value) {
        form.post(route('productos.store'));
        Swal.fire({
            title: '¡Producto guardado!',
            text: 'El producto se ha guardado exitosamente.',
            icon: 'success',
            timer: 3000, 
            showConfirmButton: false
        });
    }
};


// Importante para duplicar
watch(
    () => form.cantidad,
    (newValue) => {
        form.stock = newValue;
        form.ultima_entrada = newValue;
    }
);

// constante para manejar la seleccion automatica del producto
onMounted(() => {
    const category_id = localStorage.getItem('category_id');
    if (category_id) {
        form.category_id = category_id; // Asigna el category_id al modelo de datos del formulario
    }
});

onMounted(() => {
    const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual y la formatea como yyyy-mm-dd
    form.fecha = today; // Asigna la fecha actual al modelo de datos del formulario
});

</script>

<template>
    <AppLayout title="Crear Producto">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Ingresar Producto</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                            <ProductosForm :form="form" :categories="categories" :productos="productos" @submit="handleSubmit"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>