<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import jsPDF from 'jspdf';

const props = defineProps({
    hojaservicios: {
        type: Object,
        required: true,
    }
});
const form = useForm({
    servicio_a_realizar: '',
    razon_social: '',
    direccion: '',
    contacto: '',
    area_de_contacto: '',
    telefono_de_contacto: '',
    asesor_encargado: '',
    tecnico_encargado: '',
    hora_del_servicio: '',
    cantidad_de_instrumentos: '',
    datos_del_instrumento: '',
    trabajos_a_realizar: '',
    // n_informe: '',
});

const isEditing = ref(false);
const hojaservicio = ref(null);

const editHojaServicio = (hojaservicio) => {
    form.id = hojaservicio.id;
    form.servicio_a_realizar = hojaservicio.servicio_a_realizar;
    form.razon_social = hojaservicio.razon_social;
    form.direccion = hojaservicio.direccion;
    form.contacto = hojaservicio.contacto;
    form.area_de_contacto = hojaservicio.area_de_contacto;
    form.telefono_de_contacto = hojaservicio.telefono_de_contacto;
    form.asesor_encargado = hojaservicio.asesor_encargado;
    form.tecnico_encargado = hojaservicio.tecnico_encargado;
    form.hora_del_servicio = hojaservicio.hora_del_servicio;
    form.cantidad_de_instrumentos = hojaservicio.cantidad_de_instrumentos;
    form.datos_del_instrumento = hojaservicio.datos_del_instrumento;
    form.trabajos_a_realizar = hojaservicio.trabajos_a_realizar;
    isEditing.value = true;
};

const submitForm = () => {
    if (!form.id) {
        form.post(route('hojasservicios.store'), {
            onSuccess: () => {
                form.reset();
                showSuccessMessage('Hoja de servicio creada exitosamente.');
                form.id = null;
            },
        });
    } else {
        form.put(route('hojasservicios.update', form.id), {
            onSuccess: () => {
                form.reset();
                showSuccessMessage('Hoja de servicio actualizada exitosamente.');
                form.id = null;
                isEditing.value = false;
            },
        });
    }
};

const showSuccessMessage = (message) => {
    const Toast = Swal.mixin({
        toast: true,
        position: "bottom-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    Toast.fire({
        icon: "success",
        title: 'Éxito',
        text: message,
        customClass: {
            title: 'text-2xl font-bold tracking-widest ',
            text: 'text-2xl font-bold tracking-widest ',
            icon: 'text-base font-bold tracking-widest ',
        },
    });
};

const form2 = useForm({
    id: ''
})

const deleteHojaServicio = (id, razon_social) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar ala hoja de servicio definitivamente : ' + razon_social + '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
        customClass: {
            title: 'text-xl font-bold tracking-widest ',
            cancelButton: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form2.delete(route('hojasservicios.destroy', id), {
                onSuccess: () => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "bottom-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: "success",
                        title: 'Éxito',
                        text: "Hoja de servicio eliminado exitosamente.",
                        customClass: {
                            title: 'text-2xl font-bold tracking-widest ',
                            icon: 'text-base font-bold tracking-widest ',
                            text: 'bg-red-500 hover:bg-red-600 tracking-widest ',
                        },
                    });
                }
            });
        }
    });
}

