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
 
const props = defineProps({
    permiso: {
        type: Object
    },
});
 
const form = useForm({
    name: props.permiso && props.permiso.name ? props.permiso.name : ''
});
 
const submitForm = (id) => {
    form.put(route('permisos.update',{ id }), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'El permiso se ha actualizado correctamente.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        },
        onError: (errors) => {
            if(errors.response && errors.response.status) {
             
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al actualzar el permiso. Por favor, inténtalo de nuevo.',
                    timer: 1000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                console.error('Error HTTP:', errors.response.status);
            } else {
             
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
    <AppLayout title="Registrar-Inscripción">
        <template #header>
            <h2 class="font-semibold uppercase text-md text-gray-800 leading-tight">
              actualizar permiso
            </h2><br><hr>
        </template>
 
        <div class="flex justify-center">
            <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto container items-center">
                <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="p-6 bg-white border-gray-100 shadow-2xl dark:shadow-gray-500  rounded-lg">
                       
                        <form @submit.prevent="submitForm($props.permiso.id)">
                            <div>
                                <InputLabel for="name" value="nombre" class="text-xs"/>
                                <TextInput v-model="form.name" type="text" id="name" placeholder="escribe un permiso"  class="w-full text-xs"/>
                            </div>
                            <div class="flex justify-end mt-2">
                                <PrimaryButton type="submit">Guardar</PrimaryButton>
                                <Link :href="route('permisos.index')" class="ml-5 uppercase text-sm inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700">
                                    Cancelar
                                </Link>
                            </div>
                        </form>
                    </div>  
                </div>
            </div>
        </div>
    </AppLayout>
</template>