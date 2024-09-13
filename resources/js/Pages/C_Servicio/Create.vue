<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import Swal from 'sweetalert2';
import DangerButton from '@/Components/DangerButton.vue';
import { loadImageAsBase64 } from '@/utils/imageUtils';
import { useModalPlantilla } from '@/utils/cotizacionModalP';
import { useModalTrabajo } from '@/utils/cotizaModalTrabajo';
import { useModalCondicione } from '@/utils/cotizaModalCondicione';
import { useModalObservacione } from '@/utils/cotizaModalObservacione';
import { useModalDocumento } from '@/utils/cotizaModalDocumento';
import { useModalRecomendacione } from '@/utils/cotizaModalRecomendacione';
import { useTrealizados, useCselecionados, useOselecionados, useDselecionados, useRselecionados } from '@/utils/cotizaAcoridion';

const { plantillas, trealizados, condiciones, observaciones, documentos, recomendaciones, nCotizacion, ctrabajos } = defineProps({
    plantillas: {
        type: Object,
        required: true
    },
    trealizados: {
        type: Array,
    },
    condiciones: {
        type: Array
    },
    observaciones: {
        type: Array
    },
    documentos: {
        type: Array
    },
    recomendaciones: {
        type: Array
    },
    nCotizacion: {
        type: Object
    },
    ctrabajos: {
        type: Object
    }
});

const form = useForm({
    marca: '',
    modelo: '',
    capacidad: '',
    serie: '',
    div: '',
    plataforma: '',
    requiere: '',
    fecha: ',',
    descripcion: '',
    tselecionado: '',
    cselecionado: '',
    oselecionado: '',
    dselecionado: '',
    rselecionado: '',
    precio: '',
    moneda: '',
    fpago: '',
    garantia: '',
    dentrega: '',
    foto: '',
    foto1: '',
    total: '',
    subtotal: '',
    igv: '',
    plantilla_id: '',
    n_informe: '',
    razonSocial: '',
    n_cotizacion: nCotizacion
});


// // Primer acordeón
const {
    isFirstPairOpen, isSecondPairOpen, selectedTrealizados, selectedTrealizadosText, toggleFirstPair, toggleSecondPair, selectTrealizado, removeTrealizado, clearSelectedTrealizados
} = useTrealizados(form);

// segundo acordeón
const {
    isFirstPairOpen1, isSecondPairOpen1, selectedCselecionados, selectedCselecionadoText, toggleFirstPair1, toggleSecondPair1, selectCselecionado, removeCselecionado, clearSelectedCselecionados
} = useCselecionados(form);

// tercer acordeón
const { isFirstPairOpen2, isSecondPairOpen2, selectedOselecionados, selectedOselecionadoText, toggleFirstPair2, toggleSecondPair2, selectOselecionado, removeOselecionado, clearSelectedOselecionado
} = useOselecionados(form);

// cuarto acordeón
const {
    isFirstPairOpen3, isSecondPairOpen3, selectedDselecionados, selectedDselecionadoText, toggleFirstPair3, toggleSecondPair3, selectDselecionado, removeDselecionado, clearSelectedDselecionado
} = useDselecionados(form);

// quinto acordeón
const {
    isFirstPairOpen4, isSecondPairOpen4, selectedRselecionados, selectedRselecionadoText, toggleFirstPair4, toggleSecondPair4, selectRselecionado, removeRselecionado, clearSelectedRselecionado
} = useRselecionados(form);

//datos locales
onMounted(() => {
    const hservicioData = JSON.parse(localStorage.getItem('hservicioData'));
    if (hservicioData) {
        form.marca = hservicioData.hmarca ? hservicioData.hmarca.nombre : 'Sin marca';
        form.modelo = hservicioData.modelo;
        form.capacidad = hservicioData.capacidad;
        form.serie = hservicioData.serie;
        form.div = hservicioData.div;
        form.plataforma = hservicioData.plataforma;
        form.requiere = hservicioData.requiere;
        form.fecha = hservicioData.fecha;
        form.razonSocial = hservicioData.razonSocial;
        form.n_informe = hservicioData.n_informe;
    }
});

//hora y fecha actual
const setCurrentDate = () => {
    const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Lima' }).split('/').reverse().join('-');
    form.fecha = today;
}
const setCurrentTime = () => {
    const now = new Date();
    const hour = String(now.getHours()).padStart(2, '0');
    const minute = String(now.getMinutes()).padStart(2, '0');
    form.hora = `${hour}:${minute}`;
}
onMounted(() => {
    setCurrentDate();
    setCurrentTime();
});

//modal 3
const {
    nameInput3, modal3, title3, operation3, id3, form3, openModal3, closeModal3, save3, deletePlantilla
} = useModalPlantilla();

//modal4
const {
    nameInput4, modal4, title4, operation4, id4, form4, openModal4, closeModal4, save4, deleteTrealizado
} = useModalTrabajo();

//modal5
const {
    nameInput5, modal5, title5, operation5, id5, form5, openModal5, closeModal5, save5, deleteCondicione
} = useModalCondicione();

//modal6
const {
    nameInput6, modal6, title6, operation6, id6, form6, openModal6, closeModal6, save6, deleteObservacion
} = useModalObservacione();

