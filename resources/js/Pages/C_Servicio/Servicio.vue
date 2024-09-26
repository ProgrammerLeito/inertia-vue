<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

const props =defineProps({
    hServicios: {
        type: Object,
        required: true
    },
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
                        <div class="d-flex mt-4">
                            <div class="flex flex-wrap gap-2 justify-end">
                                {{ hServicios }}
                                <button class="inline-block bg-green-700 text-xs text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center" @click.prevent="previewPDF2">PREVISUALIZAR PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
