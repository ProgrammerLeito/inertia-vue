<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch, watchEffect, defineProps } from 'vue';
import Swal from 'sweetalert2';
import FileInput from '@/Components/FileInput.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

const nameInput2 = ref(null);
const modal2 = ref(false);
const title2 = ref('');
const operation2 = ref(1);
const id2 = ref('');
const { servicios, hmarcas } = defineProps({
    servicios: {
        type: Object,
        required: true
    },
    hmarcas: {
        type: Object
    }
});

const initialvalues = {
    servicio_id: '',
    hmarca_id: '',
    modelo: '',
    serie: '',
    div: '',
    capacidad: '',
    plataforma: '',
    fecha: '',
    requiere: '',
    diagnostico: '',
    trabajos: '',
    foto: '',
    foto2: '',
    foto3: '',
    n_informe: '',
    razonSocial: '',
};
const form2 = useForm({
    nombre: '',
});

const openModal2 = (op, nombre, hmarca) => {
    modal2.value = true;
    nextTick(() => nameInput2.value.focus());
    operation2.value = op;
    id2.value = hmarca;
    if (op === 1) {
        title2.value = 'Registrar marca';
    }
};

const closeModal2 = () => {
    modal2.value = false;
    form2.reset();
};

const save2 = () => {
    form2.post(route('hmarcas.store'), {
        onSuccess: () => { ok2('marca registrada'); }
    });
};

const ok2 = (msj) => {
    form2.reset();
    closeModal2();
    Swal.fire({
        title: msj,
        icon: 'success',
        timer: 1000,
        showConfirmButton: false
    });
};



