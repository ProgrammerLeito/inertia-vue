<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch, watchEffect, defineProps } from 'vue';
import Swal from 'sweetalert2';
import FileInput from '@/Components/FileInput.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';

const nameInput2 = ref(null);
const modal2 = ref(false);
const title2 = ref('');
const operation2 = ref(1);
const id2 = ref('');
const {servicios,hmarcas}=defineProps({
    servicios: {
        type: Object,
        required: true
    },
    hmarcas:{
        type:Object
    }
});

const initialvalues = {
    servicio_id: '',
    hmarca_id: '',
    modelo: '',
    serie: '',
    div: '',
    capacidad: '',
    plataforma: '',
    fecha: '',
    requiere: '',
    diagnostico: '',
    trabajos: '',
    foto: '',
    foto2: '',
    foto3: '',
};
const form2 = useForm({
    nombre: '',
});

const openModal2 = (op, nombre, hmarca) => {
    modal2.value = true;
    nextTick(() => nameInput2.value.focus());
    operation2.value = op;
    id2.value = hmarca;
    if (op === 1) {
        title2.value = 'Registrar marca';
    }
};

const closeModal2 = () => {
    modal2.value = false;
    form2.reset();
};

const save2 = () => {
    form2.post(route('hmarcas.store'), {
        onSuccess: () => { ok2('marca registrada'); }
    });
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



const form = useForm(initialvalues);
const imagePreview1 = ref('');
const imagePreview2 = ref('');
const imagePreview3 = ref('');
const onSelectFoto = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
        const reader = new FileReader();
        reader.onload = (e) => {
            // Actualiza la vista previa de la imagen correspondiente
            switch (fieldName) {
                case 'foto':
                    imagePreview1.value = e.target.result;
                    break;
                case 'foto2':
                    imagePreview2.value = e.target.result;
                    break;
                case 'foto3':
                    imagePreview3.value = e.target.result;
                    break;
                default:
                    break;
            }
        };
        reader.readAsDataURL(files[0]);

        // También puedes guardar el archivo en el formulario si es necesario
        switch (fieldName) {
            case 'foto':
                form.foto = files[0];
                break;
            case 'foto2':
                form.foto2 = files[0];
                break;
            case 'foto3':
                form.foto3 = files[0];
                break;
            default:
                break;
        }
    }
}

