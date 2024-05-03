<script>
export default {
    name: 'CategoriesIndex',
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
        filteredCategories() {
        // Filtrar categorías según searchQuery
        // Considere usar una expresión regular para un filtrado de nombres y números más sólido
        return this.categories.data.filter(category => {
            const normalizedQuery = this.searchQuery.toLowerCase();
            return category.name.toLowerCase().includes(normalizedQuery) ||
                category.id.toString().includes(normalizedQuery); // Suponiendo que 'id' es un número
        });
        },
    },
    methods: {
        guardarCategoriaId(categoryId) {
            // Guardar el ID de la categoría en localStorage
            localStorage.setItem('category_id', categoryId);
        },
        redirectToDetails(categoryId) {
            // Redirigir a la página de detalles usando el mismo enlace que el botón de visualización
            window.location.href = this.route('productos.index', { category_id: categoryId });
        }
    }
};
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonRegister from '@/Components/ButtonRegister.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonEdit from '@/Components/ButtonEdit.vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import Swal from 'sweetalert2';
import { nextTick, ref } from 'vue';
 
const nameInput = ref(null);
const modal = ref(false);
const title = ref('');
const operation = ref(1);
const id = ref('');
 
defineProps({
    categories: {
        type : Object,
        required: true
    }
});
 
const form = useForm ({
    name: ''
})
 
const openModal = (op,name,category)=>{
    modal.value = true;
    nextTick( () => nameInput.value.focus());
    operation.value = op;
    id.value = category;
    if(op ==1){
        title.value = 'Registrar categoria';
    }
    else{
        title.value = 'Actualizar categoria';
        form.name=name;
    }
}
 
 
const closeModal = () =>{
    modal.value = false;
    form.reset();
}
const save = () => {
    if (operation.value == 1) {
        form.post(route('categories.store'), {
            onSuccess: () => { ok('categoria registrada') } // Llama a la función ok con el mensaje correcto
        });
    } else {
        form.put(route('categories.update', id.value), {
            onSuccess: () => { ok('categoria actualizado') } // Llama a la función ok con el mensaje correcto
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
 
const deleteEmployee = (id, name) => {
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
            form.delete(route('categories.destroy', id),{
                onSuccess: () => {ok('Entrada Eiminada')}
            });
        }
    })
}

</script>

<template>
    <AppLayout title="Categorias">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Categorias</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <ButtonRegister @click.prevent="$event => openModal(1)" v-if="$page.props.user.permissions.includes('Crear Categoria')">
                            <i class="bi bi-plus-circle mx-1"></i>Registrar Categoria
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
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar la categoria">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-white uppercase bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Categorias</th>
                                        <th scope="col" class="text-center px-6 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-800 hover:bg-gray-200 cursor-pointer" @dblclick="guardarCategoriaId(category.id); redirectToDetails(category.id)" v-for="category in filteredCategories" :key="category.id">
                                        <td class="px-6 py-4 font-semibold">{{ category.name }}</td>
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <!-- <Link class="py-2 px-4 text-yellow-500" :href="route('productos.index', { category_id: category.id })"><i class="bi bi-eye"></i></Link> -->
                                            <ButtonEdit @click="$event => openModal(2, category.name, category.id)" v-if="$page.props.user.permissions.includes('Acciones Categorias')">
                                            <i class="bi bi-pencil-square py-2 px-3 rounded-lg text-white bg-green-600 hover:bg-green-700"></i>
                                            </ButtonEdit>
                                            <ButtonDelete @click="$event => deleteEmployee(category.id, category.name)" v-if="$page.props.user.permissions.includes('Acciones Categorias')">
                                            <i class="bi bi-trash3 py-2 px-3 rounded-lg text-white bg-red-600 hover:bg-red-700"></i>
                                            </ButtonDelete>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredCategories.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <Link v-if="categories.current_page > 1" :href="categories.prev_page_url" class="flex items-center justify-center px-3 h-8 me-3 text-sm font-medium text-white bg-blue-600 border rounded-lg hover:text-white hover:bg-blue-700">
                            <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                            </svg>
                            Anterior
                        </Link>
                        <Link v-if="categories.current_page < categories.last_page" :href="categories.next_page_url" class="flex items-center justify-center px-3 h-8 me-3 text-sm font-medium text-white bg-blue-600 border rounded-lg hover:text-white hover:bg-blue-700">
                            Siguiente
                            <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        <ModalResponsive :show="modal" @close="closeModal">
            <div class="p-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title }}</h2>
                <div class="p-3 ">
                    <InputLabel for="name" value="NOMBRE:" class="mb-2"></InputLabel>
                    <TextInput id="cantnameidad" ref="nameInput" v-model="form.name" type="text" class="w-full"
                        placeholder="Nombre de la categoria"></TextInput>
                    <InputError :message="form.errors.name" class="mt-2"></InputError>
                </div>
                <div class="p-3 flex justify-center">
                    <PrimaryButton :disabled="form.processing" @click="save">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation == 1 ? 'Registrar Categoria' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form.processing"
                    @click="closeModal">
                        Cancelar
                    </DangerButton>
                </div>
                <!-- Agrega más campos según sea necesario -->
            </div>
        </ModalResponsive>
    </AppLayout>
</template>