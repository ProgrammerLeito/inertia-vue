<script>
    export default {
        name: 'ClientesCreate'
    }
</script>

<script setup>

import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ClientesForm from '@/Components/Clientes/Form.vue';

defineProps({
    clientes: {
        type : Object,
        required: true
    }
})

const form = useForm ({
    cli_razonSocial: '',
    cli_ruc: '',
    cli_ciudad: '',
    cli_direccionlegal: '',
    cli_direccion1: '',
    cli_direccion2: '',
    cli_observacion: '',
})

</script>

<template>
    <AppLayout title="Crear Cliente">
        <template #header>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Crear Cliente</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                            <ClientesForm :form="form" @submit="form.post(route('clientes.store'))"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>