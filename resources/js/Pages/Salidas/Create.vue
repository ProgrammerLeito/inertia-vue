<script>
    export default {
        name: 'ProductosCreate'
    }
</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SalidasForm from '@/Components/Salidas/Form.vue';
import { onMounted } from 'vue';
import Swal from 'sweetalert2';

defineProps({
    salidas: {
        type : Object,
        required: true
    },
    productos: {
        type : Object,
        required: true
    },
    tecnico_salidas: {
        type : Object,
        required: true
    }
})

const form = useForm ({
    empresa: '',
    unidad_salida: '',
    comentario_salida: '',
    tecnico: '',
    fecha: '',
    producto_id: '',
})

onMounted(() => {
    const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual y la formatea como yyyy-mm-dd
    form.fecha = today; // Asigna la fecha actual al modelo de datos del formulario
});

function warn(event) {
    if (event) {
        console.log("ingreso")
        event.preventDefault()
    }
    Swal.fire({
        title: "Ingrese su Contraseña para confirmar su salida",
        input: "text",
        inputAttributes: {
            autocapitalize: "off"
        },
        showCancelButton: true,
        confirmButtonText: "Confirmar",
        showLoaderOnConfirm: true,
        cancelButtonText: "Cancelar",
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            console.log("holas")
            comprobar_salida
        }
    });
}

</script>

<template>
    <AppLayout title="Crear Producto">
        <template #header>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Ingresar Producto</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                            <SalidasForm :form="form" :productos="productos" :salidas="salidas" :tecnico_salidas="tecnico_salidas" @submit="warn($event)"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>