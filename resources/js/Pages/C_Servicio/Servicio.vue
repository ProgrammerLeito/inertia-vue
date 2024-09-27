<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextPrueba from '@/Components/TextPrueba.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, defineProps, onMounted } from 'vue';
import FileInput from '@/Components/FileInput.vue';

const props =defineProps({
    hServicio: {
        type: Object,
        required: true
    },
});

const form = useForm({
    n_servicio: props.hServicio.hservicio_n_servicio,
    hmarca_id: props.hServicio.hmarca_id,
    lugar_servicio: '',
    instrumento: props.hServicio.instrumento,
    rango: props.hServicio.rango,
    medida_bastago: props.hServicio.medida_bastago,
    medida_bastago2: props.hServicio.medida_bastago2,
    codigo: props.hServicio.codigo,
    material: props.hServicio.material,
    modelo: props.hServicio.modelo,
    serie: props.hServicio.serie,
    division: props.hServicio.division,
    medida_division: props.hServicio.medida_division,
    capacidad: props.hServicio.capacidad,
    medida_capacidad: props.hServicio.medida_capacidad,
    cliente_id: props.hServicio.razonSocial,
    plataforma: props.hServicio.plataforma,
    medida_plataforma: props.hServicio.medida_plataforma,
    fecha: props.hServicio.hservicio_fecha,
    fecha_final: '',
    tecnico: props.hServicio.tecnico,
    requiere: props.hServicio.requiere,
    diagnostico: props.hServicio.diagnostico,
    trabajos: props.hServicio.trabajos,
    foto: '',
    foto2: '',
});

const imagePreview1 = ref('');
const imagePreview2 = ref('');
const imagePreviews = ref(['', '', '']);

