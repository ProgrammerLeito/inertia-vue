<script>
    export default {
        name: 'ProductosCreate'
    }
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SalidasForm from '@/Components/Salidas/Form.vue';
import { onMounted } from 'vue';
import Swal from 'sweetalert2';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import axios from "axios";

defineProps({
    salidas: {
        type : Object,
        required: true
    },
    productos: {
        type : Object,
        required: true
    },
    tecnico_salidas: {
        type : Object,
        required: true
    }
})

const form = useForm ({
    empresa: '',
    unidad_salida: '',
    comentario_salida: '',
    tecnico: '',
    fecha: '',
    hora_salida:'',
    producto_id: '',
    devolucion: '0',
})
 
const setCurrentTime = () => {
    const now = new Date();
    const hour = String(now.getHours()).padStart(2, '0');
    const minute = String(now.getMinutes()).padStart(2, '0');
    form.hora_salida = `${hour}:${minute}`;
}
onMounted(() => {
    setCurrentTime();
});

onMounted(() => {
    const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Lima' }).split('/').reverse().join('-');
    form.fecha = today; // Asigna la fecha actual al modelo de datos del formulario
});

// constante para manejar la seleccion automatica del producto
onMounted(() => {
    const producto_id = localStorage.getItem('producto_id');
    if (producto_id) {
        form.producto_id = producto_id; // Asigna el producto_id al modelo de datos del formulario
    }
});

// Funcion validateform para ver si al crear la salida estan los campos rellenados
function validateForm() {
    if (!form.empresa || !form.producto_id || !form.unidad_salida || !form.tecnico || !form.fecha || !form.comentario_salida) {
        Swal.fire({
            icon: 'error',
            title: 'Todos los campos son requeridos',
            showConfirmButton: false,
            timer: 1500
        });
        return false;
    }
    return true;
}

function warn(event) {
    if (event) {
        event.preventDefault()
    }

    let producto_id = form.producto_id;
    let cantidad = form.unidad_salida;
    // console.log(form.producto_id);
    axios.post('/validarstock', {
        producto_id: producto_id
    })
    .then(response => {
        // Verifica la respuesta de la validación de stock
        // console.log(response.data[0].id);
        response = response.data[0]
        // {{ parseInt(producto.stock) + parseInt(producto.total_entradas) + parseInt(producto.total_devolucion) - parseInt(producto.total_salidas) }}    {{ producto.unidad_medida }}
        let stock = parseInt(response.stock) + parseInt(response.total_entradas) + parseInt(response.total_devolucion) - parseInt(response.total_salidas)
        // console.log(stock,cantidad);
        if (stock < parseInt(cantidad)) {
            // Si hay un error de stock insuficiente, muestra una alerta
            Swal.fire({
                icon: 'error',
                title: 'Stock no disponible',
                showConfirmButton: false,
                timer: 1500
            });
            return;
        } else if (stock >= parseInt(cantidad)) {
            // llamar a la funcion validateform para ver si al crear la salida estan los campos rellenados
            if (!validateForm()) {
                return;
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
                    allowOutsideClick: () => !Swal.isLoading(),
                    preConfirm: (password) => {
                        // Aquí puedes realizar cualquier validación adicional o acciones necesarias antes de enviar la solicitud
                        return axios.post('/comprobarSalida', { id: id, passwordconfirmacion: password })
                            .then(response => {
                                if (response.data.siexisteusuario === true) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: "Salida Exitosa",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    return true; // Esto confirma el diálogo si la validación es exitosa
                                } else if (response.data.siexisteusuario === false) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: "Contraseña Incorrecta",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    return false; // Esto impide que el diálogo se cierre si la validación falla
                                }
                            })
                            .catch(error => {
                                console.error('Error al enviar la solicitud:', error);
                                return false; // Esto impide que el diálogo se cierre si hay un error
                            });
                    }
                }).then((result) => {
                    // Aquí puedes realizar cualquier acción adicional después de que se complete la operación
                    if (result.isConfirmed) {
                        submit();
                    }
                });
            }
        }
    })
    .catch(error => {
        console.error('Error al validar el stock:', error);
        // Maneja cualquier error que pueda ocurrir durante la solicitud de validación de stock
        Swal.fire({
            icon: 'error',
            title: 'Error al validar el stock',
            showConfirmButton: false,
            timer: 2500
        });
        return;
    });
}

function submit(){
    router.post('/salidas.store', form)
}

</script>

<template>
    <AppLayout title="Crear Producto">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Ingresar Salida</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white border-gray-200 dark:bg-gray-800">
                            <SalidasForm :form="form" :productos="productos" :salidas="salidas" :tecnico_salidas="tecnico_salidas" @submit="warn($event)" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>