<template>
    <AppLayout title="Asignar Permisos">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Asignar Permisos</h1>
        </template>
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <InputLabel for="roles" value="Asignar Roles" class="block text-md font-medium text-gray-700"/>
                    <h2 class="dark:text-white"><b>Rol para editar  : {{ role.name }}</b></h2>
                    <form @submit.prevent="submitForm">
                        <div class="w-full text-md p-2 gap-4 grid grid-cols-1 gap-y-3 sm:grid-cols-4 sm:gap-x-6 mb-3">
                            <div v-for="(permiso, id) in permisos" :key="id" class="flex items-center mt-2">
                                <input type="checkbox" :id="'permiso_' + id" :value="id" v-model="form.permisos" class="w-4 h-4 text-blue-600 bg-blue-100 border-blue-900 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label class="ml-4 dark:text-white" :for="'permiso_' + id">{{ permiso }}</label>
                            </div>
                        </div>
                        <InputError :message="form.errors.roles" class="mt-2"></InputError>
                        <div class="flex justify-end mt-6">
                            <PrimaryButton>Asignar permisos</PrimaryButton>
                            <Link class="ml-5 uppercase text-sm inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700" :href="route('roles.index')">Regresar</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
 
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
 
 
export default {
    props: {
        role: Object,
        permisos: Object,
        rolePermisos: Array,
    },
    components: {
        AppLayout,
        PrimaryButton,
        Link
    },
    setup(props) {
        const form = useForm({
            permisos: props.rolePermisos
        });
 
     
        const submitForm = async () => {
            await form.put(route('roles.update', { id: props.role.id }), {
                permisos: form.permisos,
            });
            // Mostrar mensaje de éxito con SweetAlert
            Swal.fire({
                icon: 'success',
                title: 'Permisos asignados',
                text: 'Los permisos se han asignado correctamente.',
            });
        };
 
        return {
            form,
            submitForm,
        };
    },
};
</script>