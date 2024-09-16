<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';
import { formatDate } from '@/utils/funcionesglobales';
import { checkTableVisibility } from '@/utils/hFuncionesServicios';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import FileInput from '@/Components/FileInput.vue';
import InputError from '@/Components/InputError.vue';

const{hservicios}=defineProps({
    hservicios:{
        type : Array,
        required:true
    }
});

const isEditing = ref(false);

const editHojaServicio = (hservicio) => {
    form.n_servicio = hservicio.n_servicio
    form.id = hservicio.id;
    form.hmarca_id = hservicio.hmarca_id;
    form.instrumento = hservicio.instrumento;
    form.rango = hservicio.rango;
    form.medida_bastago = hservicio.medida_bastago;
    form.codigo = hservicio.codigo;
    form.material = hservicio.material;
    form.modelo = hservicio.modelo;
    form.serie = hservicio.serie;
    form.div = hservicio.div;
    form.capacidad = hservicio.capacidad;
    form.cliente_razonSocial = hservicio.cliente_razonSocial;
    form.plataforma = hservicio.plataforma;
    form.fecha = hservicio.fecha;
    form.requiere = hservicio.requiere;
    form.diagnostico = hservicio.diagnostico;
    form.trabajos = hservicio.trabajos;
    form.foto = hservicio.foto;
    form.foto2 = hservicio.foto2;
    form.foto3 = hservicio.foto3;

    imagePreview1.value = '/hservicio_img/' + hservicio.foto;
    imagePreview2.value = '/hservicio_img/' + hservicio.foto2;
    imagePreview3.value = '/hservicio_img/' + hservicio.foto3;

    images.value = [
        imagePreview1.value, // imagen 1
        imagePreview2.value, // imagen 2
        imagePreview3.value  // imagen 3
    ];

    isEditing.value = true;
};

const imagePreview1 = ref('');
const imagePreview2 = ref('');
const imagePreview3 = ref('');
const imagePreviews = ref(['', '', '']);

const onSelectFoto = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
        const reader = new FileReader();
        reader.onload = (e) => {
            // Actualiza la vista previa de la imagen correspondiente
            switch (fieldName) {
                case 'foto':
                    imagePreview1.value = e.target.result;
                    imagePreviews.value[0] = e.target.result; // Guarda en el arreglo de imágenes
                    break;
                case 'foto2':
                    imagePreview2.value = e.target.result;
                    imagePreviews.value[1] = e.target.result; // Guarda en el arreglo de imágenes
                    break;
                case 'foto3':
                    imagePreview3.value = e.target.result;
                    imagePreviews.value[2] = e.target.result; // Guarda en el arreglo de imágenes
                    break;
                default:
                    break;
            }
        };
        reader.readAsDataURL(files[0]);

        // También puedes guardar el archivo en el formulario si es necesario
        form[fieldName] = files[0];
    }
};

const form = useForm({
    n_servicio: '',
    hmarca_id: '',
    instrumento: '1',
    rango: '',
    medida_bastago: '',
    codigo: '',
    material: '',
    modelo: '',
    serie: '',
    div: '',
    capacidad: '',
    cliente_id: '',
    plataforma: '',
    fecha: '',
    requiere: '',
    diagnostico: '',
    trabajos: '',
    foto: '',
    foto2: '',
    foto3: '',
});

