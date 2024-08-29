<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue';

const props = defineProps({
    hojaservicios: {
        type: Object,
        required: true,
    }
});
const form = useForm({
    razon_social: '',
    cantidad: '',
    descripcion: '',
    fecha: '',
    lugar: '',
    contacto: '',
    nro_contacto: ''
});

const isEditing = ref(false);
const hojaservicio = ref(null);

// Definir la variable para la fecha seleccionada
const fechaSeleccionada = ref('');

const filtrarPorFecha = () => {
    router.get(route('hojasservicios.index'), { fecha: fechaSeleccionada.value });
};

const setCurrentDate = () => {
    const today = new Date().toISOString().split('T')[0];
    form.fecha = today; // Asigna la fecha actual al campo de registro
};

// Función para establecer la fecha seleccionada en el filtro de fechas
const setFechaSeleccionada = () => {
    const queryParams = new URLSearchParams(window.location.search);
    const fechaEnUrl = queryParams.get('fecha');
    
    if (fechaEnUrl) {
        fechaSeleccionada.value = fechaEnUrl; // Si hay una fecha en la URL, úsala
    } else {
        const today = new Date().toISOString().split('T')[0];
        fechaSeleccionada.value = today; // De lo contrario, usa la fecha actual
    }
};

onMounted(() => {
    setCurrentDate();         // Establece la fecha de registro
    setFechaSeleccionada();   // Establece la fecha para el filtrado
});

const editHojaServicio = (hojaservicio) => {
    form.id = hojaservicio.id;
    form.razon_social = hojaservicio.razon_social;
    form.cantidad = hojaservicio.cantidad;
    form.descripcion = hojaservicio.descripcion;
    form.fecha = hojaservicio.fecha;
    form.lugar = hojaservicio.lugar;
    form.contacto = hojaservicio.contacto;
    form.nro_contacto = hojaservicio.nro_contacto;
    isEditing.value = true;
};

const submitForm = () => {
    if (!form.id) {
        form.post(route('hojasservicios.store'), {
            onSuccess: () => {
                form.reset();
                setCurrentDate();
                setCurrentTime();
                showSuccessMessage('Hoja de servicio creada exitosamente.');
                form.id = null;
            },
        });
    } else {
        form.put(route('hojasservicios.update', form.id), {
            onSuccess: () => {
                form.reset();
                setCurrentDate();
                setCurrentTime();
                showSuccessMessage('Hoja de servicio actualizada exitosamente.');
                form.id = null;
                isEditing.value = false;
            },
        });
    }
};

const showSuccessMessage = (message) => {
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
        text: message,
        customClass: {
            title: 'text-2xl font-bold tracking-widest ',
            text: 'text-2xl font-bold tracking-widest ',
            icon: 'text-base font-bold tracking-widest ',
        },
    });
};

const form2 = useForm({
    id: ''
})

const deleteHojaServicio = (id, razon_social) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar ala hoja de servicio definitivamente : ' + razon_social + '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
        customClass: {
            title: 'text-xl font-bold tracking-widest ',
            cancelButton: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form2.delete(route('hojasservicios.destroy', id), {
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
                        text: "Hoja de servicio eliminado exitosamente.",
                        customClass: {
                            title: 'text-2xl font-bold tracking-widest ',
                            icon: 'text-base font-bold tracking-widest ',
                            text: 'bg-red-500 hover:bg-red-600 tracking-widest ',
                        },
                    });
                }
            });
        }
    });
}

</script>
<template>
    <AppLayout title="Hoja de Servicio">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Cuadro de Pendientes</h1>
        </template>

         <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-100 shadow-lg shadow-gray-600 dark:bg-gray-800  dark:shadow-gray-800 rounded-lg">
                    <form @submit.prevent="submitForm" class="uppercase font-bol">
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 md:grid-cols-3 sm:gap-x-8 mb-3">
                            <div>
                                <InputLabel for="razon_social" value="razon social *"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.razon_social" type="text" id="razon_social" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.razon_social" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="cantidad" value="cantidad"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.cantidad" type="text" id="cantidad" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.cantidad" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="contacto" value="contacto"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.contacto" type="text" id="contacto" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.contacto" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 md:grid-cols-3 md:gap-x-8 gap-x-0 mb-3">
                            <div>
                                <InputLabel for="nro_contacto" value="N° contacto"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.nro_contacto" type="text" id="nro_contacto" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.nro_contacto" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="lugar" value="lugar"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.lugar" type="text" id="lugar" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.lugar" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="fecha" value="fecha"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.fecha" type="date" id="fecha" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.fecha" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 md:grid-cols-3 mb-3">
                            <div class="col-span-2">
                                <InputLabel for="descripcion" value="descripcion"
                                    class="block text-md font-medium text-gray-700 " />
                                <textarea v-model="form.descripcion" type="text" id="descripcion" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.descripcion" class="mt-2"></InputError>
                            </div>
                            <div class="flex flex-wrap gap-2 justify-end items-end">
                                <ButtonResponsive v-if="!isEditing" @dblclick="editHojaServicio(hojaservicio)" class="uppercase text-xs">Generar Hoja de Servicio</ButtonResponsive>
                                <ButtonResponsive v-if="isEditing" @click="updateHojaServicio()" class="uppercase text-xs">Actualizar Hoja de Servicio</ButtonResponsive>
                            </div>
                        </div>
                    </form>
                    <div class="flex sm:flex-row flex-col justify-between items-center md:py-4 gap-y-2 py-2">
                        <h1 class="md:text-lg font-extrabold text-md dark:text-white">Historial de Cuadro de Pendientes </h1>
                        <div class="flex flex-wrap justify-end items-center gap-x-4">
                            <label class="text-base text-gray-900 dark:text-gray-50 font-extrabold">Filtrar por :</label>
                            <input type="date" v-model="fechaSeleccionada" @change="filtrarPorFecha"
                                class="outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 px-2.5 py-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" id="fechaHoyHoja">
                        </div>
                    </div>
                    <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-800">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Usuario</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Razon Social</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">cantidad</th>
                                    <th scope="col" class="px-20 py-3 text-center dark:border-white border-b-2">descripcion</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">fecha</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">lugar</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N° Contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-xs tracking-widest">
                                <tr @dblclick="editHojaServicio(hojaservicio)" v-for="hojaservicio in hojaservicios"
                                    :key="hojaservicio.id"
                                    class="bg-white text-black  hover:text-white border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                    <!-- Mostrar los datos de cada hservicio -->
                                    <td class="px-6 py-4 text-center">{{ hojaservicio.id }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.usuario }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b whitespace-break-spaces">{{hojaservicio.razon_social }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.cantidad }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b whitespace-break-spaces">{{hojaservicio.descripcion }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b whitespace-nowrap">{{hojaservicio.fecha }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.lugar }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.contacto }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.nro_contacto }}</td>
                                    <td class="px-6 py-3 dark:border-white relative">
                                        <button @click="$event => deleteHojaServicio(hojaservicio.id, hojaservicio.razon_social)" class="bg-red-600 hover:bg-red-700 px-1.5 py-0.5 rounded-md" title="Eliminar Cliente">
                                            <i class='bx bxs-trash text-base text-white'></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="hojaservicios.length === 0" class="text-center py-2 dark:text-white">
                            No se encontraron datos.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>