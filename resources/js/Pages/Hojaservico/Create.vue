<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextPrueba from '@/Components/TextPrueba.vue';
import InputError from '@/Components/InputError.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref, defineProps } from 'vue';
import FileInput from '@/Components/FileInput.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import { show_alerta } from '@/utils/alertasSwal';
import { formatDate } from '@/utils/funcionesglobales';
import { checkTableVisibility } from '@/utils/hFuncionesServicios';

const { hservicios } = defineProps({
    hservicios:{
        type : Array,
        required:true
    },
    clientes: {
        type : Object,
        required: true
    },
    servicios: {
        type: Object,
        required: true
    },
    hmarcas: {
        type: Object
    }
});

const form = useForm({
    n_servicio: '',
    hmarca_id: '',
    instrumento: '1',
    rango: '',
    medida_bastago: '',
    medida_bastago2: 'cm',
    codigo: '',
    material: '',
    modelo: '',
    serie: '',
    division: '',
    medida_division: 'Kg',
    capacidad: '',
    medida_capacidad: 'Kg',
    cliente_id: '',
    plataforma: '',
    medida_plataforma: 'cm',
    fecha: '',
    requiere: '',
    diagnostico: '',
    trabajos: '',
    foto: '',
    foto2: '',
    foto3: '',
});

const hservicio = ref(null);
const isEditing = ref(false);

const editHojaServicio = (hservicio) => {
    form.n_servicio = hservicio.n_servicio
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
    form.cliente_id = hservicio.cliente_id;
    form.plataforma = hservicio.plataforma;
    form.medida_plataforma = hservicio.medida_plataforma;
    form.fecha = hservicio.fecha;
    form.requiere = hservicio.requiere;
    form.diagnostico = hservicio.diagnostico;
    form.trabajos = hservicio.trabajos;
    form.foto = hservicio.foto;
    form.foto2 = hservicio.foto2;
    form.foto3 = hservicio.foto3;

    imagePreview1.value = hservicio.foto ? '/hservicio_img/' + hservicio.foto : null;
    imagePreview2.value = hservicio.foto2 ? '/hservicio_img/' + hservicio.foto2 : null;
    imagePreview3.value = hservicio.foto3 ? '/hservicio_img/' + hservicio.foto3 : null;
    // console.log(imagePreview1.value);

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

const submitForm = () => {
    $("#btnguardarHojaServicio").hide();
    $("#loading-button").show();
    if (!form.id) {
        $.ajax({
            url: '/fn_obtenerFechaHojaServicio',
            method: 'GET',
            data: {
                cliente_id: form.cliente_id,
                fecha: form.fecha
            },
            success: function(response) {
                if (response) {
                    let idServicio = response["id"]
                    form.n_servicio = idServicio;
                    console.log(form);
                    return;
                    form.post(route('hservicios.store'), {
                        onSuccess: () => {
                            show_alerta('La hoja de servicio se ha registrado correctamente.', 'success');
                            obtenerDatosTiempoReal(form.fecha);
                            resetarDatosFrm();
                            $("#btnguardarHojaServicio").show();
                            $("#loading-button").hide();
                        },
                        onError: (errors) => {
                            if (errors.response && errors.response.status) {
                                show_alerta('Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.', 'error');
                                console.error('Error HTTP:', errors.response.status);
                            } else {
                                show_alerta('Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.', 'error');
                                console.error('Error desconocido:', errors);
                            }
                        }
                    });
                }
            },
            error: function(error) {
                console.error("ERROR AL REGISTRAR COTIZACION ",error);
            }
        });
    } else {
        $("#btnactualizarHojaServicio").hide();
        $("#loading-button").show();
        form.put(route('hservicios.update', form.id), {
            onSuccess: () => {
                show_alerta('La hoja de servicio se ha actualizado correctamente.', 'success');
                form.id = null;
                isEditing.value = false;
                obtenerDatosTiempoReal(form.fecha);
                resetarDatosFrm();
                $("#btnactualizarHojaServicio").show();
                $("#loading-button").hide();
            },
        });
    }
}

const setCurrentDate = () => {
    const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Lima' }).split('/').reverse().join('-');
    form.fecha = today; // Asigna la fecha actual al campo de registro
};

onMounted(() => {
    setCurrentDate();
});

const { props } = usePage();
const user = props.auth.user.name;

// const obtenerNombreCompleto = (user) => {
//     if (user) {
//         return `${user.name} ${user.apellidopat} ${user.apellidomat}`;
//     }
//     return '';
// };

// const nombreCompleto = obtenerNombreCompleto(user);

const searchTerm = ref('');
const searchTermCodigoCli = ref('');
const filteredClientes = ref([]);
const selectedIndex = ref(-1);

const onInput = () => {
    selectedIndex.value = -1;
    if (searchTerm.value.length > 0) {
        filteredClientes.value = props.clientes.filter(cliente =>
            cliente.razonSocial.toLowerCase().includes(searchTerm.value.toLowerCase())
        );
    } else {
        filteredClientes.value = [];
    }
};

const onKeydown = (event) => {
    if (filteredClientes.value.length > 0) {
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            selectedIndex.value = (selectedIndex.value + 1) % filteredClientes.value.length;
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            selectedIndex.value = (selectedIndex.value - 1 + filteredClientes.value.length) % filteredClientes.value.length;
        } else if (event.key === 'Enter') {
            event.preventDefault();
            if (selectedIndex.value >= 0) {
                selectCliente(filteredClientes.value[selectedIndex.value]);
            }
        }
    }
};

