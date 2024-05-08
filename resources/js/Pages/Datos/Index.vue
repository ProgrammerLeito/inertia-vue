<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonEdit from '@/Components/ButtonEdit.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import Swal from 'sweetalert2';
import { nextTick, ref } from 'vue';
 
const nameInput = ref(null);
const modal = ref(false);
const title = ref('');
const operation = ref(1);
const id = ref('');
 
defineProps({
    datos: {
        type : Object,
        required: true
    },
    clientes:{
        type: Object
    }
 
});
const form = useForm({
  nombre: '',
  cargo: '',
  telefono: '',
  correo: '',
  tarjeta: '',
  cliente_id:'',
 
});
 
const openModal = (op,nombre,cargo,telefono,correo,tarjeta,cliente,dato)=>{
    modal.value = true;
    nextTick( () => nameInput.value.focus());
    operation.value = op;
    id.value = dato;
    if(op ===1){
        title.value = 'Registrar datos ';
    }
    else{
        title.value = 'Actualizar datos';
        form.nombre=nombre;
        form.cargo=cargo;
        form.telefono=telefono;
        form.correo=correo;
        form.tarjeta=tarjeta;
        form.cliente_id=cliente;
    }
}
 
const closeModal = () =>{
    modal.value = false;
    form.reset();
}
 
const save = () => {
    if (operation.value == 1) {
        form.post(route('datos.store'), {
            onSuccess: () => { ok('datos registrada') }
        });
    } else {
        form.put(route('datos.update', id.value), {
            onSuccess: () => { ok('datos actualizado') }
        });
    }
}
 
const ok = (msj) =>{
    form.reset();
    closeModal();
    Swal.fire({
        title: msj,
        icon:'success',
        timer: 1000,
        showConfirmButton: false
    });
}
 
 
const deleteDatos = (id, nombre) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });
 
    alerta.fire({
        title: '¿Estás seguro de eliminar ' +nombre+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('datos.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Cliente eliminado exitosamente',
                        timer: 1000,
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
    <AppLayout title="Datos de Clientes">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Datos del Cliente</h1>
        </template>
        
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Button @click="() => openModal(1)" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fa fa-plus-circle mx-1"></i>Registrar datos
                        </Button>
                        <Link :href="route('clientes.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fas fa-arrow-left mx-1"></i>Regresar
                        </Link>
                    </div>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cargo</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Teléfono</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">correo</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">tarjeta</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="dato in datos" :key="dato.id"  class="bg-white text-black dark:bg-gray-700 dark:text-white border-b">
                                    <td  class="px-6 py-4 text-center">{{dato.nombre }}</td>
                                    <td class="px-6 py-4 text-center">{{ dato.cargo }}</td>
                                    <td class="px-6 py-4 text-center">{{ dato.telefono }}</td>
                                    <td class="px-6 py-4 text-center">{{ dato.correo }}</td>
                                    <td class="px-6 py-4 text-center">{{ dato.tarjeta }}</td>
                                    <td class="px-6 py-4 text-center">
                                    <ButtonEdit @click="() => openModal(2, dato.nombre, dato.cargo, dato.telefono, dato.correo, dato.tarjeta, dato.cliente_id, dato.id)"><i class="bi bi-eye"></i></ButtonEdit>
                                        <button @click="deleteDatos(dato.id, dato.nombre)" class="text-red-500"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    <ModalResponsive :show="modal" @close="closeModal">
            <div class="p-4 uppercase">
                <h2 class="text-sm font-medium text-gray-900 dark:text-white text-center uppercase mb-2">{{ title }}</h2>
                <div class="p-1">
                    <div class="w-full mt-4">
                        <InputLabel for="cliente_id" value="Cliente:" class="mb-2 text-xs"></InputLabel>
                        <select id="cliente_id" v-model="form.cliente_id" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Seleccione una categoría</option>
                            <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">{{ cliente.razonSocial }}</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3">
                    <div class="w-full">
                        <InputLabel for="nombre" value="Nombre:" class="mb-2 text-xs"></InputLabel>
                        <TextInput id="nombre" ref="nameInput" v-model="form.nombre" type="text" class="w-full"
                                placeholder="Nombre"></TextInput>
                        <InputError :message="form.errors.nombre" class="mt-2"></InputError>
                    </div>
                    <div class="w-full">
                        <InputLabel for="cargo" value="Cargo:" class="mb-2 text-xs"></InputLabel>
                        <TextInput id="cargo" ref="nameInput" v-model="form.cargo" type="text" class="w-full"
                            placeholder="cargo"></TextInput>
                        <InputError :message="form.errors.cargo" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3">
                    <div class="w-full">
                        <InputLabel for="telefono" value="telefono:" class="mb-2 text-xs"></InputLabel>
                        <TextInput id="telefono" ref="nameInput" v-model="form.telefono" type="text" class="w-full"
                            placeholder="telefono"></TextInput>
                        <InputError :message="form.errors.telefono" class="mt-2"></InputError>
                    </div>
                    <div class="w-full">
                        <InputLabel for="correo" value="correo:" class="mb-2 text-xs"></InputLabel>
                        <TextInput id="correo" ref="nameInput" v-model="form.correo" type="text" class="w-full"
                            placeholder="correo"></TextInput>
                        <InputError :message="form.errors.correo" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="w-full">
                    <InputLabel for="tarjeta" value="tarjeta:" class="mb-2 text-xs"></InputLabel>
                    <TextInput id="tarjeta" ref="nameInput" v-model="form.tarjeta" type="text" class="w-full"
                        placeholder="tarjeta"></TextInput>
                    <InputError :message="form.errors.tarjeta" class="mt-2"></InputError>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form.processing" @click="save">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form.processing"
                            @click="closeModal">
                            Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
</template>