//modal7
const {
    nameInput7, modal7, title7, operation7, id7, form7, openModal7, closeModal7, save7, deleteDocumento
} = useModalDocumento();

//modal8
const {
    nameInput8, modal8, title8, operation8, id8, form8, openModal8, closeModal8, save8, deleteRecomendacione
} = useModalRecomendacione();

const submitForm = () => {
    form.post(route('c_servicos.store'), {
        onSuccess: () => {
            const Toast = Swal.mixin({
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: 'Éxito',
                text: "La cotizacion se ha registrado correctamente.",
                customClass: {
                    title: 'text-2xl font-bold tracking-widest ',
                    icon: 'text-base font-bold tracking-widest ',
                    text: 'bg-red-500 hover:bg-red-600 tracking-widest ',
                },
            });
        },
        onError: (errors) => {
            if (errors.response && errors.response.status) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "bottom-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: "error",
                    title: 'Error',
                    text: "Ha ocurrido un error al registrar la cotizacion. Por favor, inténtalo de nuevo."
                });
                console.error('Error HTTP:', errors.response.status);
            } else {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "bottom-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: "error",
                    title: 'Error',
                    text: "Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde."
                });
                console.error('Error desconocido:', errors);
            }
        }
    });
}
const { props } = usePage();
const pdfPreview = ref(null);
const user = props.auth.user;

const obtenerNombreCompleto = (user) => {
    if (user) {
        return `${user.name} ${user.apellidopat} ${user.apellidomat}`;
    }
    return '';
};
const nombreCompleto = obtenerNombreCompleto(user);


