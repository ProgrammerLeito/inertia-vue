<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextRuc from '@/Components/TextRuc.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';
 
const form = useForm({
  numeroDocumento: '',
  razonSocial: '',
  direccion: '',
  distrito: '',
  provincia: '',
  departamento:'',
  estado:'',
  cli_direccion2:'',
  cli_observacion:'',
  // Agregar otros campos según sea necesario
});
 
const errors = ref({
  numeroDocumento: '',
});
 
const consultarReniec = async () => {
  try {
    const response = await fetch(`/consultar-reniec?numero=${form.numeroDocumento}`);
 
    if (response.ok) {
      const empresa = await response.json();
      form.razonSocial = empresa.razonSocial;
      form.direccion = empresa.direccion;
      form.distrito = empresa.distrito;
      form.provincia = empresa.provincia;
      form.departamento = empresa.departamento;
      form.estado = empresa.estado;
      errors.value.numeroDocumento = ''; // Limpiar errores si la consulta es exitosa
    } else if (response.status === 404) {
      errors.value.numeroDocumento = 'No se encontró información para el DNI ingresado.';
    } else {
      throw new Error('Error en la consulta a la API de Reniec.');
    }
  } catch (error) {
    console.error(error);
  }
};
 
// const submitForm = () => {
 
// };
 
const submitForm = () => {
    form.post(route('clientes.store'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'El cliente se ha registrado correctamente.'
            });
        },
        onError: (errors) => {
            if(errors.response && errors.response.status) {
                // Si hay un error de respuesta HTTP, manejarlo aquí
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.'
                });
                console.error('Error HTTP:', errors.response.status);
            } else {
                // Si el error no tiene una propiedad de respuesta o de estado, manejarlo aquí
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
    <AppLayout title="Registrar Cliente">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Registrar Cliente</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="numeroDocumento" value="Número de RUC" />
                                    <div class="w-full flex justify-end items-end">
                                        <TextRuc v-model="form.numeroDocumento" type="text" id="numeroDocumento" placeholder="Ingrese RUC" class="mt-2 w-full"/>
                                        <button @click="consultarReniec" type="button" class="bg-blue-600 w-12 h-[42px] rounded-r-lg hover:bg-blue-700"><i class='bx bx-search-alt text-white dark:text-black font-medium text-xl'></i></button>
                                    </div>
                                    <InputError :message="errors.numeroDocumento" />
                                </div>     
                                <div>
                                    <InputLabel value="razon Social" />
                                    <TextInput v-model="form.razonSocial" type="text" placeholder="Ingrese Razon Social" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel value="estado" />
                                    <TextInput v-model="form.estado" type="text" class="mt-2 w-full"/>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel value="distrito" />
                                    <TextInput v-model="form.distrito" type="text" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel value="provincia" />
                                    <TextInput v-model="form.provincia" type="text"  class="mt-2 w-full" />
                                </div>
                                <div>
                                    <InputLabel value="departamento" />
                                    <TextInput v-model="form.departamento" type="text" class="mt-2 w-full"/>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-1 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel value="direccion legal" />
                                    <TextInput v-model="form.direccion" type="text" placeholder="Ingrese Direccion Legal" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel for="cli_direccion2" value="direccion anexa 1"/>
                                    <TextInput v-model="form.cli_direccion2" type="text" id="cli_direccion2" placeholder="Ingrese Direccion Anexa 1" class="mt-2 w-full"/>
                                </div>
                                <InputLabel for="cli_observacion" value="Observaciones"/>
                                    <textarea id="cli_observacion" v-model="form.cli_observacion" rows="4" class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escriba las observaciones..."></textarea>
                                <InputError :message="$page.props.errors.cli_observacion" class="mt-2"/>
                            </div>
                            <div class="d-flex mt-4">
                                <!-- Botón para consultar información a la API de Reniec -->
                                <!-- <div class="flex justify-start">
                                    <PrimaryButton type="button" @click="consultarReniec">Consultar Sunat</PrimaryButton>
                                </div> -->
                
                                <!-- Botón de enviar -->
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive type="submit" class="md:w-min whitespace-nowrap w-full text-center font-bold">
                                        GUARDAR
                                    </ButtonResponsive>
                                    <Link :href="route('clientes.index')" class="inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">
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