<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import FileInput from '@/Components/FileInput.vue';

const nameInput4 = ref(null);
const modal4 = ref(false);
const title4 = ref('');
const operation4 = ref(1);
const id4 = ref('');

const nameInput2 = ref(null);
const modal2 = ref(false);
const title2 = ref('');
const operation2 = ref(1);
const id2 = ref('');

const nameInput3 = ref(null);
const modal3 = ref(false);
const title3 = ref('');
const operation3 = ref(1);
const id3 = ref('');

defineProps({
    tbcategorias: {
        type: Object,
        required: true
    },
    tbsubcategorias: {
        type: Object,
        required: true
    },
    tbmarcas: {
        type: Object,
        required: true
    }

});
const form4 = useForm({
    nombre: '',

});
const form3 = useForm({
    nombre: '',
    tbsubcategoria_id: ''
});
const form2 = useForm({
    nombre: '',
    tbcategoria_id: ''
});

const initialvalues = {
    tbcategoria_id: '',
    tbsubcategoria_id: '',
    tbmarca_id: '',
    modelo: '',
    medida: '',
    moneda: '',
    precio: 0,
    descuento: 0,
    stock: 0,
    codigo: '',
    estado: '',
    capacidades: '',
    especificaciones: '',
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
    form.post(route('tbproductos.store'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'El producto se ha registrado correctamente.',
                timer: 1000,
                timerProgressBar: true,
                showConfirmButton: false

            });
        },
        onError: (errors) => {
            if (errors.response && errors.response.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.'
                });
                console.error('Error HTTP:', errors.response.status);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.'
                });
                console.error('Error desconocido:', errors);
            }
        }
    });
}


// const onFileChange = (event) => {
//     const file = event.target.files[0];
//     form.foto = file;
//     form.fotoPreview = URL.createObjectURL(file);
// }

const openModal3 = (op, nombre, tbsubcategoria, tbmarca) => {
    modal3.value = true;
    nextTick(() => nameInput3.value.focus());
    operation3.value = op;
    id3.value = tbmarca;
    if (op === 1) {
        title3.value = 'Registrar marca';
    }
    else {
        title3.value = 'Actualizar marca';
        form3.nombre = nombre;
        form3.tbsubcategoria_id = tbsubcategoria;
    }
}

const closeModal3 = () => {
    modal3.value = false;
    form3.reset();
}

const save3 = () => {
    if (operation3.value == 1) {
        form3.post(route('tbmarcas.store'), {
            onSuccess: () => { ok3('marca registrada') }
        });
    } else {
        form3.put(route('tbmarcas.update', id3.value), {
            onSuccess: () => { ok3('marca actualizado') }
        });
    }
}

const ok3 = (msj) => {
    form3.reset();
    closeModal3();
    Swal.fire({
        title: msj,
        icon: 'success',
        timer: 1000,
        showConfirmButton: false
    });
};

const openModal2 = (op, nombre, tbcategoria, tbsubcategoria) => {
    modal2.value = true;
    nextTick(() => nameInput2.value.focus());
    operation2.value = op;
    id2.value = tbsubcategoria;
    if (op === 1) {
        title2.value = 'Registrar Subcategoría';
    } else {
        title2.value = 'Actualizar Subcategoría';
        form2.nombre = nombre;
        form2.tbcategoria_id = tbcategoria;
    }
};

const closeModal2 = () => {
    modal2.value = false;
    form2.reset();
};

const save2 = () => {
    if (operation2.value === 1) {
        form2.post(route('tbsubcategorias.store'), {
            onSuccess: () => { ok2('Subcategoría registrada'); }
        });
    } else {
        form2.put(route('tbsubcategorias.update', id2.value), {
            onSuccess: () => { ok2('Subcategoría actualizada'); }
        });
    }
};
const ok2 = (msj) => {
    form2.reset();
    closeModal2();
    Swal.fire({
        title: msj,
        icon: 'success',
        timer: 1000,
        showConfirmButton: false
    });
};

const openModal4 = (op, nombre, tbcategoria) => {
    modal4.value = true;
    nextTick(() => nameInput4.value.focus());
    operation4.value = op;
    id4.value = tbcategoria;
    if (op === 1) {
        title4.value = 'Registrar categoria';
    }
    else {
        title4.value = 'Actualizar categoria';
        form4.nombre = nombre;
    }
}

const closeModal4 = () => {
    modal4.value = false;
    form4.reset();
}