let timerInterval;
function construirDatosdeServicios() {
    Swal.fire({
        title: '¡Atención!',
        html: 'Obteniendo registro de hojas de servicios, espere un momento.',
        timer: 999999999, // Establece un valor grande para que parezca indefinido
        timerProgressBar: true,
        allowOutsideClick: false,   // Desactiva el clic fuera de la alerta
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    });

    $.ajax({
        url: '/fn_ObtenerHojasServicioDiarias',
        method: 'GET',
        success: function(response) {
            let bodyDiarios = $('#tbodyHojasServicioDiarias');
            let datosOriginales = response; // Guardar los datos originales
            bodyDiarios.empty();

            // Función para mostrar los datos
            function mostrarDatos(datos) {
                // Vaciar el contenido anterior
                bodyDiarios.empty();

                // Generar el HTML para cada fila
                datos.forEach(function(item) {
                    let nuevaFila = $(`
                    <tr class="bg-white font-extrabold text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-800 hover:bg-gray-400 cursor-pointer">
                        <td class="px-4 w-40 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${formatDate(item.fecha)}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-left">${item.razonSocial}</td>
                        <td class="px-4 w-32 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${item.id}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center whitespace-nowrap hidden">${item.fecha}</td>
                    </tr>`);
                    bodyDiarios.append(nuevaFila);
                });
            }

            // Mostrar todos los datos inicialmente
            mostrarDatos(datosOriginales);

            // Función para filtrar los datos por cliente
            function filtrarPorCliente() {
                let nombreFiltrar = $('#filtrarClienteHoja').val().toUpperCase();

                if (nombreFiltrar) {
                    // Filtrar los datos originales según el nombre del cliente
                    let datosFiltrados = datosOriginales.filter(function(item) {
                        return item.razonSocial.toUpperCase().includes(nombreFiltrar);
                    });

                    // Mostrar los datos filtrados
                    mostrarDatos(datosFiltrados);
                } else {
                    // Si no hay filtro, mostrar todos los datos
                    mostrarDatos(datosOriginales);
                }
            }

            // Asignar el evento de filtrado al input
            $('#filtrarClienteHoja').on('input', filtrarPorCliente);

            // Ejecutar el filtro inicial por si hay algo escrito al cargar los datos
            filtrarPorCliente();

            // Cerrar la alerta después de procesar los datos
            Swal.close();
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo obtener los datos de las hojas de servicio. Inténtelo de nuevo más tarde.'
            });
            Swal.close();
        }
    });
}

construirDatosdeServicios();

$(document).on("dblclick", "#tbodyHojasServicioDiarias tr", function() {
    let fila = $(this).closest('tr');
    let razonSocial = fila.find('td:eq(1)').text();
    let servicio = fila.find('td:eq(2)').text();
    let fecha = fila.find('td:eq(3)').text();
    
    localStorage.setItem('razonSocial', razonSocial);
    localStorage.setItem('servicio', servicio);

    $('#nroInformeTecnico').text(`${servicio}`);
    $('#razonSocialTexto').text(`${razonSocial}`);
    
    $.ajax({
        url: '/fn_obtenerHojasServicioAnteriores',
        method: 'GET',
        data: {
            servicio: servicio,
            fecha: fecha,
        },
        success: function(response) {
            $('#tbodyContenedorHojasServiciosBalanzas').empty();
            $('#tbodyContenedorHojasServiciosTermometros').empty();
            $('#tbodyContenedorHojasServiciosPesas').empty();

            let contadorbal = 1;
            let contadorter = 1;
            let contadorpes = 1;
            response.forEach(function(hservicio) {
                let hservicioJson = JSON.stringify(hservicio);
                let nuevaFila;

                if (hservicio.instrumento == "1") {
                    // Para Balanzas
                    nuevaFila = `
                    <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ contadorbal++ }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.hmarca_id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.capacidad }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.serie }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.div }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.plataforma }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ hservicio.fecha }</td>
                    </tr>
                    `;
                    $('#tbodyContenedorHojasServiciosBalanzas').append(nuevaFila);

                } else if (hservicio.instrumento == "2") {
                    // Para Termómetros
                    nuevaFila = `
                    <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ contadorter++ }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.hmarca_id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.serie }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.rango }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.medida_bastago }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.div }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ hservicio.fecha }</td>
                    </tr>
                    `;
                    $('#tbodyContenedorHojasServiciosTermometros').append(nuevaFila);

                } else if (hservicio.instrumento == "3") {
                    // Para Pesas
                    nuevaFila = `
                    <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ contadorpes++ }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.codigo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.capacidad }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.material }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ hservicio.fecha }</td>
                    </tr>
                    `;
                    $('#tbodyContenedorHojasServiciosPesas').append(nuevaFila);
                }
            });

            $('#divTablitaHojasdeServicio').show();
            $('#divListarHojasdeServicio').hide();

            checkTableVisibility();

            $('#tbodyContenedorHojasServiciosBalanzas, #tbodyContenedorHojasServiciosTermometros, #tbodyContenedorHojasServiciosPesas').on('dblclick', 'tr', function() {
                let hservicio = JSON.parse($(this).attr('data-hservicio'));
                editHojaServicio(hservicio);
                let instrumento = hservicio.instrumento;
                actualizarVisibilidadIns(instrumento);
                $('#divMostrarInstrumento').show();
                $('#divMostrarDatosInforme').show();
                $('#divMostrarFrmCrearNuevoInformeTecnico').hide();
                $('#divMostrarImagenesInforme').show();
            });

            // Filtrar filas por cliente
            function filtrarPorCliente() {
                let nombreFiltrar = $('#filtrarRequerimientosHojas').val().toUpperCase();

                // Mostrar todas las filas al inicio en las tres tablas
                $('#construirTablaBalanzas tbody tr').show();
                $('#construirTablaTermometros tbody tr').show();
                $('#construirTablaPesas tbody tr').show();

                if (nombreFiltrar) {
                    // Filtrar la primera tabla (8 td, a partir del 1)
                    $('#construirTablaBalanzas tbody tr').each(function() {
                        let coincide = false;
                        // Recorre los 8 td a partir del índice 1
                        for (let i = 1; i <= 8; i++) {
                            let nombre = $(this).find('td:eq(' + i + ')').text().toUpperCase().trim();
                            if (nombre.indexOf(nombreFiltrar) !== -1) {
                                coincide = true;
                                break;
                            }
                        }
                        if (!coincide) {
                            $(this).hide();
                        }
                    });

                    // Filtrar la segunda tabla (8 td, a partir del 1)
                    $('#construirTablaTermometros tbody tr').each(function() {
                        let coincide = false;
                        for (let i = 1; i <= 8; i++) {
                            let nombre = $(this).find('td:eq(' + i + ')').text().toUpperCase().trim();
                            if (nombre.indexOf(nombreFiltrar) !== -1) {
                                coincide = true;
                                break;
                            }
                        }
                        if (!coincide) {
                            $(this).hide();
                        }
                    });

                    // Filtrar la tercera tabla (6 td, a partir del 1)
                    $('#construirTablaPesas tbody tr').each(function() {
                        let coincide = false;
                        for (let i = 1; i <= 6; i++) {
                            let nombre = $(this).find('td:eq(' + i + ')').text().toUpperCase().trim();
                            if (nombre.indexOf(nombreFiltrar) !== -1) {
                                coincide = true;
                                break;
                            }
                        }
                        if (!coincide) {
                            $(this).hide();
                        }
                    });
                }
            }

            // Asignar evento al input de filtrado
            $('#filtrarRequerimientosHojas').on('input', filtrarPorCliente);

            // Ejecutar el filtro inicial
            filtrarPorCliente();
        },
    });
});

