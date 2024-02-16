<script>
export default {
    name: 'EntradasForm'
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
    entradas: {
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
            {{ updating ? 'Actualizar entrada' : 'Crear nueva Entrada' }}
        </template>

        <template #description>
            {{ updating ? 'Actualizar la entrada seleccionada' : 'Crear una nueva entrada del producto' }}
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-6">
                <InputLabel for="producto_id" value="Seleccionar Producto"/>
                <select v-model="form.producto_id" name="producto_id" id="producto_id">
                    <option value="" disabled="" selected="selected">Selecciona un Producto</option>
                    <option v-for="producto in productos" :key="producto.id" :value="producto.id">{{ producto.insumo }}</option>
                </select>
                <InputError :message="$page.props.errors.producto_id" class="mt-2"/>

                <InputLabel for="cantidad" value="Cantidad"/>
                <TextInput id="cantidad" v-model="form.cantidad" type="text" autocomplete="cantidad" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.cantidad" class="mt-2"/>

                <InputLabel for="fecha" value="Fecha"/>
                <TextInput id="fecha" v-model="form.fecha" type="date" autocomplete="fecha" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.fecha" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <PrimaryButton>
                {{ updating ? 'Actualizar' : 'Crear' }}
            </PrimaryButton>
        </template>

    </FormSection>
</template>