<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Swal from 'sweetalert2';
import { formatDate } from '@/utils/funcionesglobales';
import { checkTableVisibility } from '@/utils/hFuncionesServicios';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import FileInput from '@/Components/FileInput.vue';
import axios from "axios";
import { Inertia } from '@inertiajs/inertia';
import TextPrueba from '@/Components/TextPrueba.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';

const{hservicios}=defineProps({
    hservicios:{
        type : Array,
        required:true
    }
});

const isEditing = ref(false);

const editHojaServicio = (hservicio) => {
    form.n_servicio = hservicio.n_servicio
    form.tecnico = hservicio.tecnico
    form.id = hservicio.id;
    form.hmarca_id = hservicio.hmarca_id;
    form.instrumento = hservicio.instrumento;
    form.rango = hservicio.rango;
    form.medida_bastago = hservicio.medida_bastago;
    form.medida_bastago2 = hservicio.medida_bastago2;
    form.codigo = hservicio.codigo;
    form.material = hservicio.material;
    form.modelo = hservicio.modelo;
    form.serie = hservicio.serie;
    form.division = hservicio.division;
    form.medida_division = hservicio.medida_division;
    form.capacidad = hservicio.capacidad;
    form.medida_capacidad = hservicio.medida_capacidad;
    form.cliente_razonSocial = hservicio.cliente_razonSocial;
    form.plataforma = hservicio.plataforma;
    form.medida_plataforma = hservicio.medida_plataforma;
    form.fecha = hservicio.fecha;
    form.requiere = hservicio.requiere;
    form.diagnostico = hservicio.diagnostico;
    form.trabajos = hservicio.trabajos;

    isEditing.value = true;
};

const imageInformeCli1 = ref('');
const imageInformeCli2 = ref('');
const imagePreviewsInformesCli = ref(['', '', '']);

const onSelectFoto = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
        const reader = new FileReader();
        reader.onload = (e) => {
            switch (fieldName) {
                case 'fotoInforme1':
                    imageInformeCli1.value = e.target.result;
                    imagePreviewsInformesCli.value[0] = e.target.result;
                    break;
                case 'fotoInforme2':
                    imageInformeCli2.value = e.target.result;
                    imagePreviewsInformesCli.value[1] = e.target.result;
                    break;
                default:
                    break;
            }
        };
        reader.readAsDataURL(files[0]);

        form[fieldName] = files[0];
    }
};

const setCurrentDate = () => {
    const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Lima' }).split('/').reverse().join('-');
    form.fecha_final = today;
};

onMounted(() => {
    setCurrentDate();
});

