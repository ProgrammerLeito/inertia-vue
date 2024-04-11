<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';
defineProps({
    roles: {
        type: Array,
        required: true
    }
});
 
const form = useForm({
    // Inicializa los campos del formulario
    name: '',
    email: '',
    password: '',
    sexo: '',
    celular: '',
    password_confirmation: '',
    roles: [], // Inicializa los roles como un array vacío
 
    // Define las reglas de validación del formulario
    rules: {
        password_confirmation: ['same:password']
    }
});
 
 
const submitForm = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'el usuario se ha registrado correctamente.',
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
    <AppLayout title="Registrar-rol">
        <template #header>
            <h2 class="font-semibold uppercase text-md text-gray-800 leading-tight">
              Registra usuario
            </h2><br><hr>
        </template>
 
        <div class="flex justify-center">
           <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto container items-center">
                <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="p-6 bg-white border-gray-100 shadow-2xl dark:shadow-gray-500  rounded-lg">
                       
                        <form @submit.prevent="submitForm">
                            <div>
                                <InputLabel for="name" value="nombre" class="text-xs"/>
                                <TextInput v-model="form.name" type="text" id="name" placeholder="escribe un usuario"  class="w-full text-xs"/>
                            </div>
                            <div>
                                <InputLabel for="email" value="Email" class="text-xs"/>
                                <TextInput v-model="form.email" type="email" id="email" placeholder="Escribe un correo electrónico" class="w-full text-xs"/>
                                <InputError :message="form.errors.email" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="sexo" value="Sexo" class="text-xs"/>
                                <TextInput v-model="form.sexo" type="sexo" id="sexo" placeholder="Escribe el sexo" class="w-full text-xs"/>
                                <InputError :message="form.errors.sexo" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="celular" value="Celular" class="text-xs"/>
                                <TextInput v-model="form.celular" type="celular" id="celular" placeholder="Escribe un telefono" class="w-full text-xs"/>
                                <InputError :message="form.errors.celular" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="password" value="Contraseña" class="text-xs"/>
                                <TextInput v-model="form.password" type="password" id="password" placeholder="Escribe una contraseña" class="w-full text-xs"/>
                                <InputError :message="form.errors.password" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="password_confirmation" value="Confirmar Contraseña" class="text-xs"/>
                                <TextInput v-model="form.password_confirmation" type="password" id="password_confirmation" placeholder="Confirma tu contraseña" class="w-full text-xs"/>
                                  <!-- Manejo de errores de confirmación de contraseña -->
                                <InputError v-if="form.errors.password_confirmation" :message="form.errors.password_confirmation" class="mt-2"/>
                            </div>
                            <div>
                                <InputLabel for="roles" value="Roles" class="text-xs"/>
                                <select v-model="form.roles" id="roles" multiple class="w-full text-xs" required>
                                    <option v-for="(role, index) in roles" :key="index" :value="role">{{ role }}</option>
                                </select>
                                <InputError :message="form.errors.roles" class="mt-2"></InputError>
                            </div>
 
                            <div class="flex justify-end mt-2">
                                <PrimaryButton :disabled="form.processing">Guardar</PrimaryButton>
                                <Link :href="route('users.index')" class="ml-5 uppercase text-sm inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700">
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