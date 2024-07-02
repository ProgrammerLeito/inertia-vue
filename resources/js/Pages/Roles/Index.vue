<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import Swal from 'sweetalert2';
import { nextTick, ref, defineProps } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    roles: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: ''
});

const deleteRol = (id, name) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar ' + name + '?',
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


const formPage = useForm({});
const onPageClick = (event) => {
    formPage.get(route('roles.index', { page: event }));
}
const previousPage = () => {
    const prevPage = props.roles.current_page - 1;
    formPage.get(route('roles.index', { page: prevPage }));
};

const nextPage = () => {
    const nextPage = props.roles.current_page + 1;
    formPage.get(route('roles.index', { page: nextPage }));
};

const goToPage = (page) => {
    formPage.get(route('roles.index', { page }));
};

const total_pages = props.roles.last_page;
const current_page = props.roles.current_page;
const countPerPage = props.roles.data.length;
const totalCount = props.roles.total;
</script>
<template>
    <AppLayout title="Roles">
        <template #header>
            <h2 class="font-semibold text-md tracking-widest uppercase dark:text-white text-gray-800 leading-tight">
                LISTA DE ROLES
            </h2>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto tracking-widest">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-100 shadow-2xl dark:bg-gray-800  rounded-lg">
                    <div class="flex flex-wrap py-2 gap-2 justify-between">
                        <Link :href="route('roles.create')"
                            class="text-white text-base bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                        <i class="fa fa-plus-circle mx-2"></i>Registrar Rol
                        </Link>
                        <Link :href="route('users.index')"
                            class="text-white text-base bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                        <i class="fas fa-arrow-left mx-2"></i>Usuarios
                        </Link>
                    </div>
                    <div>
                        <div
                            class="relative overflow-x-auto shadow-md md:rounded-lg rounded-xl shadow-gray-400 dark:shadow-gray-500 mt-2">
                            <table
                                class="w-full text-sm font-bold text-left rtl:text-right text-gray-500 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">#</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">nombre</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-md">
                                    <tr v-for="(role, i) in roles.data" :key="role.id"
                                        class="bg-white text-black dark:bg-gray-700 dark:text-white">
                                        <td class="px-6 py-4 text-center whitespace-nowrap"><b>{{ i + 1 }}</b></td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap"> {{ role.name }}</td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <Link :href="route('roles.edit', { role: role.id })"
                                                class="text-white text-xs bg-yellow-500 hover:bg-yellow-600 py-1 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <i class="fas fa-arrow-right fa-beat mx-1"></i>permisos
                                            </Link>
                                            <ButtonDelete class="" @click="$event => deleteRol(role.id, role.name)">
                                                <i class="hover:bg-red-500 px-1 py-1 hover:text-white rounded-md bi bi-trash3 text-red-500"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                        <div class="hidden sm:block">
                            <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                                <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                                <p class="text-gray-700 dark:text-white font-semibold">Total de Roles: {{ totalCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:text-end text-center">
                            <nav aria-label="Page navigation example mt-4">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <li>
                                        <button @click="previousPage" :disabled="!roles.prev_page_url"
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-700 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            Prev
                                        </button>
                                    </li>
                                    <li v-for="page in total_pages" :key="page">
                                        <button @click="goToPage(page)"
                                            :class="{ 'text-blue-600 border-blue-300 dark:text-gray-900 bg-blue-50 hover:bg-blue-100 hover:text-blue-700': page === current_page, 'text-gray-900 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': page !== current_page }"
                                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            {{ page }}
                                        </button>
                                    </li>
                                    <li>
                                        <button @click="nextPage" :disabled="!roles.next_page_url"
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-700 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            Next
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
