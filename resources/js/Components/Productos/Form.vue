<script>
export default {
    name: 'ProductosForm',
    methods: {
        obtenerImagen(e){
            const file = e.target.files[0];
            this.form.imagen_producto = file;

            this.cargarImagen(file);
        },

        cargarImagen(file){
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagenMiniatura= e.target.result;
            }
            reader.readAsDataURL(file);
        }
    },
    data() {
        return {
            imagenMiniatura: '',
        }
    },
    computed: {
        imagen(){
            return this.imagenMiniatura;
        }
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
    categories: {
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
            {{ updating ? 'Actualizar Producto' : 'Ingresar Nuevo Producto a Almacen' }}
        </template>

        <template #description>
            {{ updating ? 'Actualizar el producto seleccionado' : 'Crear un nuevo producto' }}
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-6">

                <InputLabel for="category_id" value="Categorias" class="ml-1"/>
                <select v-model="form.category_id" name="category_id" id="category_id" class="bg-white border mt-1 border-gray-300 text-gray-900 mb-2 text-base rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-300 dark:placeholder-black dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" disabled="" selected="selected">Selecciona una Categoria</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
                <InputError :message="$page.props.errors.category_id" class="mt-2"/>

                <InputLabel for="insumo" value="Producto" class="ml-1 mt-1"/>
                <TextInput id="insumo" v-model="form.insumo" type="text" autocomplete="insumo" class="mt-1 block w-full" placeholder="Ingrese el nombre del producto"/>
                <InputError :message="$page.props.errors.insumo" class="mt-2"/>

                <div class="mt-2 grid grid-cols-2 lg:grid-cols-20 lg:gap-x-6 lg:gap-y-8 gap-x-2 gap-y-2">
                    <div class="sm:col-span-1">
                        <InputLabel for="marca" value="Marca de producto" class="ml-1"/>
                        <TextInput id="marca" v-model="form.marca" type="text" autocomplete="marca" class="mt-1 block w-full" placeholder="Ingrese la marca"/>
                        <InputError :message="$page.props.errors.marca" class="mt-2"/>
                    </div>
                    <div class="sm:col-span-1">
                        <InputLabel for="modelo" value="Modelo de producto" class="ml-1"/>
                        <TextInput id="modelo" v-model="form.modelo" type="text" autocomplete="modelo" class="mt-1 block w-full" placeholder="Ingrese el modelo"/>
                        <InputError :message="$page.props.errors.modelo" class="mt-2"/>
                    </div>
                </div>

                <div class="mt-2 grid grid-cols-2 lg:grid-cols-20 lg:gap-x-6 lg:gap-y-8 gap-x-2 gap-y-2">
                    <div class="sm:col-span-1">
                        <InputLabel for="cantidad" value="Cantidad" class="ml-1"/>
                        <TextInput id="cantidad" v-model="form.cantidad" type="text" autocomplete="cantidad" class="validarSoloNumerosDosDecimales mt-1 block w-full" :disabled="updating" placeholder="0"/>
                        <InputError :message="$page.props.errors.cantidad" class="mt-2"/>
                    </div>
                    <div class="sm:col-span-1">
                        <InputLabel for="unidad_medida" value="Unidad de Medida" class="ml-1"/>
                        <TextInput id="unidad_medida" v-model="form.unidad_medida" type="text" autocomplete="unidad_medida" class="mt-1 block w-full" placeholder="Ingrese la medida"/>
                        <InputError :message="$page.props.errors.unidad_medida" class="mt-2"/>
                    </div>
                </div>

                <div class="hidden">
                    <div v-if="!updating">
                        <InputLabel for="stock" value="Stock"/>
                        <TextInput id="stock" v-model="form.stock" type="text" autocomplete="stock" class="mt-1 block w-full"/>
                        <InputError :message="$page.props.errors.stock" class="mt-2"/>
                    </div>

                    <div v-if="!updating">
                        <InputLabel for="ultima_entrada" value="Cantidad Ultima Entrada"/>
                        <TextInput id="ultima_entrada" v-model="form.ultima_entrada" type="text" autocomplete="ultima_entrada" class="mt-1 block w-full"/>
                        <InputError :message="$page.props.errors.ultima_entrada" class="mt-2"/>
                    </div>
                </div>

                <div class="mt-2 grid grid-cols-2 lg:grid-cols-20 lg:gap-x-6 lg:gap-y-8 gap-x-2 gap-y-2">
                    <div class="sm:col-span-1">
                        <InputLabel for="fecha" value="Fecha" class="ml-1"/>
                        <TextInput id="fecha" v-model="form.fecha" type="date" autocomplete="fecha" class="mt-1 block w-full"/>
                        <InputError :message="$page.props.errors.fecha" class="mt-2"/>
                    </div>
                    <div class="sm:col-span-1">
                        <InputLabel for="comprador" value="Comprador" class="ml-1"/>
                        <TextInput id="comprador" v-model="form.comprador" type="text" autocomplete="comprador" class="mt-1 block w-full" placeholder="Ingrese el comprador"/>
                        <InputError :message="$page.props.errors.comprador" class="mt-2"/>
                    </div>
                </div>
                
                <InputLabel for="precio" value="Precio Aproximado" class="ml-1 mt-2"/>
                <TextInput id="precio" v-model="form.precio" type="text" autocomplete="precio" class="validarSoloNumerosDosDecimales mt-1 block w-full" placeholder="0.00"/>
                <InputError :message="$page.props.errors.precio" class="mt-2"/>

                <InputLabel for="comentario" value="Comentario" class="ml-1 mt-1"/>
                <textarea id="comentario" v-model="form.comentario" rows="4" class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escriba una descripcion para el producto"></textarea>
                <InputError :message="$page.props.errors.comentario" class="mt-2"/>
                
                <div v-if="!updating">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white ml-1 mt-1" for="file_input">Seleccionar Imagen</label>
                    <input @change="obtenerImagen" :v-model="form.imagen_producto" class="p-2 block w-full text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file">
                    <InputError :message="$page.props.errors.imagen_producto" class="mt-2"/>
                    
                    <div class="flex justify-center mt-2">
                        <figure v-if="imagenMiniatura !== ''">
                            <img class="h-[150px] w-[150px]" :src="imagenMiniatura" alt="Foto del Producto">
                        </figure>
                    </div>
                </div>

            </div>
        </template>

        <template #actions>
            <PrimaryButton>
                {{ updating ? 'Actualizar' : 'Registrar Producto' }}
            </PrimaryButton>
        </template>

    </FormSection>
</template>