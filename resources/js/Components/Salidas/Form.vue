<script>
export default {
    name: 'SalidasForm'
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

defineEmits(['submit'])

</script>

<template>
    <FormSection @submitted="$emit('submit')">
        <template #title>
            {{ updating ? 'Actualizar salida' : 'Ingresar salida de producto' }}
        </template>

        <template #description>
            {{ updating ? 'Actualizar la salida seleccionada' : 'Crear una nueva salida del producto' }}
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-6">

                <!-- {{ tecnico_salidas }} -->

                <InputLabel for="empresa" value="Empresa"/>
                <TextInput id="empresa" v-model="form.empresa" type="text" autocomplete="empresa" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.empresa" class="mt-2"/>

                <InputLabel for="producto_id" value="Seleccionar Producto"/>
                <select v-model="form.producto_id" name="producto_id" id="producto_id" class="bg-white border border-gray-300 text-gray-900 mb-2 text-base rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-300 dark:placeholder-black dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" disabled="" selected="selected">Selecciona un Producto</option>
                    <option v-for="producto in productos" :key="producto.id" :value="producto.id">{{ producto.insumo }}</option>
                </select>
                <InputError :message="$page.props.errors.producto_id" class="mt-2"/>

                <InputLabel for="unidad_salida" value="Unidad de Salida"/>
                <TextInput id="unidad_salida" v-model="form.unidad_salida" type="text" autocomplete="unidad_salida" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.unidad_salida" class="mt-2"/>

                <InputLabel for="tecnico" value="Seleccionar Personal"/>
                <select v-model="form.tecnico" name="tecnico" id="tecnico" class="bg-white border border-gray-300 text-gray-900 mb-2 text-base rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-300 dark:placeholder-black dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" disabled="" selected="selected">Selecciona Personal</option>
                    <option v-for="user in tecnico_salidas" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
                <InputError :message="$page.props.errors.tecnico" class="mt-2"/>

                <InputLabel for="fecha" value="Fecha"/>
                <TextInput id="fecha" v-model="form.fecha" type="date" autocomplete="fecha" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.fecha" class="mt-2"/>

                <InputLabel for="comentario_salida" value="Comentario"/>
                <textarea id="comentario_salida" v-model="form.comentario_salida" rows="4" class="mt-2 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escriba una descripcion para el producto"></textarea>
                <InputError :message="$page.props.errors.comentario_salida" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <PrimaryButton>
                {{ updating ? 'Actualizar' : 'Crear' }}
            </PrimaryButton>
        </template>

    </FormSection>
</template>