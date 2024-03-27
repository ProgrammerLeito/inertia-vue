<script>
export default {
    name: 'EntradasIndex',
    data() {
        return {
        searchQuery: '',
        modal: false,
        title: '',
        operation: 1,
        id: '',
        };
    },
    computed: {
        filteredEntradas() {
        // Filtrar categorías según searchQuery
        // Considere usar una expresión regular para un filtrado de nombres y números más sólido
        return this.entradas.data.filter(entrada => {
            const normalizedQuery = this.searchQuery.toLowerCase();
            return entrada.producto?.insumo.toLowerCase().includes(normalizedQuery) ||
                entrada.id.toString().includes(normalizedQuery); // Suponiendo que 'id' es un número
        });
        },
    },
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonEdit from '@/Components/ButtonEdit.vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import ButtonRegister from '@/Components/ButtonRegister.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import Swal from 'sweetalert2';
import {useForm} from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { onMounted } from 'vue';
 
const nameInput = ref(null);
const modal = ref(false);
const title = ref('');
const operation = ref(1);
const id = ref('');
 
defineProps({
    entradas: {
        type : Object,
        required: true
    },
    productos: {
        type : Object,
        required: true
    },
    entradas: {
        type : Object,
        required: true
    },
});

const form = useForm ({
    cantidad: '',
    fecha: '',
    producto_id: '',
});

const openModal = (op,cantidad,fecha,producto,entrada)=>{
    modal.value = true;
    setCurrentDate();
    nextTick( () => nameInput.value.focus());
    operation.value = op;
    id.value = entrada;
    if(op ==1){
        title.value = 'Registrar entradas';
    }
    else{
        title.value = 'Actualizar entradas';
        form.cantidad=cantidad;
        form.fecha=fecha;
        form.producto_id=producto;
    }
}

const closeModal = () =>{
    modal.value = false;
    form.reset();
}

const save = () => {
    if (operation.value == 1) {
        form.post(route('entradas.store'), {
            onSuccess: () => { ok('entrada registrada') }
        });
    } else {
        form.put(route('entradas.update', id.value), {
            onSuccess: () => { ok('entrada actualizado') }
        });
    }
}

const ok = (msj) =>{
    form.reset();
    closeModal();
    Swal.fire({title:msj,icon:'success'});
}

const deleteEntrada = (id, insumo) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });
    alerta.fire({
        title: `¿Estás seguro de eliminar ${insumo}?`, // Usar comillas para incluir el nombre del insumo
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('entradas.destroy', id), {
                onSuccess: () => { ok('entrada eliminada') }
            });
        }
    })
}

const setCurrentDate = () => {
    const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual y la formatea como yyyy-mm-dd
    form.fecha = today; // Asigna la fecha actual al modelo de datos del formulario
}

// Llama a la función para establecer la fecha actual cada vez que se monte el componente
onMounted(() => {
    setCurrentDate();
});

</script>

<template>
    <AppLayout title="Entradas">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Entradas de Productos</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('categories.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            Regresar
                        </Link>
                        <ButtonRegister @click="$event => openModal(1)">
                            Ingresar Entrada
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
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar entradas">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Producto</th>
                                        <th scope="col" class="px-6 py-3">Cantidad</th>
                                        <th scope="col" class="px-6 py-3">fecha</th>
                                        <th scope="col" class="px-6 py-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white" v-for="entrada in filteredEntradas">
                                        <td class="px-6 py-4 font-semibold">{{ entrada.producto?.insumo }}</td>
                                        <td class="px-6 py-4">{{ entrada.cantidad }}</td>
                                        <td class="px-6 py-4">{{ entrada.fecha }}</td>
                                        <td class="p-3 text-center">
                                            <!-- <Link class="py-2 px-4 text-green-500" :href="route('entradas.edit', entrada.id)"><i class="bi bi-pencil-square"></i></Link>
                                            <Link class="py-2 px-4 text-red-500" @click="deleteEntradas(entrada.id)"><i class="bi bi-trash3"></i></Link> -->
                                            <ButtonEdit @click="$event => openModal(2,entrada.cantidad,entrada.fecha,entrada.producto_id,entrada.id)">
                                                <i class="bi bi-pencil-square text-green-500"></i>
                                            </ButtonEdit>
                                            <ButtonDelete @click="$event => deleteEntrada(entrada.id, entrada.producto?.insumo)" class="ml-1">
                                                <i class="bi bi-trash3 ml-2 text-red-500"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredEntradas.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Modal :show="modal" @close="closeModal">
            <div class="p-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title }}</h2>
                <div class="p-1">
                    <label for="producto_id" class="block text-sm font-medium text-black dark:text-white">Productos:</label>
                    <select id="producto_id" v-model="form.producto_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Seleccionar una Producto</option>
                        <option v-for="producto in productos" :key="producto.id" :value="producto.id" class="text-gray-900">{{ producto.insumo }}</option>
                    </select>
                    <InputError :message="form.errors.producto_id" class="mt-2"></InputError>
                </div>
                <div class="flex flex-wrap">
                    <div class="w-full sm:w-1/2 p-3">
                        <InputLabel for="cantidad" value="Cantidad:" class="mb-2"></InputLabel>
                        <TextInput id="cantidad" ref="nameInput" v-model="form.cantidad" type="text" class="w-full"
                            placeholder="cantidad"></TextInput>
                        <InputError :message="form.errors.cantidad" class="mt-2"></InputError>
                    </div>
                    <div class="w-full sm:w-1/2 p-3">
                        <InputLabel for="fecha" value="Fecha:" class="mb-2"></InputLabel>
                        <TextInput id="fecha" v-model="form.fecha" type="date" class="w-full"
                            placeholder="fecha"></TextInput>
                        <InputError :message="form.errors.fecha" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form.processing" @click="save">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation == 1 ? 'Registrar Entrada' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form.processing"
                    @click="closeModal">
                        Cancelar
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>