$(document).on("click", "#retornarbody", function() {
    $('#divMostrarDatosInforme').hide();
    $('#divMostrarInstrumento').hide();
    $('#divMostrarImagenesInforme').hide();
    $('#divMostrarFrmCrearNuevoInformeTecnico').hide();
    $('#divTablitaHojasdeServicio').hide();
    $('#divListarHojasdeServicio').show();
    form.reset();

    imagePreview1.value = "";
    imagePreview2.value = "";
    imagePreview3.value = "";

    imagePreviews.value = ['', '', ''];
});

function actualizarVisibilidadIns(instrumento) {
    if (instrumento == "1") {
        $('#divMarca').show();
        $('#divModelo').show();
        $('#divSerie').show();
        $('#divDivision').show();
        $('#divCapacidad').show();
        $('#divPlataforma').show();
        $('#divRango').hide();
        $('#divMedidaBastago').hide();
        $('#divCodigo').hide();
        $('#divMaterial').hide();
    } else if (instrumento == "2") {
        $('#divMarca').show();
        $('#divModelo').show();
        $('#divSerie').show();
        $('#divDivision').show();
        $('#divCapacidad').hide();
        $('#divPlataforma').hide();
        $('#divRango').show();
        $('#divMedidaBastago').show();
        $('#divCodigo').hide();
        $('#divMaterial').hide();
    } else if (instrumento == "3") {
        $('#divMarca').hide();
        $('#divModelo').show();
        $('#divSerie').hide();
        $('#divDivision').hide();
        $('#divCapacidad').show();
        $('#divPlataforma').hide();
        $('#divRango').hide();
        $('#divMedidaBastago').hide();
        $('#divCodigo').show();
        $('#divMaterial').show();
    }
}

