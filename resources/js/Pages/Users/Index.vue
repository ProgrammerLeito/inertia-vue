<script>
export default {
    name: 'UsuariosIndex',
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonEdit from '@/Components/ButtonEdit.vue';
import ButtonRegister from '@/Components/ButtonRegister.vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import Swal from 'sweetalert2';
import {useForm} from '@inertiajs/vue3';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';
import {nextTick, ref, computed } from 'vue';
 
const nameInput=ref(null);
const modal = ref(false);
const title= ref('');
const operation= ref(1);
const id = ref('');
 
const props = defineProps({
    users: {
        type: Object
    }
});
 
 
const form = useForm({
    name:'',email:'',password:'',sexo:'',celular:'',tipo_usu: ''
});
 
 
const formPage = useForm({});
const onPageClick = (event)=>{
    formPage.get(route('users.index',{page:event}));
}
 
const openModal = (op,name,email,password,sexo,celular,tipo_usu, users) => {
    modal.value = true;
    nextTick(() => nameInput.value.focus());
    operation.value = op;
    id.value = users;
    if (op === 1) {
        title.value = 'Registrar usuario';
    } else {
        title.value = 'Actualizar usuario';
        form.name=name;
        form.email=email;
        form.password=password;
        form.sexo=sexo;
        form.celular=celular;
        form.tipo_usu=tipo_usu;
    }
};
 
const closeModal = () => {
    modal.value = false;
    form.reset();
};
const save = () => {
    if (operation.value == 1) {
        form.post(route('users.store'), {
            onSuccess: () => { ok('Usuario registrado') }
        });
    } else {
        form.put(route('users.update', id.value), {
            onSuccess: () => { ok('Usuario actualizado') }
        });
    }
}
 
const ok = (msj) => {
    form.reset();
    closeModal();
    Swal.fire({ title: msj, icon: 'success' });
};
 
const deleteUser = (id,name) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });
    alerta.fire({
        title: '¿Estas seguro de eliminar a ' + name + '?',
        icon: 'question',
        showCancelButton:true,
        confirmButtonText:'<i class="fa-solid fa-check"></i> Si eliminar ',
        cancelButtonText: '<i class"fa-solid fa-ban"></i> Cancelar',
    }).then((result)=>{
        if(result.isConfirmed){
            form.delete(route('users.destroy',id),{
                onSuccess: () => {ok('Usuario Eliminado')}
            });
        }
    });
}

// Define una variable reactiva para almacenar el término de búsqueda
const searchQuery = ref('');

// Método para filtrar la lista de usuarios según el término de búsqueda
const filteredUsers = computed(() => {
    // Si no hay término de búsqueda, devuelve la lista completa de usuarios
    if (!searchQuery.value.trim()) {
        return props.users.data;
    }
    
    // Filtra la lista de usuarios basándose en el término de búsqueda
    return props.users.data.filter(user => {
        const searchTerm = searchQuery.value.trim().toLowerCase();
        // Verifica si el término de búsqueda coincide con alguna de las propiedades del usuario
        return (
            user.name.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm) ||
            user.celular.toLowerCase().includes(searchTerm) ||
            user.celular.toLowerCase().includes(searchTerm) ||
            user.sexo.toLowerCase().includes(searchTerm) ||
            user.tipo_usu.toLowerCase().includes(searchTerm)
        );
    });
});