const submitForm = () => {
    form.post(route('hservicios.store'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'El hoja de servico se ha registrado correctamente.',
                timer: 1000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        },
        onError: (errors) => {
            if(errors.response && errors.response.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al registrar la hoja de servicio. Por favor, inténtalo de nuevo.'
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

const setCurrentDate = () => {
    const today = new Date().toISOString().split('T')[0];
    form.fecha = today;
}
onMounted(() => {
    setCurrentDate();
});

const nInformeSeleccionado = computed(() => {
    const servicioSeleccionado = servicios.find(servicio => servicio.id === form.servicio_id);
    return servicioSeleccionado ? servicioSeleccionado.n_informe : '';
});

const razonSocialCliente = computed(() => {
    const servicioSeleccionado = servicios.find(servicio => servicio.id === form.servicio_id);
    return servicioSeleccionado && servicioSeleccionado.cliente ? servicioSeleccionado.cliente.razonSocial : '';
});
watch(form.servicio_id, () => {
    setCurrentDate();
    // Se actualiza automáticamente el número de informe y la razón social del cliente cuando se selecciona un servicio.
});


onMounted(() => {
    const idrequerimiento = localStorage.getItem('idrequerimiento');
    if (idrequerimiento) {
        // console.log('aqui toy',idrequerimiento)
        form.servicio_id = parseInt(idrequerimiento);
    }
    // document.getElementById('servicio_id').val(idrequerimiento);
});

</script>
<template>
    <AppLayout title="Registrar H-servicio-requerimientos ">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Registrar requerimientos | servicio</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">
                            <div class="font-semibold d-flex flex-wrap items-center justify-center text-center dark:text-white py-4">
                                <div class="hidden d-flex">
                                    <select id="servicio_id" v-model="form.servicio_id" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white shadow-sm sm:text-sm border-gray-300 bg-gray-400 dark:bg-gray-800">
                                        <option v-for="servicio in servicios" :key="servicio.id" :value="servicio.id">{{ servicio.n_informe }}</option>
                                    </select>
                                </div>
                            <div class="d-flex">
                                N° INFORME: {{ nInformeSeleccionado }} - {{ razonSocialCliente }}
                            </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-3">
                                <div class="flex flex-col items-start">
                                    <InputLabel for="hmarca_id" value="Marca" class="ml-1"/>
                                    <div class="flex w-full">
                                        <select id="hmarca_id" v-model="form.hmarca_id"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="" disabled selected>Selecciona una marca</option>
                                            <option v-for="hmarca in hmarcas" :key="hmarca.id"
                                                :value="hmarca.id">{{ hmarca.nombre }}</option>
                                        </select>
                                        <Button @click.prevent="() => openModal2(1)"
                                            class="bg-green-600 mt-1 py-1 text-white w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div>
                                <div>
                                    <InputLabel for="serie" value="serie"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.serie" type="text" id="serie"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.serie" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="modelo" value="modelo" />
                                    <TextInput v-model="form.modelo" type="text" id="modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.modelo" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="div" value="div" />
                                    <TextInput v-model="form.div" type="text" id="div"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.div" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="capacidad" value="capacidad" />
                                    <TextInput v-model="form.capacidad" type="text" id="capacidad"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.capacidad" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="plataforma" value="plataforma" />
                                    <TextInput v-model="form.plataforma" type="text" id="capacidad"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.plataforma" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="requiere" value="requiere" />
                                    <select id="moneda" v-model="form.requiere" required
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="">Selecciona una opcion</option>
                                            <option value="REQUIERE MANTENIMIENTO">REQUIERE MANTENIMIENTO</option>
                                            <option value="REQUIERE REPARACION">REQUIERE REPARACION</option>
                                            <option value="POR REVISAR">POR REVISAR</option>
                                            <option value="CERTIFICACION">CERTIFICACION</option>
                                            <option value="GARANTIA">GARANTIA</option>
                                            <option value="IMPLEMENTACION">IMPLEMENTACION</option>
                                            <option value="CALIBRACION">CALIBRACION</option>
                                        </select>
                                    <InputError :message="$page.props.errors.descripcion" class="" />
                                </div>
                                <div>
                                    <InputLabel for="fecha" value="Fecha"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.fecha" type="date" id="fecha"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.fecha" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="diagnostico" value="diagnostico" />
                                    <textarea id="diagnostico" v-model="form.diagnostico" rows="4" class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escriba las diagnostico..."></textarea>
                                    <InputError :message="form.errors.diagnostico" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="trabajos" value="trabajos" />
                                    <textarea id="trabajos" v-model="form.trabajos" rows="4" class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escriba las trabajos..."></textarea>
                                    <InputError :message="form.errors.trabajos" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="mt-0 flex justify-center items-center flex-wrap gap-y-0 sm:gap-x-2">
                                <div class="sm:col-span-1 flex-1 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                                        <InputLabel for="foto" value="Foto" class="block text-xs font-medium text-gray-700"/>
                                        <FileInput name="foto" @change="($event) => onSelectFoto($event, 'foto')"/>
                                        <InputError :message="$page.props.errors.foto" class="mt-2" />
                                        <div class="mt-2 flex items-center justify-center w-full" v-if="form.foto !== ''">
                                            <img :src="imagePreview1" alt="Vista previa de la foto" class="p-2 block w-36 h-36 items-center text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:col-span-1 flex-1 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                                        <InputLabel for="foto2" value="Foto 2" class="block text-xs font-medium text-gray-700"/>
                                        <FileInput name="foto2" @change="($event) => onSelectFoto($event, 'foto2')"/>
                                        <InputError :message="$page.props.errors.foto2" class="mt-2" />
                                        <div class="mt-2 flex items-center justify-center w-full" v-if="form.foto2 !== ''">
                                            <img :src="imagePreview2" alt="Vista previa de la foto 2" class="p-2 block w-36 h-36 items-center text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:col-span-1 flex-1 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                                        <InputLabel for="foto3" value="Foto 3" class="block text-xs font-medium text-gray-700"/>
                                        <FileInput name="foto3" @change="($event) => onSelectFoto($event, 'foto3')"/>
                                        <InputError :message="$page.props.errors.foto3" class="mt-2" />
                                        <div class="mt-2 flex items-center justify-center w-full" v-if="form.foto3 !== ''">
                                            <img :src="imagePreview3" alt="Vista previa de la foto 3" class="p-2 block w-36 h-36 items-center text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive
                                        class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 md:w-min whitespace-nowrap w-full text-center">
                                        GUARDAR
                                    </ButtonResponsive>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    <ModalResponsive :show="modal2" @close="closeModal2">
            <div class="p-4 uppercase">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title2 }}
                </h2>
                <div class="p-1">
                    <div class="w-full">
                        <InputLabel for="nombre" value="Nombre:" class="mb-2"></InputLabel>
                        <TextInput id="nombre" ref="nameInput2" v-model="form2.nombre" type="text" class="w-full"
                            placeholder="Nombre"></TextInput>
                        <InputError :message="form2.errors.nombre" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form2.processing" @click="save2">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation2 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form2.processing" @click="closeModal2">
                        Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
</template>