const form = useForm({
    n_servicio: '',
    tecnico: '',
    lugar_servicio: '',
    fecha_final: '',
    hmarca_id: '',
    instrumento: '1',
    rango: '',
    medida_bastago: '',
    medida_bastago2: '',
    codigo: '',
    material: '',
    modelo: '',
    serie: '',
    division: '',
    capacidad: '',
    cliente_id: '',
    plataforma: '',
    fecha: '',
    requiere: '',
    diagnostico: '',
    trabajos: '',
    fotoInforme1: '',
    fotoInforme2: '',
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
                        <td class="px-4 w-32 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${item.id}</td>
                        <td class="px-4 w-40 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${formatDate(item.fecha)}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-left">${item.razonSocial}</td>
                        <td class="px-4 w-24 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${item.balanzas}</td>
                        <td class="px-4 w-24 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${item.termometros}</td>
                        <td class="px-4 w-24 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${item.pesas}</td>
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
    let razonSocial = fila.find('td:eq(2)').text();
    let servicio = fila.find('td:eq(0)').text();
    let fecha = fila.find('td:eq(6)').text();
    
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
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center hidden">${ hservicio.id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ contadorbal++ }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.hmarca_id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.capacidad } ${hservicio.medida_capacidad}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.serie }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.division } ${hservicio.medida_division}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.plataforma} ${hservicio.medida_plataforma }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ hservicio.fecha }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize hidden">${ hservicio.n_servicio }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize hidden">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">
                            <button class="bg-blue-600 px-1 rounded rounded-md py-0.5 btn-generate-pdf"><i class='bx bx-printer text-lg text-white'></i></button>
                            <button class="bg-yellow-600 px-1.5 rounded rounded-md py-1 btnGenerarInformeTecCliente hidden"><i class='bx bxs-edit text-base text-white'></i></button>
                        </td>
                    </tr>
                    `;
                    $('#tbodyContenedorHojasServiciosBalanzas').append(nuevaFila);

                } else if (hservicio.instrumento == "2") {
                    // Para Termómetros
                    nuevaFila = `
                    <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center hidden">${ hservicio.id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ contadorter++ }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.hmarca_id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.serie }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.rango }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.medida_bastago } ${hservicio.medida_bastago2}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.division } ${hservicio.medida_division}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ hservicio.fecha }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize hidden">${ hservicio.n_servicio }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize hidden">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">
                            <button class="bg-blue-600 px-1 rounded rounded-md py-0.5 btn-generate-pdf"><i class='bx bx-printer text-lg text-white'></i></button>
                            <button class="bg-yellow-600 px-1.5 rounded rounded-md py-1 btnGenerarInformeTecCliente hidden"><i class='bx bxs-edit text-base text-white'></i></button>
                        </td>
                    </tr>
                    `;
                    $('#tbodyContenedorHojasServiciosTermometros').append(nuevaFila);

                } else if (hservicio.instrumento == "3") {
                    // Para Pesas
                    nuevaFila = `
                    <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center hidden">${ hservicio.id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ contadorpes++ }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.codigo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.capacidad }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.material }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ hservicio.fecha }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize hidden">${ hservicio.n_servicio }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize hidden">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">
                            <button class="bg-blue-600 px-1 rounded rounded-md py-0.5 btn-generate-pdf"><i class='bx bx-printer text-lg text-white'></i></button>
                            <button class="bg-yellow-600 px-1.5 rounded rounded-md py-1 btnGenerarInformeTecCliente hidden"><i class='bx bxs-edit text-base text-white'></i></button>
                        </td>
                    </tr>
                    `;
                    $('#tbodyContenedorHojasServiciosPesas').append(nuevaFila);
                }
            });

            $('#h1InformesTecnicos').hide();
            $('#h1DiagnosticodeInforme').show();
            $('#h1PdfDiagnosticoInforme').hide();
            $('#divTablitaHojasdeServicio').show();
            $('#retornarbody').hide();
            $('#divListarHojasdeServicio').hide();
            setCurrentDate();

            checkTableVisibility();

            function manejarHojaServicio(hservicio) {
                editHojaServicio(hservicio);
                let instrumento = hservicio.instrumento;
                actualizarVisibilidadIns(instrumento);
                $('#divMostrarInstrumento').show();
                $('#btnListarHojasdeServicio').hide();
                setCurrentDate();
                $('#retornarbody').show();
                $('#divTablasdeTodoslosDatosdeInstrumentos').hide();
            }

            $('#tbodyContenedorHojasServiciosBalanzas, #tbodyContenedorHojasServiciosTermometros, #tbodyContenedorHojasServiciosPesas').on('dblclick', 'tr', function() {
                let fila2 = $(this).closest('tr');
                let contadorDiagnosticoCliente = fila2.find('td:eq(1)').text();
                localStorage.setItem('contadorDiagnosticoCliente', contadorDiagnosticoCliente);
                let hservicio = JSON.parse($(this).attr('data-hservicio'));
                manejarHojaServicio(hservicio);
                $('#h1DiagnosticodeInforme').hide();
                $('#h1PdfDiagnosticoInforme').show();
            });

            $(document).on('click', '.btnGenerarInformeTecCliente', function() {
                let hservicio = JSON.parse($(this).closest('tr').attr('data-hservicio'));
                manejarHojaServicio(hservicio);
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

// $(document).on('click', '.btnGenerarInformeTecCliente', function() {
//     let fila = $(this).closest('tr');
//     let hservicioData = fila.attr('data-hservicio');
//     let hservicioId = JSON.parse(hservicioData).id;
//     return Inertia.get(route('requiere.cotiza', hservicioId));
// });

$(document).on('click', '.btn-generate-pdf', function (event) {
    let fila2 = $(this).closest('tr');
    let contadorbal = fila2.find('td:eq(1)').text();
    localStorage.setItem('contadorbalterpes', contadorbal);
    // console.log(contadorbal);

    let fila = $(this).closest('tr');
    let servicioId = fila.find('td:eq(0)').text();
    // console.log(servicioId);

    $.ajax({
        url: '/fn_consultarDatosDiagnosticosServ',
        method: 'GET',
        data: { id: servicioId }
    })
    .then(function (diagnosticoServicioResponse) {
        let hservicio = diagnosticoServicioResponse.hservicios[0];
        // console.log('Datos PE:', hservicio);

        previsualizarpdf(hservicio);
        
        Swal.close();
    })
    .catch(function (error) {
        console.error('Error en la solicitud Ajax:', error);
        Swal.fire('Error', 'No se pudieron cargar los datos.', 'error');
    });
});

function previsualizarpdf(hservicio) {
    // Función para obtener el nombre del día de la semana en español
    function getNombreDia(dia) {
        const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        return dias[dia];
    }

    // Función para obtener el nombre del mes en español
    function getNombreMes(mes) {
        const meses = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        return meses[mes];
    }

    // Función para formatear la fecha en el formato deseado
    function obtenerFechaFormateada(fechaTexto) {
        const [year, month, day] = fechaTexto.split('-');
        const fecha = new Date(year, month - 1, day);

        const diaSemana = getNombreDia(fecha.getDay());
        const dia = fecha.getDate().toString().padStart(2, '0');
        const mes = getNombreMes(fecha.getMonth());
        const año = fecha.getFullYear();

        return `${diaSemana} ${dia} de ${mes} del ${año}`;
    }

    // Utilizamos la fecha del hservicio para formatearla
    const fechaFormateada = obtenerFechaFormateada(hservicio.hservicio_fecha);

    // Construcción del PDF
    const doc = new jsPDF();

    let eje_y = 10;
    let eje_x = 10;
    let margenDerecho = 10;
    let anchoPagina = doc.internal.pageSize.width;

    doc.setTextColor(0, 0, 0);
    doc.setFontSize(9);
    doc.setFont('Helvetica', 'normal');

    // Agregar imagen de fondo
    const backgroundImg = '/img/logo_ini.png';
    doc.addImage(backgroundImg, 'JPEG', eje_x, eje_y, 60, 20);

    eje_y += 5;

    // Dibujar encabezado
    function fn_dibujarEncabezado(texto) {
        const anchoTexto = doc.getTextWidth(texto);
        const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
        doc.text(eje_x_left, eje_y, texto);
    }

    fn_dibujarEncabezado("Av. Separadora Mz A LT 8 Sector 28 de Julio");
    eje_y += 5;
    fn_dibujarEncabezado("Telf: 955571986 - 924808237 - 934094721");
    eje_y += 5;
    fn_dibujarEncabezado("Correo: industriasbalinsa@gmail.com");
    eje_y += 5;
    fn_dibujarEncabezado("RUC: 20608165585");

    eje_y += 8;

    doc.setFontSize(12);
    doc.setFont('Helvetica', 'bold');
    fn_dibujarEncabezado(`REPORTE DE INFORME TECNICO : N° ${hservicio.servicio_id}`);
    // doc.text(eje_x, eje_y, "asgjd");

    // ========== Inicia Función Dibujar Datos Generales==========

    function fn_dibujarDatosGenerales(inicioTabla) {
        doc.autoTable({
            body: [
                ['DATOS GENERALES']
            ],
            rowPageBreak: 'avoid',
            theme: 'grid',
            styles: {
                fontSize: 8,
                cellPadding: 2,
                lineWidth: 0.30,
                lineColor: [0, 0, 0],
                textColor: [0, 0, 0]
            },
            margin: { left: 10, right: 10 },
            startY: inicioTabla,
            columnStyles: {
                0: {
                    cellWidth: 190,
                    fontStyle: 'bold'
                }
            },
        });

        doc.autoTable({
            body: [
                [
                    'Técnico Responsable',
                    hservicio.tecnico,
                    'Fecha de Registro',
                    fechaFormateada

                ]
            ],
            rowPageBreak: 'avoid',
            theme: 'grid',
            styles: { 
                fontSize: 8, 
                cellPadding: 2,
                lineWidth: 0.30,
                lineColor: [0, 0, 0]
            },
            margin: {left: 10 , right: 10},
            startY: doc.lastAutoTable.finalY,
            columnStyles: {
                0: {
                    cellWidth: 40,
                    fontStyle: 'bold',
                    textColor: [0, 0, 0]
                },
                1: {
                    cellWidth: 55
                },
                2: {
                    cellWidth: 30,
                    fontStyle: 'bold',
                    textColor: [0, 0, 0]
                }
            },
        });
        doc.autoTable({
            body: [
                [
                    'Cliente',
                    hservicio.razonSocial
                ]
            ],
            rowPageBreak: 'avoid',
            theme: 'grid',
            styles: { 
                fontSize: 8, 
                cellPadding: 2,
                lineWidth: 0.30,
                lineColor: [0, 0, 0]
            },
            margin: { top: 30 , left: 10 , right: 10},
            startY: doc.lastAutoTable.finalY,
            columnStyles: {
                0: {
                    cellWidth: 40,
                    fontStyle: 'bold',
                    textColor: [0, 0, 0]
                }
            },
        });
    }

    // ========== Termina Función Dibujar Datos Generales==========

    // ========== Inicia Función Dibujar Datos del Equipo ==========

    function fn_dibujarDatosdelEquipo() {
        let contadores = localStorage.getItem('contadorbalterpes');
        // console.log(hservicio.instrumento)
        if (hservicio.instrumento == "1"){
            doc.autoTable({
                body: [
                    [
                        `DATOS DEL EQUIPO : BALANZA N°- ${contadores}`
                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0],
                    textColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY + 6,
                columnStyles: {
                    0: {
                        cellWidth: 190,
                        fontStyle: 'bold'
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'MARCA',
                        hservicio.hmarca_id,
                        'MODELO',
                        hservicio.modelo

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'SERIE',
                        hservicio.serie,
                        'DIVISION',
                        hservicio.division + ' ' + hservicio.medida_division

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'CAPACIDAD',
                        hservicio.capacidad + ' ' + hservicio.medida_capacidad,
                        'PLATAFORMA',
                        hservicio.plataforma + ' ' + hservicio.medida_plataforma

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'REQUIERE',
                        hservicio.requiere

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 150
                    }
                },
            });
        } else if (hservicio.instrumento == "2"){
            doc.autoTable({
                body: [
                    [
                        `DATOS DEL EQUIPO : TERMOMETRO N°- ${contadores}`
                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0],
                    textColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY + 6,
                columnStyles: {
                    0: {
                        cellWidth: 190,
                        fontStyle: 'bold'
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'MARCA',
                        hservicio.hmarca_id,
                        'MODELO',
                        hservicio.modelo

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'SERIE',
                        hservicio.serie,
                        'DIVISION',
                        hservicio.division + ' ' + hservicio.medida_division

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'RANGO',
                        hservicio.rango,
                        'MEDIDA DE BASTAGO',
                        hservicio.medida_bastago + ' ' + hservicio.medida_bastago2

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'REQUIERE',
                        hservicio.requiere

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 150
                    }
                },
            });
        } else if (hservicio.instrumento == "3"){
            doc.autoTable({
                body: [
                    [
                        `DATOS DEL EQUIPO : PESA N°- ${contadores}`
                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0],
                    textColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY + 6,
                columnStyles: {
                    0: {
                        cellWidth: 190,
                        fontStyle: 'bold'
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'MODELO',
                        hservicio.modelo,
                        'CAPACIDAD',
                        hservicio.capacidad + ' ' + hservicio.medida_capacidad

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'CODIGO',
                        hservicio.codigo,
                        'MATERIAL',
                        hservicio.material,

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'REQUIERE',
                        hservicio.requiere

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 150
                    }
                },
            });
        }
    }

    // ========== Termina Función Dibujar Datos del Equipo ==========

    // ========== Inicia Función Dibujar Cuentas ==========

    function fn_dibujarDiagnosticoTecnico(){
        doc.autoTable({
            body: [
                [
                    { content: 'DIAGNOSTICO DEL TECNICO :', styles: { halign: 'left' , fontStyle: 'bold', textColor: [0, 0, 0]} }
                ]
            ],
            rowPageBreak: 'avoid',
            theme: 'grid',
            styles: { 
                fontSize: 8, 
                cellPadding: { top: 2, bottom: 1, left: 2, right: 2 },
                lineWidth: 0.30,
                lineColor: [0, 0, 0]
            },
            margin: {left: 10 , right: 10},
            startY: doc.lastAutoTable.finalY + 5,
            columnStyles: {
                0: {
                    cellWidth: 190,
                    fontStyle: 'bold'
                }
            },
        });

        const lineYPosition = doc.lastAutoTable.finalY;

        doc.autoTable({
            body: [
                [
                    { content: hservicio.diagnostico, styles: { halign: 'left' , fontStyle: 'bold' } }
                ],
            ],
            rowPageBreak: 'avoid',
            theme: 'grid',
            styles: { 
                fontSize: 8, 
                cellPadding: { top: 1, bottom: 2, left: 8, right: 8 },
                lineWidth: 0.30,
                lineColor: [0, 0, 0]
            },
            margin: {left: 10 , right: 10},
            startY: doc.lastAutoTable.finalY
        });

        doc.setDrawColor(255, 255, 255);
        doc.setLineWidth(1);
        doc.line(10.1, lineYPosition, doc.internal.pageSize.width - 10.1, lineYPosition);
    }

    // ========== Termina Función Dibujar Cuentas ==========

    // ========== Inicia Función Dibujar Trabajos ==========

    function fn_dibujarTrabajos(){
        doc.autoTable({
            body: [
                [
                    { content: 'TRABAJOS A REALIZAR :', styles: { halign: 'left' , fontStyle: 'bold', textColor: [0, 0, 0]} }
                ]
            ],
            rowPageBreak: 'avoid',
            theme: 'grid',
            styles: { 
                fontSize: 8, 
                cellPadding: { top: 2, bottom: 1, left: 2, right: 2 },
                lineWidth: 0.30,
                lineColor: [0, 0, 0]
            },
            margin: {left: 10 , right: 10},
            startY: doc.lastAutoTable.finalY + 5,
            columnStyles: {
                0: {
                    cellWidth: 190,
                    fontStyle: 'bold'
                }
            },
        });

        const lineYPosition2 = doc.lastAutoTable.finalY;

        doc.autoTable({
            body: [
                [
                    { content: hservicio.trabajos, styles: { halign: 'left' , fontStyle: 'bold' } }
                ],
            ],
            rowPageBreak: 'avoid',
            theme: 'grid',
            styles: { 
                fontSize: 8, 
                cellPadding: { top: 1, bottom: 2, left: 8, right: 8 },
                lineWidth: 0.30,
                lineColor: [0, 0, 0]
            },
            margin: {left: 10 , right: 10},
            startY: doc.lastAutoTable.finalY
        });

        doc.setDrawColor(255, 255, 255);
        doc.setLineWidth(1);
        doc.line(10.1, lineYPosition2, doc.internal.pageSize.width - 10.1, lineYPosition2);
    }

    // ========== Termina Función Dibujar Trabajos ==========
    
    // ========== Inicia Función Dibujar Fotos ==========

    function fn_dibujarFotos() {
        const columnWidth = 63.3;
        const startY = doc.lastAutoTable.finalY + 5;
        const totalWidth = columnWidth * 3;
        const imagenes = [
            hservicio.foto ? "/hservicio_img/" + hservicio.foto : null,
            hservicio.foto2 ? "/hservicio_img/" + hservicio.foto2 : null,
            hservicio.foto3 ? "/hservicio_img/" + hservicio.foto3 : null
        ];

        const containerWidth = 20;
        const containerHeight = 20;
        const reductionFactor = 0.8;

        const imgWidth = containerWidth * reductionFactor;
        const imgHeight = containerHeight * reductionFactor;

        doc.autoTable({
            head: [['FOTO 1', 'FOTO 2', 'FOTO 3']],
            body: [
                [
                    { content: '', styles: { cellWidth: columnWidth, minCellHeight: columnWidth } },
                    { content: '', styles: { cellWidth: columnWidth, minCellHeight: columnWidth } },
                    { content: '', styles: { cellWidth: columnWidth, minCellHeight: columnWidth } }
                ]
            ],
            startY: startY,
            theme: 'grid',
            styles: {
                rowPageBreak: 'avoid',
                fontSize: 8,
                lineWidth: 0.30,
                lineColor: [0, 0, 0],
                halign: 'center'
            },
            tableWidth: totalWidth,
            margin: { left: 10 },
            didDrawCell: function (data) {
                // Verificar si la celda pertenece al cuerpo (body) de la tabla
                if (data.cell.section === 'body') {
                    const image = imagenes[data.column.index];

                    if (!image) {
                        doc.text("Sin Imagen", data.cell.x + (data.cell.width / 2), data.cell.y + (data.cell.height / 2), { align: 'center' });
                        return;
                    }

                    const imageProps = doc.getImageProperties(image);
                    const cellWidth = data.cell.width;
                    const cellHeight = data.cell.height;
                    const imageWidth = imageProps.width;
                    const imageHeight = imageProps.height;
                    const aspectRatio = imageWidth / imageHeight;

                    let drawWidth, drawHeight;

                    if (cellWidth / cellHeight > aspectRatio) {
                        drawHeight = cellHeight;
                        drawWidth = drawHeight * aspectRatio;
                    } else {
                        drawWidth = cellWidth;
                        drawHeight = drawWidth / aspectRatio;
                    }

                    const x = data.cell.x + (cellWidth - drawWidth) / 2;
                    const y = data.cell.y + (cellHeight - drawHeight) / 2;

                    doc.addImage(image, 'JPEG', x + 1, y + 1, drawWidth - 2, drawHeight - 2);
                }
            }
        });
    }

    // ========== Termina Función Dibujar Fotos ==========

    const inicioTabla = 42;
    fn_dibujarDatosGenerales(inicioTabla);

    doc.setFontSize(8);
    doc.setFont('Helvetica', 'bold');
    fn_dibujarDatosdelEquipo();

    doc.setFont('Helvetica', 'normal');
    fn_dibujarDiagnosticoTecnico();

    doc.setFont('Helvetica', 'normal');
    fn_dibujarTrabajos();

    fn_dibujarFotos();

    const nombreArchivo = `REPORTE DE SERVICIO TECNICO DEL CLIENTE ${hservicio.razonSocial} CON N° - ${hservicio.servicio_id}.pdf`;
    doc.setProperties({
        title: nombreArchivo
    });

    const blobUrl = doc.output('bloburl');
    window.open(blobUrl);
}

$(document).on("click", "#retornarbody", function() {
    $('#btnListarHojasdeServicio').show();
    $('#h1DiagnosticodeInforme').show();
    $('#h1PdfDiagnosticoInforme').hide();
    $('#retornarbody').hide();
    $('#divMostrarInstrumento').hide();
    $('#divTablasdeTodoslosDatosdeInstrumentos').show();
    form.reset();
});

$(document).on("click", "#btnListarHojasdeServicio", function() {
    $('#h1DiagnosticodeInforme').hide();
    $('#h1PdfDiagnosticoInforme').hide();
    $('#h1InformesTecnicos').show();
    $('#divMostrarImagenesInforme').hide();
    $('#divTablitaHojasdeServicio').hide();
    $('#divListarHojasdeServicio').show();
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

function previewPDF2() {
    Swal.fire({
        title: '¡Atención!',
        html: 'El informe tecnico del cliente se está generando, espere un momento.',
        timer: 999999999, // Establece un valor grande para que parezca indefinido
        timerProgressBar: true,
        allowOutsideClick: false, // Desactiva el clic fuera de la alerta
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    setTimeout(() => {
        let contadorDiagnosticoCliente = localStorage.getItem('contadorDiagnosticoCliente');
        const hmarca_id = document.getElementById("hmarca_id").value;
        const instrumento = document.getElementById('instrumento').options[document.getElementById("instrumento").selectedIndex].text;
        const rango = document.getElementById("rango").value;
        const medida_bastago = document.getElementById("medida_bastago").value;
        const medida_bastago2 = $("#medida_bastago2").val();
        const codigo = document.getElementById("codigo").value;
        const material = document.getElementById("material").value;
        const modelo = document.getElementById("modelo").value;
        const serie = document.getElementById("serie").value;
        const division = document.getElementById("division").value;
        const medida_division = document.getElementById('medida_division').options[document.getElementById("medida_division").selectedIndex].text;
        const capacidad = document.getElementById("capacidad").value;
        const medida_capacidad = document.getElementById('medida_capacidad').options[document.getElementById("medida_capacidad").selectedIndex].text;
        const cliente_id = document.getElementById("cliente_id").value;
        const n_servicio = document.getElementById("n_servicio").value;
        const tecnico = document.getElementById("tecnico").value;
        const lugar_servicio = document.getElementById("lugar_servicio").value;
        const fecha_final = document.getElementById("fecha_final").value;
        const plataforma = document.getElementById("plataforma").value;
        const medida_plataforma = $("#medida_plataforma").val();
        const fecha = document.getElementById("fecha").value;
        const requiere = document.getElementById("requiere").value;
        const diagnostico = document.getElementById("diagnostico").value;
        const trabajos = document.getElementById("trabajos").value;
        // Función para obtener el nombre del día de la semana en español
        function getNombreDia(dia) {
            const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            return dias[dia];
        }

        // Función para obtener el nombre del mes en español
        function getNombreMes(mes) {
            const meses = [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ];
            return meses[mes];
        }

        // Función para formatear la fecha en el formato deseado
        function obtenerFechaFormateada(fechaTexto) {
            const [year, month, day] = fechaTexto.split('-');
            const fecha = new Date(year, month - 1, day);

            const diaSemana = getNombreDia(fecha.getDay());
            const dia = fecha.getDate().toString().padStart(2, '0');
            const mes = getNombreMes(fecha.getMonth());
            const año = fecha.getFullYear();

            return `${diaSemana} ${dia} de ${mes} del ${año}`;
        }

        // Utilizamos la fecha del hservicio para formatearla
        const fechaInicio = obtenerFechaFormateada(fecha);
        const fechaFinal = obtenerFechaFormateada(fecha_final);

        // Construcción del PDF
        const doc = new jsPDF();

        let eje_y = 10;
        let eje_x = 10;
        let margenDerecho = 10;
        let anchoPagina = doc.internal.pageSize.width;

        doc.setTextColor(0, 0, 0);
        doc.setFontSize(9);
        doc.setFont('Helvetica', 'normal');

        // Agregar imagen de fondo
        const backgroundImg = '/img/logo_ini.png';
        doc.addImage(backgroundImg, 'JPEG', eje_x, eje_y, 60, 20);

        eje_y += 5;

        // Dibujar encabezado
        function fn_dibujarEncabezado(texto) {
            const anchoTexto = doc.getTextWidth(texto);
            const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
            doc.text(eje_x_left, eje_y, texto);
        }

        fn_dibujarEncabezado("Av. Separadora Mz A LT 8 Sector 28 de Julio");
        eje_y += 5;
        fn_dibujarEncabezado("Telf: 955571986 - 924808237 - 934094721");
        eje_y += 5;
        fn_dibujarEncabezado("Correo: industriasbalinsa@gmail.com");
        eje_y += 5;
        fn_dibujarEncabezado("RUC: 20608165585");

        eje_y += 8;

        doc.setFontSize(12);
        doc.setFont('Helvetica', 'bold');
        fn_dibujarEncabezado(`REPORTE DE INFORME TECNICO : N° ${n_servicio}`);
        // doc.text(eje_x, eje_y, "asgjd");

        // ========== Inicia Función Dibujar Datos Generales==========

        function fn_dibujarDatosGenerales(inicioTabla) {
            doc.autoTable({
                body: [
                    [
                        'DATOS GENERALES',
                        'Fecha Inicio',
                        fechaInicio
                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0],
                    textColor: [0, 0, 0]
                },
                margin: { left: 10, right: 10 },
                startY: inicioTabla,
                columnStyles: {
                    0: {
                        cellWidth: 95,
                        fontStyle: 'bold'
                    },
                    1: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                    },
                    2: {
                        cellWidth: 65,
                    }
                },
            });

            doc.autoTable({
                body: [
                    [
                        'Técnico Responsable',
                        tecnico,
                        'Fecha Final',
                        fechaFinal

                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 8, 
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: {left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    },
                    3: {
                        cellWidth: 65,
                        fontStyle: 'bold',
                    }
                },
            });
            doc.autoTable({
                body: [
                    [
                        'Cliente',
                        cliente_id
                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 8, 
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: { top: 30 , left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold',
                        textColor: [0, 0, 0]
                    }
                },
            });
        }

        // ========== Termina Función Dibujar Datos Generales==========

        // ========== Inicia Función Dibujar Datos del Equipo ==========

        function fn_dibujarDatosdelEquipo() {
            if (instrumento == "Balanzas"){
                doc.autoTable({
                    body: [
                        [
                            `DATOS DEL EQUIPO : BALANZA N°- ${contadorDiagnosticoCliente}`
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0],
                        textColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 6,
                    columnStyles: {
                        0: {
                            cellWidth: 190,
                            fontStyle: 'bold'
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'MARCA',
                            hmarca_id,
                            'MODELO',
                            modelo

                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'SERIE',
                            serie,
                            'DIVISION',
                            division + ' ' + medida_division

                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'CAPACIDAD',
                            capacidad + ' ' + medida_capacidad,
                            'MEDIDA DE PLATAFORMA',
                            plataforma + ' ' + medida_plataforma

                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'REQUIERE',
                            requiere,
                            'LUGAR DE SERVICIO',
                            lugar_servicio,
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });
            } else if (instrumento == "Termometros"){
                doc.autoTable({
                    body: [
                        [
                            `DATOS DEL EQUIPO : TERMOMETRO N°- ${contadorDiagnosticoCliente}`
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0],
                        textColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 6,
                    columnStyles: {
                        0: {
                            cellWidth: 190,
                            fontStyle: 'bold'
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'MARCA',
                            hmarca_id,
                            'MODELO',
                            modelo

                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'SERIE',
                            serie,
                            'DIVISION',
                            division + ' ' + medida_division

                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'RANGO',
                            rango,
                            'MEDIDA DE BASTAGO',
                            medida_bastago + ' ' + medida_bastago2

                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'REQUIERE',
                            requiere,
                            'LUGAR DE SERVICIO',
                            lugar_servicio,
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });
            } else if (instrumento == "Pesas"){
                doc.autoTable({
                    body: [
                        [
                            `DATOS DEL EQUIPO : PESA N°- ${contadorDiagnosticoCliente}`
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0],
                        textColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 6,
                    columnStyles: {
                        0: {
                            cellWidth: 190,
                            fontStyle: 'bold'
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'MODELO',
                            modelo,
                            'CAPACIDAD',
                            capacidad + ' ' + medida_capacidad

                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'CODIGO',
                            codigo,
                            'MATERIAL',
                            material,

                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'REQUIERE',
                            requiere,
                            'LUGAR DE SERVICIO',
                            lugar_servicio,
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 50
                        },
                        2: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });
            }
        }

        // ========== Termina Función Dibujar Datos del Equipo ==========

        // ========== Inicia Función Dibujar Cuentas ==========

        function fn_dibujarDiagnosticoTecnico(){
            doc.autoTable({
                body: [
                    [
                        { content: 'DIAGNOSTICO DEL TECNICO :', styles: { halign: 'left' , fontStyle: 'bold', textColor: [0, 0, 0]} }
                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 8, 
                    cellPadding: { top: 2, bottom: 1, left: 2, right: 2 },
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: {left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY + 5,
                columnStyles: {
                    0: {
                        cellWidth: 190,
                        fontStyle: 'bold'
                    }
                },
            });

            const lineYPosition = doc.lastAutoTable.finalY;

            doc.autoTable({
                body: [
                    [
                        { content: diagnostico, styles: { halign: 'left' , fontStyle: 'bold' } }
                    ],
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 8, 
                    cellPadding: { top: 1, bottom: 2, left: 8, right: 8 },
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: {left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY
            });

            doc.setDrawColor(255, 255, 255);
            doc.setLineWidth(1);
            doc.line(10.1, lineYPosition, doc.internal.pageSize.width - 10.1, lineYPosition);
        }

        // ========== Termina Función Dibujar Cuentas ==========

        // ========== Inicia Función Dibujar Trabajos ==========

        function fn_dibujarTrabajos(){
            doc.autoTable({
                body: [
                    [
                        { content: 'TRABAJOS A REALIZAR :', styles: { halign: 'left' , fontStyle: 'bold', textColor: [0, 0, 0]} }
                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 8, 
                    cellPadding: { top: 2, bottom: 1, left: 2, right: 2 },
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: {left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY + 5,
                columnStyles: {
                    0: {
                        cellWidth: 190,
                        fontStyle: 'bold'
                    }
                },
            });

            const lineYPosition2 = doc.lastAutoTable.finalY;

            doc.autoTable({
                body: [
                    [
                        { content: trabajos, styles: { halign: 'left' , fontStyle: 'bold' } }
                    ],
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 8, 
                    cellPadding: { top: 1, bottom: 2, left: 8, right: 8 },
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                margin: {left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY
            });

            doc.setDrawColor(255, 255, 255);
            doc.setLineWidth(1);
            doc.line(10.1, lineYPosition2, doc.internal.pageSize.width - 10.1, lineYPosition2);
        }

        // ========== Termina Función Dibujar Trabajos ==========
        
        // ========== Inicia Función Dibujar Fotos ==========

        function fn_dibujarFotos() {
            const columnWidth = 63.3;
            const startY = doc.lastAutoTable.finalY + 5;
            const totalWidth = columnWidth * 2;
            
            const imagenes = [
                imageInformeCli1.value ? imageInformeCli1.value : '/img/sinFoto.png',
                imageInformeCli2.value ? imageInformeCli2.value : '/img/sinFoto.png',
            ];
            
            doc.autoTable({
                head: [['IMAGEN ANTES', 'IMAGEN DESPUES']],
                body: [
                    [
                        { content: '', styles: { cellWidth: columnWidth, minCellHeight: columnWidth } },
                        { content: '', styles: { cellWidth: columnWidth, minCellHeight: columnWidth } },
                    ]
                ],
                startY: startY,
                theme: 'grid',
                styles: {
                    rowPageBreak: 'avoid',
                    fontSize: 8,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0],
                    halign: 'center'
                },
                tableWidth: totalWidth,
                margin: { left: 45 },
                didDrawCell: function (data) {
                    if (data.cell.section === 'body') {
                        const image = imagenes[data.column.index];

                        if (!image) {
                            doc.text("Sin Imagen", data.cell.x + (data.cell.width / 2), data.cell.y + (data.cell.height / 2), { align: 'center' });
                            return;
                        }

                        // Propiedades de la imagen
                        const imageProps = doc.getImageProperties(image);
                        const cellWidth = data.cell.width;
                        const cellHeight = data.cell.height;
                        const imageWidth = imageProps.width;
                        const imageHeight = imageProps.height;
                        const aspectRatio = imageWidth / imageHeight;

                        let drawWidth, drawHeight;

                        // Calcular el tamaño de la imagen dentro de la celda
                        if (cellWidth / cellHeight > aspectRatio) {
                            drawHeight = cellHeight;
                            drawWidth = drawHeight * aspectRatio;
                        } else {
                            drawWidth = cellWidth;
                            drawHeight = drawWidth / aspectRatio;
                        }

                        // Posicionar la imagen centrada en la celda
                        const x = data.cell.x + (cellWidth - drawWidth) / 2;
                        const y = data.cell.y + (cellHeight - drawHeight) / 2;

                        // Agregar la imagen a la celda
                        doc.addImage(image, 'JPEG', x + 1, y + 1, drawWidth - 2, drawHeight - 2);
                    }
                }
            });
        }


        // ========== Termina Función Dibujar Fotos ==========

        const inicioTabla = 42;
        fn_dibujarDatosGenerales(inicioTabla);

        doc.setFontSize(8);
        doc.setFont('Helvetica', 'bold');
        fn_dibujarDatosdelEquipo();

        doc.setFont('Helvetica', 'normal');
        fn_dibujarDiagnosticoTecnico();

        doc.setFont('Helvetica', 'normal');
        fn_dibujarTrabajos();

        fn_dibujarFotos();

        const nombreArchivo = `REPORTE DE SERVICIO TECNICO PARA EL CLIENTE ${cliente_id} CON N° - ${n_servicio}.pdf`;
        doc.setProperties({
            title: nombreArchivo
        });

        const blobUrl = doc.output('bloburl');
        window.open(blobUrl);

        Swal.close();
    }, 3000);
}

const submitForm = async (event) => {
    event.preventDefault();

    let formData = new FormData();
    formData.append('hmarca_id', form.hmarca_id);
    formData.append('lugar_servicio', form.lugar_servicio);
    formData.append('instrumento', form.instrumento);
    formData.append('rango', form.rango);
    formData.append('medida_bastago', form.medida_bastago);
    formData.append('medida_bastago2', form.medida_bastago2);
    formData.append('codigo', form.codigo);
    formData.append('material', form.material);
    formData.append('modelo', form.modelo);
    formData.append('serie', form.serie);
    formData.append('division', form.division);
    formData.append('medida_division', form.medida_division);
    formData.append('capacidad', form.capacidad);
    formData.append('medida_capacidad', form.medida_capacidad);
    formData.append('cliente_id', form.cliente_id);
    formData.append('plataforma', form.plataforma);
    formData.append('medida_plataforma', form.medida_plataforma);
    formData.append('fecha', form.fecha);
    formData.append('fecha_final', form.fecha_final);
    formData.append('requiere', form.requiere);
    formData.append('diagnostico', form.diagnostico);
    formData.append('trabajos', form.trabajos);
    formData.append('tecnico', form.tecnico);
    formData.append('n_servicio', form.n_servicio);

    // Incluir las imágenes solo si se seleccionaron
    if (form.fotoInforme1) {
        formData.append('fotoInforme1', form.fotoInforme1);
    }
    if (form.fotoInforme2) {
        formData.append('fotoInforme2', form.fotoInforme2);
    }

    try {
        const response = await axios.post('/fn_registrarInformesTecnicosdeCliente', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        console.log(response.data);
    } catch (error) {
        console.error(error);
    }
};
</script>

<template>
    <AppLayout title="Informes Tecnicos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white" id="h1InformesTecnicos">Informes Tecnicos</h1>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white hidden" id="h1DiagnosticodeInforme">Diagnosticos de Informes Tecnicos</h1>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white hidden" id="h1PdfDiagnosticoInforme">Informe Tecnico / Cliente</h1>
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
                                    <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">N° de Informe</th>
                                    <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Fecha</th>
                                    <th scope="col" class="px-5 py-3 text-start dark:border-white border-b-2">Cliente</th>
                                    <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Balanzas</th>
                                    <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Termometros</th>
                                    <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Pesas</th>
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
                        <button class="text-white bg-blue-600 flex justify-center items-center text-center md:gap-2 gap-4 font-bold hover:bg-blue-700 uppercase text-sm py-2 px-6 rounded md:w-min whitespace-nowrap w-full" id="btnListarHojasdeServicio">
                            <i class="fa-solid fa-arrow-left"></i>Listar Informes Tecnicos
                        </button>
                    </div>
                    <div class="py-2">
                        <div class="flex items-center font-bold p-4 border py-1 border-green-600 rounded bg-green-600">
                            <div class="bg-yellow-500 sm:text-base text-xs font-bold py-2 p-2 rounded text-white"> N° INFORME: <span id="nroInformeTecnico" class="mx-10"></span></div>
                            <p class="py-3.5 sm:text-base text-xs text-white font-bold mx-4">PARA : <span id="razonSocialTexto"></span></p>
                        </div>
                    </div>
                    <div id="divMostrarInstrumento" class="hidden">
                        <form @submit.prevent="submitForm">
                            <div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 md:mt-2 gap-y-4 mt-2 mb-1 gap-x-6 items-start">
                                    <div class="hidden">
                                        <InputLabel for="cliente_id" value="Cliente" />
                                        <TextInput v-model="form.cliente_razonSocial" type="text" id="cliente_id"
                                            disabled class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div class="hidden">
                                        <InputLabel for="tecnico" value="Tecnico" />
                                        <TextInput v-model="form.tecnico" type="text" id="tecnico"
                                            disabled class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div class="hidden">
                                        <InputLabel for="n_servicio" value="N° Servicio" />
                                        <TextInput v-model="form.n_servicio" type="text" id="n_servicio"
                                            disabled class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div class="">
                                        <InputLabel for="lugar_servicio" value="Lugar de Servicio" />
                                        <TextInput v-model="form.lugar_servicio" type="text" id="lugar_servicio"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div>
                                        <InputLabel for="fecha_final" value="Fecha Final"
                                            class="block text-md font-medium text-gray-700 " />
                                        <TextInput v-model="form.fecha_final" type="date" id="fecha_final"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div class="mt-1">
                                        <InputLabel for="instrumento" class="block text-xs uppercase font-medium text-black dark:text-white">Instrumento</InputLabel>
                                        <select v-model="form.instrumento" disabled id="instrumento" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                <option value="1" selected>Balanzas</option>
                                                <option value="2">Termometros</option>
                                                <option value="3">Pesas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-2">
                                    <div id="divMarca">
                                        <InputLabel for="hmarca_id" value="Marca" />
                                        <TextInput v-model="form.hmarca_id" type="text" id="hmarca_id"
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
                                            <div class="flex">
                                            <TextPrueba disabled v-model="form.medida_bastago" type="text" id="medida_bastago"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg" />
                                                <InputError :message="form.errors.medida_bastago" class="mt-2"></InputError>
                                                <input disabled id="medida_bastago2" v-model="form.medida_bastago2" class="mt-1 text-center focus:ring-indigo-500 focus:border-indigo-500 block w-16 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg"/>
                                            </div>
                                    </div>
                                    <div id="divDivision">
                                        <InputLabel for="div" value="Div" />
                                        <div class="flex justify-center">
                                            <TextPrueba v-model="form.division" type="text" id="division" disabled
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg" />
                                            <InputError :message="form.errors.division" class="mt-2"></InputError>
                                            <select disabled id="medida_division" v-model="form.medida_division" required
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-24 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg">
                                                <option selected value="Kg">Kg</option>
                                                <option value="g">g</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="divCapacidad">
                                        <InputLabel for="capacidad" value="Capacidad" />
                                        <div class="flex justify-center">
                                            <TextPrueba v-model="form.capacidad" type="text" id="capacidad" disabled
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg" />
                                            <InputError :message="form.errors.capacidad" class="mt-2"></InputError>
                                            <select disabled id="medida_capacidad" v-model="form.medida_capacidad" required
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-24 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg">
                                                <option selected value="Kg">Kg</option>
                                                <option value="g">g</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="divMaterial" class="hidden">
                                        <InputLabel for="material" value="Material" />
                                        <TextInput v-model="form.material" type="text" id="material" disabled
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div id="divPlataforma">
                                        <InputLabel for="plataforma" value="Medida de Plataforma" />
                                        <div class="flex justify-center items-center">
                                            <TextPrueba v-model="form.plataforma" type="text" id="plataforma" disabled
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg" />
                                            <InputError :message="form.errors.plataforma" class="mt-2"></InputError>
                                            <input disabled id="medida_plataforma" v-model="form.medida_plataforma" class="mt-1 text-center focus:ring-indigo-500 focus:border-indigo-500 block w-16 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg"/>
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel for="requiere" value="Requiere" />
                                        <select disabled id="requiere" v-model="form.requiere" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="">Selecciona una opcion</option>
                                            <option value="DIAGNOSTICO">DIAGNOSTICO</option>
                                            <option value="MANTENIMIENTO PREVENTIVO">MANTENIMIENTO PREVENTIVO</option>
                                            <option value="MANTENIMIENTO CORRECTIVO">MANTENIMIENTO CORRECTIVO</option>
                                            <option value="CALIBRACION BALINSA">CALIBRACION BALINSA</option>
                                            <option value="CALIBRACION TOTAL WEIGHT">CALIBRACION TOTAL WEIGHT</option>
                                            <option value="GARANTIA">GARANTIA</option>
                                            <option value="INOPERATIVA">INOPERATIVA</option>
                                        </select>
                                    </div>
                                    <div>
                                        <InputLabel for="fecha" value="Fecha Inicio"
                                            class="block text-md font-medium text-gray-700 " />
                                        <TextInput v-model="form.fecha" type="date" id="fecha"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3">
                                    <div>
                                        <InputLabel for="diagnostico" value="Diagnostico Tecnico" />
                                        <textarea id="diagnostico" rows="4" required v-model="form.diagnostico"
                                            class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Escriba las diagnostico..."></textarea>
                                    </div>
                                    <div>
                                        <InputLabel for="trabajos" value="Trabajos a Realizar" />
                                        <textarea id="trabajos" rows="4" required v-model="form.trabajos"
                                            class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Escriba las trabajos..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div>
                            </div>
                            <div class="mt-0 flex justify-center items-center flex-wrap gap-y-0 sm:gap-x-2">
                                <div class="sm:col-span-1 flex-1 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                                        <InputLabel for="fotoInforme1" value="Imagen Antes"
                                            class="block text-sm font-medium text-gray-700" />
                                        <FileInput class="text-sm" id="fotoInforme1" name="fotoInforme1" @change="($event) => onSelectFoto($event, 'fotoInforme1')" />
                                        <InputError :message="$page.props.errors.fotoInforme1" class="mt-2" />
                                        <div class="mt-2 flex items-center justify-center w-full"
                                            v-if="form.fotoInforme1 !== ''">
                                            <img :src="imageInformeCli1" alt="Vista previa de la foto"
                                                class="p-2 block w-36 h-36 object-contain items-center text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:col-span-1 flex-1 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                                        <InputLabel for="fotoInforme2" value="Imagen Despues"
                                            class="block text-sm font-medium text-gray-700" />
                                        <FileInput class="text-sm" id="fotoInforme2" name="fotoInforme2" @change="($event) => onSelectFoto($event, 'fotoInforme2')" />
                                        <InputError :message="$page.props.errors.fotoInforme2" class="mt-2" />
                                        <div class="mt-2 flex items-center justify-center w-full"
                                            v-if="form.fotoInforme2 !== ''">
                                            <img :src="imageInformeCli2" alt="Vista previa de la foto 2"
                                                class="p-2 block w-36 h-36 items-center object-contain text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <button class="inline-block bg-green-700 text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center text-xs" @click.prevent="previewPDF2">PREVISUALIZAR PDF</button>
                                    <ButtonResponsive 
                                        id="generate-quote-button" 
                                        class="text-white uppercase text-xs bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center"
                                        @click="submitForm">
                                        Generar Informe Tecnico / Cliente
                                    </ButtonResponsive>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="divTablasdeTodoslosDatosdeInstrumentos">
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
                                                <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Acciones</th>
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
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Acciones</th>
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
                                            <th scope="col" class="px-1 py-3 text-center dark:border-white border-b-2">Acciones</th>
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
        </div>
    </AppLayout>
</template>
