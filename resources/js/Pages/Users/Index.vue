<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import {useForm} from '@inertiajs/vue3';
import ButtonDelete from '@/Components/ButtonDelete.vue';
 
 
const props = defineProps({
    users: {
        type: Object
    }
});
 
 
const form = useForm({
    name:'',email:'',password:'',sexo:'',celular:''
   
});
 
 
const formPage = useForm({});
const onPageClick = (event)=>{
    formPage.get(route('users.index',{page:event}));
}
const deleteUser = (id, name) => {
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
            form.delete(route('users.destroy', id),{
                onSuccess: () => {ok('usuario Eiminada')}
            });
        }
    })
}
 
 
</script>
<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Usuarios</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link v-if="$page.props.user.permissions.includes('Acciones Usuarios')" :href="route('users.create')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Crear Usuarios
                        </Link>
                        <Link v-if="$page.props.user.permissions.includes('Acciones Usuarios')" :href="route('users.trashed')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Usuarios Eliminados
                        </Link>
                        <Link  :href="route('roles.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Crear Roles
                        </Link>
                        <Link v-if="$page.props.user.permissions.includes('Acciones Usuarios')" :href="route('permisos.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Crear Permisos
                        </Link>
                    </div>
                    <div class="mt-4">
                        <div class="relative overflow-x-auto shadow-md md:rounded-lg rounded-xl shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-white uppercase bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center">N°</th>
                                        <th scope="col" class="px-6 py-3 text-left">Nombre</th>
                                        <th scope="col" class="px-6 py-3 text-center">Email</th>
                                        <th scope="col" class="px-6 py-3 text-center">Sexo</th>
                                        <th scope="col" class="px-6 py-3 text-center">Celular</th>
                                        <th scope="col" class="px-6 py-3 text-center">Rol</th>
                                        <th scope="col" class="text-center px-6 py-3" v-if="$page.props.user.permissions.includes('Acciones Usuarios')">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white" v-for="(user, i) in users.data" :key="user.id">
                                        <td class="px-6 py-4 font-semibold text-center">{{ i + 1 }}</td>
                                        <td class="px-6 py-4 font-semibold text-left whitespace-nowrap">{{ user.name }}</td>
                                        <td class="px-6 py-4 font-semibold text-center whitespace-nowrap">{{ user.email }}</td>
                                        <td class="px-6 py-4 font-semibold text-center whitespace-nowrap">{{ user.sexo }}</td>
                                        <td class="px-6 py-4 font-semibold text-center whitespace-nowrap">{{ user.celular }}</td>
                                        <td class="px-6 py-4 font-semibold text-center whitespace-nowrap">
                                            <div v-if="user.roles && user.roles.length > 0">
                                                <span v-for="(role, index) in user.roles" :key="index" class="badge badge-success">
                                                    {{ role.name }}
                                                </span>
                                            </div>
                                            <div v-else>
                                                Sin rol
                                            </div>
                                        </td>
                                        <td class="p-3 text-center whitespace-nowrap" v-if="$page.props.user.permissions.includes('Acciones Usuarios')">
                                            <Link :href="route('users.edit', { user: user.id })" class="inline-flex items-center justify-center bg-amber-400 hover:bg-amber-500 px-1.5 py-0.5 rounded-md mr-2">
                                                <i class='bx bxs-edit text-base text-white'></i>
                                            </Link>
                                            <button @click="$event => deleteUser(user.id,user.name)" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 px-1.5 py-0.5 rounded-md">
                                                <i class='bx bxs-trash text-base text-white'></i>
                                            </button>
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