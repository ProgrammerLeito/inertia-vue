<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';
defineProps({
    roles: {
        type : Object,
        required: true
    }
})
 
const form = useForm({
  name: '',
 
  // Agregar otros campos según sea necesario
});
 
const submitForm = () => {
    form.post(route('roles.store'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'el rol se ha registrado correctamente.',
                timer: 1000,
                timerProgressBar: true,
                showConfirmButton: false
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
    <AppLayout title="Registrar-rol">
        <template #header>
            <h2 class="font-semibold uppercase text-md text-gray-800 leading-tight">
              Registra rol
            </h2><br><hr>
        </template>
 
        <div class="flex justify-center">
           <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto container items-center">
                <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="p-6 bg-white border-gray-100 shadow-2xl dark:shadow-gray-500  rounded-lg">
                       
                        <form @submit.prevent="submitForm">
                            <div>
                                <InputLabel for="name" value="nombre" class="text-xs"/>
                                <TextInput v-model="form.name" type="text" id="name" placeholder="escribe un rol"  class="w-full text-xs"/>
                            </div>
                            <!-- {{-- <div class="form-group">
                                <label for="permission">Permissions:</label>
                                @foreach($permission as $value)
                                <div class="form-check">
                                <input type="checkbox" name="permission[]" value="{{ $value->id }}" class="form-check-input">
                                <label class="form-check-label">{{ $value->name }}</label>
                            </div>
                           @endforeach
                            </div> --}} -->
                            <div class="flex justify-end mt-2">
                                <PrimaryButton type="submit">Guardar</PrimaryButton>
                                <Link :href="route('roles.index')" class="ml-5 uppercase text-sm inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700">
                                    Cancelar
                                </Link>
                            </div>
                        </form>
                    </div>  
                </div>
            </div>
        </div>
    </AppLayout>
</template>