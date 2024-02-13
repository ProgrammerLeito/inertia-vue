<script>
    export default {
        name: 'ProductosCreate'
    }
</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SalidasForm from '@/Components/Salidas/Form.vue';

defineProps({
    salidas: {
        type : Object,
        required: true
    },
    productos: {
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
    salida_id: '',
})
</script>

<template>
    <AppLayout title="Crear Producto">
        <template #header>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">Ingresar Producto</h1>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <SalidasForm :form="form" :productos="productos" :salidas="salidas" @submit="form.post(route('salidas.store'))"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>