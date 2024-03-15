<script>
export default {
    name: 'ProductosEdit'
}

</script>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SalidasForm from '@/Components/Salidas/Form.vue';
import Swal from 'sweetalert2';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from "axios";

const props = defineProps({
    productos: {
        type: Object,
        required: true,
    },
    salidas: {
        type: Object,
        required: true,
    },
    tecnico_salidas:{
        type: Object,
        required: true,
    }
})

const form = useForm({
    empresa: props.salidas.empresa,
    unidad_salida: props.salidas.unidad_salida,
    comentario_salida: props.salidas.comentario_salida,
    tecnico: props.salidas.tecnico,
    fecha: props.salidas.fecha,
    producto_id: props.salidas.producto_id
})

function warn(event) {
    if (event) {
        event.preventDefault()
    }
    let id = document.getElementById('tecnico').value;
    if (id == 0 || id == "") {
        Swal.fire({
            icon: 'error',
            title: 'Debe seleccionar un tecnico',
            showConfirmButton: false,
            timer: 1500
        })
    }else{
        Swal.fire({
            title: "Ingrese su Contraseña para confirmar su salida",
            input: "password",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Confirmar",
            showLoaderOnConfirm: true,
            cancelButtonText: "Cancelar",
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                let passwordconfirmacion = result.value;
    
                // Enviar solicitud al backend usando axios
                axios.post('/comprobarSalida', { id: id, passwordconfirmacion: passwordconfirmacion })
                .then(response => {
                    if (response.data.siexisteusuario === true){
                        Swal.fire({
                            icon: 'success',
                            title: "Salida Exitosa",
                            showConfirmButton: false,
                            timer: 1500
                        })
                        submit();
                    }else if (response.data.siexisteusuario === false){
                        Swal.fire({
                            icon: 'error',
                            title: "Contraseña Incorrecta",
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                })
                .catch(error => {
                    console.error('Error al enviar la solicitud:', error);
                });
            }
        });
    }
}

function submit(){
    router.post('/salidas.update', form)
}

</script>

<template>
    <AppLayout title="Editar Producto">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Editar Producto</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"></div>
                <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                    <SalidasForm :updating="true" :form="form" :productos="productos" :salidas="salidas" :tecnico_salidas="tecnico_salidas" @submit="warn($event)" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>