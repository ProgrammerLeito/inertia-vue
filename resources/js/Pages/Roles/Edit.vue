<template>
    <AppLayout>
        <template #header>
            <h2 class="text-md text-gray-800 uppercase font-bold leading-tight">Asignar Permisos</h2>
            <hr>
        </template>
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto container items-center">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-100 shadow-2xl dark:shadow-gray-800 rounded-lg">
                    <h2><b>Role: {{ role.name }}</b></h2>
                    <form @submit.prevent="submitForm">
                        <div v-for="(permiso, id) in permisos" :key="id">
                            <input type="checkbox" :id="'permiso_' + id" :value="id" v-model="form.permisos">
                            <label :for="'permiso_' + id">{{ permiso }}</label>
                        </div><br>
                        <PrimaryButton >Asignar permisos </PrimaryButton>
                        <Link class="ml-5 uppercase text-sm inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700" :href="route('roles.index')">Regresar</Link>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
 
<script>
 import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref , onMounted } from 'vue';
import Swal from 'sweetalert2';
 
 
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