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
                <InputLabel for="empresa" value="Empresa"/>
                <TextInput id="empresa" v-model="form.empresa" type="text" autocomplete="empresa" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.empresa" class="mt-2"/>

                <InputLabel for="producto_id" value="Seleccionar Producto"/>
                <select v-model="form.producto_id" name="producto_id" id="producto_id">
                    <option value="" disabled="" selected="selected">Selecciona un Producto</option>
                    <option v-for="producto in productos" :key="producto.id" :value="producto.id">{{ producto.insumo }}</option>
                </select>
                <InputError :message="$page.props.errors.producto_id" class="mt-2"/>

                <InputLabel for="unidad_salida" value="Unidad de Salida"/>
                <TextInput id="unidad_salida" v-model="form.unidad_salida" type="text" autocomplete="unidad_salida" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.unidad_salida" class="mt-2"/>

                <InputLabel for="tecnico" value="Tecnico"/>
                <TextInput id="tecnico" v-model="form.tecnico" type="text" autocomplete="tecnico" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.tecnico" class="mt-2"/>

                <InputLabel for="fecha" value="Fecha"/>
                <TextInput id="fecha" v-model="form.fecha" type="date" autocomplete="fecha" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.fecha" class="mt-2"/>

                <InputLabel for="comentario_salida" value="Comentario"/>
                <TextInput id="comentario_salida" v-model="form.comentario_salida" type="text" autocomplete="comentario_salida" class="mt-1 block w-full"/>
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