<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch, watchEffect } from 'vue';
import Swal from 'sweetalert2';
import { defineProps } from 'vue';
import FileInput from '@/Components/FileInput.vue';

const { servicios, hservicio, hmarcas } = defineProps({
    servicios: {
        type: Object,
        required: true
    },
    hservicio: {
        type: Object,
        required: true
    },
    hmarcas: {
        type: Object
    }
});

const form = useForm(hservicio);

const onSelectFoto = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
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
    form.post(route('hservicios.update', { hservicio: hservicio.id }), {
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
                text: "La hoja de servicio se ha actualizado correctamente.",
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
                    text: 'Ha ocurrido un error al actualizar rquerimientos hoja de servicio. Por favor, inténtalo de nuevo.'
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
});
</script>

<template>
    <AppLayout title="Editar H-servicio-requerimientos ">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Editar requerimientos | servicio</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">
                            <div class="font-semibold d-flex flex-wrap items-center justify-center text-center dark:text-white py-4">
                                <div class="d-flex hidden">
                                    <select id="servicio_id" v-model="form.servicio_id" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white shadow-sm sm:text-sm border-gray-300 bg-gray-400 dark:bg-gray-800">
                                        <option value="" selected>Seleccione n°informe</option>
                                        <option v-for="servicio in servicios" :key="servicio.id" :value="servicio.id">{{
                                            servicio.n_informe }}</option>
                                    </select>
                                </div>
                                <div class="flex items-center font-bold p-4 border py-2.5 border-green-600 rounded bg-green-600">
                                    <div class="bg-yellow-400 text-base font-bold py-4 p-2 rounded text-white"> N° INFORME: {{ nInformeSeleccionado }} </div>
                                    <p class="py-4 text-base text-white font-bold mx-4">PARA : {{ razonSocialCliente }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="hmarca_id" class="text-xs">marca</InputLabel>
                                    <select id="hmarca_id" v-model="form.hmarca_id"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" selected>Seleccione una marca</option>
                                        <option v-for="hmarca in hmarcas" :key="hmarca.id" :value="hmarca.id">{{
                                            hmarca.nombre
                                            }}</option>
                                    </select>
                                    <InputError :message="form.errors.hmarca_id" class="mt-2"></InputError>
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
                                    <textarea id="diagnostico" v-model="form.diagnostico" rows="4"
                                        class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Escriba los diagnosticos..."></textarea>
                                    <InputError :message="form.errors.diagnostico" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="trabajos" value="trabajos" />
                                    <textarea id="trabajos" v-model="form.trabajos" rows="4"
                                        class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Escriba las trabajos..."></textarea>
                                    <InputError :message="form.errors.trabajos" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 md:grid-cols-2 lg:grid-cols-3 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel for="foto" value="Foto"
                                        class="block text-xs font-medium text-gray-700" />
                                    <FileInput name="foto" @change="($event) => onSelectFoto($event, 'foto')" />
                                    <InputError :message="$page.props.errors.foto" class="mt-2" />
                                    <img class="h-36 w-auto mt-2 mx-auto rounded dark:text-white" :src="'/storage/' + hservicio.foto"
                                        alt="Foto actual">
                                </div>
                                <div>
                                    <InputLabel for="foto2" value="Foto 2"
                                        class="block text-xs font-medium text-gray-700" />
                                    <FileInput name="foto2" @change="($event) => onSelectFoto($event, 'foto2')" />
                                    <InputError :message="$page.props.errors.foto2" class="mt-2" />
                                    <img class="h-36 mt-2 w-auto mx-auto rounded dark:text-white" :src="'/storage/' + hservicio.foto2"
                                        alt="Foto actual">
                                </div>
                                <div>
                                    <InputLabel for="foto3" value="Foto 3"
                                        class="block text-xs font-medium text-gray-700" />
                                    <FileInput name="foto3" @change="($event) => onSelectFoto($event, 'foto3')" />
                                    <InputError :message="$page.props.errors.foto3" class="mt-2" />
                                    <img class="h-36 w-auto mt-2 mx-auto rounded dark:text-white" :src="'/storage/' + hservicio.foto3"
                                        alt="Foto actual">
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive>
                                        ACTUALIZAR
                                    </ButtonResponsive>
                                    <Link :href="route('hservicios.index', { servicio_id: $page.props.hservicio ? $page.props.hservicio.servicio_id : null })"
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
    </AppLayout>
</template>
