<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';
 
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
            email: props.user.email,
            password: props.user.password,
            sexo: props.user.sexo,
            celular: props.user.celular,
            password_confirmation: props.user.password_confirmation,
            roles: props.userRoles
        });
 
        const submitForm = () => {
            form.put(route('users.update', { id: props.user.id }));
        };
 
        return {
            form,
            submitForm
        };
    }
};
</script>
<template>
    <AppLayout>
        <template #header>
            <h2 class="text-md text-gray-800 uppercase font-bold leading-tight">Editar Usuario</h2><br><hr>
        </template>
       
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto container items-center">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-100 shadow-2xl dark:shadow-gray-800 rounded-lg">
                    <form @submit.prevent="submitForm">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input v-model="form.name" type="text" name="name" id="name" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <InputError :errors="form.errors.name" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo</label>
                            <input v-model="form.email" type="email" name="email" id="email" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <InputError :errors="form.errors.email" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <label for="sexo" class="block text-sm font-medium text-gray-700">Sexo</label>
                            <input v-model="form.sexo" type="sexo" name="sexo" id="sexo" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <InputError :errors="form.errors.sexo" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <label for="celular" class="block text-sm font-medium text-gray-700">Celular</label>
                            <input v-model="form.celular" type="celular" name="celular" id="celular" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <InputError :errors="form.errors.celular" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input v-model="form.password" type="password" name="password" id="password" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <InputError :errors="form.errors.password" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">confirmar Contraseña</label>
                            <input v-model="form.password_confirmation" type="password" name="password_confirmation" id="password_confirmation" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <InputError :errors="form.errors.password_confirmation" class="mt-2" />
                        </div>
 
                       
                       
                        <!-- Aquí puedes agregar más campos del usuario como email, password, etc. -->
 
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Roles</label>
                            <div class="mt-2">
                                <div v-for="role in roles" :key="role">
                                    <input v-model="form.roles" :value="role" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-gray-600">{{ role }}</span>
                                </div>
                            </div>
                            <InputError :errors="form.errors.roles" class="mt-2" />
                        </div>
 
                        <div class="mt-4">
                            <PrimaryButton >Actualizar</PrimaryButton>
                            <Link class="ml-5 uppercase text-sm inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700" :href="route('users.index')">regresar</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>