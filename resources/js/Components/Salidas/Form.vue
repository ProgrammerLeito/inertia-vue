<script>
export default {
    name: 'SalidasForm',
    mounted() {
        const checkbox = document.getElementById('default-checkbox');
        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
            }
        });
    },
    // entrada para validar solo campos numero con una clase en cajas de texto
    mounted() {
        this.$nextTick(() => {
            $('.validarSoloNumerosDosDecimales').on('input', function() {
                let inputValue = $(this).val();
                inputValue = inputValue.replace(/[^0-9.]/g, '');
                if (inputValue.indexOf('.') !== -1) {
                    inputValue = inputValue.replace(/(\..*)\./g, '$1');
                    let decimalPart = inputValue.split('.')[1];
                    if (decimalPart && decimalPart.length > 2) {
                        decimalPart = decimalPart.substring(0, 2);
                        inputValue = inputValue.split('.')[0] + '.' + decimalPart;
                    }
                }
                $(this).val(inputValue);
            });
        });
    }
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
                <InputLabel for="producto_id" value="Seleccionar Producto" class="ml-1"/>
                <!-- Usar v-model para enlazar el valor seleccionado con la referencia reactiva -->
                <select v-model="form.producto_id" name="producto_id" id="producto_id" class="bg-white border mt-1 border-gray-300 text-gray-900 mb-2 text-base rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-300 dark:placeholder-black dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" disabled="" selected="selected">Selecciona un Producto</option>
                    <option v-for="producto in productos" :key="producto.id" :value="producto.id">{{ producto.insumo }}</option>
                </select>
                <InputError :message="$page.props.errors.producto_id" class="mt-2"/>
                
                <InputLabel for="empresa" value="Empresa" class="ml-1 mt-1"/>
                <TextInput id="empresa" v-model="form.empresa" type="text" autocomplete="empresa" class="mt-1 block w-full" placeholder="Ingrese el nombre de la empresa"/>
                <InputError :message="$page.props.errors.empresa" class="mt-2"/>

                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:gap-x-6 lg:gap-y-8 gap-x-2 gap-y-2">
                    <div class="sm:col-span-1">
                        <InputLabel for="unidad_salida" value="Unidad de Salida" class="ml-1 mt-1"/>
                        <TextInput id="unidad_salida" v-model="form.unidad_salida" type="text" autocomplete="unidad_salida" :disabled="updating === true ? updating != $page.props.user.permissions.includes('Acciones Administrador') : false" class="validarSoloNumerosDosDecimales mt-1 block w-full" placeholder="0"/>
                        <InputError :message="$page.props.errors.unidad_salida" class="mt-2"/>
                    </div>
                    <div class="sm:col-span-1">
                        <InputLabel for="tecnico" value="Seleccionar Personal" class="ml-1 mt-1"/>
                        <select v-model="form.tecnico" name="tecnico" id="tecnico" class="bg-white border border-gray-300 text-gray-900 mb-2 text-base rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-300 dark:placeholder-black dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="" disabled="" selected="selected">Selecciona Personal</option>
                            <option v-for="user in tecnico_salidas" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <InputError :message="$page.props.errors.tecnico" class="mt-2"/>
                    </div>
                </div>

                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:gap-x-6 lg:gap-y-8 gap-x-2 gap-y-2">
                    <div class="sm:col-span-1">
                        <InputLabel for="fecha" value="Fecha" class="ml-1 mt-1"/>
                        <TextInput id="fecha" v-model="form.fecha" type="date" autocomplete="fecha" class="mt-1 block w-full"/>
                        <InputError :message="$page.props.errors.fecha" class="mt-2"/>
                    </div>
                    <div class="sm:col-span-1">
                        <InputLabel for="hora_salida" value="Hora" class="ml-1 mt-1"/>
                        <TextInput id="hora_salida" v-model="form.hora_salida" type="time" autocomplete="hora_salida" class="mt-1 block w-full"/>
                        <InputError :message="$page.props.errors.hora_salida" class="mt-2"/>
                    </div>
                </div>

                <InputLabel for="comentario_salida" value="Comentario" class="ml-1 mt-1"/>
                <textarea id="comentario_salida" v-model="form.comentario_salida" rows="4" class="mt-2 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escriba una descripcion para el producto"></textarea>
                <InputError :message="$page.props.errors.comentario_salida" class="mt-2"/>

                <div class="mt-2 flex justify-center items-center flex-wrap">
                    <div class="sm:col-span-1 flex-1 whitespace-nowrap" :hidden="updating && form.devolucion != 1">
                        <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                            <label for="default-checkbox" class="ms-2 text-sm font-bold text-black dark:text-white">¿Se Espera Devolucion del Producto?</label>
                            <!-- <input id="default-checkbox" type="checkbox" v-model="form.devolucion" true-value="1" false-value="0" :disabled="updating && form.devolucion == 1" class="ml-2 w-4 h-4 text-blue-600 bg-blue-100 border-blue-900 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"> -->
                            <div class="flex">
                                <div class="flex items-center me-4">
                                    <input checked id="inline-checked-radio" v-model="form.devolucion" type="radio" value="0" :disabled="updating && form.devolucion == 1" name="inline-radio-group" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-blue-800 focus:ring-2 dark:bg-gray-700 dark:border-white">
                                    <label for="inline-checked-radio" class="ms-2 text-sm font-bold text-gray-900 dark:text-white">NO</label>
                                </div>
                                <div class="flex items-center me-4">
                                    <input id="inline-2-radio" v-model="form.devolucion" type="radio" value="1" :disabled="updating && form.devolucion == 1" name="inline-radio-group" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-white">
                                    <label for="inline-2-radio" class="ms-2 text-sm font-bold text-gray-900 dark:text-white">SI</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-1 flex-1 whitespace-nowrap" v-if="updating && form.devolucion == 1">
                        <InputLabel for="unidad_devolucion" value="Unidad de Devolucion" class="ml-1 mt-1"/>
                        <TextInput id="unidad_devolucion" v-model="form.unidad_devolucion" type="text" autocomplete="unidad_devolucion" class="mt-1 block w-full"/>
                        <InputError :message="$page.props.errors.unidad_devolucion" class="mt-2"/>
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <PrimaryButton>
                {{ updating ? 'Actualizar' : 'Retirar Producto' }}
            </PrimaryButton>
        </template>

    </FormSection>
</template>