<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Swal from 'sweetalert2';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';
import {nextTick , ref , defineProps } from 'vue';
import { Head, Link , useForm} from '@inertiajs/vue3';
 
defineProps({
    roles: {
        type: Object,
        required: true
    }
});
 
const formPage = useForm({});
 
const onPageClick = (event) => {
    formPage.get(route('roles.index', { page: event }));
};
const form = useForm({
    name:''
});
 
const deleteRol = (id, name) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });
 
    alerta.fire({
        title: '¿Estás seguro de eliminar ' +name+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('roles.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Producto eliminado exitosamente',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
}
</script>
<template>
    <AppLayout title="Roles" >
        <template #header>
            <h2 class="font-semibold text-md text-gray-800 leading-tight">
               LISTA DE ROL
            </h2><br><hr>
        </template>
 
        <div class="flex justify-center  ">
            <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto container items-center">
                <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="p-6 bg-white border-gray-100 shadow-2xl rounded-lg">
                        <div class="flex flex-wrap gap-2 justify-between">
                            <Link :href="route('roles.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fa fa-plus-circle"></i>registra rol
                            </Link>
                            <Link :href="route('users.index')" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                               
                                <i class="fas fa-arrow-left mx-2"></i>usuarios
                            </Link>
                        </div>
                        <div class="mt-4 overflow-auto">
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left rtl:text-right dark:text-gray-900">
                                    <thead class="text-xs text-white text-center uppercase bg-green-600 dark:bg-green-600">
                                        <tr>
                                        <th scope="col" class="px-6 py-2">#</th>
                                        <th scope="col" class="px-6 py-2">nombre</th>
                                        <th scope="col" class="px-6 py-2">acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-xs">
                                        <tr v-for="(role, i) in roles" :key="role.id">
                                            <td class="border  border-gray-400  px-2 py-2">{{ i + 1 }}</td>
                                            <td class="border border-gray-400 px-2 py-2"> {{ role.name }}</td>
                                            <td class="border border-gray-400 px-2 py-2">
                                                <Link :href="route('roles.edit', { role: role.id })" class="text-xs hover:bg-yellow-500 bg-yellow-300 rounded-full py-1 mx-2  hover:text-green-500">
                                                    <i class="fa-solid fa-edit fa-sm">asigna permisos </i>
                                                </Link>
 
                                                <ButtonDelete @click="$event => deleteRol(role.id,role.name)" class="ml-1">
                                                    <i class="fa-solid fa-trash mr-1 fa-sm"></i>
                                                </ButtonDelete>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="bg-white grid v-screen text-xs place-items-center mt-4">
                                    <vueTailwindPaginationUmd
                                        :current="roles.currentPage" :total="roles.total"
                                        :per-page="roles.perPage"
                                        @page-changed="$event => onPageClick($event)"
                                    ></vueTailwindPaginationUmd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>