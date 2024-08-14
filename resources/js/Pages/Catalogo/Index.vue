<script setup>
import { ref, watchEffect, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import Swal from 'sweetalert2';
import { Link, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';

const filteredTbproductos = ref([]);
const searchQuery = ref('');
const selectedCategoria = ref('');
const selectedSubcategoria = ref('');
 
const props = defineProps({
    tbproductos: {
        type : Object,
        required: true
    },
    tbsubcategorias: {
        type : Object,
        required: true
    },
    tbcategorias: {
        type : Object,
        required: true
    },
    tbmarcas: {
        type : Object,
        required: true
    }
});
 
const form = useForm ({
    id : '',
});
 
const deleteTbproducto = (id, modelo) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });
 
    alerta.fire({
        title: '¿Estás seguro de eliminar ' +modelo+ '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('tbproductos.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Producto eliminado exitosamente',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
}
 
watchEffect(() => {
    filteredTbproductos.value = props.tbproductos.data.filter(tbproducto => {
        const searchLowerCase = searchQuery.value.toLowerCase();
        return (
            (tbproducto.modelo.toLowerCase().includes(searchLowerCase) ||
             tbproducto.precio_list.toLowerCase().includes(searchLowerCase) ||
             tbproducto.codigo.toLowerCase().includes(searchLowerCase) ||
             tbproducto.precio_min.toLowerCase().includes(searchLowerCase) ||
             tbproducto.precio_max.toLowerCase().includes(searchLowerCase) ||
             tbproducto.garantia.toLowerCase().includes(searchLowerCase) ||
             tbproducto.tbcategoria.nombre.toLowerCase().includes(searchLowerCase) ||
             tbproducto.tbsubcategoria.nombre.toLowerCase().includes(searchLowerCase)) &&
            (selectedCategoria.value === '' || tbproducto.tbcategoria_id === selectedCategoria.value) &&
            (selectedSubcategoria.value === '' || tbproducto.tbsubcategoria_id === selectedSubcategoria.value)
        );
    });
});

// Método para sincronizar automáticamente la subcategoría seleccionada con la categoría seleccionada
watchEffect(() => {
    // Buscar la primera subcategoría relacionada con la categoría seleccionada, si existe
    const categoriaSeleccionada = props.tbcategorias.find(categoria => categoria.id === selectedCategoria.value);
    if (categoriaSeleccionada) {
        selectedSubcategoria.value = categoriaSeleccionada.tbsubcategorias.length > 0 ? categoriaSeleccionada.tbsubcategorias[0].id : '';
    }
});

watchEffect(() => {
    if (props.tbcategorias.length > 0 && selectedCategoria.value === '') {
        selectedCategoria.value = props.tbcategorias[0].id;
    }
});

</script>
 
<template>
    <AppLayout title="Catalago Productos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Catalago de Productos</h1>
        </template>
        <div>
            <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
                <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                        <div class="flex flex-wrap gap-2 justify-between">
                            <Link v-if="$page.props.user.permissions.includes('Registrar Catalago')" :href="route('tbproductos.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                <i class="bi bi-clipboard-plus mx-1"></i>Registrar Producto
                            </Link>
                        </div>
                        <div>
                            <div class="py-2 uppercase">
                                <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                    <div class="flex flex-col">
                                        <InputLabel class="block text-md font-medium text-gray-700">Categoría</InputLabel>
                                        <select v-model="selectedCategoria" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="" disabled>Seleccione una Categoría</option>
                                            <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id" :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <InputLabel class="block text-md font-medium text-gray-700">Subcategoría</InputLabel>
                                        <select v-model="selectedSubcategoria" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="" disabled selected >Seleccione una Subcategoría</option>
                                            <option v-for="tbsubcategoria in tbsubcategorias.filter(subcategoria => subcategoria.tbcategoria_id === selectedCategoria)" :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <InputLabel for="table-search" class="block text-md font-medium text-gray-700">Buscar</InputLabel>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                                </svg>
                                            </div>
                                            <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar el producto">
                                        </div>
                                    </div>
                                </div>
                            </div>
 
                            <div class="relative overflow-x-auto shadow-md md:rounded-lg rounded-md shadow-gray-200 dark:shadow-gray-500">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                                    <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Codigo</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 hidden">Cat</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 hidden">SubCat</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Marca</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Capacidades</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Especificaciones</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Foto</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">P_Proveedor</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio_Min</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio_Max</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Garantia</th>
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2" v-if="$page.props.user.permissions.includes('Acciones Catalago')">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(tbproducto, i) in filteredTbproductos" :key="tbproducto.id" class="bg-white text-black dark:bg-gray-700 dark:text-white">
                                            <!-- <td class="px-6 py-4 text-center"><b>{{ i + 1 }}</b></td> -->
                                            <td class="px-6 py-4 text-center border-r border-b">{{ tbproducto.codigo }}</td>
                                            <td class="px-6 py-4 text-center border-r border-b hidden">{{ tbproducto.tbcategoria.nombre }}</td>
                                            <td class="px-6 py-4 text-center border-r border-b hidden">{{ tbproducto.tbsubcategoria.nombre }}</td>
                                            <td class="px-6 py-4 text-center border-r border-b">{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }} </td>
                                            <td class="px-6 py-4 text-center border-r border-b"><b>{{ tbproducto.modelo }}</b></td>
                                            <td class="px-6 py-2 text-center border-r border-b whitespace-nowrap">
                                                <ul class="list-disc px-6 py-4 text-left -translate-y-1">
                                                    <li v-for="(capacidad, index) in (tbproducto.capacidades || '').split('\n')" :key="index">{{ capacidad }}</li>
                                                </ul>
                                            </td>
                                            <td class="px-6 py-4 text-center border-r border-b whitespace-nowrap">
                                                <div class="accordions">
                                                    <dl>
                                                        <dt @click="toggleAccordion(i)" class="cursor-pointer normal-case">
                                                            Especificaciones
                                                            <i :class="{'fa-solid fa-arrow-up-long ml-2': isActive(i), 'fa-solid fa-arrow-down-long ml-2': !isActive(i)}"></i>
                                                        </dt>
                                                        <dd v-if="isActive(i)" class="ml-4">
                                                            <ul class="list-disc px-6 py-4 text-left">
                                                                <li v-for="(item, index) in (tbproducto.especificaciones || '').split('\n')" :key="index">{{ item }}</li>
                                                            </ul>
                                                        </dd>
                                                    </dl>
                                                </div>
                                            </td>
                                            <td class="md:px-2 px-2 py-2 text-center border-r border-b">
                                                <img @click="openModal('/productos_img/' + tbproducto.foto)"
                                                    :src="'/productos_img/' + tbproducto.foto" alt="Foto"
                                                    style="max-width: 90px; cursor: pointer; object-fit: cover;" class="rounded-md py-1 mx-auto">
                                            </td>
                                            <td class="px-6 py-4 text-center border-r border-b"> {{ tbproducto.precio_list ? `${tbproducto.moneda} ${tbproducto.precio_list}` : 'Por asignar' }} </td>
                                            <td class="px-6 py-4 text-center border-r border-b"> {{ tbproducto.precio_min ? `${tbproducto.moneda} ${tbproducto.precio_min}` : 'Por asignar' }} </td>
                                            <td class="px-6 py-4 text-center border-r border-b"> {{ tbproducto.precio_max ? `${tbproducto.moneda} ${tbproducto.precio_max}` : 'Por asignar' }} </td>
                                            <td class="px-6 py-4 text-center border-r border-b text-red-500 dark:text-white font-bold"> {{ tbproducto.garantia }} </td>
                                            <!-- <td class="p-3 text-center border-b" v-if="$page.props.user.permissions.includes('Acciones Administrador')">
                                                <Link :href="route('tbproductos.edit', { tbproducto: tbproducto.id })"
                                                    class="py-1 p-1 bg-green-500 dark:hover:bg-white dark:text-white dark:hover:text-green-500 rounded">
                                                    <i class="bi bi-pencil-square"></i>
                                                </Link>
                                                <Button @click="$event => deleteTbproducto(tbproducto.id, tbproducto.modelo)"
                                                    class="ml-1 bg-red-600 dark:hover:bg-white dark:hover:text-red-600 py-1 px-1 font-extrabold dark:text-white rounded cursor-pointer text-white">
                                                    <i class="bi bi-trash3"></i>
                                                </Button>
                                            </td> -->
                                            <td class="p-3 text-center border-b" v-if="$page.props.user.permissions.includes('Acciones Catalago')">
                                                <Link :href="route('tbproductos.edit', { tbproducto: tbproducto.id })" class="inline-flex items-center justify-center bg-amber-400 hover:bg-amber-500 px-1.5 py-0.5 rounded-md mr-2">
                                                    <i class='bx bxs-edit text-base text-white'></i>
                                                </Link>
                                                <button @click="$event => deleteTbproducto(tbproducto.id, tbproducto.modelo)" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 px-1.5 py-0.5 rounded-md">
                                                    <i class='bx bxs-trash text-base text-white'></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div v-if="filteredTbproductos.length === 0" class="text-center py-2 dark:text-white">
                                    No se encontraron datos.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed right-0 top-[110px] bottom-[-60px] md:left-[50px] left-[-2px] overflow-y-auto z-[10000] bg-gray-200/40" v-if="modalOpen">
            <div class="flex justify-center items-center flex-col h-full max-h-[90%] py-10 m-auto rounded-lg" style="backdrop-filter: blur(5px);" @click="modalOpen = false">
                <div class="w-full max-w-sm max-h-[90%] h-full bg-gray-50 rounded-t-lg">
                    <img :src="modalImageUrl" alt="Imagen ampliada" class="max-w-sm w-full h-full object-contain">
                </div>
                <!-- Botón de cierre del modal -->
                <div class="bg-gray-50 p-2 w-full max-w-sm flex justify-end rounded-b-lg">
                    <button @click="modalOpen = false" type="button" class="w-full justify-center rounded-md border border-transparent shadow-sm px-14 py-0 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
 
<script>

export default {
    data() {
        return {
            modalOpen: false, // Estado del modal
            modalImageUrl: '', // URL de la imagen para mostrar en el modal
            activeAccordion: [] // Array para manejar múltiples acordeones activos
        };
    },
    methods: {
        // Método para abrir el modal con la imagen seleccionada
        openModal(imageUrl) {
            this.modalImageUrl = imageUrl; // Establece la URL de la imagen
            this.modalOpen = true; // Abre el modal
        },
        toggleAccordion(index) {
            // Verifica si el índice está en el array de acordeones activos
            const indexPosition = this.activeAccordion.indexOf(index);
            if (indexPosition === -1) {
                // Si no está, agrégalo
                this.activeAccordion.push(index);
            } else {
                // Si ya está, quítalo
                this.activeAccordion.splice(indexPosition, 1);
            }
        },
        isActive(index) {
            // Método para verificar si un acordeón está activo
            return this.activeAccordion.includes(index);
        }
    }
}

</script>