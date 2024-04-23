<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

export default {
    props: {
        user: Object,
        roles: Array,
        userRoles: Array
    },
    components: {
        AppLayout,
        Link,
        PrimaryButton,
        Head
    },
    setup(props) {
        const form = useForm({
            name: props.user.name,
            apellidopat: props.user.apellidopat,
            apellidomat: props.user.apellidomat,
            email: props.user.email,
            password: props.user.password,
            sexo: props.user.sexo,
            celular: props.user.celular,
            password_confirmation: props.user.password_confirmation,
            roles: props.userRoles
        });

        const isRoleSelected = computed(() => {
            return form.roles.length > 0;
        });
 
        const submitForm = () => {
            form.put(route('users.update', { id: props.user.id }));
        };
 
        return {
            form,
            submitForm,
            isRoleSelected
        };
    }
};
</script>
<template>
    <AppLayout title="Editar Usuario">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Editar Usuario</h1>
        </template>
       
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                    
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white">Nombre</label>
                                    <input v-model="form.name" type="text" name="name" id="name" class="h-[37.6px] text-sm mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <InputError :errors="form.errors.name" class="mt-2" />
                                </div>
                                <div>
                                    <label for="apellidopat" class="block text-sm font-medium text-gray-700 dark:text-white">Apellido Paterno</label>
                                    <input v-model="form.apellidopat" type="text" name="apellidopat" id="apellidopat" class="h-[37.6px] text-sm mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <InputError :errors="form.errors.apellidopat" class="mt-2" />
                                </div>
                                <div>
                                    <label for="apellidomat" class="block text-sm font-medium text-gray-700 dark:text-white">Apellido Materno</label>
                                    <input v-model="form.apellidomat" type="text" name="apellidomat" id="apellidomat" class="h-[37.6px] text-sm mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <InputError :errors="form.errors.apellidomat" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-white">Correo</label>
                                    <input v-model="form.email" type="email" name="email" id="email" class="h-[37.6px] text-sm mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <InputError :errors="form.errors.email" class="mt-2" />
                                </div>
                                <div>
                                    <label for="sexo" class="block text-sm font-medium text-gray-700 dark:text-white">Sexo</label>
                                    <select id="sexo" v-model="form.sexo" required
                                            class="h-[37.6px] mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona el sexo</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="celular" class="block text-sm font-medium text-gray-700 dark:text-white">Celular</label>
                                    <input v-model="form.celular" type="text" name="celular" id="celular" class="text-sm h-[37.6px] mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <InputError :errors="form.errors.celular" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-white">Contraseña</label>
                                    <input v-model="form.password" type="password" name="password" id="password" class="text-sm h-[37.6px] mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <InputError :errors="form.errors.password" class="mt-2" />
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-white">confirmar Contraseña</label>
                                    <input v-model="form.password_confirmation" type="password" name="password_confirmation" id="password_confirmation" class="text-sm h-[37.6px] mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <InputError :errors="form.errors.password_confirmation" class="mt-2" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-white">Roles</label>
                                <div class="w-full text-md p-2 gap-4">
                                    <div v-for="(roleOption, index) in roles" :key="index" class="flex items-center mt-2 text-sm">
                                        <input type="checkbox" 
                                            :id="`role-${index}`" 
                                            :value="roleOption" 
                                            v-model="form.roles"
                                            :disabled="isRoleSelected && form.roles.indexOf(roleOption) === -1"
                                            class="w-4 h-4 text-blue-600 bg-blue-100 border-blue-900 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label class="ml-3 dark:text-white" :for="`role-${index}`">{{ roleOption }}</label>
                                    </div>

                                </div>
                                <InputError :errors="form.errors.roles" class="mt-2" />
                            </div>
    
                            <div class="flex justify-end mt-6">
                                <PrimaryButton >Actualizar</PrimaryButton>
                                <Link class="ml-5 uppercase text-sm inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700" :href="route('users.index')">regresar</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>