<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineProps({
    permisos: {
        type : Object,
        required: true
    }
})
 
const form = useForm({
  name: '',
 
  // Agregar otros campos según sea necesario
});
 
const submitForm = () => {
    form.post(route('permisos.store'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'La inscripciones se ha registrado correctamente.',
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
                    text: 'Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.'
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
}
 
</script>
<template>
    <AppLayout title="Registrar Permiso">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Registrar Permisos</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submitForm">
                        <div>
                            <InputLabel for="name" value="Nombre" class="text-md ml-1"/>
                            <TextInput v-model="form.name" type="text" id="name" placeholder="Escribe un permiso" class="w-full mt-2 text-sm"/>
                        </div>
                        <div class="flex justify-end mt-4">
                            <PrimaryButton type="submit">Guardar</PrimaryButton>
                            <Link :href="route('permisos.index')" class="ml-5 uppercase text-sm inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700">
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>  
            </div>
        </div>
    </AppLayout>
</template>