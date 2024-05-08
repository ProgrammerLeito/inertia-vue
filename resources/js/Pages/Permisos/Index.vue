<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from 'sweetalert2';
import { Link, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    permisos: {
        type: Object
    }
});
 
const formPage = useForm({});
 
const onPageClick = (event) => {
    formPage.get(route('permisos.index', { page: event }));
};

const form = useForm({
    name:''
});

const deletePermiso = (id, name) => {
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
            form.delete(route('permisos.destroy', id),{
                onSuccess: () => {ok('usuario Eiminada')}
            });
        }
    })
}
</script>
<template>
    <AppLayout title="Permisos Usuarios">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Permisos</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('permisos.create')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Registrar Permiso
                        </Link>
                        <Link :href="route('users.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Regresar
                        </Link>
                    </div>
                    <div class="mt-4">
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-white uppercase bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center">N°</th>
                                        <th scope="col" class="px-6 py-3 text-left">Nombre</th>
                                        <th scope="col" class="text-center px-6 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white" v-for="(permiso, i) in permisos.data" :key="permiso.id">
                                        <td class="px-6 py-4 font-semibold text-center">{{ i + 1 }}</td>
                                        <td class="px-6 py-4 font-semibold text-left">{{ permiso.name }}</td>
                                        <td class="p-3 text-center">
                                            <Link class="py-2 px-4 text-green-500" :href="route('permisos.edit', { permiso: permiso.id })">
                                                <i class="bi bi-pencil-square"></i>
                                            </Link>
                                            <ButtonDelete @click="$event => deletePermiso(permiso.id,permiso.name)">
                                                <i class="bi bi-trash3 text-red-500"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>