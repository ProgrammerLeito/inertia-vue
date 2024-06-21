<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
const props = defineProps({
    hojaservicios: {
        type: Object
    }
});
const form = useForm({

    servicio_a_realizar: '',
    razon_social: '',
    direccion: '',
    contacto: '',
    area_de_contacto: '',
    telefono_de_contacto: '',
    asesor_encargado: '',
    hora_del_servicio: '',
    cantidad_de_instrumentos: '',
    datos_del_instrumento: '',
    trabajos_a_realizar: '',
    // n_informe: '',
});
const submitForm = () => {
    form.post(route('hojasservicios.store'), {
        onSuccess: () => {
            form.reset();
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
                text: "El hoja de servico se ha registrado correctamente.",
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
</script>
<template>
    <AppLayout title="Hoja de Servicio">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Hoja de Servicio</h1>
        </template>
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div
                    class="p-6 bg-white border-gray-100 shadow-lg shadow-gray-600 dark:bg-gray-800  dark:shadow-gray-800 rounded-lg">
                    <form @submit.prevent="submitForm" class="my-5 uppercase font-bol">
                        <!-- <div class="hidden">
                            <InputLabel>N° informe</InputLabel>
                            <TextInput v-model="form.n_informe" type="text"
                                class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                        </div> -->
                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                            <div>
                                <InputLabel for="servicio_a_realizar" value="servicio a realizar"
                                    class="cruz block text-md font-medium text-gray-700" />
                                <select id="servicio_a_realizar" v-model="form.servicio_a_realizar" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Seleccione un servicio</option>
                                    <option value="Mantenimiento">Mantenimiento</option>
                                    <option value="Calibracion">Calibracion</option>
                                    <option value="Diagnostico">Diagnostico</option>
                                    <option value="Certificacion Balinsa">Certificacion Balinsa</option>
                                    <option value="Certificacion  total weight">Certificacion  total weight</option>
                                    <option value="Reparacion ">Reparacion</option>
                                    <option value="Entrega">Entrega</option>
                                    <option value="Recojo">Recojo</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel for="razon_social" value="razon social *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.razon_social" type="text" id="razon_social" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.razon_social" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="direccion" value="Direccion *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.direccion" type="text" id="direccion" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.direccion" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                            <div>
                                <InputLabel for="contacto" value="contacto *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.contacto" type="text" id="contacto" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.contacto" class="mt-2"></InputError>
                            </div>

                            <div>
                                <InputLabel for="area_de_contacto" value="area de contacto *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.area_de_contacto" type="text" id="area_de_contacto" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.area_de_contacto" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="telefono_de_contacto" value="telefono de contacto *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.telefono_de_contacto" type="text" id="telefono_de_contacto" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.telefono_de_contacto" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">

                            <div>
                                <InputLabel for="asesor_encargado" value="asesor encargado *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.asesor_encargado" type="text" id="asesor_encargado"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.asesor_encargado" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="hora_del_servicio" value="hora del servicio *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.hora_del_servicio" type="time" id="hora_del_servicio"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.hora_del_servicio" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="cantidad_de_instrumentos" value="cantidad de instrumentos *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.cantidad_de_instrumentos" type="text"
                                    id="cantidad_de_instrumentos"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.cantidad_de_instrumentos" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8 mb-3">
                            <div>
                                <InputLabel for="datos_del_instrumento" value="datos del instrumento *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <textarea id="datos_del_instrumento" v-model="form.datos_del_instrumento" rows="4"
                                    class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Escriba las datos_del_instrumento..."></textarea>
                            </div>
                            <div>
                                <InputLabel for="trabajos_a_realizar" value="trabajos a realizar"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <textarea id="trabajos_a_realizar" v-model="form.trabajos_a_realizar" rows="4"
                                    class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Escriba las trabajos_a_realizar..."></textarea>
                            </div>
                        </div>
                        <div class="d-flex mt-4">
                            <div class="flex flex-wrap gap-2 justify-end">
                                <ButtonResponsive>
                                    Previsualizar Pdf
                                </ButtonResponsive>
                                <ButtonResponsive>
                                    Generar Hoja de Servicio
                                </ButtonResponsive>
                            </div>
                        </div>
                    </form>
                    <div class="mt-4 relative overflow-x-auto shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-800">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N° inf</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">razon social</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">direccion</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Area de contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">
                                        <i class="fas fa-phone mx-2"></i> de contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">
                                        asesor encargado</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">hora del servicio
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">cantidad de instrumentos
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">datos del instrumento
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">trabajos a realizar
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-xs tracking-widest">
                                <tr v-for="hojaservicio in hojaservicios" :key="hojaservicio.id"
                                    class="bg-white text-black  hover:text-white border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                    <!-- Mostrar los datos de cada hservicio -->
                                    <td class="px-6 py-4 text-center">{{ hojaservicio.n_informe}}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.razon_social }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.direccion }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.contacto }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.area_de_contacto }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.telefono_de_contacto }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.asesor_encargado }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.hora_del_servicio }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.cantidad_de_instrumentos }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.datos_del_instrumento }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{ hojaservicio.trabajos_a_realizar}}</td>
                                    <!-- <td class="px-6 py-3 text-center dark:border-white border-b flex">
                                        <Link :href="route('hservicios.edit',  hservicio.id)"  class="mx-2 px-1 py-1 bg-green-800 text-white rounded hover:bg-green-400 dark:hover:bg-white dark:hover:text-green-500">
                                            <i class="bi bi-pencil-square"></i>
                                        </Link>
                                        <Button @click="$event => deleteHservicio(hservicio.id,hservicio.modelo)" class=" ml-1 px-1 py-1 bg-red-800 text-white rounded hover:bg-red-400 dark:hover:bg-white dark:hover:text-red-600">
                                            <i class="bi bi-trash3"></i>
                                        </Button>
                                    </td> -->
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