<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import ModalResponsive from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Swal from 'sweetalert2';
import {useForm} from '@inertiajs/vue3';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';
import {nextTick, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
 
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
    <AppLayout title="Usuarios" >
        <template #header>
            <h2 class="font-semibold text-md text-gray-800 leading-tight">
               HISTORIAL DE USUARIOS ELIMINADOS
            </h2><br><hr>
        </template>
 
        <div class="flex justify-center  ">
            <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto container items-center">
                <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="p-6 bg-white border-gray-100 shadow-2xl rounded-lg">
                        <div class="flex flex-wrap gap-2 justify-between">
                            <Link :href="route('users.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                regresar
                            </Link>
                         
                        </div>
                        <div class="mt-4 overflow-auto">
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-400 dark:text-gray-900">
                                    <thead class="text-xs text-white text-center uppercase bg-green-600 dark:bg-green-600">
                                        <tr>
                                        <th scope="col" class="px-6 py-2">#</th>
                                        <th scope="col" class="px-6 py-2">nombre</th>
                                        <th scope="col" class="px-6 py-2">email</th>
                                        <th scope="col" class="px-6 py-2">sexo</th>
                                        <th scope="col" class="px-6 py-2">celular</th>
                                        <th scope="col" class="px-6 py-2">rol</th>
                                        <th scope="col" class="px-6 py-2">acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-xs">
                                        <tr v-for="(user, i) in users.data" :key="user.id">
                                        <td class="border  border-gray-400 px-2 py-2">{{ i + 1 }}</td>
                                        <td class="border border-gray-400 px-2 py-2"> {{ user.name }}</td>
                                        <td class="border border-gray-400 px-2 py-2"> {{ user.email }}</td>
                                        <td class="border border-gray-400 px-2 py-2"> {{ user.sexo }}</td>
                                        <td class="border border-gray-400 px-2 py-2"> {{ user.celular }}</td>
                                        <td class="border border-gray-400 px-2 py-2">
                                            <div v-if="user.roles && user.roles.length > 0">
                                                <span v-for="(role, index) in user.roles" :key="index" class="badge badge-success">
                                                    {{ role.name }}
                                                </span>
                                            </div>
                                            <div v-else>
                                                Sin rol
                                            </div>
                                        </td>
 
                                        <td class="border border-gray-400 px-2 py-2">
                                            <Link :href="route('users.restore', { id: user.id })" class="text-xs hover:bg-yellow-500 bg-yellow-300 rounded-full py-1 mx-2  hover:text-green-500">
                                                <i class="fa-solid fa-edit fa-sm">restaurar </i>
                                            </Link>
                                            <Link @click="deleteUser(user.id)" >
                                                <i class="fa-solid fa-trash mr-1 fa-sm"></i>
                                            </Link>                                        
                                   
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="bg-white grid v-screen place-items-center mt-4">
                                    <vueTailwindPaginationUmd
                                        :current="users.currentPage" :total="users.total"
                                        :per-page="users.perPage"
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