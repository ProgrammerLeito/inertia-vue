<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import Swal from 'sweetalert2';
import { defineProps } from 'vue';
 
// Define los props esperados
const props = defineProps({
    cliente: {
        type : Object,
        // required: true
    },
    tbprovincias: {
        type : Object,
        // required: true
    }
});
 
// Lógica del componente
const form = useForm({
    numeroDocumento: props.cliente.numeroDocumento,
    razonSocial: props.cliente.razonSocial,
    direccion: props.cliente.direccion,
    distrito: props.cliente.distrito,
    provincia: props.cliente.provincia,
    departamento: props.cliente.departamento,
    estado: props.cliente.estado,
    cli_direccion2: props.cliente.cli_direccion2,
    cli_observacion: props.cliente.cli_observacion,
    tbprovincia_id: props.cliente.tbprovincia_id,
});
 
 
 
// Función para enviar el formulario
const submitForm = (id) => {
    form.put(route('clientes.update',{ id }), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'El cliente se ha actualizado correctamente.',
                timer: 1000,
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
                    text: 'Ha ocurrido un error al actualzar el cliente. Por favor, inténtalo de nuevo.',
                    timer: 1000,
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
    <AppLayout title="Actualizar Cliente">
        <!-- Encabezado -->
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Actualizar Cliente</h1>
        </template>
 
        <!-- Contenido del formulario -->
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] dark:bg-gray-800 overflow-auto uppercase text-sm shadow-2xl bg-white rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8 ">
                        <!-- Formulario de edición -->
                        <form @submit.prevent="submitForm($props.cliente.id)">
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel value="razon Social" />
                                    <TextInput v-model="form.numeroDocumento" type="text" placeholder="Ingrese Razon Social" class="mt-2 w-full"/>
                                </div>    
                                <div>
                                    <InputLabel value="razon Social" />
                                    <TextInput v-model="form.razonSocial" type="text" placeholder="Ingrese Razon Social" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel value="estado" />
                                    <TextInput v-model="form.estado" type="text" class="mt-2 w-full"/>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel value="distrito" />
                                    <TextInput v-model="form.distrito" type="text" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel value="provincia" />
                                    <TextInput v-model="form.provincia" type="text"  class="mt-2 w-full" />
                                </div>
                                <div>
                                    <InputLabel value="departamento" />
                                    <TextInput v-model="form.departamento" type="text" class="mt-2 w-full"/>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="tbprovincia_id" value="Ciudad" class="ml-1"/>
                                    <select v-model="form.tbprovincia_id" name="tbprovincia_id" id="tbprovincia_id" class="bg-white border mt-1 border-gray-300 text-gray-900 mb-2 text-base rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-300 dark:placeholder-black dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="" disabled="" selected="selected">Selecciona una Ciudad</option>
                                        <option v-for="tbprovincia in tbprovincias" :key="tbprovincia.id" :value="tbprovincia.id">{{ tbprovincia.prov_nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.prov_clientes" class="mt-2"/>
                                </div>
                                <div>
                                    <InputLabel value="direccion fiscal" />
                                    <TextInput v-model="form.direccion" type="text" placeholder="Ingrese Direccion Legal" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel for="cli_direccion2" value="direccion anexa 1"/>
                                    <TextInput v-model="form.cli_direccion2" type="text" id="cli_direccion2" placeholder="Ingrese Direccion Anexa 1" class="mt-2 w-full uppercase"/>
                                </div>
                            </div>
                                <InputLabel for="cli_observacion" value="Observaciones"/>
                                    <textarea id="cli_observacion" v-model="form.cli_observacion" rows="2" class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escriba las observaciones..."></textarea>
                                <InputError :message="$page.props.errors.cli_observacion" class=""/>
                           
                              <!-- Botones de acción -->
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive class="font-bold">ACTUALIZAR</ButtonResponsive>
                                    <Link :href="route('clientes.index')" class="inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">Cancelar</Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>