const updateSelection = (index) => {
    selectedIndex.value = index;
};

const informeNumero = ref('');

function obtenerDatosTiempoReal(fecha){
    $.ajax({
        url: '/fn_ObtenerHojasServicio',
        method: 'GET',
        data: {
            cliente_id: form.cliente_id,
            fecha: fecha,
        },
        success: function(response) {
            $('#tbodyContenedorHojasServiciosBalanzas').empty();
            $('#tbodyContenedorHojasServiciosTermometros').empty();
            $('#tbodyContenedorHojasServiciosPesas').empty();

            let contadorbal = 1;
            let contadorter = 1;
            let contadorpes = 1;
            informeNumero.value = 'S/N'; 
            response.forEach(function(hservicio) {
                informeNumero.value = hservicio.n_servicio || '';
                let hservicioJson = JSON.stringify(hservicio);
                let nuevaFila;

                if (hservicio.instrumento == "1") {
                    // Para Balanzas
                    nuevaFila = `
                    <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ contadorbal++ }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.hmarca_id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.capacidad } ${hservicio.medida_capacidad}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.serie }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.division } ${hservicio.medida_division}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.plataforma} ${hservicio.medida_plataforma }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ formatDate(hservicio.fecha) }</td>
                    </tr>
                    `;
                    $('#tbodyContenedorHojasServiciosBalanzas').append(nuevaFila);

                } else if (hservicio.instrumento == "2") {
                    // Para Termómetros
                    nuevaFila = `
                    <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ contadorter++ }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.hmarca_id }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.serie }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.rango }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.medida_bastago } ${hservicio.medida_bastago2}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.division } ${hservicio.medida_division}</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ formatDate(hservicio.fecha) }</td>
                    </tr>
                    `;
                    $('#tbodyContenedorHojasServiciosTermometros').append(nuevaFila);

                } else if (hservicio.instrumento == "3") {
                    // Para Pesas
                    nuevaFila = `
                    <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ contadorpes++ }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.modelo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.codigo }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.capacidad }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.material }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                        <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ formatDate(hservicio.fecha) }</td>
                    </tr>
                    `;
                    $('#tbodyContenedorHojasServiciosPesas').append(nuevaFila);
                }
            });

            checkTableVisibility();
            // Añadir el manejador de eventos de doble clic
            $('#tbodyContenedorHojasServiciosBalanzas, #tbodyContenedorHojasServiciosTermometros, #tbodyContenedorHojasServiciosPesas').on('dblclick', 'tr', function() {
                let hservicio = JSON.parse($(this).attr('data-hservicio'));
                editHojaServicio(hservicio);
                let instrumento = hservicio.instrumento;
                actualizarVisibilidadIns(instrumento);
            });
            
        },
        error: function(error) {
            console.error("ERROR AL REGISTRAR COTIZACION ",error);
        }
    });
}

const selectCliente = (cliente) => {
    searchTerm.value = cliente.razonSocial;
    searchTermCodigoCli.value = cliente.id;
    form.cliente_id = searchTermCodigoCli.value;
    filteredClientes.value = [];
    obtenerDatosTiempoReal(form.fecha);
};

