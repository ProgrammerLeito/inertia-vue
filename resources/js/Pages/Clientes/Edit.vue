<script>
export default {
    name: 'ClientesEdit'
}

</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ClientesForm from '@/Components/Clientes/Form.vue';

const props = defineProps({
    cliente: {
        type: Object,
        required: true,
    }
})

const form = useForm({
    cli_razonSocial: props.cliente.cli_razonSocial,
    cli_ruc: props.cliente.cli_ruc,
    cli_ciudad: props.cliente.cli_ciudad,
    cli_direccionlegal: props.cliente.cli_direccionlegal,
    cli_direccion1: props.cliente.cli_direccion1,
    cli_direccion2: props.cliente.cli_direccion2,
    cli_observacion: props.cliente.cli_observacion
})

</script>

<template>
    <AppLayout title="Edit Category">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Editar Categoria</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"></div>
                <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                    <ClientesForm :updating="true" :form="form" @submit="form.put(route('clientes.update', cliente.idCliente))"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>