//modal para calcular tipo moneda en dolares
const igvEnabled = ref(false);
const updateCurrency = () => {
    if (form.moneda === 'dolares') {
        Swal.fire({
            title: 'Ingrese el tipo de cambio del dólar',
            input: 'number',
            inputLabel: 'Tipo de cambio',
            inputPlaceholder: 'Ingrese el tipo de cambio',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Aceptar',
            inputValidator: (value) => {
                if (!value || value <= 0) {
                    return 'Por favor, ingrese un tipo de cambio válido.';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const tipoCambio = result.value;
                form.subtotal = (parseFloat(form.precio) / tipoCambio).toFixed(2);
                // Calcular el valor del IGV y el total automáticamente
                const igv = form.subtotal * 0.18;
                form.igv = igv.toFixed(2);
                form.total = (parseFloat(form.subtotal) + igv).toFixed(2);
            }
        });
    } else {
        form.subtotal = parseFloat(form.precio).toFixed(2);
        form.igv = igvEnabled.value ? (form.subtotal * 0.18).toFixed(2) : 0;
        form.total = (parseFloat(form.subtotal) + parseFloat(form.igv)).toFixed(2);
    }
};

const updateTotal = () => {
    form.igv = igvEnabled.value ? (form.subtotal * 0.18).toFixed(2) : 0;
    form.total = (parseFloat(form.subtotal) + parseFloat(form.igv)).toFixed(2);
};
</script>
<template>
    <AppLayout title="Crear Servicio">
        <template #header>
            <h1 class="font-semibold text-base tracking-widest uppercase text-gray-800 leading-tight dark:text-white">
                datos de
                cotizacion
            </h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto tracking-widest">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div
                    class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel>N° informe</InputLabel>
                                    <TextInput v-model="form.n_informe" type="text"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div>
                                    <InputLabel>N° Cotización</InputLabel>
                                    <TextInput v-model="form.n_cotizacion" type="text"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div>
                                    <InputLabel>cliente</InputLabel>
                                    <TextInput v-model="form.razonSocial" type="text"
                                        class="input w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block  shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel>Descripcion</InputLabel>
                                    <TextInput v-model="form.requiere" type="text"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div>
                                    <InputLabel>fecha</InputLabel>
                                    <TextInput v-model="form.fecha" type="date"
                                        class="input w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block  shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-5 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel>Marca</InputLabel>
                                    <TextInput v-model="form.marca" type="text"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div>
                                    <InputLabel>Modelo</InputLabel>
                                    <TextInput v-model="form.modelo" type="text"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div>
                                    <InputLabel>Capacidad</InputLabel>
                                    <TextInput v-model="form.capacidad" type="text"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div>
                                    <InputLabel>Div</InputLabel>
                                    <TextInput v-model="form.div" type="text"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div>
                                    <InputLabel>Plataforma</InputLabel>
                                    <TextInput v-model="form.plataforma" type="text"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-1 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel>Serie</InputLabel>
                                    <TextInput v-model="form.serie" type="text"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                            </div>
                            <div
                                class="grid grid-cols-1 gap-y-6 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 sm:gap-x-8 mb-3">
                                <div class="flex items-center">
                                    <select id="plantilla" v-model="form.plantilla_id" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block tracking-widest w-full shadow-sm sm:text-sm border-gray-300 rounded-lg">
                                        <option value="" disabled selected>Seleccione una plantilla de cotizacion
                                        </option>
                                        <option v-for="plantilla in plantillas" :key="plantilla.id"
                                            :value="plantilla.id">{{
                                                plantilla.descripcion }}</option>
                                    </select>
                                </div>
                                <div>
                                    <Button @click.prevent="() => openModal3(1)"
                                        class="bg-green-600 text-white tracking-widest hover:bg-green-500  py-3 p-2 rounded-lg">
                                        <i class="fas fa-plus mx-1"></i>Agregar plantilla
                                    </Button>
                                    <button @click.prevent="openModal33"
                                        class="ml-4 bg-red-600 text-white tracking-widest dark:hover:bg-white font-bold bg- py-3 dark:hover:text-red-500 hover:bg-red-200 hover:text-red-600 rounded-lg p-2">
                                        <i class="bi bi-trash3 mx-1"></i>
                                        Eliminar datos</button>
                                </div>
                            </div>

                            <div class="flex flex-wrap justify-between mt-2">
                                <!-- Primer par de acordeones -->
                                <div id="accordion-collapse-1" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-1">
                                        <button type="button"
                                            class="flex items-center justify-between rounded w-full p-3 bg-blue-700 text-white font-medium rtl:text-right dark:text-white border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 hover:bg-blue-500 dark:hover:bg-blue-600 gap-3"
                                            :aria-expanded="isFirstPairOpen" aria-controls="accordion-collapse-body-1">
                                            <Button @click.prevent="() => openModal4(1)"
                                                class="bg-green-600 rounded shadow-lg fa-bounce  hover:bg-green-400 dark:hover:bg-green-400 text-white py-1">
                                                <i class="fas fa-plus  mx-2"></i>
                                            </Button>
                                            <span class="tracking-widest">Trabajos a Realizar</span>
                                            <svg @click.prevent="toggleFirstPair"
                                                :class="{ 'rotate-180': isFirstPairOpen }" data-accordion-icon
                                                class="w-4 h-4 font-bold shrink-0 " aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isFirstPairOpen" id="accordion-collapse-body-1"
                                        aria-labelledby="accordion-collapse-heading-1"
                                        class="h-60 shadow overflow-y-auto">
                                        <div
                                            class="p-5 border justify-between  border-t-0 border-gray-200 dark:border-gray-700">
                                            <tr v-for="(trealizado, i) in trealizados" :key="trealizado.id"
                                                class="text-white dark:bg-gray-600 dark:hover:bg-gray-500 text-justify  hover:bg-gray-800 dark:hover:text-yellow-400 cursor-pointer flex bg-gray-400 p-1 mb-1 rounded dark:text-white"
                                                @dblclick="selectTrealizado(trealizado)">
                                                <td class="px-1 ">{{ i + 1 }}</td>
                                                <td class="px-3 ">{{ trealizado.descripcion }}</td>
                                                <td class="p-1  flex flex-wrap items-center justify-end">
                                                    <Button
                                                        @click.prevent="openModal4(2, trealizado.descripcion, trealizado.categoria, trealizado.id)"
                                                        class="bg-green-600 rounded dark:text-white shadow-lg p-1 hover:bg-green-400 dark:hover:bg-green-400 text-white">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </Button>
                                                    <Button
                                                        @click.prevent="$event => deleteTrealizado(trealizado.id, trealizado.descripcion)"
                                                        class="ml-1 bg-red-600 rounded dark:text-white shadow-lg p-1 hover:bg-red-400 dark:hover:bg-red-400 text-white">
                                                        <i class="bi bi-trash3"></i>
                                                    </Button>
                                                </td>
                                            </tr>
                                        </div>
                                    </div>
                                </div>

                                <div id="accordion-collapse-2" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-2">
                                        <button type="button"
                                            class="flex items-center justify-between rounded w-full bg-blue-700 text-white p-3 font-medium rtl:text-right dark:text-white border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 hover:bg-blue-500 dark:hover:bg-blue-600 gap-3"
                                            :aria-expanded="isFirstPairOpen" aria-controls="accordion-collapse-body-2">
                                            <Button @click.prevent="clearSelectedTrealizados"
                                                class="dark:hover:bg-red-400 tracking-widest text-white rounded dark:text-white px-2 bg-red-600 py-1">
                                                <i class="bi bi-x-circle"></i> Quitar Todos
                                            </Button>
                                            <span class="tracking-widest">Trabajos seleccionados</span>
                                            <svg @click="toggleFirstPair" :class="{ 'rotate-180': isFirstPairOpen }"
                                                data-accordion-icon class="w-4 h-4 font-bold shrink-0"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isFirstPairOpen" id="accordion-collapse-body-2"
                                        aria-labelledby="accordion-collapse-heading-2">
                                        <div class="p-5 border border-t-0 border-gray-200 dark:border-gray-700">
                                            <textarea id="tselecionado" v-model="form.tselecionado" rows="10"
                                                placeholder="Escribe las especificaciones..."
                                                class="mt-1 focus:ring-indigo-500 tracking-widest  focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md "></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap justify-between mt-5">
                                <!-- Segundo par de acordeones -->
                                <div id="accordion-collapse-3" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-3">
                                        <button type="button"
                                            class="flex items-center justify-between rounded w-full p-3 bg-green-600 font-medium rtl:text-right text-white border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-white hover:bg-green-500 dark:hover:bg-green-500 gap-3"
                                            :aria-expanded="isSecondPairOpen1"
                                            aria-controls="accordion-collapse-body-3">
                                            <Button @click.prevent="() => openModal5(1)"
                                                class="bg-green-700 rounded shadow-lg hover:bg-green-400 dark:hover:bg-green-400 text-white mt- py-1 ">
                                                <i class="fas fa-plus mx-2"></i>
                                            </Button>
                                            <span class="tracking-widest">Condiciones</span>
                                            <svg @click.prevent="toggleSecondPair1"
                                                :class="{ 'rotate-180': isSecondPairOpen1 }" data-accordion-icon
                                                class="w-4 h-4 shrink-0 font-bold" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isSecondPairOpen1" id="accordion-collapse-body-3"
                                        aria-labelledby="accordion-collapse-heading-3">
                                        <div class="p-5 border border-t-0  border-gray-200 dark:border-gray-700">
                                            <table>
                                                <tr v-for="(condicione, i) in condiciones" :key="condicione.id"
                                                    class="text-white dark:bg-gray-600 dark:hover:bg-gray-500 hover:bg-gray-800 dark:hover:text-yellow-400 cursor-pointer flex items-center justify-between bg-gray-400 p-1 mb-1 rounded dark:text-white"
                                                    @dblclick="selectCselecionado(condicione)">
                                                    <td class="px-1 text-center">{{ i + 1 }}</td>
                                                    <td class="px-1 ">{{ condicione.descripcion }}</td>
                                                    <td
                                                        class="p-1 text-center flex flex-wrap items-center justify-center">
                                                        <Button
                                                            @click.prevent="openModal5(2, condicione.descripcion, condicione.categoria, condicione.id)"
                                                            class="bg-green-600 rounded dark:text-white shadow-lg p-1 hover:bg-green-400 dark:hover:bg-green-400 text-white">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </Button>
                                                        <Button
                                                            @click.prevent="$event => deleteCondicione(condicione.id, condicione.descripcion)"
                                                            class="ml-1 p-1 bg-red-600 rounded dark:text-white shadow-lg hover:bg-red-400 dark:hover:bg-red-400 text-white">
                                                            <i class="bi bi-trash3"></i>
                                                        </Button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="accordion-collapse-4" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-4">
                                        <button @click="toggleSecondPair1" type="button"
                                            class="flex items-center justify-between rounded w-full p-3 bg-green-600 font-medium rtl:text-right text-white border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-white hover:bg-green-500 dark:hover:bg-green-500 gap-3"
                                            :aria-expanded="isSecondPairOpen1"
                                            aria-controls="accordion-collapse-body-4">
                                            <Button @click.prevent="clearSelectedCselecionados"
                                                class="dark:hover:bg-red-400 tracking-widest text-white rounded dark:text-white px-2 bg-red-600  py-1">
                                                <i class="bi bi-x-circle"></i> Quitar Todos
                                            </Button>
                                            <span class="tracking-widest">Condiciones seleccionadas</span>
                                            <svg :class="{ 'rotate-180': isSecondPairOpen1 }" data-accordion-icon
                                                class="w-4 h-4 shrink-0 font-bold" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isSecondPairOpen1" id="accordion-collapse-body-4"
                                        aria-labelledby="accordion-collapse-heading-4">
                                        <div class="p-5 border border-t-0 border-gray-200 dark:border-gray-700">

                                            <textarea id="cselecionado" v-model="form.cselecionado" rows="5"
                                                placeholder="Escribe las especificaciones..."
                                                class="mt-1 focus:ring-indigo-500 tracking-widest  focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap justify-between mt-5">
                                <!-- tercer par de acordeones -->
                                <div id="accordion-collapse-3" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-3">
                                        <button type="button"
                                            class="flex items-center justify-between rounded w-full p-3 bg-yellow-600 font-medium rtl:text-right text-white border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-white hover:bg-yellow-500  dark:hover:bg-yellow-500  gap-3"
                                            :aria-expanded="isSecondPairOpen2"
                                            aria-controls="accordion-collapse-body-3">
                                            <Button @click.prevent="() => openModal6(1)"
                                                class="bg-green-600 rounded shadow-lg hover:bg-green-400 dark:hover:bg-green-400 text-white mt- py-1 ">
                                                <i class="fas fa-plus mx-2"></i>
                                            </Button>
                                            <span class="tracking-widest">Observaciones</span>
                                            <svg @click.prevent="toggleSecondPair2"
                                                :class="{ 'rotate-180': isSecondPairOpen2 }" data-accordion-icon
                                                class="w-4 h-4 shrink-0 font-bold" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isSecondPairOpen2" id="accordion-collapse-body-3"
                                        aria-labelledby="accordion-collapse-heading-3">
                                        <div class="p-5 border border-t-0  border-gray-200 dark:border-gray-700">
                                            <table>
                                                <tr v-for="(observacione, i) in observaciones" :key="observacione.id"
                                                    class="text-white dark:bg-gray-600 dark:hover:bg-gray-500 hover:bg-gray-800 dark:hover:text-yellow-400 cursor-pointer flex items-center justify-between bg-gray-400 p-1 mb-1 rounded dark:text-white"
                                                    @dblclick="selectOselecionado(observacione)">
                                                    <td class="px-1text-center">{{ i + 1 }}</td>
                                                    <td class="px-2 ">{{ observacione.descripcion }}</td>
                                                    <td
                                                        class="p-2 text-center flex flex-wrap items-center justify-center">
                                                        <Button
                                                            @click.prevent="openModal6(2, observacione.descripcion, observacione.categoria, observacione.id)"
                                                            class="bg-green-600 rounded dark:text-white shadow-lg p-1 hover:bg-green-400 dark:hover:bg-green-400 text-white">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </Button>
                                                        <Button
                                                            @click.prevent="$event => deleteObservacion(observacione.id, observacione.descripcion)"
                                                            class="ml-1 p-1 bg-red-700 dark:text-white text-white dark:hover:bg-red-400 rounded">
                                                            <i class="bi bi-trash3"></i>
                                                        </Button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="accordion-collapse-4" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-4">
                                        <button type="button"
                                            class="flex items-center justify-between rounded w-full p-3 bg-yellow-600  font-medium rtl:text-right text-white border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-white hover:bg-yellow-500  dark:hover:bg-yellow-500  gap-3"
                                            :aria-expanded="isSecondPairOpen2"
                                            aria-controls="accordion-collapse-body-4">
                                            <Button @click.prevent="clearSelectedOselecionado"
                                                class="dark:hover:bg-red-400 tracking-widest text-white rounded dark:text-white px-2 bg-red-600  py-1">
                                                <i class="bi bi-x-circle"></i> Quitar Todos
                                            </Button>
                                            <span class="tracking-widest">Observaciones seleccionadas</span>
                                            <svg @click="toggleSecondPair2" :class="{ 'rotate-180': isSecondPairOpen2 }"
                                                data-accordion-icon class="w-4 h-4 shrink-0 font-bold"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isSecondPairOpen2" id="accordion-collapse-body-4"
                                        aria-labelledby="accordion-collapse-heading-4">
                                        <div class="p-5 border border-t-0 border-gray-200 dark:border-gray-700">
                                            <textarea id="oselecionado" v-model="form.oselecionado" rows="5"
                                                placeholder="Escribe las especificaciones..."
                                                class="mt-1 focus:ring-indigo-500 tracking-widest  focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-between mt-5">
                                <!-- cuarto par de acordeones -->
                                <div id="accordion-collapse-3" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-3">
                                        <button type="button"
                                            class="flex items-center justify-between rounded w-full p-3 bg-blue-500 font-medium rtl:text-right text-white border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-white hover:bg-blue-400 dark:hover:bg-blue-400 gap-3"
                                            :aria-expanded="isSecondPairOpen3"
                                            aria-controls="accordion-collapse-body-3">
                                            <Button @click.prevent="() => openModal7(1)"
                                                class="bg-green-600 rounded shadow-lg hover:bg-green-400 dark:hover:bg-green-400 text-white mt- py-1 ">
                                                <i class="fas fa-plus mx-2"></i>
                                            </Button>
                                            <span class="tracking-widest">Documentos</span>
                                            <svg @click.prevent="toggleSecondPair3"
                                                :class="{ 'rotate-180': isSecondPairOpen3 }" data-accordion-icon
                                                class="w-4 h-4 shrink-0 font-bold" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isSecondPairOpen3" id="accordion-collapse-body-3"
                                        aria-labelledby="accordion-collapse-heading-3">
                                        <div class="p-5 border border-t-0  border-gray-200 dark:border-gray-700">
                                            <table>
                                                <tr v-for="(documento, i) in documentos" :key="documento.id"
                                                    class="text-white dark:bg-gray-600 dark:hover:bg-gray-500 hover:bg-gray-800 dark:hover:text-yellow-400 cursor-pointer flex items-center justify-between bg-gray-400 p-1 mb-1 rounded dark:text-white"
                                                    @dblclick="selectDselecionado(documento)">
                                                    <td class="px-1 py-4 text-center">{{ i + 1 }}</td>
                                                    <td class="px-3 py-4">{{ documento.descripcion }}</td>
                                                    <td
                                                        class="p-3 text-center flex flex-wrap items-center justify-center">
                                                        <Button
                                                            @click.prevent="openModal7(2, documento.descripcion, documento.id)"
                                                            class="bg-green-600 rounded dark:text-white shadow-lg p-1 hover:bg-green-400 dark:hover:bg-green-400 text-white">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </Button>
                                                        <Button
                                                            @click.prevent="$event => deleteDocumento(documento.id, documento.descripcion)"
                                                            class="ml-1 p-1 bg-red-700 dark:text-white text-white dark:hover:bg-red-400 rounded">
                                                            <i class="bi bi-trash3"></i>
                                                        </Button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="accordion-collapse-4" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-4">
                                        <button type="button"
                                            class="flex items-center justify-between rounded w-full p-3 bg-blue-500 font-medium rtl:text-right text-white border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-white hover:bg-blue-400 dark:hover:bg-blue-400 gap-3"
                                            :aria-expanded="isSecondPairOpen3"
                                            aria-controls="accordion-collapse-body-4">
                                            <Button @click.prevent="clearSelectedDselecionado"
                                                class="dark:hover:bg-red-400 tracking-widest text-white rounded dark:text-white px-2 bg-red-600  py-1">
                                                <i class="bi bi-x-circle"></i> Quitar Todos
                                            </Button>
                                            <span class="tracking-widest">Documentos seleccionados</span>
                                            <svg @click="toggleSecondPair3" :class="{ 'rotate-180': isSecondPairOpen3 }"
                                                data-accordion-icon class="w-4 h-4 shrink-0 font-bold"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isSecondPairOpen3" id="accordion-collapse-body-4"
                                        aria-labelledby="accordion-collapse-heading-4">
                                        <div class="p-5 border border-t-0 border-gray-200 dark:border-gray-700">
                                            <textarea id="dselecionado" v-model="form.dselecionado" rows="5"
                                                placeholder="Escribe las especificaciones..."
                                                class="mt-1 focus:ring-indigo-500 tracking-widest  focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-between mt-5">
                                <!-- quinto par de acordeones -->
                                <div id="accordion-collapse-3" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-3">
                                        <button type="button"
                                            class="flex items-center justify-between rounded w-full p-3 bg-red-500 font-medium rtl:text-right text-white border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-white hover:bg-red-500 dark:hover:bg-red-400 gap-3"
                                            :aria-expanded="isSecondPairOpen4"
                                            aria-controls="accordion-collapse-body-3">
                                            <Button @click.prevent="() => openModal8(1)"
                                                class="bg-green-600 rounded shadow-lg hover:bg-green-400 dark:hover:bg-green-400 text-white mt- py-1 ">
                                                <i class="fas fa-plus mx-2"></i>
                                            </Button>
                                            <span class="tracking-widest">Recomendaciones</span>
                                            <svg @click.prevent="toggleSecondPair4"
                                                :class="{ 'rotate-180': isSecondPairOpen4 }" data-accordion-icon
                                                class="w-4 h-4 shrink-0 font-bold" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isSecondPairOpen4" id="accordion-collapse-body-3"
                                        aria-labelledby="accordion-collapse-heading-3">
                                        <div class="p-5 border border-t-0  border-gray-200 dark:border-gray-700">
                                            <table>
                                                <tr v-for="(recomendacione, i) in recomendaciones"
                                                    :key="recomendacione.id"
                                                    class="text-white dark:bg-gray-600 dark:hover:bg-gray-500 hover:bg-gray-800 dark:hover:text-yellow-400 cursor-pointer flex items-center justify-between bg-gray-400 p-1 mb-1 rounded dark:text-white"
                                                    @dblclick="selectRselecionado(recomendacione)">
                                                    <td class="px-1 py-4 text-center">{{ i + 1 }}</td>
                                                    <td class="px-3 py-4">{{ recomendacione.descripcion }}</td>
                                                    <td
                                                        class="p-3 text-center flex flex-wrap items-center justify-center">
                                                        <Button
                                                            @click.prevent="openModal8(2, recomendacione.descripcion, recomendacione.id)"
                                                            class="bg-green-600 rounded dark:text-white shadow-lg p-1 hover:bg-green-400 dark:hover:bg-green-400 text-white">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </Button>
                                                        <Button
                                                            @click.prevent="$event => deleteRecomendacione(recomendacione.id, recomendacione.descripcion)"
                                                            class="ml-1 p-1 bg-red-700 dark:text-white text-white dark:hover:bg-red-400 rounded">
                                                            <i class="bi bi-trash3"></i>
                                                        </Button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="accordion-collapse-4" class="w-full lg:w-1/2" data-accordion="collapse">
                                    <h2 id="accordion-collapse-heading-4">
                                        <button type="button"
                                            class="flex items-center justify-between rounded w-full p-3 bg-red-500 font-medium rtl:text-right text-white border border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-white hover:bg-red-500 dark:hover:bg-red-400 gap-3"
                                            :aria-expanded="isSecondPairOpen4"
                                            aria-controls="accordion-collapse-body-4">
                                            <Button @click.prevent="clearSelectedRselecionado"
                                                class="dark:hover:bg-white tracking-widest  text-white dark:hover:text-red-500 hover:bg-red-200 hover:text-red-500 rounded dark:text-white px-2 bg-red-600  py-1">
                                                <i class="bi bi-x-circle "></i> Quitar Todos
                                            </Button>
                                            <span class="tracking-widest">Rcomndaciones slccionadas</span>
                                            <svg @click="toggleSecondPair4" :class="{ 'rotate-180': isSecondPairOpen4 }"
                                                data-accordion-icon class="w-4 h-4 shrink-0 font-bold"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                            </svg>
                                        </button>
                                    </h2>
                                    <div v-show="isSecondPairOpen4" id="accordion-collapse-body-4"
                                        aria-labelledby="accordion-collapse-heading-4">
                                        <div class="p-5 border border-t-0 border-gray-200 dark:border-gray-700">
                                            <textarea id="rselecionado" v-model="form.rselecionado" rows="5"
                                                placeholder="Escribe las especificaciones..."
                                                class="mt-1 focus:ring-indigo-500 tracking-widest  focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-5 sm:gap-x-8 mt-8 mb-3">
                                <div>
                                    <InputLabel>Precios</InputLabel>
                                    <TextInput v-model="form.precio" type="number" placeholder="Ingrese el precio"
                                        @input="updateTotal"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.precio" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="moneda" class="block font-medium text-gray-900">Moneda</InputLabel>
                                    <select id="moneda" v-model="form.moneda" required @change="updateCurrency"
                                        class="mt-1 focus:ring-indigo-500 tracking-widest focus:border-indigo-500 block w-full cursor-pointer shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona una moneda</option>
                                        <option value="soles">Soles</option>
                                        <option value="dolares">Dólares</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="fpago" class="block font-medium text-gray-900">Forma
                                        de pago
                                    </InputLabel>
                                    <select id="fpago" v-model="form.fpago" required
                                        class="mt-1 focus:ring-indigo-500 tracking-widest focus:border-indigo-500 cursor-pointer block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona forma de pago</option>
                                        <option value="Al contado">Al contado</option>
                                        <option value="Credito 15 dias">Credito 15 dias</option>
                                        <option value="Credito 30 dias">Credito 30 dias</option>
                                        <option value="Credito 60 dias">Credito 60 dias</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="garantia" class="block  font-medium text-gray-900">Garantia
                                    </InputLabel>
                                    <select id="garantia" v-model="form.garantia" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona una opcion</option>
                                        <option value="3 meses">3 meses</option>
                                        <option value="6 meses">6 meses</option>
                                        <option value="1 año">1 año</option>
                                        <option value="sin garantia">Sin garantia</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="dentrega" class="block font-medium text-gray-900">Días
                                        entrega</InputLabel>
                                    <select id="dentrega" v-model="form.dentrega" required
                                        class="mt-1 focus:ring-indigo-500 cursor-pointer focus:border-indigo-500 block w-full tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona días de entrega</option>
                                        <!-- Generar opciones del 1 al 31 -->
                                        <option v-for="dia in 31" :key="dia" :value="dia">{{ dia }} día{{ dia > 1 ? 's'
                                            : '' }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel>Subtotal</InputLabel>
                                    <TextInput v-model="form.subtotal" type="number"
                                        :readonly="form.moneda === 'dolares'" disabled
                                        class="input mt-1 focus:ring-indigo-500 bg-gray-400 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.subtotal" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel>Igv</InputLabel>
                                    <div class="flex items-center">
                                        <input type="checkbox" v-model="igvEnabled" @change="updateTotal"
                                            class="form-checkbox rounded-full h-8 w-8 text-indigo-600" />
                                        <TextInput v-model="form.igv" type="number"
                                            :readonly="form.moneda === 'dolares'" disabled
                                            class="input mt-1 ml-2 focus:ring-indigo-500 bg-gray-400 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <InputError :message="$page.props.errors.igv" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel>Total</InputLabel>
                                    <TextInput v-model="form.total" type="number" :readonly="form.moneda === 'dolares'"
                                        disabled
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-400 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.total" class="mt-2" />
                                </div>
                            </div>
                            <div class="text-end">
                                <iframe ref="pdfPreview" class="hidden" width="100%" height="500"></iframe>
                                <PrimaryButton class="mt-3 tracking-widest ml-2 mx-2"
                                    >Ver
                                    PDF</PrimaryButton>
                                <PrimaryButton class="mt-3 tracking-widest">
                                    Generar cotizacion
                                </PrimaryButton>
                                <Link :href="route('c_servicos.index')"
                                    class="text-white ml-1 bg-red-700 hover:bg-red-500 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                <i class="fas fa-arrow-left  mx-2"></i>cancelar
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <ModalResponsive :show="modal3" @close="closeModal3">
            <div class="p-4 uppercase tracking-widest">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title3 }}
                </h2>
                <hr>
                <div class="p-1 py-4">
                    <div class="w-full">
                        <InputLabel for="descripcion" value="descripcion:" class="mb-4"></InputLabel>
                        <TextInput id="descripcion" ref="nameInput3" v-model="form3.descripcion" type="text"
                            class="w-full mb-4" placeholder="descripcion"></TextInput>
                        <InputError :message="form3.errors.descripcion" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form3.processing" @click="save3">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation3 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form3.processing" @click="closeModal3">
                        Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
        <ModalResponsive :show="modal4" @close="closeModal4">
            <div class="p-4 uppercase tracking-widest">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title4 }}
                </h2>
                <hr>
                <div class="p-1 py-4">
                    <InputLabel for="ctrabajo_id" value="categoria" class="block mb-4 font-medium text-gray-900" />
                    <select id="plantilla" v-model="form4.ctrabajo_id" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 text-black block tracking-widest w-full shadow-sm sm:text-sm border-gray-300 rounded-lg">
                        <option value="" disabled selected>Seleccione una opcion</option>
                        <option v-for="ctrabajo in ctrabajos" :key="ctrabajo.id" :value="ctrabajo.id">{{
                            ctrabajo.categoria }}</option>
                    </select>
                </div>
                <div class="p-1">
                    <div class="w-full">
                        <InputLabel for="descripcion" value="descripcion:" class="mb-4"></InputLabel>
                        <TextInput id="descripcion" ref="nameInput4" v-model="form4.descripcion" type="text"
                            class="w-full mb-4" placeholder="descripcion"></TextInput>
                        <InputError :message="form4.errors.descripcion" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form4.processing" @click="save4">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation4 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form4.processing" @click="closeModal4">
                        Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
        <ModalResponsive :show="modal5" @close="closeModal5">
            <div class="p-4 uppercase tracking-widest">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title5 }}
                </h2>
                <hr>
                <div class="p-1 py-4">
                    <InputLabel for="categoria" value="categoria" class="block mb-4 font-medium text-gray-900" />
                    <select id="categoria" v-model="form5.categoria" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block mb-2 tracking-widest w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="" disabled selected>Selecciona una opcion</option>
                        <option value="SERVICIO">SERVICIO</option>
                        <option value="CALIBRACION">CALIBRACION</option>
                    </select>
                </div>
                <div class="p-1">
                    <div class="w-full">
                        <InputLabel for="descripcion" value="descripcion:" class="mb-4"></InputLabel>
                        <TextInput id="descripcion" ref="nameInput5" v-model="form5.descripcion" type="text"
                            class="w-full mb-4" placeholder="descripcion"></TextInput>
                        <InputError :message="form5.errors.descripcion" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form5.processing" @click="save5">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation5 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form5.processing" @click="closeModal5">
                        Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
        <ModalResponsive :show="modal6" @close="closeModal6">
            <div class="p-4 uppercase tracking-widest">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title6 }}
                </h2>
                <hr>
                <div class="p-1 py-4">
                    <InputLabel for="categoria" value="categoria" class="block font-medium text-gray-900" />
                    <select id="categoria" v-model="form6.categoria" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 tracking-widest block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="" disabled selected>Selecciona una opcion</option>
                        <option value="SERVICIO">SERVICIO</option>
                        <option value="CALIBRACION">CALIBRACION</option>
                    </select>
                </div>
                <div class="p-1">
                    <div class="w-full">
                        <InputLabel for="descripcion" value="descripcion:" class="mb-2"></InputLabel>
                        <TextInput id="descripcion" ref="nameInput6" v-model="form6.descripcion" type="text"
                            class="w-full mb-4" placeholder="descripcion"></TextInput>
                        <InputError :message="form6.errors.descripcion" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form6.processing" @click="save6">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation6 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form6.processing" @click="closeModal6">
                        Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
        <ModalResponsive :show="modal7" @close="closeModal7">
            <div class="p-4 uppercase tracking-widest">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title7 }}
                </h2>
                <hr>
                <div class="p-1 py-4">
                    <div class="w-full">
                        <InputLabel for="descripcion" value="descripcion:" class="mb-2"></InputLabel>
                        <TextInput id="descripcion" ref="nameInput7" v-model="form7.descripcion" type="text"
                            class="w-full mb-4" placeholder="descripcion"></TextInput>
                        <InputError :message="form7.errors.descripcion" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form7.processing" @click="save7">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation7 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form7.processing" @click="closeModal7">
                        Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
        <ModalResponsive :show="modal8" @close="closeModal8">
            <div class="p-4 uppercase tracking-widest">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title8 }}
                </h2>
                <hr>
                <div class="p-1 py-4">
                    <div class="w-full">
                        <InputLabel for="descripcion" value="descripcion:" class="mb-2"></InputLabel>
                        <TextInput id="descripcion" ref="nameInput8" v-model="form8.descripcion" type="text"
                            class="w-full mb-4" placeholder="descripcion"></TextInput>
                        <InputError :message="form8.errors.descripcion" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form8.processing" @click="save8">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation8 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form8.processing" @click="closeModal8">
                        Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
        <div v-if="isModalVisible"
            class="modal fixed inset-0 flex items-center text-justify p-3 justify-center bg-black bg-opacity-40"
            @click.self="closeModal33">
            <div
                class="bg-white p-6 rounded tracking-widest dark:bg-gray-900   shadow-xl w-5/4 sm:w-5/4  md:w-5/4 lg:w-5/4 xl:w-5/4 2xl:w-5/4 ">
                <div class="flex justify-between my-1">
                    <div class="sm:text-md md:text-xl dark:text-white uppercase xl:text-2xl font-bold ">Todas las
                        plantillas
                    </div>
                    <span
                        class="float-right text-gray-900 py-1 bg-gray-300 dark:bg-white dark:hover:bg-red-500 dark:hover:text-white  hover:bg-red-500 hover:text-white rounded p-2 mx-3 text-2xl cursor-pointer"
                        @click="closeModal33">&times;</span>
                </div>
                <hr>
                <div
                    class="p-5 border border-t-0 sm:text-xs md:text-xs lg:text-sm xl:text-sm   border-gray-200 dark:border-gray-100 mt-3">
                    <p class="py-3 font-bold text-blue-700 underline uppercase">lista de plantillas guardado en el
                        sistema</p>
                    <table>
                        <tr v-for="(plantilla, i) in plantillas" :key="plantilla.id"
                            class="text-white dark:bg-gray-800 dark:hover:bg-gray-500  dark:hover:text-yellow-400 flex items-center justify-between bg-gray-400 p-1 mb-1 rounded dark:text-white">
                            <td class="px-1 text-center">{{ i + 1 }}</td>
                            <td class="px-2 ">{{ plantilla.descripcion }}</td>
                            <td class="p-2 text-center flex flex-wrap items-center justify-center">
                                <Button @click.prevent="$event => deletePlantilla(plantilla.id, plantilla.descripcion)"
                                    class="ml-1 p-1 bg-red-700 dark:text-white text-white hover:bg-red-400 font-bold cursor-pointer  dark:hover:bg-white dark:hover:text-red-500 rounded">
                                    <i class="bi bi-trash3"></i>
                                </Button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script>
export default {
    name: 'App',
    data() {
        return {
            isModalVisible: false
        };
    },
    methods: {
        openModal33() {
            this.isModalVisible = true;
        },
        closeModal33() {
            this.isModalVisible = false;
        },
    }
}
</script>