const onSelectFoto = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
        const reader = new FileReader();
        reader.onload = (e) => {
            switch (fieldName) {
                case 'foto':
                    imagePreview1.value = e.target.result;
                    imagePreviews.value[0] = e.target.result;
                    break;
                case 'foto2':
                    imagePreview2.value = e.target.result;
                    imagePreviews.value[1] = e.target.result;
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

const previewPDF2 = () => {
    // ========== Inicia Construción de PDF ==========

    let doc = new jsPDF('portrait');

    function fn_dibujarEncabezado(texto){
        const anchoTexto = doc.getTextWidth(texto);
        const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
        doc.text(eje_x_left, eje_y, texto);
    }

    let eje_y = 10;
    let eje_x = 10;
    let margenDerecho = 10;
    let anchoPagina = doc.internal.pageSize.width;

    doc.setTextColor(0,0,0);
    doc.setFontSize(9);
    doc.setFont('Helvetica', 'normal');

    const backgroundImg = '/img/plantilla.png';
    doc.addImage(backgroundImg, 'JPEG',1, 1, 208, 295);
    
    eje_y += 5;

    fn_dibujarEncabezado("Av. Separadora Mz A LT 8 Sector 28 de Julio");
    eje_y += 5;
    fn_dibujarEncabezado("Telf: 955571986 - 924808237 - 934094721");
    eje_y += 5;
    fn_dibujarEncabezado("Correo: industriasbalinsa@gmail.com");
    eje_y += 5;
    fn_dibujarEncabezado("www.balinsa.com");
    eje_y += 5;
    fn_dibujarEncabezado("RUC: 20608165585");

    // eje_y += 10;

    // doc.setFontSize(12);
    // doc.setFont('Helvetica', 'bold');
    // fn_dibujarEncabezado(`COTIZACION : N° ${añoCotizacion} - ${numeroCotizacionFormateado}`);
    // doc.text(eje_x, eje_y, fechaFormateada);

    // const inicioTabla = 50;

    // fn_dibujarDatosClientes(inicioTabla);

    // doc.setFontSize(8);
    // doc.setFont('Helvetica', 'bold');
    // fn_dibujarProductos();
    
    // fn_dibujarCondiciones();

    // doc.setFont('Helvetica', 'normal');
    // fn_dibujarCuentas();

    // ========== Finaliza Construción de PDF ==========

    const blob = doc.output('blob');
    const url = URL.createObjectURL(blob);
    window.open(url);
}

$(document).on('change', '#instrumento', function () {
    let instrumento = $('#instrumento').val();
    actualizarVisibilidadIns(instrumento);
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

</script>

<template>
    <AppLayout title="Requerimientos de Servicio">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Generar Hoja Informe Tecnico / Cliente</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 md:py-0 py-2 gap-y-2 gap-6">
                                <div id="divClienteId">
                                    <InputLabel for="cliente_id" value="Cliente" class="text-xs" />
                                    <TextInput disabled v-model="form.cliente_id" type="text" id="cliente_id"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.cliente_id" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="lugar_servicio" value="Lugar de Servicio" class="text-xs" />
                                    <TextInput v-model="form.lugar_servicio" type="text" id="lugar_servicio"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.lugar_servicio" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="instrumento" class="block text-xs font-medium text-black dark:text-white">Instrumento</InputLabel>
                                    <select disabled id="instrumento" v-model="form.instrumento" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="1" selected>Balanzas</option>
                                            <option value="2">Termometros</option>
                                            <option value="3">Pesas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-2 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-3 py-2">
                                <div id="divTecnicoResp">
                                    <InputLabel for="tecnico" value="Tecnico" />
                                    <TextInput disabled v-model="form.tecnico" type="text" id="tecnico"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.tecnico" class="mt-2"></InputError>
                                </div>
                                <div id="divMarca">
                                    <InputLabel for="hmarca_id" value="marca" />
                                    <TextInput disabled v-model="form.hmarca_id" type="text" id="hmarca_id"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.hmarca_id" class="mt-2"></InputError>
                                </div>
                                <div id="divModelo">
                                    <InputLabel for="modelo" value="modelo" />
                                    <TextInput disabled v-model="form.modelo" type="text" id="modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.modelo" class="mt-2"></InputError>
                                </div>
                                <div id="divCodigo" class="hidden">
                                    <InputLabel for="codigo" value="codigo"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput disabled v-model="form.codigo" type="text" id="codigo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.codigo" class="mt-2"></InputError>
                                </div>
                                <div id="divSerie">
                                    <InputLabel for="serie" value="serie"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput disabled v-model="form.serie" type="text" id="serie"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.serie" class="mt-2"></InputError>
                                </div>
                                <div id="divRango" class="hidden">
                                    <InputLabel for="rango" value="rango"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput disabled v-model="form.rango" type="text" id="rango"
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
                                        <select disabled id="medida_division" v-model="form.medida_division" required
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
                                        <select disabled id="medida_capacidad" v-model="form.medida_capacidad" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-24 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg">
                                            <option selected value="Kg">Kg</option>
                                            <option value="g">g</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="divMaterial" class="hidden">
                                    <InputLabel for="material" value="material" />
                                    <TextInput disabled v-model="form.material" type="text" id="material"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.material" class="mt-2"></InputError>
                                </div>
                                <div id="divPlataforma">
                                    <InputLabel for="plataforma" value="plataforma" />
                                    <div class="flex justify-center items-center">
                                        <TextPrueba v-model="form.plataforma" type="text" id="plataforma"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg" />
                                        <InputError :message="form.errors.plataforma" class="mt-2"></InputError>
                                        <input id="medida_plataforma" disabled v-model="form.medida_plataforma" class="mt-1 text-center focus:ring-indigo-500 focus:border-indigo-500 block w-16 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg"/>
                                    </div>
                                </div>
                                <div>
                                    <InputLabel for="requiere" value="requiere" />
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
                                    <InputError :message="$page.props.errors.descripcion" class="" />
                                </div>
                                <div>
                                    <InputLabel for="fecha" value="Fecha Inicio"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput disabled v-model="form.fecha" type="date" id="fecha" :disabled="!$page.props.user.permissions.includes('Acciones Administrador')"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.fecha" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="fecha_final" value="Fecha Final"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput disabled v-model="form.fecha_final" type="date" id="fecha_final" :disabled="!$page.props.user.permissions.includes('Acciones Administrador')"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.fecha_final" class="mt-2"></InputError>
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
                                            <InputLabel for="foto" value="Imagen Antes"
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
                                            <InputLabel for="foto2" value="Imagen Despues"
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
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-x-4 gap-y-4 justify-end">
                                    <button class="inline-block bg-green-700 text-xs text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center" @click.prevent="previewPDF2">PREVISUALIZAR PDF</button>
                                    <!-- <ButtonResponsive v-if="isEditing" id="crearNuevaHojaServicio" class="uppercase text-xs bg-red-600 hover:bg-red-700">Nuevo Informe Tecnico</ButtonResponsive>
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
                                    <ButtonResponsive id="btnactualizarHojaServicio" v-if="isEditing" class="uppercase text-xs">Actualizar Informe Tecnico</ButtonResponsive> -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
