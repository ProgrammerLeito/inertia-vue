<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
 
// Define los props esperados
const props = defineProps({
    tbproducto: {
        type: Object,
        required: true
    },
    tbmarcas: {
        type: Array,
        required: true
    },
    tbcategorias: {
        type: Array,
        required: true
    },
    tbsubcategorias: {
        type: Array,
        required: true
    }
});
 
// Lógica del componente
const form = useForm({
    tbcategoria_id: props.tbproducto.tbcategoria_id,
    tbsubcategoria_id: props.tbproducto.tbsubcategoria_id,
    tbmarca_id: props.tbproducto.tbmarca_id,
    modelo: props.tbproducto.modelo,
    medida: props.tbproducto.medida,
    moneda: props.tbproducto.moneda,
    precio: props.tbproducto.precio,
    descuento: props.tbproducto.descuento,
    stock: props.tbproducto.stock,
    codigo: props.tbproducto.codigo,
    estado: props.tbproducto.estado,
    capacidades: props.tbproducto.capacidades,
    especificaciones: props.tbproducto.especificaciones,
    fotoPreview: props.tbproducto.foto,
});
 
const onFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;
 
    // Actualizar la vista previa de la foto
    const reader = new FileReader();
    reader.onload = (e) => {
        form.fotoPreview = e.target.result; // Actualizar la vista previa con la imagen cargada
    };
    reader.readAsDataURL(file);
 
    // Actualizar el formulario con la nueva foto
    form.foto = file; // Actualizar la propiedad 'foto' en el formulario con el nuevo archivo seleccionado
};
 
 
// Función para enviar el formulario
const submitForm = (id) => {
    form.put(route('tbproductos.update',{ id }), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'El producto se ha actualizado correctamente.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        },
        onError: (errors) => {
            if(errors.response && errors.response.status) {
                // Si hay un error de respuesta HTTP, manejarlo aquí
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al actualzar el producto. Por favor, inténtalo de nuevo.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                console.error('Error HTTP:', errors.response.status);
            } else {
                // Si el error no tiene una propiedad de respuesta o de estado, manejarlo aquí
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.'
                });
                console.error('Error desconocido:', errors);
            }
        }
    });
};
</script>
 