const form = useForm(initialvalues);
const imagePreview1 = ref('');
const imagePreview2 = ref('');
const imagePreview3 = ref('');
const imagePreviews = ref(['', '', '']); // Arreglo para guardar las vistas previas de las imágenes

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
    form.post(route('hservicios.store'), {
        onSuccess: () => {
            const Toast = Swal.mixin({
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: 'Éxito',
                text: "El hoja de servico se ha registrado correctamente.",
                customClass: {
                    title: 'text-2xl font-bold tracking-widest ',
                    icon: 'text-base font-bold tracking-widest ',
                    text: 'bg-red-500 hover:bg-red-600 tracking-widest ',
                },
            });
        },
        onError: (errors) => {
            if (errors.response && errors.response.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al registrar la hoja de servicio. Por favor, inténtalo de nuevo.'
                });
                console.error('Error HTTP:', errors.response.status);
            } else {
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

const setCurrentDate = () => {
    const today = new Date().toISOString().split('T')[0];
    form.fecha = today;
}
onMounted(() => {
    setCurrentDate();
});

const nInformeSeleccionado = computed(() => {
    const servicioSeleccionado = servicios.find(servicio => servicio.id === form.servicio_id);
    return servicioSeleccionado ? servicioSeleccionado.n_informe : '';
});

const razonSocialCliente = computed(() => {
    const servicioSeleccionado = servicios.find(servicio => servicio.id === form.servicio_id);
    return servicioSeleccionado && servicioSeleccionado.cliente ? servicioSeleccionado.cliente.razonSocial : '';
});

onMounted(() => {
    const idrequerimiento = localStorage.getItem('idrequerimiento');
    if (idrequerimiento) {
        form.servicio_id = parseInt(idrequerimiento);
        form.n_informe = nInformeSeleccionado.value;
        form.razonSocial = razonSocialCliente.value;
    }
});

watch(form.servicio_id, () => {
    setCurrentDate();
    form.n_informe = nInformeSeleccionado.value;
    form.razonSocial = razonSocialCliente.value;
});

const { props } = usePage();
const user = props.auth.user;

const obtenerNombreCompleto = (user) => {
    if (user) {
        return `${user.name} ${user.apellidopat} ${user.apellidomat}`;
    }
    return '';
};

const nombreCompleto = obtenerNombreCompleto(user);

const previewPDF2 = () => {

    const fechaEncabezadoCotizacion = new Date();
    const añoCotizacion = fechaEncabezadoCotizacion.getFullYear();
    const numeroCotizacion = form.n_informe
    const numeroCotizacionFormateado = numeroCotizacion.toString().padStart(8, '0');
    const marca = document.getElementById('hmarca_id').options[document.getElementById("hmarca_id").selectedIndex].text;
    const serie = document.getElementById("serie").value;
    const modelo = document.getElementById("modelo").value;
    const div = document.getElementById("div").value;
    const capacidad = document.getElementById("capacidad").value;
    const plataforma = document.getElementById("plataforma").value;
    const requiere = document.getElementById('requiere').options[document.getElementById("requiere").selectedIndex].text;
    
    const diagnostico = document.getElementById("diagnostico").value;
    const trabajos = document.getElementById("trabajos").value;

    const razonSocial = document.getElementById("razonSocial").value;

    // Función para obtener el nombre del día de la semana en español
    function getNombreDia(dia) {
        const dias = [
            'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'
        ];
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
    function obtenerFechaFormateada() {
        const fecha = new Date();

        const diaSemana = getNombreDia(fecha.getDay());
        const dia = fecha.getDate().toString().padStart(2, '0');
        const mes = getNombreMes(fecha.getMonth());
        const año = fecha.getFullYear();

        return `${diaSemana} ${dia} de ${mes} del ${año}`;
    }

    const fechaFormateada = obtenerFechaFormateada();

    // ========== Inicia Función Dibujar Encabezado ==========

    function fn_dibujarEncabezado(texto){
        const anchoTexto = doc.getTextWidth(texto);
        const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
        doc.text(eje_x_left, eje_y, texto);
    }

    // ========== Termina Función Dibujar Encabezado ==========

    // ========== Inicia Función Dibujar Datos Generales ==========

    function fn_dibujarDatosGenerales(inicioTabla){
        doc.autoTable({
            body: [
                [
                    'DATOS GENERALES'
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
            margin: {left: 10 , right: 10},
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
                    nombreCompleto,
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
                    razonSocial
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

    // ========== Termina Función Dibujar Datos Generales ==========

    // ========== Inicia Función Dibujar Datos del Equipo ==========

    function fn_dibujarDatosdelEquipo(){
        doc.autoTable({
            body: [
                [
                    'DATOS DEL EQUIPO'
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
            margin: {left: 10 , right: 10},
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
                    marca,
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
                    'SERIE',
                    serie,
                    'DIVISION',
                    div

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
                    'CAPACIDAD',
                    capacidad,
                    'PLATAFORMA',
                    plataforma

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
            margin: {left: 10 , right: 10},
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
        const columnWidth = 63.3; // Ancho de cada columna para FOTO 2 y FOTO 3
        const startY = doc.lastAutoTable.finalY + 5;
        const totalWidth = columnWidth * 3; // Ancho total del encabezado (ajustado a 3 columnas)

        const imagen1 = imagePreview1.value;
        const imagen2 = imagePreview2.value;
        const imagen3 = imagePreview3.value;
        
        // Crear tabla de encabezado para las fotos
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
            tableWidth: totalWidth, // Ancho total de la tabla
            margin: { left: 10 }, // Ajusta el margen izquierdo para centrar la tabla
            didDrawCell: function (data) {
                // Determinar qué imagen insertar en la celda correspondiente
                let image;
                if (data.column.index === 0 && data.cell.section === 'body') {
                    image = imagen1;
                } else if (data.column.index === 1 && data.cell.section === 'body') {
                    image = imagen2;
                } else if (data.column.index === 2 && data.cell.section === 'body') {
                    image = imagen3;
                }

                // Agregar la imagen a la celda si está disponible
                if (image) {
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

                    doc.addImage(image, 'JPEG', x + 1, y + 1, drawWidth - 2, drawHeight - 2); // Ajusta el formato según sea necesario
                }
            },
        });
    }


    // ========== Termina Función Dibujar Fotos ==========

    // ========== Inicia Construción de PDF ==========

    const doc = new jsPDF();
    // const doc = new jsPDF('landscape'); // Horizontal

    let eje_y = 10;
    let eje_x = 10;
    let margenDerecho = 10;
    let anchoPagina = doc.internal.pageSize.width;

    doc.setTextColor(0,0,0);
    doc.setFontSize(9);
    doc.setFont('Helvetica', 'normal');

    const backgroundImg = '/img/logo_ini.png';
    doc.addImage(backgroundImg, 'JPEG', eje_x, eje_y, 80, 25);
    
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

    eje_y += 10;

    doc.setFontSize(12);
    doc.setFont('Helvetica', 'bold');
    fn_dibujarEncabezado(`REPORTE DE MANTENIMIENTO : N° ${añoCotizacion} - ${numeroCotizacionFormateado}`);
    // doc.text(eje_x, eje_y, fechaFormateada);

    const inicioTabla = 50;

    fn_dibujarDatosGenerales(inicioTabla);

    doc.setFontSize(8);
    doc.setFont('Helvetica', 'bold');
    fn_dibujarDatosdelEquipo();

    doc.setFont('Helvetica', 'normal');
    fn_dibujarDiagnosticoTecnico();

    doc.setFont('Helvetica', 'normal');
    fn_dibujarTrabajos();

    fn_dibujarFotos();

    // ========== Finaliza Construción de PDF ==========

    const blob = doc.output('blob');
    const url = URL.createObjectURL(blob);
    window.open(url);
}

</script>
<template>
    <AppLayout title="Registrar H-servicio-requerimientos ">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Registrar requerimientos | servicio</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">
                            <div
                                class="font-semibold d-flex flex-wrap items-center justify-center text-center dark:text-white py-4">
                                <div class="d-flex hidden">
                                    <select id="servicio_id" v-model="form.servicio_id" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white shadow-sm sm:text-sm border-gray-300 bg-gray-400 dark:bg-gray-800">
                                        <option v-for="servicio in servicios" :key="servicio.id" :value="servicio.id">{{
                                            servicio.n_informe }}</option>
                                    </select>
                                </div>
                                <div class="flex items-center font-bold p-4 border py-2.5 border-green-600 rounded bg-green-600">
                                    <div class="bg-yellow-400 text-base font-bold py-4 p-2 rounded text-white"> N° INFORME: {{ nInformeSeleccionado }} </div>
                                    <p class="py-4 text-base text-white font-bold mx-4">PARA : {{ razonSocialCliente }}</p>
                                </div>
                            </div>
                            <!-- no se muestran los campos ocultos -->
                            <div class="hidden">
                                <div>
                                <InputLabel for="n_informe" value="n_informe"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.n_informe" type="text" id="n_informe"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.n_informe" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="razonSocial" value="razonSocial"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.razonSocial" type="text" id="razonSocial"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.razonSocial" class="mt-2"></InputError>
                            </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-3">
                                <div class="flex flex-col items-start">
                                    <InputLabel for="hmarca_id" value="Marca" class="ml-1" />
                                    <div class="flex w-full">
                                        <select id="hmarca_id" v-model="form.hmarca_id"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="" disabled selected>Selecciona una marca</option>
                                            <option v-for="hmarca in hmarcas" :key="hmarca.id" :value="hmarca.id">{{
                                                hmarca.nombre }}</option>
                                        </select>
                                        <Button @click.prevent="() => openModal2(1)"
                                            class="bg-green-600 mt-1 py-1 text-white w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div>
                                <div>
                                    <InputLabel for="serie" value="serie"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.serie" type="text" id="serie"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.serie" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="modelo" value="modelo" />
                                    <TextInput v-model="form.modelo" type="text" id="modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.modelo" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="div" value="div" />
                                    <TextInput v-model="form.div" type="text" id="div"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.div" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="capacidad" value="capacidad" />
                                    <TextInput v-model="form.capacidad" type="text" id="capacidad"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.capacidad" class="mt-2"></InputError>
                                </div>
                                <div>
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
                                        <option value="REQUIERE MANTENIMIENTO">REQUIERE MANTENIMIENTO</option>
                                        <option value="REQUIERE REPARACION">REQUIERE REPARACION</option>
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
                                    <TextInput v-model="form.fecha" type="date" id="fecha"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.fecha" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="diagnostico" value="diagnostico" />
                                    <textarea id="diagnostico" v-model="form.diagnostico" rows="4"
                                        class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Escriba las diagnostico..."></textarea>
                                    <InputError :message="form.errors.diagnostico" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="trabajos" value="trabajos" />
                                    <textarea id="trabajos" v-model="form.trabajos" rows="4"
                                        class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Escriba las trabajos..."></textarea>
                                    <InputError :message="form.errors.trabajos" class="mt-2"></InputError>
                                </div>
                            </div>
                            <div class="mt-0 flex justify-center items-center flex-wrap gap-y-0 sm:gap-x-2">
                                <div class="sm:col-span-1 flex-1 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-4 items-center mb-4 mt-4">
                                        <InputLabel for="foto" value="Foto"
                                            class="block text-xs font-medium text-gray-700" />
                                        <FileInput name="foto" @change="($event) => onSelectFoto($event, 'foto')" />
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
                                        <FileInput name="foto2" @change="($event) => onSelectFoto($event, 'foto2')" />
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
                                        <FileInput name="foto3" @change="($event) => onSelectFoto($event, 'foto3')" />
                                        <InputError :message="$page.props.errors.foto3" class="mt-2" />
                                        <div class="mt-2 flex items-center justify-center w-full"
                                            v-if="form.foto3 !== ''">
                                            <img :src="imagePreview3" alt="Vista previa de la foto 3"
                                                class="p-2 block w-36 h-36 items-center object-contain text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <button class="inline-block bg-green-700 text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center" @click.prevent="previewPDF2">PREVISUALIZAR PDF</button>
                                    <ButtonResponsive
                                        class="inline-block uppercase bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 md:w-min whitespace-nowrap w-full text-center">
                                        Guadar Hoja de Servicio
                                    </ButtonResponsive>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    <ModalResponsive :show="modal2" @close="closeModal2">
        <div class="p-4 uppercase">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title2 }}
            </h2>
            <div class="p-1">
                <div class="w-full">
                    <InputLabel for="nombre" value="Nombre:" class="mb-2"></InputLabel>
                    <TextInput id="nombre" ref="nameInput2" v-model="form2.nombre" type="text" class="w-full"
                        placeholder="Nombre"></TextInput>
                    <InputError :message="form2.errors.nombre" class="mt-2"></InputError>
                </div>
            </div>
            <div class="p-1 flex justify-center">
                <PrimaryButton :disabled="form2.processing" @click="save2">
                    <i class="fa-solid fa-save mx-1"></i>{{ operation2 == 1 ? 'Registrar' : 'Actualizar' }}
                </PrimaryButton>
                <DangerButton class="ml-3" :disabled="form2.processing" @click="closeModal2">
                    Cancelar
                </DangerButton>
            </div>
        </div>
    </ModalResponsive>
</template>