$(document).on('change', '#instrumento', function () {
    let instrumento = $('#instrumento').val();
    actualizarVisibilidadIns(instrumento);
});

function actualizarVisibilidadIns2(instrumento) {
    if (instrumento == "1") {
        $('#divMarca2').show();
        $('#divModelo2').show();
        $('#divSerie2').show();
        $('#divDivision2').show();
        $('#divCapacidad2').show();
        $('#divPlataforma2').show();
        $('#divRango2').hide();
        $('#divMedidaBastago2').hide();
        $('#divCodigo2').hide();
        $('#divMaterial2').hide();
    } else if (instrumento == "2") {
        $('#divMarca2').show();
        $('#divModelo2').show();
        $('#divSerie2').show();
        $('#divDivision2').show();
        $('#divCapacidad2').hide();
        $('#divPlataforma2').hide();
        $('#divRango2').show();
        $('#divMedidaBastago2').show();
        $('#divCodigo2').hide();
        $('#divMaterial2').hide();
    } else if (instrumento == "3") {
        $('#divMarca2').hide();
        $('#divModelo2').show();
        $('#divSerie2').hide();
        $('#divDivision2').hide();
        $('#divCapacidad2').show();
        $('#divPlataforma2').hide();
        $('#divRango2').hide();
        $('#divMedidaBastago2').hide();
        $('#divCodigo2').show();
        $('#divMaterial2').show();
    }
}

$(document).on('change', '#instrumento2', function () {
    let instrumento = $('#instrumento2').val();
    actualizarVisibilidadIns2(instrumento);
});

$(document).on('click', '#crearNuevaHojaServicio', function (){
    form.reset();
});

$(document).on('click', '#crearNuevaHojaServicio', function () {
    $('#divMostrarDatosInforme').hide();
    $('#divMostrarInstrumento').hide();
    $('#divMostrarFrmCrearNuevoInformeTecnico').show();
    $('#divMostrarImagenesInforme').hide();
});

// Variables reactivas
const showModal = ref(false);
const currentIndex = ref(0);
const images = ref([]);

// Función para abrir el modal con el índice de la imagen
const openModal = (index) => {
  currentIndex.value = index;
  showModal.value = true;
};

// Función para cerrar el modal
const closeModal = () => {
  showModal.value = false;
};

// Función para pasar a la imagen anterior
const prevImage = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--;
  } else {
    currentIndex.value = images.value.length - 1; // Si estamos en la primera, va a la última
  }
};

// Función para pasar a la imagen siguiente
const nextImage = () => {
  if (currentIndex.value < images.value.length - 1) {
    currentIndex.value++;
  } else {
    currentIndex.value = 0; // Si estamos en la última, vuelve a la primera
  }
};

const submitForm = () => {
    // $("#btnguardarHojaServicio").hide();
    // $("#loading-button").show();
    // $.ajax({
    //     url: '/fn_obtenerFechaHojaServicio',
    //     method: 'GET',
    //     data: {
    //         cliente_id: form.cliente_id,
    //         fecha: form.fecha
    //     },
    //     success: function(response) {
    //         if (response) {
    //             let idServicio = response["id"]
    //             form.n_servicio = idServicio;
    //             form.post(route('hservicios.store'), {
    //                 onSuccess: () => {
    //                     show_alerta('La hoja de servicio se ha registrado correctamente.', 'success');
    //                     obtenerDatosTiempoReal();
    //                     resetarDatosFrm();
    //                     $("#btnguardarHojaServicio").show();
    //                     $("#loading-button").hide();
    //                 },
    //                 onError: (errors) => {
    //                     if (errors.response && errors.response.status) {
    //                         show_alerta('Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.', 'error');
    //                         console.error('Error HTTP:', errors.response.status);
    //                     } else {
    //                         show_alerta('Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.', 'error');
    //                         console.error('Error desconocido:', errors);
    //                     }
    //                 }
    //             });
    //         }
    //     },
    //     error: function(error) {
    //         console.error("ERROR AL REGISTRAR COTIZACION ",error);
    //     }
    // });
    $("#btnguardarHojaServicio").hide();
    $("#loading-button").show();
    console.log(form);
    $("#btnguardarHojaServicio").show();
}
</script>

