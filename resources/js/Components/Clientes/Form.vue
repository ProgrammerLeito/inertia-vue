<script>
export default {
    name: 'ClientesForm'
}
</script>

<script setup>

import FormSection from '@/Components/FormSection.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

defineProps({
    form: {
        type: Object,
        required: true
    },
    updating: {
        type: Boolean,
        required: false,
        default: false
    },
    clientes: {
        type : Object,
        required: true
    }
})

defineEmits(['submit'])

</script>

<template>
    <FormSection @submitted="$emit('submit')">
        <template #title>
            {{ updating ? 'Actualizar Cliente' : 'Ingresar Nuevo Cliente' }}
        </template>

        <template #description>
            {{ updating ? 'Actualizar el cliente seleccionado' : 'Crear un nuevo cliente' }}
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-6">

                <InputLabel for="cli_ruc" value="Ruc"/>
                <TextInput id="cli_ruc" v-model="form.cli_ruc" type="text" autocomplete="cli_ruc" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.cli_ruc" class="mt-2"/>

                <!-- <label id="razon-social-two">{{ $razonSocial }}</label> -->

                <InputLabel for="cli_razonSocial" value="Razon Social"/>
                <TextInput id="cli_razonSocial" v-model="form.cli_razonSocial" type="text" autocomplete="cli_razonSocial" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.cli_razonSocial" class="mt-2"/>

                <InputLabel for="cli_ciudad" value="Ciudad"/>
                <TextInput id="cli_ciudad" v-model="form.cli_ciudad" type="text" autocomplete="cli_ciudad" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.cli_ciudad" class="mt-2"/>

                <InputLabel for="cli_direccionlegal" value="Direccion Legal"/>
                <TextInput id="cli_direccionlegal" v-model="form.cli_direccionlegal" type="text" autocomplete="cli_direccionlegal" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.cli_direccionlegal" class="mt-2"/>

                <InputLabel for="cli_direccion1" value="Direccion Anexa 1"/>
                <TextInput id="cli_direccion1" v-model="form.cli_direccion1" type="text" autocomplete="cli_direccion1" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.cli_direccion1" class="mt-2"/>

                <InputLabel for="cli_direccion2" value="Direccion Anexa 2"/>
                <TextInput id="cli_direccion2" v-model="form.cli_direccion2" type="text" autocomplete="cli_direccion2" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.cli_direccion2" class="mt-2"/>

                <InputLabel for="cli_observacion" value="Observaciones"/>
                <textarea id="cli_observacion" v-model="form.cli_observacion" rows="4" class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escriba una descripcion para el producto"></textarea>
                <InputError :message="$page.props.errors.cli_observacion" class="mt-2"/>

            </div>
        </template>

        <template #actions>
            <PrimaryButton>
                {{ updating ? 'Actualizar' : 'Ingresar' }}
            </PrimaryButton>
        </template>

    </FormSection>
</template>