</script>
<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Listar Usuarios</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <ButtonRegister @click="$event => openModal(1)">
                            Registrar Usuario
                        </ButtonRegister>
                    </div>
                    <div class="mt-4">
                        <div class="pb-4 bg-white dark:bg-gray-800">
                            <label for="table-search" class="sr-only">Buscar</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar Usuario">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-white uppercase bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">#</th>
                                        <th scope="col" class="px-6 py-3">Nombre</th>
                                        <th scope="col" class="px-6 py-3">Email</th>
                                        <th scope="col" class="px-6 py-3">Celular</th>
                                        <th scope="col" class="px-6 py-3">sexo</th>
                                        <th scope="col" class="px-6 py-3">Tipo de Usuario</th>
                                        <th scope="col" class="text-center px-6 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white" v-for="(user, i) in filteredUsers" :key="user.id">
                                        <td class="px-6 py-4 font-semibold">{{ user.id}}</td>
                                        <td class="px-6 py-4 font-semibold">{{ user.name }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ user.email }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ user.celular }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ user.sexo }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ user.tipo_usu }}</td>
                                        <td class="p-3 text-center">
                                            <ButtonEdit @click="$event => openModal(2,user.name,user.email,user.password,user.sexo,user.celular,user.tipo_usu,user.id)">
                                                <i class="bi bi-pencil-square text-green-500"></i>
                                            </ButtonEdit>
                                            <ButtonDelete @click="$event => deleteUser(user.id,user.name)" class="ml-3">
                                                <i class="bi bi-trash3 text-red-500"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredUsers.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Modal :show="modal" @close="closeModal">
            <div class="p-4 uppercase mt-2">
                <h2 class="text-lg font-bold text-center text-gray-600 mb-2 dark:text-white">{{ title }}</h2>
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-1/2 p-3">
                        <InputLabel for="name" value="Nombre:" class="mb-2"></InputLabel>
                        <TextInput id="name" ref="nameInput" v-model="form.name" type="text" class="w-full"
                            placeholder="Escribe un nombre "></TextInput>
                        <InputError :message="form.errors.name" class="mt-2"></InputError>
                    </div>
                    <div class="w-full sm:w-1/2 p-3">
                        <InputLabel for="email" value="email:" class="mb-2"></InputLabel>
                        <TextInput id="email" ref="nameInput" v-model="form.email" type="email" class="w-full"
                            placeholder="Escribe un email "></TextInput>
                        <InputError :message="form.errors.email" class="mt-2"></InputError>
                    </div>
                </div>

                <div class="flex flex-wrap">
                    <div class="w-full sm:w-1/2 p-3">
                        <label for="sexo" class="block text-sm font-medium text-black dark:text-white">Sexo:</label>
                        <select id="sexo" v-model="form.sexo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="" disabled>Seleccionar sexo</option>
                            <option class="text-gray-900">Masculino</option>
                            <option class="text-gray-900">Femenino</option>
                        </select>
                        <InputError :message="form.errors.sexo" class="mt-2"></InputError>
                    </div>
                    <div class="w-full sm:w-1/2 p-3">
                        <InputLabel for="celular" value="celular:" class="mb-2"></InputLabel>
                        <TextInput id="celular" ref="nameInput" v-model="form.celular" type="text" class="w-full"
                            placeholder="Escribe numero de celular "></TextInput>
                        <InputError :message="form.errors.celular" class="mt-2"></InputError>
                    </div>
                </div>
                
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-1/2 p-3">
                        <InputLabel for="tipo_usu" value="Tipo de Usuario:" class="mb-2"></InputLabel>
                        <TextInput id="tipo_usu" ref="nameInput" v-model="form.tipo_usu" type="text" class="w-full"
                            placeholder="Escribe numero de celular "></TextInput>
                        <InputError :message="form.errors.tipo_usu" class="mt-2"></InputError>
                    </div>
                    <div class="w-full sm:w-1/2 p-3">
                        <InputLabel for="password" value="password:" class="mb-2"></InputLabel>
                        <TextInput id="password" ref="nameInput" v-model="form.password" type="password" class="w-full"
                            placeholder="Escribe un contraseña "></TextInput>
                        <InputError :message="form.errors.password" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-3 flex justify-center">
                    <PrimaryButton :disabled="form.processing" @click="save">
                        <i class="fa-solid fa-save mx-2"></i>{{ operation === 1 ? 'Registro' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form.processing"
                    @click="closeModal">
                        CANCELAR
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
 