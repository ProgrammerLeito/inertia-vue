<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import FileInput from '@/Components/FileInput.vue';
import Swal from 'sweetalert2';
import { ref } from 'vue'; // Importa ref de vue

const props = defineProps({
    productos: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    imagenProducto: {
        type: Object, // Si es necesario, ajusta el tipo según la estructura del elemento de archivo
        required: true
    }
})

const form = useForm({
    insumo: props.productos.insumo,
    marca: props.productos.marca,
    modelo: props.productos.modelo,
    cantidad: props.productos.cantidad,
    unidad_medida: props.productos.unidad_medida,
    fecha: props.productos.fecha,
    comprador: props.productos.comprador,
    comentario: props.productos.comentario,
    stock: props.productos.stock,
    precio: props.productos.precio,
    ultima_entrada: props.productos.ultima_entrada,
    category_id: props.productos.category_id,
    imagen_producto: props.productos.imagen_producto
})

const submitForm = () => {
    form.post(route('producto.update', props.productos.id), {
        onSuccess: () => {
            const Toast = Swal.mixin({
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: 'Éxito',
                text: 'El producto se ha actualizado correctamente.',
                customClass: {
                    title: 'text-2xl font-bold tracking-widest ',
                    text: 'text-2xl font-bold tracking-widest ',
                    icon: 'text-base font-bold tracking-widest ',
                },
            });
        },
    });
};

const imagePreview2 = ref('');
const onSelectFoto2 = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
        const reader = new FileReader();
        reader.onload = (e) => {
            switch (fieldName) {
                case 'foto':
                    imagePreview2.value = e.target.result;
                    break;
            }
        };
        reader.readAsDataURL(files[0]);
        switch (fieldName) {
            case 'foto':
                form.imagen_producto = files[0];
                break;
        }
    }
}

</script>

<template>
    <AppLayout title="Editar Producto">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Editar Producto</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submitForm">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                            <div class="flex flex-col md:flex-row justify-center items-start md:gap-40 gap-0 gap-y-5">
                                <div>
                                    <p class="text-gray-900 dark:text-white font-extrabold">Actualizar Producto</p>
                                    <p class="text-gray-900 dark:text-white font-normal">Actualizar el producto seleccionado</p>
                                </div>
                                <div class="md:flex-1 w-full">
                                    <div class="class dark:bg-gray-700 bg-gray-100 rounded-t-lg p-5">
                                        <InputLabel for="category_id" value="Categorias" class="ml-1"/>
                                        <select v-model="form.category_id" name="category_id" id="category_id" class="bg-white border mt-1 border-gray-300 text-gray-900 mb-2 text-base rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-300 dark:placeholder-black dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="" disabled="" selected="selected">Selecciona una Categoria</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                        </select>
                                        <InputError :message="$page.props.errors.category_id" class="mt-2"/>
                                        <InputLabel for="insumo" value="Producto" class="ml-1 mt-1"/>
                                        <TextInput id="insumo" v-model="form.insumo" type="text" autocomplete="insumo" class="mt-1 block w-full" placeholder="Ingrese el nombre del producto"/>
                                        <InputError :message="$page.props.errors.insumo" class="mt-2"/>
        
                                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:gap-x-6 lg:gap-y-8 gap-x-2 gap-y-2">
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
        
                                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:gap-x-6 lg:gap-y-8 gap-x-2 gap-y-2">
                                            <div class="sm:col-span-1">
                                                <InputLabel for="cantidad" value="Cantidad" class="ml-1"/>
                                                <TextInput id="cantidad" v-model="form.cantidad" type="text" autocomplete="cantidad" :disabled="!$page.props.user.permissions.includes('Acciones Administrador')" class="mt-1 block w-full" placeholder="0"/>
                                                <InputError :message="$page.props.errors.cantidad" class="mt-2"/>
                                            </div>
                                            <div class="sm:col-span-1">
                                                <InputLabel for="unidad_medida" value="Unidad de Medida" class="ml-1"/>
                                                <TextInput id="unidad_medida" v-model="form.unidad_medida" type="text" autocomplete="unidad_medida" class="mt-1 block w-full" placeholder="Ingrese la medida"/>
                                                <InputError :message="$page.props.errors.unidad_medida" class="mt-2"/>
                                            </div>
                                        </div>
        
                                        <div class="hidden">
                                            <div>
                                                <InputLabel for="stock" value="Stock"/>
                                                <TextInput id="stock" v-model="form.stock" type="text" autocomplete="stock" class="mt-1 block w-full"/>
                                                <InputError :message="$page.props.errors.stock" class="mt-2"/>
                                            </div>
        
                                            <div>
                                                <InputLabel for="ultima_entrada" value="Cantidad Ultima Entrada"/>
                                                <TextInput id="ultima_entrada" v-model="form.ultima_entrada" type="text" autocomplete="ultima_entrada" class="mt-1 block w-full"/>
                                                <InputError :message="$page.props.errors.ultima_entrada" class="mt-2"/>
                                            </div>
                                        </div>
        
                                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:gap-x-6 lg:gap-y-8 gap-x-2 gap-y-2">
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
                    
                                        <div>
                                            <InputLabel for="foto" value="Foto" class="block text-md font-medium text-gray-700" />
                                            <FileInput name="foto" @change="(e) => onSelectFoto2(e, 'foto')" class="text-sm" />
                                            <InputError :message="$page.props.errors.imagen_producto" class="mt-2" />

                                            <div class="flex flex-wrap gap-10 justify-center items-center mb-4 mt-4">
                                                <!-- Foto Actual -->
                                                <div class="text-center dark:text-white">
                                                    <img :src="'/productos_inventario/' + productos.imagen_producto" alt="Foto actual" class="p-2 block w-36 h-36 items-center object-contain text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-200 dark:bg-gray-600 focus:outline-none dark:border-gray-500">
                                                    <p class="text-sm dark:text-white text-gray-900 mt-1">Foto actual</p>
                                                </div>
                                                <div v-if="imagePreview2">
                                                    <i class="fa-solid fa-right-left dark:text-white text-gray-900 text-4xl"></i>
                                                </div>
                                                
                                                <!-- Vista Previa de la Nueva Foto -->
                                                <div v-if="imagePreview2" class="text-center dark:text-white">
                                                    <img :src="imagePreview2" alt="Vista previa de la foto" class="p-2 block w-36 h-36 items-center object-contain text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-200 dark:bg-gray-600 focus:outline-none dark:border-gray-500">
                                                    <p class="text-sm dark:text-white text-gray-900 mt-1">Nueva Foto</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end dark:bg-gray-600 bg-gray-200 py-3 pr-4 rounded-b-lg">
                                        <button class="px-4 py-2 text-white font-medium tracking-wider text-xs uppercase rounded-lg bg-blue-700 hover:bg-blue-800 focus:outline-none focus:shadow-outline">
                                            Actualizar Producto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>