<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref, watch } from 'vue';
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

const { tbcategorias, tbsubcategorias, tbmarcas  }= defineProps({
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
    tbsubcategoria_id: '0',
    tbmarca_id: '0',
    modelo: '',
    medida: '',
    moneda: '',
    precio_min: 0,
    precio_max: 0,
    precio_list: 0,
    stock: 0,
    codigo: '',
    capacidades: '',
    especificaciones: '',
    foto: null,
    garantia: '',
    link_producto: '',
};

const form = useForm(initialvalues);

 const imagePreview1 = ref('');
const onSelectFoto = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
        const reader = new FileReader();
        reader.onload = (e) => {
            switch (fieldName) {
                case 'foto':
                    imagePreview1.value = e.target.result;
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

const filteredSubcategorias = ref(tbsubcategorias);
const filteredMarcas = ref(tbmarcas);
 
const updateFilteredSubcategorias = () => {
    if (form.tbcategoria_id) {
        filteredSubcategorias.value = tbsubcategorias.filter(subcategoria => subcategoria.tbcategoria_id == form.tbcategoria_id);
        // Reset the subcategoria and marca when categoria changes
        form.tbsubcategoria_id = '';
        form.tbmarca_id = '';
        filteredMarcas.value = [];
    } else {
        filteredSubcategorias.value = tbsubcategorias;
        filteredMarcas.value = tbmarcas;
    }
};

const updateFilteredMarcas = () => {
    if (form.tbsubcategoria_id) {
        filteredMarcas.value = tbmarcas.filter(marca => marca.tbsubcategoria_id == form.tbsubcategoria_id);
        // Reset the marca when subcategoria changes
        form.tbmarca_id = '';
    } else {
        filteredMarcas.value = [];
    }
};

watch(() => form.tbcategoria_id, () => {
    updateFilteredSubcategorias();
});

watch(() => form.tbsubcategoria_id, () => {
    updateFilteredMarcas();
});

updateFilteredSubcategorias();
updateFilteredMarcas();

const submitForm = () => {
    $("#btnRegistrarPoductoCatalago").hide();
    $("#loading-button").show();
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
            $("#btnRegistrarPoductoCatalago").show();
            $("#loading-button").hide();
        },
        onError: (errors) => {
            if (errors.response && errors.response.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.'
                });
                console.error('Error HTTP:', errors.response.status);
                $("#btnRegistrarPoductoCatalago").show();
                $("#loading-button").hide();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.'
                });
                console.error('Error desconocido:', errors);
                $("#btnRegistrarPoductoCatalago").show();
                $("#loading-button").hide();
            }
        }
    });
}

const openModal3 = (op,tbmarca) => {
    modal3.value = true;
    nextTick(() => nameInput3.value.focus());
    operation3.value = op;
    id3.value = tbmarca;
    if (op === 1) {
        title3.value = 'Registrar marca';
    }
}

const closeModal3 = () => {
    modal3.value = false;
    form3.reset();
}

