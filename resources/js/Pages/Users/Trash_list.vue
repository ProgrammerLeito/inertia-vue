<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
 
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
 
 
const deleteUser = async (userId) => {
    const confirmed = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Estás a punto de eliminar permanentemente este usuario. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
 
    if (confirmed.isConfirmed) {
        try {
            // Realiza una solicitud HTTP DELETE para eliminar permanentemente el usuario
            await form.delete(`/delete_users_deletePermanently/${userId}`);
       
            Swal.fire('Usuario eliminado', 'El usuario ha sido eliminado permanentemente.', 'success');
            location.reload();
        } catch (error) {
            console.error('Error al eliminar el usuario:', error);
            Swal.fire('Error', 'Hubo un error al eliminar el usuario.', 'error');
        }
    }
};
</script>
<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Usuarios Eliminados</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
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
                                        <th scope="col" class="px-6 py-3 text-center">Email</th>
                                        <th scope="col" class="px-6 py-3 text-center">Sexo</th>
                                        <th scope="col" class="px-6 py-3 text-center">Celular</th>
                                        <th scope="col" class="px-6 py-3 text-center">Rol</th>
                                        <th scope="col" class="text-center px-6 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white" v-for="(user, i) in users.data" :key="user.id">
                                        <td class="px-6 py-4 font-semibold text-center">{{ i + 1 }}</td>
                                        <td class="px-6 py-4 font-semibold text-left">{{ user.name }}</td>
                                        <td class="px-6 py-4 font-semibold text-center">{{ user.email }}</td>
                                        <td class="px-6 py-4 font-semibold text-center">{{ user.sexo }}</td>
                                        <td class="px-6 py-4 font-semibold text-center">{{ user.celular }}</td>
                                        <td class="px-6 py-4 font-semibold text-center">
                                            <div v-if="user.roles && user.roles.length > 0">
                                                <span v-for="(role, index) in user.roles" :key="index" class="badge badge-success">
                                                    {{ role.name }}
                                                </span>
                                            </div>
                                            <div v-else>
                                                Sin rol
                                            </div>
                                        </td>
                                        <td class="p-3 text-center">
                                            <Link class="py-0.5 px-2.5 text-xs text-black font-semibold bg-yellow-300 rounded-lg border-solid border-2 hover:bg-yellow-400" :href="route('users.restore', { id: user.id })">
                                                <i class='bx bxs-share'><label class="ml-2">Restaurar</label></i>
                                            </Link>
                                            <Link @click="deleteUser(user.id)" class="ml-4">
                                                <i class="bi bi-trash3 text-red-500"></i>
                                            </Link> 
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