const save4 = () => {
    if (operation4.value == 1) {
        form4.post(route('tbcategorias.store'), {
            onSuccess: () => { ok4('categoria registrada') }
        });
    } else {
        form4.put(route('tbcategorias.update', id4.value), {
            onSuccess: () => { ok4('categoria actualizado') }
        });
    }
}

const ok4 = (msj) => {
    form4.reset();
    closeModal4();
    Swal.fire({
        title: msj,
        icon: 'success',
        timer: 1000,
        showConfirmButton: false
    });
};

</script>
 
<template>
    <AppLayout title="Crear Producto">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Registrar Producto</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
               
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <!-- Categoría -->
                                <div class="flex flex-col items-start">
                                    <InputLabel for="tbcategoria" value="Categoria" class="ml-1"/>
                                    <div class="flex w-full">
                                        <select id="tbcategoria" v-model="form.tbcategoria_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="" disabled selected>Seleccione una categoría</option>
                                            <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id" :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                        </select>
                                        <Button @click.prevent="() => openModal4(1)" class="bg-green-600 text-white mt-1 py-1 w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div>
 
                                <!-- Subcategoría -->
                                <div class="flex flex-col items-start">
                                    <InputLabel for="tbsubcategoria" value="Sub Categoria" class="ml-1"/>
                                    <div class="flex w-full">
                                        <!-- <label for="tbsubcategoria" >Subcategoría</label> -->
                                        <select id="tbsubcategoria" v-model="form.tbsubcategoria_id"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="" disabled selected>Seleccione una subcategoría</option>
                                            <option v-for="tbsubcategoria in tbsubcategorias" :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                                        </select>
                                        <Button  @click.prevent="() => openModal2(1)" class="bg-green-600 mt-1 py-1 text-white w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div>
                                <!-- Marca -->
                                <div class="flex flex-col items-start">
                                    <InputLabel for="tbmarca" value="Marca" class="ml-1"/>
                                    <div class="flex w-full">
                                        <select id="tbmarca" v-model="form.tbmarca_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="" disabled selected>Seleccione una marca</option>
                                            <!-- Iterar sobre las marcas -->
                                            <option v-for="tbmarca in tbmarcas" :key="tbmarca.id" :value="tbmarca.id">{{ tbmarca.nombre }}</option>
                                        </select>
                                        <Button @click.prevent="() => openModal3(1)" class="bg-green-600 py-1 text-white mt-1 w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-4 sm:gap-x-8">
                                <!-- Modelo -->
                                <div>
                                    <InputLabel for="modelo" class="block text-md font-medium text-gray-700">Modelo</InputLabel>
                                    <TextInput type="text" id="modelo" v-model="form.modelo" required placeholder="Escribe un modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.modelo" class="mt-2"/>
                                </div>
                                <!-- Medida -->
                                <div>
                                    <InputLabel for="medida" class="block text-sm font-medium text-gray-700">Medida</InputLabel>
                                    <TextInput type="text" id="medida" v-model="form.medida" required placeholder="Escribe la medida"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.medida" class="mt-2"/>
                                </div>
                                <!-- Moneda -->
                                <div>
                                    <InputLabel for="moneda" class="block text-sm font-medium text-gray-700">Moneda</InputLabel>
                                    <select id="moneda" v-model="form.moneda" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected >Selecciona una moneda</option>
                                        <option value="sol">Sol</option>
                                        <option value="dolares">Dólares</option>
                                    </select>
                                </div>
 
                                <!-- Precio -->
                                <div>
                                    <InputLabel for="precio" class="block text-sm font-medium text-gray-700">Precio</InputLabel>
                                    <TextInput type="text" id="precio" v-model="form.precio" required placeholder="0.00"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.precio" class="mt-2"/>
                                </div>
                                <!-- Descuento -->
                                <div>
                                    <InputLabel for="descuento" class="block text-sm font-medium text-gray-700">Descuento(%)</InputLabel>
                                    <TextInput type="text" id="descuento" v-model="form.descuento" placeholder="0.00"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.descuento" class="mt-2"/>
                                </div>
                                <!-- Stock -->
                                <div>
                                    <InputLabel for="stock" class="block text-sm font-medium text-gray-700">Stock</InputLabel>
                                    <TextInput type="number" id="stock" v-model="form.stock" required placeholder="0"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.stock" class="mt-2"/>
                                </div>
                                <!-- Código -->
                                <div>
                                    <InputLabel for="codigo" class="block text-sm font-medium text-gray-700">Código</InputLabel>
                                    <TextInput type="text" id="codigo" v-model="form.codigo" required placeholder="Ingrese el codigo del producto"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.codigo" class="mt-2"/>
                                </div>
                                <!-- Estado -->
                                <div>
                                    <InputLabel for="estado" class="block text-sm font-medium text-gray-700">Estado</InputLabel>
                                    <select id="estado" v-model="form.estado" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected >Selecciona un estado</option>
                                        <option value="activado">Activado</option>
                                        <option value="suspendido">Suspendido</option>
                                    </select>
                                </div>
 
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mt-2">
                                <!-- Capacidades -->
                                <div>
                                    <InputLabel for="capacidades" class="block text-sm font-medium text-gray-700">Capacidades</InputLabel>
                                    <textarea id="capacidades" v-model="form.capacidades" rows="5" placeholder="Escribe las capacidades..."
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.capacidades" class="mt-2"/>
                                </div>
                                <!-- Especificaciones -->
                                <div>
                                    <InputLabel for="especificaciones" class="block text-sm font-medium text-gray-700">Especificaciones</InputLabel>
                                    <textarea id="especificaciones" v-model="form.especificaciones" rows="5" placeholder="Escribe las especificaciones..."
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.especificaciones" class="mt-2"/>
                                </div>
                                <!-- Foto -->
                                <div>
                                    <InputLabel for="foto" value="Foto" class="block text-xs font-medium text-gray-700"/>
                                    <FileInput name="foto" @change="onSelectFoto"/>
                                    <InputError :message="$page.props.errors.foto" class="mt-2" />
                                    <div class="py-1 dark:text-white normal-case">De preferencia Subir Imagenes Cuadradas</div>
                                </div>
                            </div>
 
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive>
                                        REGISTRAR
                                    </ButtonResponsive>
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
        <ModalResponsive :show="modal4" @close="closeModal4">
            <div class="p-4 uppercase">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title4 }}</h2>
                <div class="p-1">
                    <div class="w-full">
                        <InputLabel for="nombre" value="Nombre:" class="mb-2"></InputLabel>
                        <TextInput id="nombre" ref="nameInput4" v-model="form4.nombre" type="text" class="w-full"
                                placeholder="Nombre"></TextInput>
                        <InputError :message="form4.errors.nombre" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form4.processing" @click="save4">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation4 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form4.processing"
                            @click="closeModal4">
                            Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
        <ModalResponsive :show="modal2" @close="closeModal2">
            <div class="p-4 uppercase">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title2 }}</h2>
                <div class="p-1">
                    <div class="w-full">
                        <InputLabel for="nombre" value="Nombre:" class="mb-2"></InputLabel>
                        <TextInput id="nombre" ref="nameInput2" v-model="form2.nombre" type="text" class="w-full"
                                placeholder="Nombre"></TextInput>
                        <InputError :message="form2.errors.nombre" class="mt-2"></InputError>
                    </div>
                    <div class="w-full mt-4">
                        <InputLabel for="tbcategoria_id" value="Categoría ID:" class="mb-2"></InputLabel>
                        <select id="tbcategoria_id" v-model="form2.tbcategoria_id" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Seleccione una categoría</option>
                            <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id" :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                        </select>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form2.processing" @click="save2">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation2 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form2.processing"
                            @click="closeModal2">
                            Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
        <ModalResponsive :show="modal3" @close="closeModal3">
            <div class="p-4 uppercase">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title3 }}</h2>
                <div class="p-1">
                    <div class="w-full">
                        <InputLabel for="nombre" value="Nombre:" class="mb-2"></InputLabel>
                        <TextInput id="nombre" ref="nameInput3" v-model="form3.nombre" type="text" class="w-full"
                                placeholder="Nombre"></TextInput>
                        <InputError :message="form3.errors.nombre" class="mt-2"></InputError>
                    </div>
                    <div class="w-full mt-4">
                        <InputLabel for="tbsubcategoria_id" value="Categoría ID:" class="mb-2"></InputLabel>
                        <select id="tbsubcategoria_id" v-model="form3.tbsubcategoria_id" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Seleccione una categoría</option>
                            <option v-for="tbsubcategoria in tbsubcategorias" :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                        </select>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form3.processing" @click="save3">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation3 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form3.processing"
                            @click="closeModal3">
                            Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
    </AppLayout>
</template>