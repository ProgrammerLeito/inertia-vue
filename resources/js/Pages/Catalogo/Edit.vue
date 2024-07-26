<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, usePage, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';
import FileInput from '@/Components/FileInput.vue';

const { tbproducto, tbmarcas, tbcategorias, tbsubcategorias } = defineProps({
    tbproducto: {
        type: Object,
        // required: true
    },
    tbmarcas: {
        type: Object,
        // required: true
    },
    tbcategorias: {
        type: Object,
        // required: true
    },
    tbsubcategorias: {
        type: Object,
        // required: true
    },
});
 
// Lógica del componente
const form = useForm(tbproducto);

const imagePreview2 = ref('');
const onSelectFoto2 = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
        const reader = new FileReader();
        reader.onload = (e) => {
            switch (fieldName) {
                case 'foto':
                    imagePreview2.value = e.target.result;
                    break;
            }
        };
        reader.readAsDataURL(files[0]);
        switch (fieldName) {
            case 'foto':
                form.foto = files[0];
                break;
        }
    }
}

const filteredSubcategorias = ref([]);
const filteredMarcas = ref([]);
 
const updateFilteredSubcategoriasYMarcas = () => {
    if (form.tbcategoria_id) {
        // Filtrar subcategorías basadas en la categoría seleccionada
        filteredSubcategorias.value = tbsubcategorias.filter(subcategoria => subcategoria.tbcategoria_id == form.tbcategoria_id);
 
        if (form.tbsubcategoria_id) {
            // Si se ha seleccionado una subcategoría, filtrar marcas basadas en la subcategoría seleccionada
            filteredMarcas.value = tbmarcas.filter(marca => marca.tbsubcategoria_id == form.tbsubcategoria_id);
        } else {
            // Si no se ha seleccionado ninguna subcategoría, mostrar todas las marcas relacionadas con las subcategorías filtradas
            filteredMarcas.value = tbmarcas.filter(marca => filteredSubcategorias.value.some(subcategoria => subcategoria.id == marca.tbsubcategoria_id));
        }
    } else {
        // Si no se ha seleccionado ninguna categoría, restablecer tanto subcategorías como marcas
        filteredSubcategorias.value = [];
        filteredMarcas.value = [];
    }
};
 
watch(() => form.tbcategoria_id, () => {
    // Observar cambios en la categoría seleccionada y actualizar subcategorías y marcas
    updateFilteredSubcategoriasYMarcas();
});
updateFilteredSubcategoriasYMarcas();
 
watch(() => form.tbsubcategoria_id, () => {
    // Observar cambios en la subcategoría seleccionada y actualizar marcas
    updateFilteredSubcategoriasYMarcas();
});
updateFilteredSubcategoriasYMarcas();

const submitForm = () => {
    form.post(route('tbproductos.update', { tbproducto: tbproducto.id }), {
        onSuccess: () => {
            const Toast = Swal.mixin({
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: 'Éxito',
                text: 'El producto se ha actualizado correctamente.',
                customClass: {
                    title: 'text-2xl font-bold tracking-widest ',
                    text: 'text-2xl font-bold tracking-widest ',
                    icon: 'text-base font-bold tracking-widest ',
                },
            });
        },
    });
};

</script>