const previewPDF = () => {

    const doc = new jsPDF();

    const maxWidth = 150; // Ancho máximo permitido
    const lineHeight = doc.getLineHeight() / doc.internal.scaleFactor; // Altura de línea para el salto
    const pageHeight = doc.internal.pageSize.height; // Altura de la página

    const servicio_a_realizar = document.getElementById("servicio_a_realizar").options[document.getElementById("servicio_a_realizar").selectedIndex].text;
    const razon_social = document.getElementById("razon_social").value;
    const direccion = document.getElementById("direccion").value;
    const contacto = document.getElementById("contacto").value;
    const area_de_contacto = document.getElementById("area_de_contacto").value;
    const telefono_de_contacto = document.getElementById("telefono_de_contacto").value;
    const asesor_encargado = document.getElementById("asesor_encargado").value;
    const tecnico_encargado = document.getElementById("tecnico_encargado").value;
    const cantidad_de_instrumentos = document.getElementById("cantidad_de_instrumentos").value;
    const hora_del_servicio = document.getElementById("hora_del_servicio").value;
    const datos_del_instrumento = document.getElementById("datos_del_instrumento").value;
    const trabajos_a_realizar = document.getElementById("trabajos_a_realizar").value;

    let eje_y = 20; // Starting y-coordinate

    // Función para verificar y cambiar de página si es necesario
    const checkPageHeight = (lineHeight) => {
        if (eje_y + lineHeight > pageHeight - 10) { // 10 es un espacio adicional para evitar bordes
            doc.addPage();
            eje_y = 20; // Reiniciar en la parte superior de la nueva página
        }
    };

    doc.setFontSize(12);

    doc.setFont('Helvetica', 'bold');
    doc.text("HOJA DE SERVICIO", 85, eje_y);
    eje_y += 15;

    doc.setFont('Helvetica', 'bold');
    doc.text("Servicio a realizar: ", 20, eje_y);
    doc.setFontSize(10.5);
    doc.setFont('Helvetica', 'normal');
    doc.setFontSize(12);
    doc.text(`${servicio_a_realizar}`, 60, eje_y);
    eje_y += 10;

    doc.setFont('Helvetica', 'bold');
    doc.setFontSize(11);
    doc.text("\u2022 Razon Social: ", 25, eje_y);
    doc.setFont('Helvetica', 'normal');
    doc.text(`${razon_social}`, 54, eje_y);
    eje_y += 10;

    doc.setFont('Helvetica', 'bold');
    doc.text("\u2022 Dirección: ", 25, eje_y);
    doc.setFontSize(11);
    doc.setFont('Helvetica', 'normal');
    var direccionLines = doc.splitTextToSize(`${direccion}`, maxWidth);
    // Dibujar cada línea de texto en el PDF
    for (let line of direccionLines) {
        checkPageHeight(lineHeight);
        doc.text(line, 48, eje_y);
        eje_y += lineHeight;
    }

    eje_y += 4;
    doc.setFont('Helvetica', 'bold');
    doc.text("\u2022 Contacto: ", 25, eje_y);
    doc.setFontSize(11);
    doc.setFont('Helvetica', 'normal');
    doc.text(`${contacto}`, 47, eje_y);

    eje_y += 10;
    doc.setFont('Helvetica', 'bold');
    doc.text("\u2022 Área de Contacto: ", 25, eje_y);
    doc.setFontSize(11);
    doc.setFont('Helvetica', 'normal');
    doc.text(`${area_de_contacto}`, 62, eje_y);

    eje_y += 10;
    doc.setFont('Helvetica', 'bold');
    doc.text("\u2022 Teléfono de Contacto: ", 25, eje_y);
    doc.setFontSize(11);
    doc.setFont('Helvetica', 'normal');
    doc.text(`${telefono_de_contacto}`, 70, eje_y);

    eje_y += 10;
    doc.setFont('Helvetica', 'bold');
    doc.text("\u2022 Asesor Encargado: ", 25, eje_y);
    doc.setFontSize(11);
    doc.setFont('Helvetica', 'normal');
    doc.text(`${asesor_encargado}`, 65, eje_y);

    eje_y += 10;
    doc.setFont('Helvetica', 'bold');
    doc.text("\u2022 Tecnico Encargado: ", 25, eje_y);
    doc.setFontSize(11);
    doc.setFont('Helvetica', 'normal');
    doc.text(`${tecnico_encargado}`, 66, eje_y);

    eje_y += 10;
    doc.setFont('Helvetica', 'bold');
    doc.text("\u2022 Hora del Servicio: ", 25, eje_y);
    doc.setFontSize(11);
    doc.setFont('Helvetica', 'normal');
    doc.text(`${hora_del_servicio}`, 62, eje_y);

    eje_y += 10;
    doc.setFont('Helvetica', 'bold');
    doc.text("\u2022 Cantidad de Instrumentos: ", 25, eje_y);
    doc.setFontSize(11);
    doc.setFont('Helvetica', 'normal');
    doc.text(`${cantidad_de_instrumentos}`, 78, eje_y);

    eje_y += 10;
    doc.setFont('Helvetica', 'bold');
    doc.text("Datos del Instrumento: ", 20, eje_y);
    doc.setFontSize(10.5);
    doc.setFont('Helvetica', 'normal');
    eje_y += 10;

    // Separar el contenido del textarea en líneas
    const datosInstrumentoLines = `${datos_del_instrumento}`.split('\n');

    // Procesar cada línea
    datosInstrumentoLines.forEach((line) => {
        // Dividir la línea en varias líneas si excede el ancho máximo
        const splitLines = doc.splitTextToSize('\u2022 ' + line, maxWidth);
        // Añadir cada línea procesada al documento PDF
        splitLines.forEach((splitLine) => {
            checkPageHeight(lineHeight);
            doc.text(splitLine, 27, eje_y);
            eje_y += lineHeight;
        });
        // Añadir un pequeño espacio adicional después de cada línea del textarea
        eje_y += lineHeight * 0.1;
    });

    // Aumentar el eje Y para el espacio adecuado antes de "Trabajos a Realizar"
    eje_y += 5;

    doc.setFont('Helvetica', 'bold');4
    doc.setFontSize(11);
    doc.text("Trabajos a Realizar: ", 20, eje_y);

    doc.setFontSize(10.5);
    doc.setFont('Helvetica', 'normal');

    eje_y += 10;

    // Separar el contenido del textarea en líneas
    const trabajosARealizarLines = `${trabajos_a_realizar}`.split('\n');

    // Procesar cada línea
    trabajosARealizarLines.forEach((line) => {
        // Dividir la línea en varias líneas si excede el ancho máximo
        const splitLines = doc.splitTextToSize('\u2022 ' + line, maxWidth);
        // Añadir cada línea procesada al documento PDF
        splitLines.forEach((splitLine) => {
            checkPageHeight(lineHeight);
            doc.text(splitLine, 27, eje_y);
            eje_y += lineHeight;
        });
        // Añadir un pequeño espacio adicional después de cada línea del textarea
        eje_y += lineHeight * 0.1;
    });

    // Vista previa del PDF en una nueva ventana
    doc.output('dataurlnewwindow');
};