function resetarDatosFrm(){
    form.id = null;
    form.hmarca_id = '';
    form.rango = '';
    form.medida_bastago = '';
    form.codigo = '';
    form.material = '';
    form.modelo = '';
    form.serie = '';
    form.division = '';
    form.medida_division = 'Kg';
    form.capacidad = '';
    form.medida_capacidad = 'Kg';
    form.plataforma = '';
    form.requiere = '';
    form.diagnostico = '';
    form.trabajos = '';
    form.foto = '';
    form.foto2 = '';
    form.foto3 = '';

    isEditing.value = false;
}

$(document).on('click', '#crearNuevaHojaServicio', function () {
    form.id = null;
    isEditing.value = false;
    resetarDatosFrm();
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

$(document).on('change', '#fecha', function () {
    let fecha = $('#fecha').val();
    let cliente_id = form.cliente_id;

    if (fecha) {
        $.ajax({
            url: '/fn_verificarFechayDatos',
            method: 'GET',
            data: {
                cliente_id: cliente_id, 
                fecha: fecha, 
            },
            success: function(response) {
                $('#tbodyContenedorHojasServiciosBalanzas').empty();
                $('#tbodyContenedorHojasServiciosTermometros').empty();
                $('#tbodyContenedorHojasServiciosPesas').empty();

                let contadorbal = 1;
                let contadorter = 1;
                let contadorpes = 1;
                informeNumero.value = 'S/N'; 

                if (response.length > 0) {
                    response.forEach(function(hservicio) {
                        informeNumero.value = hservicio.n_servicio || '';
                        let hservicioJson = JSON.stringify(hservicio);
                        let nuevaFila;

                        if (hservicio.instrumento == "1") {
                            // Para Balanzas
                            nuevaFila = `
                            <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ contadorbal++ }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.hmarca_id }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.capacidad } ${hservicio.medida_capacidad}</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.serie }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.division } ${hservicio.medida_division}</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.plataforma} ${hservicio.medida_plataforma }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ formatDate(hservicio.fecha) }</td>
                            </tr>
                            `;
                            $('#tbodyContenedorHojasServiciosBalanzas').append(nuevaFila);

                        } else if (hservicio.instrumento == "2") {
                            // Para Termómetros
                            nuevaFila = `
                            <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ contadorter++ }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.hmarca_id }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.modelo }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.serie }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.rango }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.medida_bastago }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.division }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ formatDate(hservicio.fecha) }</td>
                            </tr>
                            `;
                            $('#tbodyContenedorHojasServiciosTermometros').append(nuevaFila);

                        } else if (hservicio.instrumento == "3") {
                            // Para Pesas
                            nuevaFila = `
                            <tr data-hservicio='${hservicioJson}' class="bg-white text-black border-b text-xs border-gray-300 dark:bg-gray-700 dark:text-white hover:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ contadorpes++ }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-4 text-center">${ hservicio.modelo }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.codigo }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.capacidad }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.material }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.requiere }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center">${ hservicio.tecnico }</td>
                                <td class="px-4 border-b-2 border-r-[0.1px] dark:border-gray-500 dark:border-b-gray-400 py-3 text-center capitalize">${ formatDate(hservicio.fecha) }</td>
                            </tr>
                            `;
                            $('#tbodyContenedorHojasServiciosPesas').append(nuevaFila);
                        }
                    });
                } else {
                    show_alerta('No se encontraron registros en esta fecha.', 'error');
                }

                checkTableVisibility();
                $('#tbodyContenedorHojasServiciosBalanzas, #tbodyContenedorHojasServiciosTermometros, #tbodyContenedorHojasServiciosPesas').on('dblclick', 'tr', function() {
                    let hservicio = JSON.parse($(this).attr('data-hservicio'));
                    editHojaServicio(hservicio);
                    let instrumento = hservicio.instrumento;
                    actualizarVisibilidadIns(instrumento);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                show_alerta('Error en la solicitud. Inténtalo de nuevo más tarde.', 'error');
            }
        });
    }
});

function previsualizarpdf() {
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
    const fechaFormateada = obtenerFechaFormateada(fecha);

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
    fn_dibujarEncabezado(`REPORTE DE INFORME TECNICO : N° 0`);
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
                    user,
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
                        `DATOS DEL EQUIPO : BALANZA`
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
                        requiere

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
        } else if (instrumento == "Termometros"){
            doc.autoTable({
                body: [
                    [
                        `DATOS DEL EQUIPO : TERMOMETRO`
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
                        requiere

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
        } else if (instrumento == "Pesas"){
            doc.autoTable({
                body: [
                    [
                        `DATOS DEL EQUIPO : PESA`
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
                        requiere

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
        const totalWidth = columnWidth * 3;
        
        const imagenes = [
            imagePreview1.value ? imagePreview1.value : '/img/sinFoto.png',
            imagePreview2.value ? imagePreview2.value : '/img/sinFoto.png',
            imagePreview3.value ? imagePreview3.value : '/img/sinFoto.png'
        ];
        // console.log(imagePreview1.value);
        // console.log(imagePreview2.value);
        // console.log(imagePreview3.value);

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
                        // Si no hay imagen, muestra el texto "Sin Imagen"
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

    const blob = doc.output('blob');
    const url = URL.createObjectURL(blob);
    window.open(url);
}

</script>

<template>
    <AppLayout title="Registrar Informe Tecnico">
        <template #header>
            <div class="flex flex-wrap">
                <div class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Registrar Informe Tecnico</div>
            </div>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="md:p-1 flex justify-center items-center text-center w-full">
                            <div class="font-semibold text-base uppercase text-white md:w-80 w-full bg-green-600 rounded-md p-2 leading-tight dark:text-white">N° de Informe : {{ informeNumero }}</div>
                        </div>
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 sm:grid-cols-3 md:py-0 py-2">
                                <div class="w-full -mb-2">
                                    <InputLabel for="cliente_id" class="text-xs">Cliente</InputLabel>
                                    <div class="relative">
                                        <TextInput v-model="searchTerm" autocomplete="off" type="text" id="cliente_id" @input="onInput" placeholder="Ingresa razon social"
                                            @keydown="onKeydown" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                        <div id="contenedorDeClientes"
                                            class="w-full z-50 max-h-60 border border-gray-300 rounded-lg absolute overflow-auto text-sm divide-y divide-gray-200 bg-white dark:bg-gray-800"
                                            v-show="filteredClientes.length > 0">
                                            <div v-for="(cliente, index) in filteredClientes" :key="cliente.id"
                                                :class="['text-gray-800 text-sm dark:text-white font-medium cursor-pointer overflow-hidden whitespace-nowrap text-ellipsis dark:hover:bg-gray-700 hover:bg-gray-200 option p-2', { 'bg-gray-200 dark:bg-gray-700': index === selectedIndex }]"
                                                @click="selectCliente(cliente)" @mouseover="updateSelection(index)">
                                                {{ cliente.razonSocial }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-1 col-start-0 col-end-0 sm:col-start-3 sm:col-end-4 mb-2">
                                    <InputLabel for="instrumento" class="block text-xs font-medium text-black dark:text-white">Instrumento</InputLabel>
                                    <select id="instrumento" v-model="form.instrumento" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="1" selected>Balanzas</option>
                                            <option value="2">Termometros</option>
                                            <option value="3">Pesas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-3">
                                <div id="divMarca">
                                    <InputLabel for="hmarca_id" value="marca" />
                                    <TextInput v-model="form.hmarca_id" type="text" id="hmarca_id"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.hmarca_id" class="mt-2"></InputError>
                                </div>
                                <div id="divModelo">
                                    <InputLabel for="modelo" value="modelo" />
                                    <TextInput v-model="form.modelo" type="text" id="modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.modelo" class="mt-2"></InputError>
                                </div>
                                <div id="divCodigo" class="hidden">
                                    <InputLabel for="codigo" value="codigo"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.codigo" type="text" id="codigo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.codigo" class="mt-2"></InputError>
                                </div>
                                <div id="divSerie">
                                    <InputLabel for="serie" value="serie"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.serie" type="text" id="serie"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.serie" class="mt-2"></InputError>
                                </div>
                                <div id="divRango" class="hidden">
                                    <InputLabel for="rango" value="rango"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.rango" type="text" id="rango"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.rango" class="mt-2"></InputError>
                                </div>
                                <div id="divMedidaBastago" class="hidden">
                                    <InputLabel for="medida_bastago" value="medida de bastago"
                                        class="block text-md font-medium text-gray-700 " />
                                    <div class="flex">
                                        <TextPrueba v-model="form.medida_bastago" type="text" id="medida_bastago"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg" />
                                        <InputError :message="form.errors.medida_bastago" class="mt-2"></InputError>
                                        <input id="medida_bastago2" disabled v-model="form.medida_bastago2" class="mt-1 text-center focus:ring-indigo-500 focus:border-indigo-500 block w-16 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg"/>
                                    </div>
                                </div>
                                <div id="divDivision">
                                    <InputLabel for="div" value="div" />
                                    <div class="flex justify-center">
                                        <TextPrueba v-model="form.division" type="text" id="division"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg" />
                                        <InputError :message="form.errors.division" class="mt-2"></InputError>
                                        <select id="medida_division" v-model="form.medida_division" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-24 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg">
                                            <option selected value="Kg">Kg</option>
                                            <option value="g">g</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="divCapacidad">
                                    <InputLabel for="capacidad" value="capacidad" />
                                    <div class="flex justify-center">
                                        <TextPrueba v-model="form.capacidad" type="text" id="capacidad"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg" />
                                        <InputError :message="form.errors.capacidad" class="mt-2"></InputError>
                                        <select id="medida_capacidad" v-model="form.medida_capacidad" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-24 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg">
                                            <option selected value="Kg">Kg</option>
                                            <option value="g">g</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="divMaterial" class="hidden">
                                    <InputLabel for="material" value="material" />
                                    <TextInput v-model="form.material" type="text" id="material"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.material" class="mt-2"></InputError>
                                </div>
                                <div id="divPlataforma">
                                    <InputLabel for="plataforma" value="Medida de Plataforma" />
                                    <div class="flex justify-center items-center">
                                        <TextPrueba v-model="form.plataforma" type="text" id="plataforma"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg" />
                                        <InputError :message="form.errors.plataforma" class="mt-2"></InputError>
                                        <input id="medida_plataforma" disabled v-model="form.medida_plataforma" class="mt-1 text-center focus:ring-indigo-500 focus:border-indigo-500 block w-16 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg"/>
                                    </div>
                                </div>
                                <div>
                                    <InputLabel for="requiere" value="requiere" />
                                    <select id="requiere" v-model="form.requiere" required
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
                                    <InputLabel for="diagnostico" value="diagnostico tecnico" />
                                    <textarea id="diagnostico" v-model="form.diagnostico" rows="4" required
                                        class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Escriba las diagnostico..."></textarea>
                                    <InputError :message="form.errors.diagnostico" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="trabajos" value="trabajos a realizar" />
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
                                                class="block text-xs font-medium text-gray-700" />
                                            <FileInput id="foto" name="foto" @change="($event) => onSelectFoto($event, 'foto')" />
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
                                                class="block text-xs font-medium text-gray-700" />
                                            <FileInput id="foto2" name="foto2" @change="($event) => onSelectFoto($event, 'foto2')" />
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
                                                class="block text-xs font-medium text-gray-700" />
                                            <FileInput id="foto3" name="foto3" @change="($event) => onSelectFoto($event, 'foto3')" />
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
                                    <ButtonResponsive v-if="isEditing" id="crearNuevaHojaServicio" class="uppercase text-xs bg-red-600 hover:bg-red-700">Nuevo Informe Tecnico</ButtonResponsive>
                                    <button @click.prevent="previsualizarpdf" class="inline-block bg-green-700 text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center uppercase text-xs">Previsualizar Informe Tecnico</button>
                                    <ButtonResponsive id="btnguardarHojaServicio" v-if="!isEditing" class="uppercase text-xs">Guadar Informe Tecnico</ButtonResponsive>
                                    <button 
                                        id="loading-button" 
                                        disabled 
                                        type="button" 
                                        class="text-white whitespace-nowrap md:w-min w-full bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 rounded-lg text-sm px-5 py-2 justify-center text-center font-extrabold dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 hidden">
                                        <svg aria-hidden="true" role="status" class="inline w-4 mt-[3px] h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                                        </svg>
                                        Cargando...
                                    </button>
                                    <ButtonResponsive id="btnactualizarHojaServicio" v-if="isEditing" class="uppercase text-xs">Actualizar Informe Tecnico</ButtonResponsive>
                                </div>
                            </div>
                        </form>
                        <div class="py-4 flex flex-col gap-4">
                            <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-800">
                                <table id="construirTablaBalanzas" class="w-full hidden text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
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
                                <table id="construirTablaTermometros" class="w-full hidden text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
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
                                <table id="construirTablaPesas" class="w-full hidden text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
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
        </div>
    </AppLayout>
</template>
