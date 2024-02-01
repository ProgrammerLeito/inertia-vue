<script>
export default {
    name: 'ProductosForm'
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
    categories: {
        type : Object,
        required: true
    }
})

defineEmits(['submit'])

</script>

<template>
    <FormSection @submitted="$emit('submit')">
        <template #title>
            {{ updating ? 'Actualizar Producto' : 'Ingresar Nuevo Producto a Almacen' }}
        </template>

        <template #description>
            {{ updating ? 'Actualizar el producto seleccionado' : 'Crear un nuevo producto' }}
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-6">

                <InputLabel for="insumo" value="Insumo"/>
                <TextInput id="insumo" v-model="form.insumo" type="text" autocomplete="insumo" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.insumo" class="mt-2"/>

                <InputLabel for="marca" value="Marca"/>
                <TextInput id="marca" v-model="form.marca" type="text" autocomplete="marca" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.marca" class="mt-2"/>

                <InputLabel for="modelo" value="Modelo"/>
                <TextInput id="modelo" v-model="form.modelo" type="text" autocomplete="modelo" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.modelo" class="mt-2"/>

                <InputLabel for="cantidad" value="Cantidad"/>
                <TextInput id="cantidad" v-model="form.cantidad" type="text" autocomplete="cantidad" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.cantidad" class="mt-2"/>

                <InputLabel for="unidad_medida" value="Unidad de Medida"/>
                <TextInput id="unidad_medida" v-model="form.unidad_medida" type="text" autocomplete="unidad_medida" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.unidad_medida" class="mt-2"/>
                
                <InputLabel for="fecha" value="Fecha"/>
                <TextInput id="fecha" v-model="form.fecha" type="date" autocomplete="fecha" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.fecha" class="mt-2"/>
                
                <InputLabel for="empresa" value="Empresa"/>
                <TextInput id="empresa" v-model="form.empresa" type="text" autocomplete="empresa" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.empresa" class="mt-2"/>

                <InputLabel for="comentario" value="Comentario"/>
                <TextInput id="comentario" v-model="form.comentario" type="text" autocomplete="comentario" class="mt-1 block w-full"/>
                <InputError :message="$page.props.errors.comentario" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <PrimaryButton>
                {{ updating ? 'Actualizar' : 'Ingresar' }}
            </PrimaryButton>
        </template>

    </FormSection>
</template>