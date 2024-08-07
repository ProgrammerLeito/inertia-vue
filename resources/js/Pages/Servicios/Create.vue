<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch, watchEffect } from 'vue';
import Swal from 'sweetalert2';
import { defineProps } from 'vue';

const { datos, clientes, users, nInforme } = defineProps({
    clientes: {
        type: Object,
        required: true
    },
    datos: {
        type: Array,
        required: true
    },
    users: {
        type: Object,
        required: true
    },
    nInforme: {
        type: Object
    }
});
//campos
const form = useForm({
    cliente_id: '',
    dato_id: '',
    user_id: '',
    direccion: '',
    n_guia: '',
    fecha: '',
    hora: '',
    descripcion: '',
    pdf: '',
    e_servicio: '',
    n_informe: nInforme,
});

//hora y fecha actual
const setCurrentDate = () => {
    const today = new Date().toISOString().split('T')[0];
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
//filtros de acuerdo al cliente seleccionado
const filteredDatos = ref([]);
const updateFilteredDatos = () => {

    if (form.cliente_id) {
        filteredDatos.value = datos.filter(dato => dato.cliente_id == form.cliente_id);
        const clienteSeleccionado = clientes.find(cliente => cliente.id == form.cliente_id);
        if (clienteSeleccionado) {
            form.direccion = clienteSeleccionado.direccion;
        } else {
            form.direccion = '';
        }
    } else {
        filteredDatos.value = datos;
        form.direccion = '';
    }
};
watch(form.cliente_id, () => {
    updateFilteredDatos();
});
//guardar metodo
const submitForm = () => {
    form.post(route('servicios.store'), {
        onSuccess: () => {
            const Toast = Swal.mixin({
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: 'Éxito',
                text: "Hoja tecnica se ha registrado correctamente.",
                customClass: {
                    title: 'text-2xl font-bold tracking-widest ',
                    icon: 'text-base font-bold tracking-widest ',
                    text: 'bg-red-500 hover:bg-red-600 tracking-widest ',
                },
            });
        },
        onError: (errors) => {
            if (errors.response && errors.response.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al registrar la hoja tecnica. Por favor, inténtalo de nuevo.'
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

const obtenerRazonSocial = (clienteId) => {
    const cliente = clientes.find(cliente => cliente.id === clienteId);
    return cliente ? cliente.razonSocial : '';
};

const obtenerContactoTelefono = (datoId) => {
    const dato = datos.find(dato => dato.id === datoId);
    if (dato) {
        return `${dato.nombre} - ${dato.cargo} - ${dato.telefono}`;
    }
    return '';
};

const obtenerNombreUsuario = (userId) => {
    const usuario = users.find(usuario => usuario.id === userId);
    if (usuario) {
        return `${usuario.name} - ${usuario.apellidopat}`;
    }
    return '';
};

const { props } = usePage();
const pdfPreview = ref(null);
const user = props.auth.user;
const rol = props.auth.user.roles[0]?.name;

const obtenerNombreCompleto = (user) => {
    if (user) {
        return `${user.name} ${user.apellidopat} ${user.apellidomat}`;
    }
    return '';
};
const nombreCompleto = obtenerNombreCompleto(user);

</script>

<template>
    <AppLayout title="Registrar H-servicio ">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Registrar Hoja de Servicio</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">
                            <div class="hidden">
                                <InputLabel>N° informe</InputLabel>
                                <TextInput v-model="form.n_informe" type="text"
                                    class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="cliente_id" value="Cliente"
                                        class="block text-md font-medium text-gray-700" />
                                    <select id="cliente_id" v-model="form.cliente_id" required
                                        @change="updateFilteredDatos"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione un cliente</option>
                                        <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">{{
                                            cliente.razonSocial }}</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="direccion" value="Direccion/Ruta"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.direccion" type="text" id="direccion"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.direccion" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="dato_id" value="Contacto + N° Telefono"
                                        class="block text-md font-medium text-gray-700" />
                                    <select id="dato_id" v-model="form.dato_id" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione un contacto</option>
                                        <option v-for="dato in filteredDatos" :key="dato.id" :value="dato.id">{{
                                            `${dato.nombre}
                                            - ${dato.cargo} - ${dato.telefono}` }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="fecha" value="Fecha de atencion al cliente"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.fecha" type="date" id="fecha"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.fecha" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="hora" value="Hora de atencion al cliente"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.hora" type="time" id="fecha"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.hora" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="n_guia" value="Numero Guia"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.n_guia" type="text" id="fecha"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.n_guia" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="descripcion" value="descripcion" />
                                    <select id="moneda" v-model="form.descripcion" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Selecciona una descripcion</option>
                                        <option value="DIAGNOSTICO DE BALANZAS">DIAGNOSTICO DE BALANZAS</option>
                                        <option value="DIAGNOSTICO DE TERMOMETRO">DIAGNOSTICO DE TERMOMETRO</option>
                                    </select>
                                    <InputError :message="$page.props.errors.descripcion" class="" />
                                </div>
                                <div>
                                    <InputLabel for="user_id" value="Responsable del Transporte"
                                        class="block text-md font-medium text-gray-700" />
                                    <select id="user_id" v-model="form.user_id"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione un usuario</option>
                                        <option v-for="user in users" :key="user.id" :value="user.id">{{ `${user.name}  ${user.apellidopat}`}}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="e_servicio" value="Encargado del Servicio (Tecnico evaluador)"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.e_servicio" type="text" id="e_servicio"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.e_servicio" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <!-- <ButtonResponsive class="uppercase tracking-widest mx-2" @click.prevent="generarYPresentarPDF">
                                        Ver Pdf
                                    </ButtonResponsive> -->
                                    <ButtonResponsive class="uppercase">
                                        Generar Hoja de Informe
                                    </ButtonResponsive>
                                    <Link :href="route('servicios.index')" class="inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">
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