const save3 = () => {
    if (operation3.value == 1) {
        form3.post(route('tbmarcas.store'), {
            onSuccess: () => { 
                ok3('marca registrada') 
            }
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
    $.ajax({
        url: '/fn_obtenerMarcasRegistradas',
        method: 'GET',
        success: function(response) {
            // Obtener el select
            let selectPresentacion = $('#tbmarca');
            
            // Vaciar el select actual, si es necesario
            selectPresentacion.empty();

            // Agregar la opción inicial "Seleccione tipo"
            selectPresentacion.append($('<option>', {
                value: '0',
                text: 'Seleccione una marca',
                disabled: true,
                selected: true
            }));

            // Iterar sobre los objetos y mostrar sus propiedades
            response.forEach(function(obj) {
                let option = $('<option>', {
                    value: obj.id,
                    text: obj.nombre
                });
                selectPresentacion.append(option);
             });
        }
    });
};

const openModal2 = (op, tbsubcategoria) => {
    modal2.value = true;
    nextTick(() => nameInput2.value.focus());
    operation2.value = op;
    id2.value = tbsubcategoria;
    if (op === 1) {
        title2.value = 'Registrar Subcategoría';
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
    $.ajax({
        url: '/fn_obtenerSubcategoriasRegistradas',
        method: 'GET',
        success: function(response) {
            // Obtener el select
            let selectPresentacion = $('#tbsubcategoria');
            
            // Vaciar el select actual, si es necesario
            selectPresentacion.empty();

            // Agregar la opción inicial "Seleccione tipo"
            selectPresentacion.append($('<option>', {
                value: '0',
                text: 'Seleccione una subcategoria',
                disabled: true,
                selected: true
            }));

            // Iterar sobre los objetos y mostrar sus propiedades
            response.forEach(function(obj) {
                let option = $('<option>', {
                    value: obj.id,
                    text: obj.nombre
                });
                selectPresentacion.append(option);
             });
        }
    });
};

const openModal4 = (op, tbcategoria) => {
    modal4.value = true;
    nextTick(() => nameInput4.value.focus());
    operation4.value = op;
    id4.value = tbcategoria;
    if (op === 1) {
        title4.value = 'Registrar categoria';
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
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-8 mb-3">
                                <div class="flex flex-col items-start">
                                    <InputLabel for="tenor_id" class="block text-xs font-medium text-gray-700">
                                        Categoria
                                    </InputLabel>
                                    <div class="flex w-full">
                                        <select id="tbcategoria" v-model="form.tbcategoria_id" required
                                            @change="updateFilteredSubcategorias"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="" disabled selected>Seleccione una categoría</option>
                                            <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id"
                                                :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                        </select>
                                        <Button @click.prevent="() => openModal4(1)"
                                            class="bg-green-600 text-white mt-1 py-1 w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div>
                                <div class="flex flex-col items-start">
                                    <InputLabel for="tenor_id" class="block text-xs font-medium text-gray-700">
                                        Subcategoria
                                    </InputLabel>
                                    <div class="flex w-full">
                                        <select id="tbsubcategoria" v-model="form.tbsubcategoria_id"
                                            @change="updateFilteredMarcas"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="0" disabled selected>Seleccione una subcategoría</option>
                                            <option v-for="tbsubcategoria in filteredSubcategorias"
                                                :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                                        </select>
                                        <Button @click.prevent="() => openModal2(1)"
                                            class="bg-green-600 mt-1 py-1 text-white w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                            <div class="grid lg:grid-cols-5 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="codigo" class="block text-xs font-medium text-gray-700">Código
                                    </InputLabel>
                                    <TextInput type="text" id="codigo" v-model="form.codigo" required
                                        placeholder="Ingrese el codigo del producto"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.codigo" class="mt-2" />
                                </div>
                                <div class="flex flex-col items-start">
                                    <InputLabel for="tenor_id" class="block text-xs font-medium text-gray-700">
                                        Marca
                                    </InputLabel>
                                    <div class="flex w-full">
                                        <select id="tbmarca" v-model="form.tbmarca_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="0" disabled selected>Seleccione una marca</option>
                                            <option v-for="tbmarca in filteredMarcas" :key="tbmarca.id" :value="tbmarca.id">{{ tbmarca.nombre }}</option>
                                        </select>
                                        <Button @click.prevent="() => openModal3(1)"
                                            class="bg-green-600 py-1 text-white mt-1 w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div>
                                <div>
                                    <InputLabel for="modelo" class="block text-xs font-medium text-gray-700">Modelo
                                    </InputLabel>
                                    <TextInput type="text" id="modelo" v-model="form.modelo"
                                        placeholder="Escribe un modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.modelo" class="mt-2" />
                                </div>
 
                                <div>
                                    <InputLabel for="medida" class="block text-xs font-medium text-gray-700">Medida
                                    </InputLabel>
                                    <TextInput type="text" id="medida" v-model="form.medida"
                                        placeholder="Escribe la medida"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.medida" class="mt-2" />
                                </div>
 
                                <div>
                                    <InputLabel for="moneda" class="block text-xs font-medium text-gray-700">Moneda
                                    </InputLabel>
                                    <select id="moneda" v-model="form.moneda" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona una moneda</option>
                                        <option value="s/">Sol</option>
                                        <option value="$">Dólares</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="precio_min" class="block text-xs font-medium text-gray-700">
                                        Precio_minim
                                    </InputLabel>
                                    <TextInput type="text" id="precio_min" v-model="form.precio_min"
                                        placeholder="escribe solo numeros"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.precio_min" class="mt-2" />
                                </div>
 
                                <div>
                                    <InputLabel for="precio_max" value="Precio_maximo"
                                        class="block text-xs font-medium text-gray-700">
                                    </InputLabel>
                                    <TextInput type="text" id="precio_max" v-model="form.precio_max"
                                        placeholder="escribe solo numeros"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.precio_max" class="mt-2" />
                                </div>
 
                                <div>
                                    <InputLabel for="precio_list" class="block text-xs font-medium text-gray-700">
                                        Precio_Lista
                                    </InputLabel>
                                    <TextInput type="text" id="precio_list" v-model="form.precio_list"
                                        placeholder="escribe solo numeros"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.precio_list" class="mt-2" />
                                </div>
 
                                <div>
                                    <InputLabel for="stock" class="block text-xs font-medium text-gray-700">Stock
                                    </InputLabel>
                                    <TextInput type="number" id="stock" v-model="form.stock" required
                                        placeholder="escribe solo numeros"
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
                            <div class="grid grid-cols-2 gap-y-4 mt-3">
                                <div>
                                    <InputLabel for="link_producto" class="block text-xs font-medium text-gray-700">Link 
                                    </InputLabel>
                                    <TextInput type="text" id="link_producto" v-model="form.link_producto"
                                        placeholder="Escribe el link del producto"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.link_producto" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-4 md:grid-cols-2 lg:grid-cols-3 sm:gap-x-8 mt-2">
                                <div>
                                    <InputLabel for="capacidades" class="block text-xs font-medium text-gray-700">
                                        Capacidades
                                    </InputLabel>
                                    <textarea id="capacidades" v-model="form.capacidades" rows="5"
                                        placeholder="Escribe las capacidades..."
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.capacidades" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="especificaciones" class="block text-xs font-medium text-gray-700">
                                        Especificaciones</InputLabel>
                                    <textarea id="especificaciones" v-model="form.especificaciones" rows="5"
                                        placeholder="Escribe las especificaciones..."
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    <InputError :message="$page.props.errors.especificaciones" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="foto" value="Foto"
                                        class="block text-xs font-medium text-gray-700" />
                                    <FileInput name="foto" @change="(e) => onSelectFoto(e, 'foto')" />
                                    <InputError :message="$page.props.errors.foto" class="mt-2" />
                                    <div class="flex flex-wrap justify-center items-center px-4 py-2" v-if="imagePreview1">
                                        <div class="mt-2">
                                            <img :src="imagePreview1" alt="Vista previa de la foto"
                                            class="p-2 w-40 h-40 object-contain text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-200 dark:bg-gray-600 dark:border-gray-500">
                                        </div>
                                    </div>
                                </div>
                            </div>
 
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive id="btnRegistrarPoductoCatalago">
                                        REGISTRAR
                                    </ButtonResponsive>
                                    <button 
                                        id="loading-button" 
                                        disabled 
                                        type="button" 
                                        class="text-white whitespace-nowrap md:w-min w-full bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 rounded-lg text-sm px-5 py-2 justify-center text-center font-extrabold dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 hidden">
                                        <svg aria-hidden="true" role="status" class="inline w-4 mt-[3px] h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                                        </svg>
                                        Cargando...
                                    </button>
                                    <Link :href="route('tbproductos.index')"
                                    class="inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">
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
                                placeholder="Nombre de la categoria"></TextInput>
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
                                placeholder="Nombre de la Subcategoria"></TextInput>
                        <InputError :message="form2.errors.nombre" class="mt-2"></InputError>
                    </div>
                    <div class="w-full mt-4">
                        <InputLabel for="tbcategoria_id" value="Categoría:" class="mb-2"></InputLabel>
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
                                placeholder="Nombre de la marca"></TextInput>
                        <InputError :message="form3.errors.nombre" class="mt-2"></InputError>
                    </div>
                    <div class="w-full mt-4">
                        <InputLabel for="tbsubcategoria_id" value="SubCategoría:" class="mb-2"></InputLabel>
                        <select id="tbsubcategoria_id" v-model="form3.tbsubcategoria_id" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Seleccione una subcategoría</option>
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