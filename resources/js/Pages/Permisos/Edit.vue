<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2';
import { useForm, Link } from '@inertiajs/vue3';
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
    <AppLayout title="Editar Permisos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Editar Permisos</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submitForm($props.permiso.id)">
                        <div>
                            <InputLabel for="name" value="Nombre" class="text-md ml-1"/>
                            <TextInput v-model="form.name" type="text" id="name" placeholder="escribe un permiso" class="w-full mt-2 text-sm"/>
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