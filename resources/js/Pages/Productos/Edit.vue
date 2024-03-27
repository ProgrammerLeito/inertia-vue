<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductosForm from '@/Components/Productos/Form.vue';
import Swal from 'sweetalert2';
import { ref } from 'vue'; // Importa ref de vue

const props = defineProps({
    productos: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    imagenProducto: {
        type: Object, // Si es necesario, ajusta el tipo según la estructura del elemento de archivo
        required: true
    }
})

const form = useForm({
    insumo: props.productos.insumo,
    marca: props.productos.marca,
    modelo: props.productos.modelo,
    cantidad: props.productos.cantidad,
    unidad_medida: props.productos.unidad_medida,
    fecha: props.productos.fecha,
    comprador: props.productos.comprador,
    comentario: props.productos.comentario,
    stock: props.productos.stock,
    precio: props.productos.precio,
    ultima_entrada: props.productos.ultima_entrada,
    category_id: props.productos.category_id,
    imagen_producto: props.productos.imagen_producto
})



// Define una referencia para almacenar el valor del elemento de entrada de archivo
const imagenProducto = ref(null);
const handleSubmit = async () => {
    try {
        // Accede al valor del elemento de entrada de archivo usando la referencia
        if (imagenProducto.value && imagenProducto.value.files.length > 0) {
            form.imagen_producto = imagenProducto.value.files[0];
        }
        await form.put(route('productos.update', props.productos.id)); // Acceder a props.productos en lugar de productos
        Swal.fire({
            icon: 'success',
            title: '¡Producto actualizado!',
            text: 'El producto se ha actualizado exitosamente.',
            timer: 3000, 
            showConfirmButton: false
        });
    } catch (error) {
        console.error('Error al actualizar el producto:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al actualizar el producto. Por favor, inténtalo de nuevo.'
        });
    }
};

</script>

<template>
    <AppLayout title="Editar Producto">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Editar Producto</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"></div>
                <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                    <!-- Pasa la referencia al componente de formulario -->
                    <ProductosForm :updating="true" :form="form" :categories="categories" :productos="productos" :imagenProducto="imagenProducto" @submit="handleSubmit"/>

                </div>
            </div>
        </div>
    </AppLayout>
</template>