<template>
    <AppLayout title="Actualizar Producto">
        <!-- Encabezado -->
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Actualizar Producto</h1>
        </template>
 
        <!-- Contenido del formulario -->
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] dark:bg-gray-800 overflow-auto uppercase text-sm shadow-2xl bg-white rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8 ">
                        <!-- Formulario de edición -->
                        <form @submit.prevent="submitForm($props.tbproducto.id)">
                            <!-- Campos de selección -->
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3  sm:gap-x-8 mb-3">
                                <!-- Categoría -->
                                <div>
                                    <InputLabel for="tbcategoria" class="block text-sm font-medium text-gray-700">Categoría</InputLabel>
                                    <select id="tbcategoria" v-model="form.tbcategoria_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una categoría</option>
                                        <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id" :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.tbcategoria_id" class="mt-2"/>
                                </div>
 
                                <!-- Subcategoría -->
                                <div>
                                    <InputLabel for="tbsubcategoria" class="block text-sm font-medium text-gray-700">Subcategoría</InputLabel>
                                    <select id="tbsubcategoria" v-model="form.tbsubcategoria_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una subcategoría</option>
                                        <option v-for="tbsubcategoria in tbsubcategorias" :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.tbsubcategoria_id" class="mt-2"/>
                                </div>
 
                                <!-- Marca -->
                                <div>
                                    <InputLabel for="tbmarca" class="block text-sm font-medium text-gray-700">Marca</InputLabel>
                                    <select id="tbmarca" v-model="form.tbmarca_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una marca</option>
                                        <option v-for="tbmarca in tbmarcas" :key="tbmarca.id" :value="tbmarca.id">{{ tbmarca.nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.tbmarca_id" class="mt-2"/>
                                </div>
                            </div>
 
                            <!-- Otros campos -->
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-4 sm:gap-x-8 mb-3">
                                <!-- Modelo -->
                                <div>
                                    <InputLabel for="modelo" class="block text-sm font-medium text-gray-700">Modelo</InputLabel>
                                    <TextInput type="text" id="modelo" v-model="form.modelo" required placeholder="Ingrese el modelo"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.modelo" class="mt-2"/>
                                </div>
 
                                <!-- Medida -->
                                <div>
                                    <InputLabel for="medida" class="block text-sm font-medium text-gray-700">Medida</InputLabel>
                                    <TextInput type="text" id="medida" v-model="form.medida" required placeholder="Ingrese la medida"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.medida" class="mt-2"/>
                                </div>
 
                                <!-- Moneda -->
                                <div>
                                    <InputLabel for="moneda" class="block text-sm font-medium text-gray-700">Moneda</InputLabel>
                                    <select id="moneda" v-model="form.moneda" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Selecciona una moneda</option>
                                        <option value="sol">Sol</option>
                                        <option value="dolares">Dólares</option>
                                    </select>
                                    <InputError :message="$page.props.errors.moneda" class="mt-2"/>
                                </div>
 
                                <!-- Precio -->
                                <div>
                                    <InputLabel for="precio" class="block text-sm font-medium text-gray-700">Precio</InputLabel>
                                    <TextInput type="number" id="precio" v-model="form.precio" required placeholder="Ingrese el precio"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.precio" class="mt-2"/>
                                </div>
 
                                <!-- Descuento -->
                                <div>
                                    <InputLabel for="descuento" class="block text-sm font-medium text-gray-700">Descuento (%)</InputLabel>
                                    <TextInput type="number" id="descuento" v-model="form.descuento" placeholder="Ingrese el descuento"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.descuento" class="mt-2"/>
                                </div>
 
                                <!-- Stock -->
                                <div>
                                    <InputLabel for="stock" class="block text-sm font-medium text-gray-700">Stock</InputLabel>
                                    <TextInput type="number" id="stock" v-model="form.stock" required placeholder="Ingrese el stock"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.stock" class="mt-2"/>
                                </div>
 
                                <!-- Código -->
                                <div>
                                    <InputLabel for="codigo" class="block text-sm font-medium text-gray-700">Código</InputLabel>
                                    <TextInput type="text" id="codigo" v-model="form.codigo" required placeholder="Ingrese el código"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.codigo" class="mt-2"/>
                                </div>
 
                                <!-- Estado -->
                                <div>
                                    <InputLabel for="estado" class="block text-sm font-medium text-gray-700">Estado</InputLabel>
                                    <select id="estado" v-model="form.estado" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Selecciona un estado</option>
                                        <option value="activado">Activado</option>
                                        <option value="suspendido">Suspendido</option>
                                    </select>
                                    <InputError :message="$page.props.errors.estado" class="mt-2"/>
                                </div>
                            </div>
 
                            <!-- Capacidades -->
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8">
                                <div>
                                    <InputLabel for="capacidades" class="block text-sm font-medium text-gray-700">Capacidades</InputLabel>
                                    <textarea id="capacidades" v-model="form.capacidades" rows="5" required placeholder="Ingrese las capacidades"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.capacidades" class="mt-2"/>
                                </div>
 
                                <!-- Especificaciones -->
                                <div>
                                    <InputLabel for="especificaciones" class="block text-sm font-medium text-gray-700">Especificaciones</InputLabel>
                                    <textarea id="especificaciones" v-model="form.especificaciones" rows="5" required placeholder="Ingrese las especificaciones"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.especificaciones" class="mt-2"/>
                                </div>
 
                                <!-- Foto -->
                                <div>
                                    <!-- <InputLabel for="foto" class="block text-sm font-medium text-gray-700">Foto</InputLabel>
                                    <input @change="onFileChange" type="file" id="foto" accept="image/*"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.foto" class="mt-2"/>
                                    <div v-if="form.fotoPreview" class="mt-2 shadow-lg">
                                        <img :src="form.fotoPreview" alt="Vista previa de la foto" style="max-width: 200px; max-height: 300px;">
                                    </div>
                                    <div v-else class="mt-3">
                                        <p>No hay foto seleccionada</p>
                                    </div> -->
                                    <InputLabel for="foto" class="block text-sm font-medium text-gray-700">Foto</InputLabel>
                                    <input @change="onFileChange" type="file" id="foto" accept="image/*"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.foto" class="mt-2"/>
                                    <div v-if="form.fotoPreview" class="mt-2 shadow-lg">
                                        <img :src="form.fotoPreview" alt="Vista previa de la foto" style="max-width: 200px; max-height: 300px;">
                                    </div>
                                    <div v-else class="mt-3">
                                        <p>No hay foto seleccionada</p>
                                    </div>
                                </div>
                            </div>
                              <!-- Botones de acción -->
                            <div class="mt-6">
                                <PrimaryButton>Actualizar</PrimaryButton>
                                <Link :href="route('tbproductos.index')" class="ml-5 inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700">Cancelar</Link>
                            </div>
                        </form>
 
 
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
 