<template>
    <AppLayout title="Actualizar Producto">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Actualizar Producto</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] dark:bg-gray-800 overflow-auto uppercase text-xs shadow-2xl bg-white rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8 ">
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel for="tbcategoria" class="block text-sm font-medium text-gray-700">
                                        Categoría
                                    </InputLabel>
                                    <select id="tbcategoria" v-model="form.tbcategoria_id" required
                                        @change="updateFilteredSubcategoriasYMarcas"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una categoría</option>
                                        <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id"
                                            :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.tbcategoria_id" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="tbsubcategoria" class="block text-sm font-medium text-gray-700">
                                        Subcategoría</InputLabel>
                                    <select id="tbsubcategoria" v-model="form.tbsubcategoria_id" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una subcategoría</option>
                                        <option v-for="tbsubcategoria in filteredSubcategorias" :key="tbsubcategoria.id"
                                            :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.tbsubcategoria_id" class="mt-2" />
                                </div>
 
                            </div>
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 sm:gap-x-8">
                                <div>
                                    <InputLabel for="codigo" class="block text-xs font-medium text-gray-700">Código
                                    </InputLabel>
                                    <TextInput type="text" id="codigo" v-model="form.codigo" required
                                        placeholder="Ingrese el código"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.codigo" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="tbmarca" class="block text-sm font-medium text-gray-700">Marca
                                    </InputLabel>
                                    <select id="tbmarca" v-model="form.tbmarca_id" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una marca</option>
                                        <option v-for="tbmarca in filteredMarcas" :key="tbmarca.id" :value="tbmarca.id">
                                            {{
                                                tbmarca.nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.tbmarca_id" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="modelo" class="block text-xsfont-medium text-gray-700">Modelo
                                    </InputLabel>
                                    <TextInput type="text" id="modelo" v-model="form.modelo"
                                        placeholder="Ingrese el modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.modelo" class="mt-2" />
                                </div>
 
                                <!-- Medida -->
                                <div>
                                    <InputLabel for="medida" class="block text-xs font-medium text-gray-700">Medida
                                    </InputLabel>
                                    <TextInput type="text" id="medida" v-model="form.medida"
                                        placeholder="Ingrese la medida"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.medida" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="moneda" class="block text-xs font-medium text-gray-700">Moneda
                                    </InputLabel>
                                    <select id="moneda" v-model="form.moneda" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Selecciona una moneda</option>
                                        <option value="s/">Sol</option>
                                        <option value="$">Dólares</option>
                                    </select>
                                    <InputError :message="$page.props.errors.moneda" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 sm:gap-x-8 mt-3">
                                <div>
                                    <InputLabel for="precio_min" class="block text-xs font-medium text-gray-700">
                                        Precio_min
                                    </InputLabel>
                                    <TextInput type="text" id="precio_min" v-model="form.precio_min"
                                        placeholder="Ingrese el precio"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.precio" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="precio_max" value="Precio_maximo"
                                        class="block text-xs font-medium text-gray-700"></InputLabel>
                                    <TextInput type="text" id="precio_max" v-model="form.precio_max"
                                        placeholder="escribe solo numeros"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.precio_max" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="precio_list" class="block text-xs font-medium text-gray-700">
                                        Precio_list
                                    </InputLabel>
                                    <TextInput type="number" id="precio_list" v-model="form.precio_list"
                                        placeholder="Ingrese el precio_list"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.precio_list" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="stock" class="block text-xs font-medium text-gray-700">Stock
                                    </InputLabel>
                                    <TextInput type="number" id="stock" v-model="form.stock"
                                        placeholder="Ingrese el stock"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.stock" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="garantia" class="block text-xs font-medium text-gray-700">Garantia
                                    </InputLabel>
                                    <select id="garantia" v-model="form.garantia" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected >Selecciona una opcion</option>
                                        <option value="3 meses">3 meses</option>
                                        <option value="6 meses">6 meses</option>
                                        <option value="1 año">1 año</option>
                                        <option value="sin garantia">Sin garantia</option>
                                    </select>
                                    <InputError :message="$page.props.errors.garantia" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-4 md:grid-cols-2 lg:grid-cols-3 sm:gap-x-8 mt-3">
                                <div>
                                    <InputLabel for="capacidades" class="block text-xs font-medium text-gray-700">
                                        Capacidades
                                    </InputLabel>
                                    <textarea id="capacidades" v-model="form.capacidades" rows="5"
                                        placeholder="Ingrese las capacidades"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.capacidades" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="especificaciones" class="block text-xs font-medium text-gray-700">
                                        Especificaciones</InputLabel>
                                    <textarea id="especificaciones" v-model="form.especificaciones" rows="5"
                                        placeholder="Ingrese las especificaciones"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.especificaciones" class="mt-2" />
                                </div>
 
                                <div>
                                    <InputLabel for="foto" value="Foto" class="block text-xs font-medium text-gray-700" />
                                    <FileInput name="foto" @change="(e) => onSelectFoto2(e, 'foto')"  />
                                    <InputError :message="$page.props.errors.foto" class="mt-2" />

                                    <div class="mt-4 flex space-x-4 justify-between items-center">
                                        <!-- Foto Actual -->
                                        <div class="text-center dark:text-white">
                                            <img class="h-32 rounded shadow" :src="'/productos_img/' + tbproducto.foto" alt="Foto actual">
                                            <p class="text-sm dark:text-white text-gray-900 mt-1">Foto actual</p>
                                        </div>
                                        <div>
                                            <i class="fa-solid fa-right-left dark:text-white text-gray-900 text-4xl"></i>
                                        </div>
                                        
                                        <!-- Vista Previa de la Nueva Foto -->
                                        <div v-if="imagePreview2" class="text-center dark:text-white">
                                            <img :src="imagePreview2" alt="Vista previa de la foto" class="h-32 rounded shadow">
                                            <p class="text-sm dark:text-white text-gray-900 mt-1">Nueva Foto</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive class="font-bold">ACTUALIZAR</ButtonResponsive>
                                    <Link :href="route('tbproductos.index')"
                                    class="inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">
                                    Cancelar</Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>