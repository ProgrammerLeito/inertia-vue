<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { format, parse } from 'date-fns';
import { es } from 'date-fns/locale';

const{hservicios}=defineProps({
    hservicios:{
        type : Array,
        required:true
    }
});

//Funcion para dia/mes/año
function formatDate(dateString) {
    if (!dateString) return 'Fecha inválida';
    const parsedDate = parse(dateString, 'yyyy-MM-dd', new Date());
    if (isNaN(parsedDate)) return 'Fecha inválida';
    return format(parsedDate, 'EEEE dd/MM/yyyy', { locale: es });
}

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

            // Función para agrupar y generar el HTML
            function agruparYMostrar(datos) {
                // Agrupar los datos por fecha
                let agrupadosPorFecha = {};
                datos.forEach(function(item) {
                    if (!agrupadosPorFecha[item.fecha]) {
                        agrupadosPorFecha[item.fecha] = [];
                    }
                    agrupadosPorFecha[item.fecha].push(item);
                });

                // Vaciar el contenido anterior
                bodyDiarios.empty();

                // Generar el HTML agrupado
                for (const fecha in agrupadosPorFecha) {
                    // Insertar una fila con la fecha como título del grupo
                    let filaFecha = `
                    <tr class="bg-gray-200 dark:bg-gray-600">
                        <td colspan="3" class="px-4 py-2 font-bold text-start dark:border-gray-500 bg-gray-500 text-white font-extrabold border-b-2">${formatDate(fecha)}</td>
                    </tr>`;
                    bodyDiarios.append(filaFecha);

                    // Insertar las filas correspondientes a esa fecha
                    agrupadosPorFecha[fecha].forEach(function(item) {
                        let nuevaFila = $(`
                        <tr class="bg-white font-extrabold text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-800 hover:bg-gray-400 cursor-pointer">
                            <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${item.id}</td>
                            <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-left">${item.razonSocial}</td>
                            <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${item.fecha}</td>
                        </tr>`);
                        bodyDiarios.append(nuevaFila);
                    });
                }
            }

            // Mostrar todos los datos agrupados inicialmente
            agruparYMostrar(datosOriginales);

            // Función para filtrar los datos por cliente
            function filtrarPorCliente() {
                let nombreFiltrar = $('#filtrarClienteHoja').val().toUpperCase();

                if (nombreFiltrar) {
                    // Filtrar los datos originales según el nombre del cliente
                    let datosFiltrados = datosOriginales.filter(function(item) {
                        return item.razonSocial.toUpperCase().includes(nombreFiltrar);
                    });

                    // Agrupar y mostrar los datos filtrados
                    agruparYMostrar(datosFiltrados);
                } else {
                    // Si no hay filtro, mostrar todos los datos
                    agruparYMostrar(datosOriginales);
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
    let servicio = fila.find('td:eq(0)').text();
    let fecha = fila.find('td:eq(2)').text();
    
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

            response.forEach(function(hservicio) {
                let hservicioJson = JSON.stringify(hservicio);
                let nuevaFila;

                if (hservicio.instrumento == "1") {
                    // Para Balanzas
                    nuevaFila = `
                    <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.hmarca_id }</td>
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
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.hmarca_id }</td>
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
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.modelo }</td>
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

            // Añadir el manejador de eventos de doble clic
            // $('#tbodyContenedorHojasServiciosBalanzas, #tbodyContenedorHojasServiciosTermometros, #tbodyContenedorHojasServiciosPesas').on('dblclick', 'tr', function() {
            //     let hservicio = JSON.parse($(this).attr('data-hservicio'));
            //     editHojaServicio(hservicio);
            //     let instrumento = hservicio.instrumento;
            //     actualizarVisibilidadIns(instrumento);
            // });
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
    $('#divTablitaHojasdeServicio').hide();
    $('#divListarHojasdeServicio').show();
});
</script>

<template>
    <AppLayout title="Lista de Hojas Servicio">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Hojas Servicio</h1>
        </template>

        <div id="divListarHojasdeServicio" class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 uppercase text-sm justify-between">
                        <Link :href="route('hservicios.create')" class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fa fa-plus-circle mx-2"></i>Ingresar Hoja de Servicio
                        </Link>
                    </div>
                    <div class="flex flex-col py-2">
                        <div class="flex md:w-96 w-full">
                            <span
                                class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                <i class='bx bxs-user-circle text-xl'></i>
                            </span>
                            <input id="filtrarClienteHoja"
                                class="w-full outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-primary-600 focus:border-primary-600 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                type="text" autocomplete="off"
                                placeholder="Ingrese Nombre del Cliente">
                        </div>
                    </div>
                    <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white" id="tbHojaServicioBuscar">
                            <thead class="text-xs text-white uppercase bg-blue-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                    <th scope="col" class="px-5 py-3 text-start dark:border-white border-b-2">Cliente</th>
                                    <th scope="col" class="px-5 py-3 text-start dark:border-white border-b-2">fecha</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyHojasServicioDiarias" class="text-center text-xs">
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                        <div class="hidden sm:block">
                            <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                                <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                                <p class="text-gray-700 dark:text-white font-semibold">Total de Hojas de Servicios: {{ totalCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:text-end text-center">
                            <nav aria-label="Page navigation example mt-4">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <li>
                                        <button @click="previousPage" :disabled="!hservicios.prev_page_url"
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-700 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            Prev
                                        </button>
                                    </li>
                                    <li v-for="page in total_pages" :key="page">
                                        <button @click="goToPage(page)"
                                            :class="{ 'text-blue-600 border-blue-300 dark:text-gray-900 bg-blue-50 hover:bg-blue-100 hover:text-blue-700': page === current_page, 'text-gray-900 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': page !== current_page }"
                                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            {{ page }}
                                        </button>
                                    </li>
                                    <li>
                                        <button @click="nextPage" :disabled="!hservicios.next_page_url"
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-700 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            Next
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <div id="divTablitaHojasdeServicio" class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto hidden">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="w-full">
                        <button class="text-white bg-blue-600 flex justify-center items-center text-center md:gap-2 gap-4 font-bold hover:bg-blue-700 uppercase text-sm py-2 px-6 rounded md:w-min whitespace-nowrap w-full" id="retornarbody">
                            <i class="fa-solid fa-arrow-left"></i>retornar
                        </button>
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
                                            <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Requiere</th>
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
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Requiere</th>
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
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Requiere</th>
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