<template>
    <AppLayout title="Informes Tecnicos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Informes Tecnicos</h1>
        </template>

        <div id="divListarHojasdeServicio" class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-col py-2">
                        <div class="flex md:w-96 w-full">
                            <span
                                class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                <i class="fa-solid fa-folder-open text-md"></i>
                            </span>
                            <input id="filtrarClienteHoja"
                                class="w-full outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-primary-600 focus:border-primary-600 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                type="text" autocomplete="off"
                                placeholder="Ingrese Nombre del Cliente">
                        </div>
                    </div>
                    <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white" id="tbHojaServicioBuscar">
                            <thead class="text-xs text-white uppercase bg-green-600">
                                <tr>
                                    <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Fecha</th>
                                    <th scope="col" class="px-5 py-3 text-start dark:border-white border-b-2">Cliente</th>
                                    <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">N° de Informe</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyHojasServicioDiarias" class="text-center text-xs">
                                <tr class="rounded-lg border-2 dark:border-gray-700 dark:text-gray-200 text-base"><td colspan="4" class="text-center">No hay datos</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="divTablitaHojasdeServicio" class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto hidden">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="w-full flex justify-between flex-col md:flex-row gap-y-1.5 py-1">
                        <button class="text-white bg-blue-600 flex justify-center items-center text-center md:gap-2 gap-4 font-bold hover:bg-blue-700 uppercase text-sm py-2 px-6 rounded md:w-min whitespace-nowrap w-full" id="retornarbody">
                            <i class="fa-solid fa-arrow-left"></i>Regresar
                        </button>
                        <ButtonResponsive id="crearNuevaHojaServicio" class="uppercase text-xs bg-red-600 hover:bg-red-700">Nuevo Informe Tecnico</ButtonResponsive>
                    </div>
                    <div class="py-2">
                        <div class="flex items-center font-bold p-4 border py-1 border-green-600 rounded bg-green-600">
                            <div class="bg-yellow-500 sm:text-base text-xs font-bold py-2 p-2 rounded text-white"> N° INFORME: <span id="nroInformeTecnico" class="mx-10"></span></div>
                            <p class="py-3.5 sm:text-base text-xs text-white font-bold mx-4">PARA : <span id="razonSocialTexto"></span></p>
                        </div>
                    </div>
                    <div id="divMostrarInstrumento" class="hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-3 md:mt-2 gap-y-4 mt-2 mb-1">
                            <div class="w-full -mb-2 hidden">
                                <InputLabel for="cliente_id" value="Cliente" />
                                <TextInput v-model="form.cliente_razonSocial" type="text" id="cliente_id"
                                    disabled class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div class="col-span-1 col-start-0 col-end-0 sm:col-start-3 sm:col-end-4 mb-2">
                                <InputLabel for="instrumento" class="block text-xs uppercase font-medium text-black dark:text-white">Instrumento</InputLabel>
                                <select v-model="form.instrumento" disabled id="instrumento" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="1" selected>Balanzas</option>
                                        <option value="2">Termometros</option>
                                        <option value="3">Pesas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="divMostrarDatosInforme" class="hidden">
                        <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-2">
                            <div id="divMarca">
                                <InputLabel for="hmarca_id" value="Marca" />
                                <TextInput v-model="form.hmarca_id" type="text" id="hmarca_id" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div id="divModelo">
                                <InputLabel for="modelo" value="Modelo" />
                                <TextInput v-model="form.modelo" type="text" id="modelo" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div id="divCodigo" class="hidden">
                                <InputLabel for="codigo" value="Codigo"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.codigo" type="text" id="codigo" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div id="divSerie">
                                <InputLabel for="serie" value="Serie"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.serie" type="text" id="serie" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div id="divRango" class="hidden">
                                <InputLabel for="rango" value="rango"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.rango" type="text" id="rango" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div id="divMedidaBastago" class="hidden">
                                <InputLabel for="medida_bastago" value="Medida de Bastago"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.medida_bastago" type="text" id="medida_bastago" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div id="divDivision">
                                <InputLabel for="div" value="Div" />
                                <TextInput v-model="form.div" type="text" id="div" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div id="divCapacidad">
                                <InputLabel for="capacidad" value="Capacidad" />
                                <TextInput v-model="form.capacidad" type="text" id="capacidad" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div id="divMaterial" class="hidden">
                                <InputLabel for="material" value="Material" />
                                <TextInput v-model="form.material" type="text" id="material" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div id="divPlataforma">
                                <InputLabel for="plataforma" value="Plataforma" />
                                <TextInput v-model="form.plataforma" type="text" id="plataforma" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div>
                                <InputLabel for="requiere" value="Requiere" />
                                <select id="requiere" v-model="form.requiere" required disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Selecciona una opcion</option>
                                    <option value="MANTENIMIENTO">MANTENIMIENTO</option>
                                    <option value="REPARACION">REPARACION</option>
                                    <option value="POR REVISAR">POR REVISAR</option>
                                    <option value="CERTIFICACION">CERTIFICACION</option>
                                    <option value="GARANTIA">GARANTIA</option>
                                    <option value="IMPLEMENTACION">IMPLEMENTACION</option>
                                    <option value="CALIBRACION">CALIBRACION</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel for="fecha" value="Fecha"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.fecha" type="date" id="fecha" disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3">
                            <div>
                                <InputLabel for="diagnostico" value="Diagnostico" />
                                <textarea id="diagnostico" rows="4" required v-model="form.diagnostico" disabled
                                    class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Escriba las diagnostico..."></textarea>
                            </div>
                            <div>
                                <InputLabel for="trabajos" value="Trabajos" />
                                <textarea id="trabajos" rows="4" required v-model="form.trabajos" disabled
                                    class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Escriba las trabajos..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div id="divMostrarImagenesInforme" class="hidden">
                        <div class="mt-0 flex justify-center items-center flex-wrap gap-y-0 sm:gap-x-2 gap-4">
                            <div v-if="imagePreview1" class="sm:col-span-1 flex-1 whitespace-nowrap">
                                <div class="flex flex-wrap gap-4 items-center justify-center mb-4 mt-1.5">
                                    <div class="dark:text-white font-extrabold">Foto 1</div>
                                    <div class="mt-2 flex items-center justify-center w-full">
                                        <img @click="openModal(0)" :src="imagePreview1" alt="Vista previa de la foto"
                                        class="p-2 block w-36 h-36 object-contain items-center text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                    </div>
                                </div>
                            </div>
                            <div v-if="imagePreview2" class="sm:col-span-1 flex-1 whitespace-nowrap">
                                <div class="flex flex-wrap gap-4 items-center justify-center mb-4 mt-1.5">
                                    <div class="dark:text-white font-extrabold">Foto 2</div>
                                    <div class="mt-2 flex items-center justify-center w-full">
                                        <img @click="openModal(1)" :src="imagePreview2" alt="Vista previa de la foto"
                                        class="p-2 block w-36 h-36 object-contain items-center text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                    </div>
                                </div>
                            </div>
                            <div v-if="imagePreview3" class="sm:col-span-1 flex-1 whitespace-nowrap">
                                <div class="flex flex-wrap gap-4 items-center justify-center mb-4 mt-1.5">
                                    <div class="dark:text-white font-extrabold">Foto 3</div>
                                    <div class="mt-2 flex items-center justify-center w-full">
                                        <img @click="openModal(2)" :src="imagePreview3" alt="Vista previa de la foto"
                                        class="p-2 block w-36 h-36 object-contain items-center text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="showModal" class="fixed right-0 top-[110px] bottom-[-60px] md:left-[50px] left-[-2px] overflow-y-auto z-[10000] bg-gray-200/40">
                            <div class="flex justify-center items-center flex-col h-full max-h-[90%] py-10 m-auto rounded-lg" style="backdrop-filter: blur(5px);">
                                <div class="w-full max-w-sm max-h-[90%] h-full bg-gray-50 dark:bg-gray-500 rounded-t-lg relative">
                                    <button @click="prevImage" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-600 text-white px-2 py-1 rounded-l text-xl hover:bg-gray-700">
                                        &lt;
                                    </button>
                                    <img :src="images[currentIndex]" alt="Imagen ampliada" class="max-w-sm w-full h-full object-contain">
                                    <button @click="nextImage" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-600 text-white px-2 py-1 rounded-r text-xl hover:bg-gray-700">
                                        &gt;
                                    </button>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-400 p-2 w-full max-w-sm flex justify-end rounded-b-lg">
                                    <button @click="closeModal" type="button" class="w-full justify-center rounded-md border border-transparent shadow-sm px-14 py-0 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="divMostrarFrmCrearNuevoInformeTecnico" class="hidden">
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 sm:grid-cols-3 md:py-0 py-2">
                                <div class="w-full -mb-2">
                                    <InputLabel for="cliente_id" value="Cliente" />
                                    <TextInput v-model="form.cliente_razonSocial" type="text" id="cliente_id"
                                        disabled class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                </div>
                                <div class="col-span-1 col-start-0 col-end-0 sm:col-start-3 sm:col-end-4 mb-2">
                                    <InputLabel for="instrumento" class="block text-xs font-medium text-black dark:text-white">Instrumento</InputLabel>
                                    <select id="instrumento2" v-model="form.instrumento" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="1" selected>Balanzas</option>
                                            <option value="2">Termometros</option>
                                            <option value="3">Pesas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-3">
                                <div id="divMarca2">
                                    <InputLabel for="hmarca_id" value="marca" />
                                    <TextInput v-model="form.hmarca_id" type="text" id="hmarca_id"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.hmarca_id" class="mt-2"></InputError>
                                </div>
                                <div id="divModelo2">
                                    <InputLabel for="modelo" value="modelo" />
                                    <TextInput v-model="form.modelo" type="text" id="modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.modelo" class="mt-2"></InputError>
                                </div>
                                <div id="divCodigo2" class="hidden">
                                    <InputLabel for="codigo" value="codigo"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.codigo" type="text" id="codigo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.codigo" class="mt-2"></InputError>
                                </div>
                                <div id="divSerie2">
                                    <InputLabel for="serie" value="serie"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.serie" type="text" id="serie"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.serie" class="mt-2"></InputError>
                                </div>
                                <div id="divRango2" class="hidden">
                                    <InputLabel for="rango" value="rango"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.rango" type="text" id="rango"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.rango" class="mt-2"></InputError>
                                </div>
                                <div id="divMedidaBastago2" class="hidden">
                                    <InputLabel for="medida_bastago" value="medida de bastago"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.medida_bastago" type="text" id="medida_bastago"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.medida_bastago" class="mt-2"></InputError>
                                </div>
                                <div id="divDivision2">
                                    <InputLabel for="div" value="div" />
                                    <TextInput v-model="form.div" type="text" id="div"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.div" class="mt-2"></InputError>
                                </div>
                                <div id="divCapacidad2">
                                    <InputLabel for="capacidad" value="capacidad" />
                                    <TextInput v-model="form.capacidad" type="text" id="capacidad"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.capacidad" class="mt-2"></InputError>
                                </div>
                                <div id="divMaterial2" class="hidden">
                                    <InputLabel for="material" value="material" />
                                    <TextInput v-model="form.material" type="text" id="material"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.material" class="mt-2"></InputError>
                                </div>
                                <div id="divPlataforma2">
                                    <InputLabel for="plataforma" value="plataforma" />
                                    <TextInput v-model="form.plataforma" type="text" id="plataforma"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.plataforma" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="requiere" value="requiere" />
                                    <select id="requiere" v-model="form.requiere" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Selecciona una opcion</option>
                                        <option value="MANTENIMIENTO">MANTENIMIENTO</option>
                                        <option value="REPARACION">REPARACION</option>
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
                                    <TextInput v-model="form.fecha" type="date" id="fecha" :disabled="!$page.props.user.permissions.includes('Acciones Administrador')"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.fecha" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="diagnostico" value="diagnostico" />
                                    <textarea id="diagnostico" v-model="form.diagnostico" rows="4" required
                                        class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Escriba las diagnostico..."></textarea>
                                    <InputError :message="form.errors.diagnostico" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="trabajos" value="trabajos" />
                                    <textarea id="trabajos" v-model="form.trabajos" rows="4" required
                                        class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Escriba las trabajos..."></textarea>
                                    <InputError :message="form.errors.trabajos" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div>
                                <div class="mt-0 flex justify-center items-center flex-wrap gap-y-0 sm:gap-x-2">
                                    <div class="sm:col-span-1 flex-1 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                                            <InputLabel for="foto" value="Foto"
                                                class="block text-sm font-medium text-gray-700" />
                                            <FileInput class="text-sm" name="foto" @change="($event) => onSelectFoto($event, 'foto')" />
                                            <InputError :message="$page.props.errors.foto" class="mt-2" />
                                            <div class="mt-2 flex items-center justify-center w-full"
                                                v-if="form.foto !== ''">
                                                <img :src="imagePreview1" alt="Vista previa de la foto"
                                                    class="p-2 block w-36 h-36 object-contain items-center text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-1 flex-1 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                                            <InputLabel for="foto2" value="Foto 2"
                                                class="block text-sm font-medium text-gray-700" />
                                            <FileInput class="text-sm"name="foto2" @change="($event) => onSelectFoto($event, 'foto2')" />
                                            <InputError :message="$page.props.errors.foto2" class="mt-2" />
                                            <div class="mt-2 flex items-center justify-center w-full"
                                                v-if="form.foto2 !== ''">
                                                <img :src="imagePreview2" alt="Vista previa de la foto 2"
                                                    class="p-2 block w-36 h-36 items-center object-contain text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-1 flex-1 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                                            <InputLabel for="foto3" value="Foto 3"
                                                class="block text-sm font-medium text-gray-700" />
                                            <FileInput class="text-sm" name="foto3" @change="($event) => onSelectFoto($event, 'foto3')" />
                                            <InputError :message="$page.props.errors.foto3" class="mt-2" />
                                            <div class="mt-2 flex items-center justify-center w-full"
                                                v-if="form.foto3 !== ''">
                                                <img :src="imagePreview3" alt="Vista previa de la foto 3"
                                                    class="p-2 block w-36 h-36 items-center object-contain text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-x-4 gap-y-4 justify-end">
                                    <ButtonResponsive id="btnguardarHojaServicio" class="uppercase text-xs">Guadar Informe Tecnico</ButtonResponsive>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="py-4 flex flex-col gap-4">
                        <div class="flex flex-col py-1">
                            <div class="flex md:w-96 w-full">
                                <span
                                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                    <i class='bx bxs-user-circle text-xl'></i>
                                </span>
                                <input id="filtrarRequerimientosHojas"
                                    class="w-full outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-primary-600 focus:border-primary-600 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    type="text" autocomplete="off"
                                    placeholder="Buscar datos de todas las hojas">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-800">
                            <table id="construirTablaBalanzas" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                                    <caption class="bg-gray-500 dark:bg-gray-700 p-2 w-full rounded-lt-lg border-b-2 text-sm font-bold text-gray-100">Balanzas</caption>
                                    <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                        <tr>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">N°</th>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Marca</th>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Capacidad</th>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Serie</th>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Div</th>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Plataforma</th>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Requiere</th>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Tecnico</th>
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyContenedorHojasServiciosBalanzas">
                                        <tr class="rounded-lg border-2 dark:border-gray-700 dark:text-gray-200"><td colspan="10" class="text-center">No hay datos</td></tr>
                                    </tbody>
                            </table>
                        </div>
                        <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-800">
                            <table id="construirTablaTermometros" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                                <caption class="bg-gray-500 dark:bg-gray-700 p-2 w-full rounded-lt-lg border-b-2 text-sm font-bold text-gray-100">Termometros</caption>
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">N°</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Marca</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Serie</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Rango</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Medida Bastago</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Div</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Requiere</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Tecnico</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyContenedorHojasServiciosTermometros">
                                    <tr class="rounded-lg border-2 dark:border-gray-700 dark:text-gray-200"><td colspan="10" class="text-center">No hay datos</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-800">
                            <table id="construirTablaPesas" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                                <caption class="bg-gray-500 dark:bg-gray-700 p-2 w-full rounded-lt-lg border-b-2 text-sm font-bold text-gray-100">Pesas</caption>
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">N°</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Codigo</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Capacidad</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Material</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Requiere</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Tecnico</th>
                                        <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyContenedorHojasServiciosPesas">
                                    <tr class="rounded-lg border-2 dark:border-gray-700 dark:text-gray-200"><td colspan="8" class="text-center">No hay datos</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
