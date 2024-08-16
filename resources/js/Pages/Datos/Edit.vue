<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import FileInput from '@/Components/FileInput.vue';


const { clientes, dato } = defineProps({
    clientes: {
        type: Object,
        required: true
    },
    dato: {
        type: Object
    }
});
const form = useForm(dato);

const onSelectFoto = (e) => {
    const files = e.target.files;
    if (files.length) {
        form.tarjeta = files[0]
    }
}

const submitForm = () => {
    form.post(route('datos.update', { dato: dato.id }), {
        onSuccess: () => {
            const Toast = Swal.mixin({
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: 'Éxito',
                text: "Los datos se ha actualizado correctamente"
            });
        },
    });
};
</script>

<template>
    <AppLayout title="Actualizar datos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">
                Actualizar Datos
            </h1>
        </template>
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div
                    class="py-2 md:py-4 min-h-[calc(100vh-185px)] dark:bg-gray-800 overflow-auto uppercase text-xs shadow-2xl bg-white rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8 ">
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3  sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel for="cliente_id" value="Cliente" class="block text-sm font-medium text-gray-700"/>
                                    <select id="cliente_id" v-model="form.cliente_id" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Seleccione una categoría</option>
                                        <option class="text-sm" v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">{{
                                            cliente.razonSocial }}</option>
                                    </select>
                                    <InputError :message="$page.props.errors.cliente_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="nombre" value="Nombre"
                                        class="block text-sm font-medium text-gray-700">
                                    </InputLabel>
                                    <TextInput type="text" id="nombre" v-model="form.nombre" required
                                        placeholder="Ingrese el modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.nombre" class="mt-2" />
                                </div>


                                <div>
                                    <InputLabel for="cargo" value="Cargo"
                                        class="block text-sm font-medium text-gray-700" />
                                    <TextInput type="text" id="cargo" v-model="form.cargo" required
                                        placeholder="Ingrese la medida"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.cargo" class="mt-2" />
                                </div>

                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3  sm:gap-x-8 mb-3">
                                <div>
                                    <InputLabel for="telefono" value="Telefono"
                                        class="block text-sm font-medium text-gray-700" />
                                    <TextInput type="text" id="telefono" v-model="form.telefono" required
                                        placeholder="Ingrese el numero"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.telefono" class="mt-2" />
                                </div>
                                <!-- Precio_max -->
                                <div>
                                    <InputLabel for="correo" value="Correo"
                                        class="block  text-sm font-medium text-gray-700" />
                                    <TextInput type="text" id="correo" v-model="form.correo" required
                                        placeholder="escribe solo numeros"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="$page.props.errors.correo" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="tarjeta" value="tarjeta:"
                                        class="block mb-2 text-sm font-medium text-gray-700" />
                                    <FileInput class="text-sm" name="tarjeta" @change="onSelectFoto" />
                                    <InputError :message="$page.props.errors.tarjeta" class="mt-2" />
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive>ACTUALIZAR</ButtonResponsive>
                                    <Link :href="route('datos.index', { cliente_id: $page.props.dato ? $page.props.dato.cliente_id : null })" class="inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">
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
