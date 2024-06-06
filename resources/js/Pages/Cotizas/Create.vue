<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import Swal from 'sweetalert2';
import { onMounted , watch , computed , ref , nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
 
import jsPDF from 'jspdf';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
 
const nameInput2 = ref(null);
const modal2 = ref(false);
const title2 = ref('');
const operation2 = ref(1);
const id2 = ref('');
 
const modal3 = ref(false);
const tbproductosAgregados = ref([]);
 
 
const toggleModal3 = () => {
    modal3.value = !modal3.value;
};
 
defineProps({
    clientes: {
        type : Object,
        required: true
    },
    tenors:{
        type:Object,
        required:true
    },
    tbproductos: {
        type : Object,
        required: true
    },
    tbsubcategorias: {
        type : Object,
        required: true
    },
    tbcategorias: {
        type : Object,
        required: true
    },
    tbmarcas: {
        type : Object,
        required: true
    }
 
});
 
const form2 = useForm({
    name: '',
});
const form = useForm ({
    cliente_id: '',
    tenor_id:'',
    fecha:'',
    moneda:'',
    garantia:'',
    forma_pago:'',
    dias_entrega:'',
    subtotal: 0,
    igv: 0,
    total: 0,
    igvEnabled: false,
});
 
 
// llenan automaticamente cuando se agregan productos y se activa o desactiva el igv
const agregarProducto = (producto) => {
    producto.cantidad = 1;
    tbproductosAgregados.value.push(producto);
    form.subtotal = tbproductosAgregados.value.reduce((acc, curr) => acc + (curr.precio * curr.cantidad), 0);
    form.igv = form.subtotal * 0.18;
    form.total = form.subtotal + form.igv;
    toggleModal3();
    Swal.fire({
        title: 'Producto agregado',
        text: 'El producto ha sido agregado correctamente',
        icon: 'success',
        timer: 1000,
        showConfirmButton: false
    });
};
 
 
const quitarProducto = (index) => {
    tbproductosAgregados.value.splice(index, 1);
    form.subtotal = tbproductosAgregados.value.reduce((acc, curr) => acc + (curr.precio * curr.cantidad), 0);
    form.igv = form.subtotal * 0.18;
    form.total = form.subtotal + form.igv;
};
 
const igvEnabled = ref(false);
 
// Watcher para actualizar el total cuando se cambia el estado del IGV
watch(igvEnabled, (newValue) => {
    if (newValue) {
        form.igv = form.subtotal * 0.18;
    } else {
        form.igv = 0;
    }
    form.total = form.subtotal + form.igv;
});
 
// Watcher para actualizar el subtotal, el IGV y el total cuando se cambia la cantidad de productos
watch(tbproductosAgregados, (newProductos) => {
    form.subtotal = newProductos.reduce((acc, curr) => acc + (curr.precio * curr.cantidad), 0);
    form.igv = igvEnabled.value ? form.subtotal * 0.18 : 0;
    form.total = form.subtotal + form.igv;
}, { deep: true });
 
// Computed para deshabilitar el campo de IGV si no hay productos agregados
const igvDisabled = computed(() => tbproductosAgregados.value.length === 0);
 
const calcularTotal = () => {
    if (form.moneda === 'dolares $') {
        form.total = (form.subtotal + form.igv) * form.tipoCambio;
    } else {
        form.total = form.subtotal + form.igv;
    }
};
 
//modal para ve la imagen
const modalOpen = ref(false);
const modalImageUrl = ref('');
 
const openModal = (imageUrl) => {
    modalImageUrl.value = imageUrl;
    modalOpen.value = true;
};
 
 
watch(() => form.moneda, (newValue) => {
    if (newValue === 'dolares $') {
        Swal.fire({
            title: 'Tipo de Cambio',
            input: 'number',
            inputLabel: 'Ingresa el tipo de cambio del dólar',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value) {
                    return 'Debes ingresar el tipo de cambio del dólar';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const tipoCambio = result.value;
                form.subtotal = parseFloat((form.subtotal / tipoCambio).toFixed(2));
                form.igv = parseFloat((form.igv / tipoCambio).toFixed(2));
                form.total = parseFloat((form.total / tipoCambio).toFixed(2));
                form.tipoCambio = tipoCambio;
            } else {
                form.moneda = '';
            }
        });
    } else if (newValue === 'soles s/' && form.tipoCambio) {
        form.subtotal = form.subtotal * form.tipoCambio;
        form.igv = form.igv * form.tipoCambio;
        form.total = form.total * form.tipoCambio;
        form.tipoCambio = null;
    }
});
 