</script>
<template>
    <AppLayout title="Hoja de Servicio">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Generar Hoja de Servicio</h1>
        </template>

         <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-100 shadow-lg shadow-gray-600 dark:bg-gray-800  dark:shadow-gray-800 rounded-lg">
                    <div class="tracking-widest font-extrabold text-center uppercase">
                        <h1 class="md:text-lg mb-2 text-md dark:text-white underline decoration-solid underline-offset-8">aqui puedes crear una hoja servicio y actualizar al hacer doble click</h1>
                    </div>
                    <form @submit.prevent="submitForm" class="my-5 uppercase font-bol">
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 md:grid-cols-3 sm:gap-x-8 mb-3">
                            <div>
                                <InputLabel for="servicio_a_realizar" value="servicio a realizar"
                                    class="cruz block text-md font-medium text-gray-700" />
                                <select id="servicio_a_realizar" v-model="form.servicio_a_realizar" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Seleccione un servicio</option>
                                    <option value="Mantenimiento">Mantenimiento</option>
                                    <option value="Calibracion">Calibracion</option>
                                    <option value="Diagnostico">Diagnostico</option>
                                    <option value="Certificacion Balinsa">Certificacion Balinsa</option>
                                    <option value="Certificacion  total weight">Certificacion  total weight</option>
                                    <option value="Reparacion ">Reparacion</option>
                                    <option value="Entrega">Entrega</option>
                                    <option value="Recojo">Recojo</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel for="razon_social" value="razon social *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.razon_social" type="text" id="razon_social" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.razon_social" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="direccion" value="Direccion *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.direccion" type="text" id="direccion" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.direccion" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 md:grid-cols-3 sm:gap-x-8 mb-3">
                            <div>
                                <InputLabel for="contacto" value="contacto *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.contacto" type="text" id="contacto" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.contacto" class="mt-2"></InputError>
                            </div>

                            <div>
                                <InputLabel for="area_de_contacto" value="area de contacto *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.area_de_contacto" type="text" id="area_de_contacto" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.area_de_contacto" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="telefono_de_contacto" value="telefono de contacto *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.telefono_de_contacto" type="text" id="telefono_de_contacto" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.telefono_de_contacto" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 md:grid-cols-4 sm:gap-x-8 mb-3">
                            <div>
                                <InputLabel for="asesor_encargado" value="asesor encargado *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.asesor_encargado" type="text" id="asesor_encargado"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.asesor_encargado" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="tecnico_encargado" value="tecnico encargado *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.tecnico_encargado" type="text" id="tecnico_encargado"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.tecnico_encargado" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="hora_del_servicio" value="hora del servicio *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.hora_del_servicio" type="time" id="hora_del_servicio"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.hora_del_servicio" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="cantidad_de_instrumentos" value="cantidad de instrumentos *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.cantidad_de_instrumentos" type="text"
                                    id="cantidad_de_instrumentos"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.cantidad_de_instrumentos" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 sm:grid-cols-2 sm:gap-x-8 mb-3">
                            <div>
                                <InputLabel for="datos_del_instrumento" value="datos del instrumento *"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <textarea id="datos_del_instrumento" v-model="form.datos_del_instrumento" rows="4"
                                    class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Escriba las datos_del_instrumento..."></textarea>
                            </div>
                            <div>
                                <InputLabel for="trabajos_a_realizar" value="trabajos a realizar"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <textarea id="trabajos_a_realizar" v-model="form.trabajos_a_realizar" rows="4"
                                    class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Escriba las trabajos_a_realizar..."></textarea>
                            </div>
                        </div>
                        <div class="d-flex mt-4">
                            <div class="flex flex-wrap gap-2 justify-end">
                                <button class="inline-block bg-indigo-700 text-white font-bold py-2 px-4 rounded hover:bg-indigo-800 text-xs uppercase md:w-min whitespace-nowrap w-full text-center" @click.prevent="previewPDF">Ver pdf</button>
                                <ButtonResponsive v-if="!isEditing" @dblclick="editHojaServicio(hojaservicio)" class="uppercase text-xs">Generar Hoja de Especificaciones</ButtonResponsive>
                                <ButtonResponsive v-if="isEditing" @click="updateHojaServicio()" class="uppercase text-xs">Actualizar Hoja de Servicio</ButtonResponsive>
                            </div>
                        </div>
                    </form>
                    <div class="tracking-widest font-extrabold text-center uppercase">
                        <h1 class="md:text-lg mb-3 text-md dark:text-white underline decoration-wavy">Historial de Hojas de Servicio </h1>
                    </div>
                    <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-800">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 hidden">N° inf</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">razonsocial</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">direccion</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Area decontacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2"><i class="fas fa-phone mx-2"></i> de contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">asesor encargado</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">tecnico encargado</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">hora delservicio</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">cantidad instrumentos</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">datos instrumento</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">trabajos a realizar</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-xs tracking-widest">
                                <tr @dblclick="editHojaServicio(hojaservicio)" v-for="hojaservicio in hojaservicios"
                                    :key="hojaservicio.id"
                                    class="bg-white text-black  hover:text-white border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                    <!-- Mostrar los datos de cada hservicio -->
                                    <td class="px-6 py-4 text-center hidden">{{ hojaservicio.n_informe }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.razon_social }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.direccion }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.contacto }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.area_de_contacto }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.telefono_de_contacto }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.asesor_encargado }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.tecnico_encargado }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.hora_del_servicio }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.cantidad_de_instrumentos }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.datos_del_instrumento }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{hojaservicio.trabajos_a_realizar }}</td>
                                    <td class="px-6 py-3 justify-center dark:border-white flex">
                                        <button @click="$event => deleteHojaServicio(hojaservicio.id, hojaservicio.razon_social)"
                                            class="text-center ml-1 text-white shadow-lg shadow-gray-500 dark:shadow-red-600 dark:hover:bg-white dark:hover:text-red-600 bg-red-500 hover:bg-red-600 py-1 px-2 rounded-md"
                                            title="Eliminar Cliente"><i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="hojaservicios.length === 0" class="text-center py-2 dark:text-white">
                            No se encontraron datos.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>