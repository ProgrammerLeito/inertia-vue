<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch, defineProps } from 'vue';
import Swal from 'sweetalert2';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';

const {plantillas,cservicio} = defineProps({
    plantillas: {
        type: Object,
        required: true
    },
    cservicio: {
        type: Object,
        required: true
    },
});

const form = useForm(cservicio);

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

const submitForm = () => {
    form.put(route('c_servicos.update', { c_servico: cservicio.id }), {
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
                text: "La cotización por servico se ha actualizado correctamente.",
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
                    text: "Ha ocurrido un error al registrar la cotización. Por favor, inténtalo de nuevo."
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
</script>
<template>
    <AppLayout title="Crear Servicio">
        <template #header>
            <h1 class="font-semibold text-base tracking-widest uppercase text-gray-800 leading-tight dark:text-white">
                Actualizar
                cotizacion por servicio
            </h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto tracking-widest">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div
                    class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">

                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel>Id cotizacion</InputLabel>
                                    <TextInput v-model="form.n_cotizacion" type="text" disabled
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div>
                                    <InputLabel>fecha</InputLabel>
                                    <TextInput v-model="form.fecha" type="date"
                                        class="input w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block  shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div class=" items-center ">
                                    <InputLabel>Descripcion</InputLabel>
                                    <select id="plantilla" v-model="form.plantilla_id" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block tracking-widest w-full shadow-sm sm:text-sm border-gray-300 rounded-lg">
                                        <option value="" disabled selected>Seleccione una plantilla de cotizacion
                                        </option>
                                        <option v-for="plantilla in plantillas" :key="plantilla.id"
                                            :value="plantilla.id">{{
                                                plantilla.descripcion }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel>Trabajos a Realizar</InputLabel>
                                    <textarea id="tselecionado" v-model="form.tselecionado" rows="8"
                                        placeholder="Escribe las especificaciones..."
                                        class="mt-1 focus:ring-indigo-500 tracking-widest  focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md "></textarea>
                                </div>
                                <div>
                                    <InputLabel>Lista de trabajos Sugeridos</InputLabel>
                                    <textarea id="tselecionado" v-model="form.tselecionado" rows="8"
                                        placeholder="Escribe las especificaciones..."
                                        class="mt-1 focus:ring-indigo-500 tracking-widest  focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md "></textarea>
                                </div>
                            </div>

                            <div
                                class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 sm:gap-x-8 mt-8 mb-3">
                                <div>
                                    <InputLabel>Precios</InputLabel>
                                    <TextInput v-model="form.precio" type="text" placeholder="solo numeros"
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.precio" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="fpago" class="block font-medium text-gray-900">Forma
                                        de pago
                                    </InputLabel>
                                    <select id="fpago" v-model="form.fpago" required
                                        class="mt-1 focus:ring-indigo-500 tracking-widest focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
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
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-md">
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
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-md">
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
                                    <TextInput v-model="form.subtotal" type="text" disabled
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.subtotal" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel>Igv</InputLabel>
                                    <TextInput v-model="form.igv" type="text" disabled
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.igv" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel>Total</InputLabel>
                                    <TextInput v-model="form.total" type="text" disabled
                                        class="input mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.total" class="mt-2" />
                                </div>
                            </div>

                            <div class="text-end">
                                <PrimaryButton class="mt-3 tracking-widest">
                                    Actualizar cotizacion
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
    </AppLayout>
</template>