//modal para crear el tenor
const openModal2 = (op, name, tenor) => {
    modal2.value = true;
    nextTick(() => nameInput2.value.focus());
    operation2.value = op;
    id2.value = tenor;
    if (op === 1) {
        title2.value = 'Registrar Descripcion';
    } else {
        title2.value = 'Actualizar Descripcion';
        form2.name = name;
    }
};
 
const closeModal2 = () => {
    modal2.value = false;
    form2.reset();
};
 
const save2 = () => {
    if (operation2.value === 1) {
        form2.post(route('tenors.store'), {
            onSuccess: () => { ok2('registrado con exito'); }
        });
    } else {
        form2.put(route('tenors.update', id2.value), {
            onSuccess: () => { ok2('actualizado con exito'); }
        });
    }
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
 
onMounted(() => {
    const today = new Date().toISOString().split('T')[0];
    form.fecha = today;
});
 
const submitForm = (event) => {
    if (event) {
        event.preventDefault();
    }

    // Definir los valores
    let precio = parseFloat(tbproductosAgregados.precio);
    let precio_max = parseFloat(tbproductosAgregados.precio_max);
    let subtotal = parseFloat(form.subtotal);

    // Validar el rango del subtotal
    if (subtotal < precio || subtotal > precio_max) {
        Swal.fire({
            icon: 'error',
            title: 'Error en el subtotal',
            text: `El subtotal debe estar entre ${precio} y ${precio_max}.`,
            showConfirmButton: false,
            timer: 2000
        });
        return; // Detener el envío del formulario
    }

    form.post(route('cventas.store'), {
        onSuccess: () => {
            form.reset();
            Swal.fire({
                title: 'Cotización guardada',
                text: 'La cotización se ha guardado exitosamente.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            Inertia.visit(route('cventas.index'));
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

const { props } = usePage();
const user = props.auth.user;
const obtenerNombreCompleto = (user) => {
    if (user) { return `${user.name} ${user.apellidopat} ${user.apellidomat}` }
    return '';
}
const nombreCompleto = obtenerNombreCompleto(user);

const previewPDF = () => {
    const doc = new jsPDF();

    let plantilla = '/storage/catalago_productos/plantillacotizacion.png';

    doc.addImage(plantilla, 'PNG', 1, 1, 208, 295); // Agregar la imagen en las coordenadas fijas

    const fechaValor = document.getElementById("fecha").value;

    // Parsear la fecha para formatearla
    const partesFecha = fechaValor.split('-');
    const fecha = new Date(partesFecha[0], partesFecha[1] - 1, partesFecha[2]);
    const opcionesFecha = { year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fecha.toLocaleDateString('es-ES', opcionesFecha);
   
    const cliente = document.getElementById("cliente_id").options[document.getElementById("cliente_id").selectedIndex].text;
    const descripcion = document.getElementById("tenor_id").options[document.getElementById("tenor_id").selectedIndex].text;
    const moneda = document.getElementById("moneda").options[document.getElementById("moneda").selectedIndex].text;
    const garantia = document.getElementById("garantia").options[document.getElementById("garantia").selectedIndex].text;
    const forma_pago = document.getElementById("forma_pago").options[document.getElementById("forma_pago").selectedIndex].text;
    const dias_entrega = document.getElementById("dias_entrega").value;
    const subtotal = document.getElementById("subtotal").value;
    const igv = document.getElementById("igv").value;
    const total = document.getElementById("total").value;
 
    var eje_y = 50;
    var yPos = 0;
    var eje_r = 30;

    doc.setTextColor(255,0,0);//Color de texto
    doc.setFontSize(12);//Tamaño de texto
    doc.setFont('Helvetica', 'bold');//estilos de texto
    doc.text(120, eje_y, 'COTIZACION N° - ');
    doc.text(157, eje_y, '0000001');
    eje_y += 5; // vale 15

    doc.setTextColor(0,0,0);//Color de texto
    doc.setFont('Helvetica', 'bold');//estilos de texto
    doc.setFontSize(10.5);//Tamaño de texto
    doc.text(132, eje_y, `${fechaFormateada}`);
    eje_y += 5; // vale 15

    doc.setTextColor(0,0,0);//Color de texto
    doc.setFontSize(10);//Tamaño de texto
    doc.setFont('Helvetica', 'normal');//estilos de texto
    doc.text(20, eje_y, 'Señores:');
    doc.setFont('Helvetica', 'bold');//estilos de texto
    doc.setFontSize(10.5);//Tamaño de texto
    eje_y += 5; // vale 15
    doc.text(20, eje_y, `${cliente}`);
    
    // user
    // doc.setTextColor(0,0,0);//Color de texto
    // doc.setFontSize(10);//Tamaño de texto
    // doc.setFont('Helvetica', 'bold');//estilos de texto
    // doc.setFontSize(10.5);//Tamaño de texto
    // eje_y += 5; // vale 15
    // doc.text(20, eje_y, `${nombreCompleto}`);

    doc.setTextColor(0,0,0);//Color de texto
    doc.setFontSize(10);//Tamaño de texto
    doc.setFont('Helvetica', 'normal');//estilos de texto
    eje_y += 14; // vale 29
    doc.text(20, eje_y, 'Estimados Señores:');
    var splitten = doc.splitTextToSize('En atención a su solicitud nos es grato dirigirnos a Ustedes, para presentarles la mejor propuesta del mercado:', 160);
    eje_y += 5; // vale 34
    doc.text(20, eje_y, splitten);

    doc.setTextColor(0,0,0);//Color de texto
    doc.setFontSize(10.5);//Tamaño de texto
    doc.setFont('Helvetica', 'bold');//estilos de texto
    eje_y += 11; // vale 45
    doc.text(20, eje_y, `${descripcion}`);

    yPos = eje_y;

    const addProductData = (producto, index, nextPageCallback) => {
        const img = new Image();
        img.src = `/storage/${producto.foto}`;

        img.onload = () => {
            if (yPos + 150 > doc.internal.pageSize.height) {
                doc.addPage();
                doc.addImage(plantilla, 'PNG', 1, 1, 208, 295);
                yPos = 40;
                eje_y = 40;
            }
            doc.addImage(img, 'JPEG', 135, yPos + 5, 40, 40);
            yPos += 150;

            // Continúa con el contenido de producto
            doc.setTextColor(0,0,0);
            doc.setFontSize(10);
            doc.setFont('Helvetica', 'bold');
            eje_y += 20;
            doc.text(20, eje_y, 'Marca');
            doc.setFont('Helvetica', 'normal');
            doc.setFontSize(10.5);
            doc.text(55, eje_y, ': ' + `${producto.tbmarca ? producto.tbmarca.nombre : 'Sin marca'}`);

            eje_y += 5;
            doc.setFont('Helvetica', 'bold');
            doc.setFontSize(10);
            doc.text(20, eje_y, 'Modelo');
            doc.setFont('Helvetica', 'normal');
            doc.setFontSize(10.5);
            doc.text(55, eje_y, ': ' + `${producto.modelo}`);

            eje_y += 5;
            doc.setFont('Helvetica', 'bold');
            doc.setFontSize(10);
            doc.text(20, eje_y, 'Capacidades');
            const capacidadTecnica = `${producto.capacidades}`;
            const capacidadesDivididas = doc.splitTextToSize(capacidadTecnica, 70);

            capacidadesDivididas.forEach((linea, indice) => {
                const texto = (indice === 0) ? ': ' + linea : '  '+ linea;
                doc.setFont('Helvetica', 'normal');
                doc.setFontSize(10.5);
                doc.text(55, eje_y, texto);
                eje_y += 5;
            });

            eje_y += 20;
            doc.setFont('Helvetica', 'bold');
            doc.setFontSize(10);
            doc.text(20, eje_y, 'Especificaciones Tecnicas');
            doc.setFont('Helvetica', 'normal');
            doc.setFontSize(10);

            const especificaciones = producto.especificaciones.split('\n');
            const viñeta = '\u2022';
            const maxWidth = 160;

            eje_y += 10;
            especificaciones.forEach((especificacion) => {
                const splitten = doc.splitTextToSize(especificacion, maxWidth);
                splitten.forEach((fragment, index) => {
                    if (eje_y > 240) {
                        doc.addPage();
                        doc.addImage(plantilla, 'PNG', 1, 1, 208, 295);
                        eje_y = 40;
                    }
                    const prefix = index === 0 ? `${viñeta} ` : '   ';
                    doc.text(20, eje_y, `${prefix}${fragment}`);
                    eje_y += 5;
                });
            });

            nextPageCallback();
        };
    };

    const addAllProducts = (productos, index) => {
        if (index >= productos.length) {
            addPriceUnit();
            return;
        }
        
        addProductData(productos[index], index, () => {
            addAllProducts(productos, index + 1);
        });
    };

    const addPriceUnit = () => {
        let precioProducto = total;
        let precioProducto1 = parseFloat(precioProducto).toLocaleString('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
            useGrouping: true
        });
        let precioProducto2 = precioProducto1.padStart(11);

        doc.setFont('Helvetica', 'bold');
        doc.setFontSize(14);
        eje_y += 10;
        doc.setFillColor(255, 255, 0);
        doc.rect(54, eje_y - 7.5, 45, 12, 'F');
        doc.text(60, eje_y, 'Precio Unitario');

        let simboloMoneda = '';
        if (moneda.includes('soles')) {
            simboloMoneda = 'S/.';
        } else if (moneda.includes('dolares')) {
            simboloMoneda = '$';
        }

        doc.setFont('Helvetica', 'bold');
        doc.setFontSize(14);
        doc.setFillColor(255, 255, 0);
        doc.rect(98, eje_y - 7.5, 45, 12, 'F');
        doc.text(105, eje_y, ': ' + simboloMoneda + `${precioProducto2}`);

        addGeneralConditions();
    };

    const addGeneralConditions = () => {
        doc.addPage();
        doc.addImage(plantilla, 'PNG', 1, 1, 208, 295);
        // Establecer el color de la línea
        doc.setDrawColor(0, 0, 0);

        // Establecer el grosor de la línea (en este caso, 0.5)
        doc.setLineWidth(0.4);

        // Dibujar una línea debajo del texto
        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'bold');//estilos de texto
        eje_r += 20; // vale 125
        var texto = 'CONDICIONES GENERALES:';
        var textWidth = doc.getStringUnitWidth(texto) * doc.internal.getFontSize() / doc.internal.scaleFactor;
        var textHeight = doc.internal.getLineHeight();
        var startX = 20;
        var startY = eje_r + 2; // Ajustar según el tamaño de la fuente
        doc.text(texto, startX, startY);
        doc.line(startX, startY + 1, startX + textWidth, startY + 1);

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'bold');//estilos de texto
        eje_r += 8; // vale 133
        doc.text(20, eje_r, 'Garantia');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10.5);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        doc.text(55, eje_r, ': ' + `${garantia}`);

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'bold');//estilos de texto
        eje_r += 5; // vale 138
        doc.text(20, eje_r, 'Plazo de Entrega');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10.5);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        doc.text(55, eje_r, ': ' + `${dias_entrega}` + ' Dias');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'bold');//estilos de texto
        eje_r += 5; // vale 143
        doc.text(20, eje_r, 'Forma de Pago');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10.5);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        doc.text(55, eje_r, ': ' + `${forma_pago}`);

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        eje_r += 5; // vale 148
        doc.text(20, eje_r, 'Deposito a cuenta de Ahorros');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'bold');//estilos de texto
        eje_r += 5; // vale 153
        doc.text(20, eje_r, 'BBVA : ');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        doc.text(33, eje_r, 'S/ 0011 0267 0201320316  /  CCI: 011 267 000201320316 27');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'bold');//estilos de texto
        eje_r += 5; // vale 158
        doc.text(20, eje_r, 'BCP : ');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        doc.text(32, eje_r, '$ 475-2156380-1-04  /  S/ 475-2156367-0-62');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        eje_r += 5; // vale 163
        doc.text(20, eje_r, 'Orden de compra irrevocable');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        eje_r += 5; // vale 168
        doc.text(20, eje_r, 'Servicio gratuito dentro del tiempo de garantia');

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        eje_r += 5; // vale 173
        doc.text(20, eje_r, 'A la espera de su orden');

        doc.output('dataurlnewwindow');
    };

    addAllProducts(tbproductosAgregados.value, 0);
};

