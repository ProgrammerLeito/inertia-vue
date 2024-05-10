<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, usePage, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import FileInput from '@/Components/FileInput.vue';

const page = usePage();

const tbproducto = ref(page.props.tbproducto);
const tbmarcas = page.props.tbmarcas;
const tbcategorias = page.props.tbcategorias;
const tbsubcategorias = page.props.tbsubcategorias;

// Lógica del componente
const initialvalues={
    tbcategoria_id: tbproducto.value.tbcategoria_id,
    tbsubcategoria_id: tbproducto.value.tbsubcategoria_id,
    tbmarca_id: tbproducto.value.tbmarca_id,
    modelo: tbproducto.value.modelo,
    medida: tbproducto.value.medida,
    moneda: tbproducto.value.moneda,
    precio: tbproducto.value.precio,
    descuento: tbproducto.value.descuento,
    stock: tbproducto.value.stock,
    codigo: tbproducto.value.codigo,
    estado: tbproducto.value.estado,
    capacidades: tbproducto.value.capacidades,
    especificaciones: tbproducto.value.especificaciones,
    foto: null,
};
const form = useForm(initialvalues)

const onSelectFoto= (e) =>{
    const files = e.target.files;
    if(files.length){
        form.foto= files[0]
    }
}



const submitForm = () => {
    form.post(route('tbproductos.update', { tbproducto: tbproducto.value.id }), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'El producto se ha actualizado correctamente.',
                timer: 1000,
                timerProgressBar: true,
                showConfirmButton: false
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
                            <!-- Campos de selección -->
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3  sm:gap-x-8 mb-3">
                                <!-- Categoría -->
                                <div>
                                    <InputLabel for="tbcategoria" class="block text-sm font-medium text-gray-700">Categoria</InputLabel>
                                    <select id="tbcategoria" v-model="form.tbcategoria_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una categoría</option>
                                        <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id" :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.tbcategoria_id" class="mt-2"/>
                                </div>

                                <!-- Subcategoría -->
                                <div>
                                    <InputLabel for="tbsubcategoria" class="block text-sm font-medium text-gray-700">Sub categoria</InputLabel>
                                    <select id="tbsubcategoria" v-model="form.tbsubcategoria_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una subcategoría</option>
                                        <option v-for="tbsubcategoria in tbsubcategorias" :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.tbsubcategoria_id" class="mt-2"/>
                                </div>

                                <!-- Marca -->
                                <div>
                                    <InputLabel for="tbmarca" class="block text-sm font-medium text-gray-700">Marca</InputLabel>
                                    <select id="tbmarca" v-model="form.tbmarca_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una marca</option>
                                        <option v-for="tbmarca in tbmarcas" :key="tbmarca.id" :value="tbmarca.id">{{ tbmarca.nombre }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.tbmarca_id" class="mt-2"/>
                                </div>
                            </div>

                            <!-- Otros campos -->
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-4 sm:gap-x-8 mb-3">
                                <!-- Modelo -->
                                <div>
                                    <InputLabel for="modelo" class="block text-sm font-medium text-gray-700">Modelo</InputLabel>
                                    <TextInput type="text" id="modelo" v-model="form.modelo" required placeholder="Ingrese el modelo"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.modelo" class="mt-2"/>
                                </div>

                                <!-- Medida -->
                                <div>
                                    <InputLabel for="medida" class="block text-sm font-medium text-gray-700">Medida</InputLabel>
                                    <TextInput type="text" id="medida" v-model="form.medida" required placeholder="Ingrese la medida"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.medida" class="mt-2"/>
                                </div>

                                <!-- Moneda -->
                                <div>
                                    <InputLabel for="moneda" class="block text-sm font-medium text-gray-700">Moneda</InputLabel>
                                    <select id="moneda" v-model="form.moneda" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Selecciona una moneda</option>
                                        <option value="sol">Sol</option>
                                        <option value="dolares">Dólares</option>
                                    </select>
                                    <InputError :message="$page.props.errors.moneda" class="mt-2"/>
                                </div>

                                <!-- Precio -->
                                <div>
                                    <InputLabel for="precio" class="block text-sm font-medium text-gray-700">Precio</InputLabel>
                                    <TextInput type="number" id="precio" v-model="form.precio" required placeholder="Ingrese el precio"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.precio" class="mt-2"/>
                                </div>

                                <!-- Descuento -->
                                <div>
                                    <InputLabel for="descuento" class="block text-sm font-medium text-gray-700">Descuento (%)</InputLabel>
                                    <TextInput type="number" id="descuento" v-model="form.descuento" placeholder="Ingrese el descuento"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.descuento" class="mt-2"/>
                                </div>

                                <!-- Stock -->
                                <div>
                                    <InputLabel for="stock" class="block text-sm font-medium text-gray-700">Stock</InputLabel>
                                    <TextInput type="number" id="stock" v-model="form.stock"  required placeholder="Ingrese el stock"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.stock" class="mt-2"/>
                                </div>

                                <!-- Código -->
                                <div>
                                    <InputLabel for="codigo" class="block text-sm font-medium text-gray-700">Código</InputLabel>
                                    <TextInput type="text" id="codigo" v-model="form.codigo" required placeholder="Ingrese el código"
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.codigo" class="mt-2"/>
                                </div>

                                <!-- Estado -->
                                <div>
                                    <InputLabel for="estado" class="block text-sm font-medium text-gray-700">Estado</InputLabel>
                                    <select id="estado" v-model="form.estado" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Selecciona un estado</option>
                                        <option value="activado">Activado</option>
                                        <option value="suspendido">Suspendido</option>
                                    </select>
                                    <InputError :message="$page.props.errors.estado" class="mt-2"/>
                                </div>
                            </div>

                            <!-- Capacidades -->
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8">
                                <div>
                                    <InputLabel for="capacidades" class="block text-sm font-medium text-gray-700">Capacidades</InputLabel>
                                    <textarea id="capacidades" v-model="form.capacidades" rows="5" required placeholder="Ingrese las capacidades"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.capacidades" class="mt-2"/>
                                </div>

                                <!-- Especificaciones -->
                                <div>
                                    <InputLabel for="especificaciones" class="block text-sm font-medium text-gray-700">Especificaciones</InputLabel>
                                    <textarea id="especificaciones" v-model="form.especificaciones" rows="5" required placeholder="Ingrese las especificaciones"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.especificaciones" class="mt-2"/>
                                </div>

                                <div>
                                    <InputLabel for="foto" value="Foto" class="block text-sm font-medium text-gray-700"/>
                                    <FileInput name="foto" @change="onSelectFoto" class="text-sm"/>
                                    <InputError :message="$page.props.errors.foto" class="mt-2" />
                                    <img class="h-16 mt-2" :src="'/storage/' + tbproducto.foto" alt="Foto actual" >
                                    <div class="py-1 dark:text-white normal-case text-sm">De preferencia Subir Imagenes Cuadradas</div>
                                </div>

                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive class="font-bold">ACTUALIZAR</ButtonResponsive>
                                    <Link :href="route('tbproductos.index')" class="inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">
                                        Cancelar
                                    </Link>
                                </div>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>