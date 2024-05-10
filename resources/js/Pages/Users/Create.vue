<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const selectedRole = ref(null);

defineProps({
    roles: {
        type: Array,
        required: true
    }
});
 
const form = useForm({
    // Inicializa los campos del formulario
    name: '',
    apellidopat: '',
    apellidomat: '',
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


const handleRoleSelection = (role) => {
    if (selectedRole.value === role) {
        selectedRole.value = null;  // Des-seleccionar si es el mismo
    } else {
        selectedRole.value = role;  // Seleccionar nuevo rol
    }
    form.roles = selectedRole.value ? [selectedRole.value] : [];
};

</script>
<template>
    <AppLayout title="Registrar Usuario ">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Crear Usuarios</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                       
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel for="name" value="nombre" class="block text-md font-medium text-gray-700"/>
                                    <TextInput v-model="form.name" type="text" id="name" placeholder="Escribe un usuario" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="form.errors.name" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="apellidopat" value="Apellido Paterno" class="block text-md font-medium text-gray-700"/>
                                    <TextInput v-model="form.apellidopat" type="text" id="apellidopat" placeholder="Escribe Apellido Paterno" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="form.errors.apellidopat" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="apellidomat" value="Apellido Materno" class="block text-md font-medium text-gray-700"/>
                                    <TextInput v-model="form.apellidomat" type="text" id="apellidomat" placeholder="Escribe Apellido Materno" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="form.errors.apellidomat" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel for="email" value="Email" class="block text-md font-medium text-gray-700"/>
                                    <TextInput v-model="form.email" type="email" id="email" placeholder="Escribe un correo electrónico" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="form.errors.email" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="sexo" class="block text-sm font-medium text-gray-700">Sexo</InputLabel>
                                    <select id="sexo" v-model="form.sexo" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona el sexo</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="celular" value="Celular" class="block text-md font-medium text-gray-700"/>
                                    <TextInput v-model="form.celular" type="text" id="celular" placeholder="Escribe un telefono" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="form.errors.celular" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel for="password" value="Contraseña" class="block text-md font-medium text-gray-700"/>
                                    <TextInput v-model="form.password" type="password" id="password" placeholder="Escribe una contraseña" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="form.errors.password" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="password_confirmation" value="Confirmar Contraseña" class="block text-md font-medium text-gray-700"/>
                                    <TextInput v-model="form.password_confirmation" type="password" id="password_confirmation" placeholder="Confirma tu contraseña" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError v-if="form.errors.password_confirmation" :message="form.errors.password_confirmation" class="mt-2"/>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="roles" value="Asignar Roles" class="block text-md font-medium text-gray-700"/>
                                <div class="w-full text-md p-2 gap-4">
                                    <div v-for="(roleOption, index) in roles" :key="index" class="flex items-center mt-2">
                                        <input 
                                            type="checkbox" 
                                            :id="`role-${index}`" 
                                            :value="roleOption" 
                                            :checked="selectedRole === roleOption" 
                                            @change="handleRoleSelection(roleOption)" 
                                            :disabled="selectedRole !== null && selectedRole !== roleOption"
                                            class="w-4 h-4 text-blue-600 bg-blue-100 border-blue-900 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label class="ml-3 dark:text-white" :for="`role-${index}`">{{ roleOption }}</label>
                                    </div>
                                </div>
                                <InputError :message="form.errors.roles" class="mt-2"></InputError>
                            </div>
                            <div class="flex justify-end mt-6">
                                <PrimaryButton :disabled="form.processing">Registrar</PrimaryButton>
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