</script>
 
<template>
    <AppLayout title="Cotizar por Venta">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Crear Cotizacion por Venta</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-xs  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
               
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-3 sm:gap-x-8 mb-3 sm:py-0 py-2">
                                <!-- cliente -->
                                <div class="w-full">
                                    <InputLabel for="cliente_id" class="text-xs">Cliente</InputLabel>
                                    <select id="cliente_id" v-model="form.cliente_id" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Seleccione un cliente</option>
                                        <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">{{ cliente.razonSocial }}</option>
                                    </select>
                                </div>
                                <!-- tenor -->
                                <div class="flex flex-col items-start">
                                    <InputLabel for="tenor_id" class="block text-xs font-medium text-gray-700">Descripcion</InputLabel>
                                    <div class="flex w-full">
                                        <select id="tenor_id" v-model="form.tenor_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="" disabled selected>Seleccione una descripcion</option>
                                            <!-- Iterar sobre las marcas -->
                                            <option v-for="tenor in tenors" :key="tenor.id" :value="tenor.id">{{ tenor.name }}</option>
                                        </select>
                                        <Button @click.prevent="() => openModal2(1)" class="bg-green-600 py-1 text-white mt-1 w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div>
                                 <!-- fecha -->
                                 <div>
                                    <InputLabel for="fecha" class="block text-xs font-medium text-gray-700">Fecha</InputLabel>
                                    <TextInput type="date" id="fecha" v-model="form.fecha" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.fecha" class="mt-2"/>
                                </div>
                            </div>
                            <div class="text-end">
                                <!-- Botón para abrir el tercer modal -->
                                <button class="text-white uppercase text-xs bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center" @click.prevent="toggleModal3">Agregar Producto</button>
                            </div>
                            <div class="sm:py-5 py-2">
                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2  max-h-80 overflow-y-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-black dark:text-white">
                                        <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Foto</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Especificaciones</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Marca</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Capacidades</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio sin igv</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Importe</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Quitar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(tbproducto, i) in tbproductosAgregados" :key="index">
                                                <td class="px-3 py-4 text-center border-r border-b">{{ tbproducto.modelo }}</td>
                                                <td class="px-6 py-3 text-center border-r border-b">
                                                    <img @click="openModal('/storage/' + tbproducto.foto)" :src="'/storage/' + tbproducto.foto" alt="Foto" class="w-10 h-10 cursor-pointer object-cover rounded-md">
                                                </td>
                                                <td class="px-6 py-3 text-center border-r border-b whitespace-nowrap">
                                                    <div class="accordions">
                                                        <dl>
                                                            <dt @click="toggleAccordion(i)" class="cursor-pointer normal-case">
                                                                Especificaciones
                                                                <i :class="{'fa-solid fa-arrow-up-long ml-2': activeAccordion === i, 'fa-solid fa-arrow-down-long ml-2': activeAccordion !== i}"></i>
                                                            </dt>
                                                            <dd v-if="activeAccordion === i" class="ml-4">
                                                                <ul class="list-disc px-6 py-4 text-left">
                                                                    <li v-for="(item, index) in tbproducto.especificaciones.split('\n')" :key="index">{{ item }}</li>
                                                                </ul>
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3 text-center border-r border-b">{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }}</td>
                                                <td class="px-6 py-3 text-left border-r border-b whitespace-nowrap">
                                                    <ul class="list-disc px-6 py-4 text-left -translate-y-1">
                                                        <li v-for="(capacidad, index) in tbproducto.capacidades.split('\n')" :key="index">{{ capacidad }}</li>
                                                    </ul>
                                                </td>
                                                <td class="px-6 py-4 text-center border-r border-b">{{ parseFloat(tbproducto.precio).toFixed(2) }} / {{ parseFloat(tbproducto.precio_max).toFixed(2) }}</td>
                                                <td class="px-3 py-3 text-center border-r border-b"><input class="text-center dark:bg-gray-800 bg-white text-black border-white dark:border-gray-800 dark:text-white w-16" type="number" v-model="tbproducto.cantidad"></td>
                                                <td class="px-3 py-3 text-center border-r border-b">s/{{ tbproducto.precio * tbproducto.cantidad }}</td>
                                                <td class="px-3 py-3 text-center border-r border-b"><button @click.prevent="quitarProducto(index)"><i class="bi bi-trash3 text-red-500"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-4 sm:gap-x-8 sm:py-0 py-1">
                                <!-- garantia -->
                                <div>
                                    <InputLabel for="garantia" class="block text-xs font-medium text-gray-700">Garantia</InputLabel>
                                    <select id="garantia" v-model="form.garantia" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected >Selecciona una opcion</option>
                                        <option value="3 meses">3 meses</option>
                                        <option value="6 meses">6 meses</option>
                                        <option value="1 año">1 año</option>
                                        <option value="sin garantia">Sin garantia</option>
                                    </select>
                                </div>
                                <!-- Moneda -->
                                <div>
                                    <div>
                                        <InputLabel for="moneda" class="block text-xs font-medium text-gray-700">Moneda</InputLabel>
                                        <select id="moneda" v-model="form.moneda" required
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="" disabled selected>Selecciona una moneda</option>
                                            <option value="soles s/">Soles</option>
                                            <option value="dolares $">Dólares</option>
                                        </select>
                                    </div>
                                </div>
 
                                <!-- Forma pago -->
                                <div>
                                    <InputLabel for="forma_pago" class="block text-xs font-medium text-gray-700">Forma de pago</InputLabel>
                                    <select id="forma_pago" v-model="form.forma_pago" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona una forma de pago</option>
                                        <option value="Al contado">Al contado</option>
                                        <option value="Credito 15 dias">Credito 15 dias</option>
                                        <option value="Credito 30 dias">Credito 30 dias</option>
                                        <option value="Credito 60 dias">Credito 60 dias</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="dias_entrega" class="block text-xs font-medium text-gray-700">Días de entrega</InputLabel>
                                    <select id="dias_entrega" v-model="form.dias_entrega" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona días de entrega</option>
                                        <!-- Generar opciones del 1 al 31 -->
                                        <option v-for="dia in 31" :key="dia" :value="dia">{{ dia }} día{{ dia > 1 ? 's' : '' }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-4 py-2 card sm:gap-x-8">
                                <div>
                                    <InputLabel class="text-xs" for="subtotal" :value="'Subtotal (' + (form.moneda === 'soles s/' ? 'S/' : '$') + '):'"></InputLabel>
                                    <TextInput id="subtotal" v-model="form.subtotal" type="number" class="mt-2 w-full uppercase"></TextInput>
                                </div>
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="igvCheckbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" v-model="igvEnabled">
                                        <label for="igvCheckbox" class="ms-2 text-xs font-medium text-black dark:text-white">Aplicar IGV (18%)</label>
                                    </div>
                                    <InputLabel class="ml-6 py-1 hidden" for="igv" :value="'IGV (18%) (' + (form.moneda === 'soles s/' ? 'S/' : '$') + '):'" v-if="igvEnabled"></InputLabel>
                                    <TextInput id="igv" v-model="form.igv" type="number" class="mt-2 w-full uppercase" disabled></TextInput>
                                </div>
                                <div>
                                    <InputLabel class="text-xs" for="total" :value="'Total (' + (form.moneda === 'soles s/' ? 'S/' : '$') + '):'"></InputLabel>
                                    <TextInput id="total" v-model="form.total" type="number" class="mt-2 w-full bg-green-400 uppercase" disabled></TextInput>
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <button class="inline-block bg-green-700 text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center" @click.prevent="previewPDF">PREVISUALIZAR PDF</button>
                                    <ButtonResponsive class="text-white uppercase text-xs bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                        Generar Cotizacion
                                    </ButtonResponsive>
                                    <Link :href="route('cventas.index')" class="inline-block bg-red-600 text-white font-bold py-2 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">
                                        Cancelar
                                    </Link>
                                    <!--  -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <ModalResponsive :show="modal2" @close="closeModal2">
            <div class="p-4 uppercase">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title2 }}</h2>
                <div class="p-1">
                    <div class="w-full">
                        <InputLabel for="name" value="Nombre:" class="mb-2"></InputLabel>
                        <TextInput id="name" ref="nameInput2" v-model="form2.name" type="text" class="w-full"
                                placeholder="Nombre"></TextInput>
                        <InputError :message="form2.errors.name" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form2.processing" @click="save2">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation2 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form2.processing"
                            @click="closeModal2">
                            Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
 
        <div v-if="modal3" class="fixed inset-0 overflow-y-auto z-50 bg-gray-200/40 flex justify-center items-center" style="backdrop-filter: blur(5px);" @click.self="toggleModal3">
            <div class="modal-content bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-2xl max-w-7xl w-full sm:max-w-4xl md:max-w-3xl lg:max-w-4xl xl:max-w-6xl">
                <button @click="toggleModal3" class="close absolute top-0.5 right-0.5 p-2 text-gray-500 hover:text-gray-700">
                    &times;
                </button>
                <div class="px-2 py-2 dark:text-white">
                    <h1 class="uppercase text-lg font-bold">Catálogo de productos</h1>
                    <p class="text-sm">Seleccione los productos a cotizar</p>
                    <hr class="my-1">
                </div>
                <div class="py-0 px-2">
                    <div class="py-0">
                        <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-8 mb-3">
                            <div class="flex flex-col">
                                <InputLabel class="block text-md font-medium text-gray-700">Categoría</InputLabel>
                                <select class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="" disabled selected >Seleccione una Categoría</option>
                                    <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id" :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                </select>
                            </div>
                            <div class="flex flex-col">
                                <InputLabel class="block text-md font-medium text-gray-700">Subcategoría</InputLabel>
                                <select class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="" disabled selected >Seleccione una Subcategoría</option>
                                    <option v-for="tbsubcategoria in tbsubcategorias" :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                                </select>
                            </div>
                            <div class="flex flex-col">
                                <InputLabel for="table-search" class="block text-md font-medium text-gray-700">Buscar</InputLabel>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                        </svg>
                                    </div>
                                    <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar el producto">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-3 px-2">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 max-h-80 overflow-y-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-black dark:text-white">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Modelo</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Foto</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Especificaciones</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Marca</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Capacidades</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Precio sin igv</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Agregar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tbproducto in filteredProductos" :key="tbproducto.id" class="bg-white text-black dark:bg-gray-700 dark:text-white">
                                    <td class="px-6 py-3 text-center border-r border-b whitespace-nowrap">{{ tbproducto.modelo }}</td>
                                    <td class="px-6 py-3 text-center border-r border-b">
                                        <img @click="openModal('/storage/' + tbproducto.foto)" :src="'/storage/' + tbproducto.foto" alt="Foto" class="w-10 h-10 cursor-pointer object-cover rounded-md">
                                    </td>
                                    <td class="px-6 py-3 text-center border-r border-b whitespace-nowrap">
                                        <div class="accordions">
                                            <dl>
                                                <dt @click="toggleAccordion(tbproducto.id)" class="cursor-pointer">
                                                    Especificaciones
                                                    <i :class="{'fa-solid fa-arrow-up-long ml-2': activeAccordion === tbproducto.id, 'fa-solid fa-arrow-down-long ml-2': activeAccordion !== tbproducto.id}"></i>
                                                </dt>
                                                <dd v-if="activeAccordion === tbproducto.id" class="ml-4">
                                                    <ul class="list-disc px-6 py-4 text-left">
                                                        <li v-for="(item, index) in tbproducto.especificaciones.split('\n')" :key="index">{{ item }}</li>
                                                    </ul>
                                                </dd>
                                            </dl>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-center border-r border-b">{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }}</td>
                                    <td class="px-6 py-3 text-left border-r border-b whitespace-nowrap">
                                        <ul class="list-disc px-6 py-4 text-left -translate-y-1">
                                            <li v-for="(capacidad, index) in tbproducto.capacidades.split('\n')" :key="index">{{ capacidad }}</li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 text-center border-r border-b">{{ parseFloat(tbproducto.precio).toFixed(2) }} / {{ parseFloat(tbproducto.precio_max).toFixed(2) }}</td>
                                    <td class="px-6 py-3 text-center border-r border-b">
                                        <button @click="agregarProducto(tbproducto)" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:bg-green-700">
                                            Agregar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="filteredProductos.length === 0" class="text-center py-2 dark:text-white">
                            No se encontraron datos.
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        <div class="fixed inset-0 overflow-y-auto z-50 bg-gray-200/40" v-if="modalOpen">
            <div class="flex justify-center  items-center h-full">
                <div class="bg-white dark:bg-gray-700 rounded-lg w-full max-w-lg mx-4 sm:mx-auto">
                    <div class="py-1 px-1 sm:px-1">
                        <img :src="modalImageUrl" alt="Imagen ampliada" class="w-full max-h-80 object-contain">
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-2 sm:px-6 flex justify-end rounded-b-lg">
                        <button @click="modalOpen = false" type="button" class="w-full sm:w-auto justify-center rounded-md border border-transparent shadow-sm px-2 py-1 bg-indigo-600 text-xs uppercase text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    data() {
        return {
            activeAccordion: null,
            searchQuery: '', // Agregar el data para el search query
        };
    },
    computed: {
        filteredProductos() {
            // Filtrar productos basados en el search query
            return this.tbproductos.filter(producto => {
                // Filtrar por modelo o cualquier otro criterio que desees
                return producto.modelo.toLowerCase().includes(this.searchQuery.toLowerCase());
            });
        }
    },
    methods: {
        toggleAccordion(index) {
            this.activeAccordion = this.activeAccordion === index ? null : index;
        }
    }
}
</script>