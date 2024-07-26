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
import { onMounted , watch , computed , ref , nextTick, watchEffect } from 'vue';
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
 
const { clientes, tenors, tbproductos, tbsubcategorias, tbcategorias, tbmarcas, nCotizacion } = defineProps({
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
    },
    nCotizacion: {
        type: String,
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
    forma_pago:'',
    dias_entrega:'',
    subtotal: 0,
    igv: 0,
    total: 0,
    igvEnabled: false,
    n_cotizacion: nCotizacion
});

const searchTerm = ref('');
const searchTermCodigoCli = ref('');
const filteredClientes = ref([]);
const selectedIndex = ref(-1);
const activeAccordion = ref([]);

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

const selectCliente = (cliente) => {
    searchTerm.value = cliente.razonSocial;
    searchTermCodigoCli.value = cliente.id;
    form.cliente_id = searchTermCodigoCli.value;
    filteredClientes.value = [];
};

const toggleAccordion = (index) => {
    const indexPosition = activeAccordion.value.indexOf(index);
    if (indexPosition === -1) {
        activeAccordion.value.push(index);
    } else {
        activeAccordion.value.splice(indexPosition, 1);
    }
};

const isActive = (index) => {
    return activeAccordion.value.includes(index);
};
 
 
// llenan automaticamente cuando se agregan productos y se activa o desactiva el igv
const agregarProducto = (producto) => {
    producto.cantidad = 1;
    tbproductosAgregados.value.push(producto);
    form.subtotal = tbproductosAgregados.value.reduce((acc, curr) => acc + (curr.precio_min * curr.cantidad), 0);
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
    setTimeout(() => {
        const moneda = document.getElementById('moneda').options[document.getElementById("moneda").selectedIndex].text;
        const tipoCambio = document.getElementById('tipoCambio').value === null ? 1 : document.getElementById('tipoCambio').value;
        
        
        let total = 0;
        const rows = document.querySelectorAll('tr'); // Selecciona todas las filas de la tabla

        rows.forEach(function(row) {
            const importeCell = row.querySelector('.inputImporte');
            const monedaCell = row.children[14]; // Supongo que la celda 15 es la columna 14 (index 0 basado)
            if (importeCell && monedaCell) {
                if(moneda == "Soles"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "$") {
                            total += value * parseFloat(tipoCambio);
                        } else {
                            total += value;
                        }
                    }
                }else if(moneda == "Dólares"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "s/") {
                            total += value / parseFloat(tipoCambio);
                        } else {
                            total += value;
                        }
                    }
                }
            }
        });

        document.getElementById('subtotal').value = total.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
        
    }, 300);
};
 
 
const quitarProducto = (index) => {
    tbproductosAgregados.value.splice(index, 1);
    form.subtotal = tbproductosAgregados.value.reduce((acc, curr) => acc + (curr.precio_min * curr.cantidad), 0);
    form.igv = form.subtotal * 0.18;
    form.total = form.subtotal + form.igv;
};
 
const igvEnabled = ref(false);
 
// Watcher para actualizar el total cuando se cambia el estado del IGV
watch(igvEnabled, (newValue) => {
    const rTotal = document.getElementById('subtotal').value; 
    if (newValue) {
        form.igv = parseFloat(rTotal) * 0.18;
    } else {
        form.igv = 0;
    }
    form.total = parseFloat(rTotal) + form.igv;
});
 
// Watcher para actualizar el subtotal, el IGV y el total cuando se cambia la cantidad de productos
watch(tbproductosAgregados, (newProductos) => {
    form.subtotal = newProductos.reduce((acc, curr) => acc + (curr.precio_min * curr.cantidad), 0);
    form.igv = igvEnabled.value ? form.subtotal * 0.18 : 0;
    form.total = form.subtotal + form.igv;
    setTimeout(() => {
        const moneda = document.getElementById('moneda').options[document.getElementById("moneda").selectedIndex].text;
        const tipoCambio = document.getElementById('tipoCambio').value === null ? 1 : document.getElementById('tipoCambio').value;
        
        
        let total = 0;
        const rows = document.querySelectorAll('tr'); // Selecciona todas las filas de la tabla

        rows.forEach(function(row) {
            const importeCell = row.querySelector('.inputImporte');
            const monedaCell = row.children[14]; // Supongo que la celda 15 es la columna 14 (index 0 basado)
            if (importeCell && monedaCell) {
                if(moneda == "Soles"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "$") {
                            total += value * parseFloat(tipoCambio);
                        } else {
                            total += value;
                        }
                    }
                }else if(moneda == "Dólares"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "s/") {
                            total += value / parseFloat(tipoCambio);
                        } else {
                            total += value;
                        }
                    }
                }
            }
        });

        document.getElementById('subtotal').value = total.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
        
    }, 50);
}, { deep: true });
 
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

                console.log('Tipo de cambio ingresado:', tipoCambio);
            } else {
                form.moneda = '';
            }
        });
    } else if (newValue === 'soles s/') {
        Swal.fire({
            title: 'Tipo de Cambio',
            input: 'number',
            inputLabel: 'Ingresa el tipo de cambio de soles',
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
                form.subtotal = parseFloat((form.subtotal * tipoCambio).toFixed(2));
                form.igv = parseFloat((form.igv * tipoCambio).toFixed(2));
                form.total = parseFloat((form.total * tipoCambio).toFixed(2));
                form.tipoCambio = tipoCambio;

                console.log('Tipo de cambio ingresado:', tipoCambio);
            } else {
                form.moneda = '';
            }
        });
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
const roles = props.auth.user.roles[0]?.name;

const obtenerNombreCompleto = (user) => {
    if (user) {
        return `${user.name} ${user.apellidopat} ${user.apellidomat}`;
    }
    return '';
};
const nombreCompleto = obtenerNombreCompleto(user);

const previewPDF = () => {
    const accordions = document.querySelectorAll('.accordions dt');
    accordions.forEach((accordion, index) => {
        // Simula un clic en cada acordeón para abrirlo
        if (accordion) {
        accordion.click();
        }
    });

    setTimeout(() => {

        const doc = new jsPDF();

        let plantilla = '/img/plantillacotizacion.png';

        doc.addImage(plantilla, 'PNG', 1, 1, 208, 295); // Agregar la imagen en las coordenadas fijas

        const fechaValor = document.getElementById("fecha").value;

        // Parsear la fecha para formatearla
        const partesFecha = fechaValor.split('-');
        const fecha = new Date(partesFecha[0], partesFecha[1] - 1, partesFecha[2]);
        const opcionesFecha = { year: 'numeric', month: 'long', day: 'numeric' };
        const fechaFormateada = fecha.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }).replace(/(\d{1,2} de )([a-záéíóúñ]+)/, (match, p1, p2) => p1 + p2.charAt(0).toUpperCase() + p2.slice(1));
    
        const cliente = document.getElementById("cliente_id").value;
        // const descripcion = document.getElementById("tenor_id").options[document.getElementById("tenor_id").selectedIndex].text;
        const moneda = document.getElementById("moneda").options[document.getElementById("moneda").selectedIndex].text;
        const igvEnabled = document.getElementById("igvCheckbox").checked;
        // const igvValue = document.getElementById("igv").value;
        // const garantia = document.getElementById("garantia").options[document.getElementById("garantia").selectedIndex].text;
        // const forma_pago = document.getElementById("forma_pago").options[document.getElementById("forma_pago").selectedIndex].text;
        // const dias_entrega = document.getElementById("dias_entrega").value;
        const subtotal = document.getElementById("subtotal").value;
        // const igv = document.getElementById("igv").value;
        const total = document.getElementById("total").value;
    
        var eje_y = 50;
        var yPos = 0;
        var eje_r = 30;
        var eje_x = 20;

        const numeroCotizacion = form.n_cotizacion
        const numeroCotizacionFormateado = numeroCotizacion.toString().padStart(8, '0');
        const anchoPagina = doc.internal.pageSize.width;
        const margenDerecho = 20;

        doc.setTextColor(255,0,0);//Color de texto
        doc.setFontSize(12);//Tamaño de texto
        doc.setFont('Helvetica', 'bold'); // Estilos de texto
        const texto = `COTIZACION N° - ${numeroCotizacionFormateado}`;
        const anchoTexto = doc.getTextWidth(texto);
        const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
        doc.text(eje_x_left, eje_y, texto);
        eje_y += 5; // vale 15

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFont('Helvetica', 'bold');//estilos de texto
        doc.setFontSize(10.5);//Tamaño de texto
        const texto2 = `${fechaFormateada}`;
        const anchoTexto2 = doc.getTextWidth(texto2);
        const eje_x_left2 = anchoPagina - anchoTexto2 - margenDerecho;
        doc.text(eje_x_left2, eje_y, texto2);
        eje_y += 5; // vale 15

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        doc.text(20, eje_y, 'Señores:');
        doc.setFont('Helvetica', 'bold');//estilos de texto
        doc.setFontSize(10.5);//Tamaño de texto
        eje_y += 5; // vale 15
        doc.text(eje_x, eje_y, `${cliente}`);

        doc.setTextColor(0,0,0);//Color de texto
        doc.setFontSize(10);//Tamaño de texto
        doc.setFont('Helvetica', 'normal');//estilos de texto
        eje_y += 10; // vale 25
        doc.text(eje_x, eje_y, 'Estimados Señores:');
        var splitten = doc.splitTextToSize('En atención a su solicitud nos es grato dirigirnos a Ustedes, para presentarles la mejor propuesta del mercado:', 160);
        eje_y += 5; // vale 30
        doc.text(eje_x, eje_y, splitten);

        eje_y += 10;
        yPos = eje_y;

        // Obtener los datos de la tabla
        const rows = document.querySelectorAll('.relative table tbody tr');

        const addProductData = (row, index, nextPageCallback) => {

            const categoria = row.querySelector('td:nth-child(1)').innerText.trim();
            const modelo = row.querySelector('td:nth-child(2)').innerText.trim();
            const foto = row.querySelector('td:nth-child(3) img').src;
            const especificaciones = row.querySelector('td:nth-child(4) ul').innerText.trim();
            const marca = row.querySelector('td:nth-child(5)').innerText.trim();
            const capacidades = row.querySelector('td:nth-child(6) ul').innerText.trim();
            const precioList = row.querySelector('td:nth-child(7)').innerText.trim();
            const precioMin = row.querySelector('td:nth-child(8)').innerText.trim();
            const precioMax = row.querySelector('td:nth-child(9)').innerText.trim();
            const cantidad = row.querySelector('td:nth-child(10) input').value.trim();
            const importe = row.querySelector('td:nth-child(11)').innerText.trim();
            const garantia = row.querySelector('td:nth-child(12)').innerText.trim();
            const diasEntrega = row.querySelector('td:nth-child(13)').innerText.trim();
            const formaPago = row.querySelector('td:nth-child(14)').innerText.trim();
            const moneda = row.querySelector('td:nth-child(15)').innerText.trim();
            
            const img = new Image();
            img.src = foto;

            const categoriaText = cantidad === "1"
            ? (categoria.toLowerCase().endsWith("es") ? categoria.slice(0, -2)
            : (categoria.toLowerCase().endsWith("s") ? categoria.slice(0, -1)
            : categoria))
            : categoria;

            eje_y += 5;
            doc.setFont('Helvetica', 'bold');
            doc.setFontSize(12);

            img.onload = () => {
                if (yPos + 150 > doc.internal.pageSize.height) {
                    doc.addPage();
                    doc.addImage(plantilla, 'PNG', 1, 1, 208, 295);
                    yPos = 40;
                    eje_y = 50;
                }
                doc.addImage(img, 'JPEG', 135, yPos + 10, 40, 40);
                yPos += 150;

                doc.text(20, eje_y, `Venta de ${cantidad} ${categoriaText.charAt(0).toUpperCase() + categoriaText.slice(1).toLowerCase()} de las siguientes características:`);

                doc.setTextColor(0,0,0);
                doc.setFontSize(10);
                doc.setFont('Helvetica', 'bold');
                eje_y += 15;
                doc.text(eje_x, eje_y, 'Marca');
                doc.setFont('Helvetica', 'normal');
                doc.setFontSize(10.5);
                doc.text(50, eje_y, ': ' + marca);

                eje_y += 5;
                doc.setFont('Helvetica', 'bold');
                doc.setFontSize(10);
                doc.text(eje_x, eje_y, 'Modelo');
                doc.setFont('Helvetica', 'normal');
                doc.setFontSize(10.5);
                doc.text(50, eje_y, ': ' + modelo);

                eje_y += 5;
                doc.setFont('Helvetica', 'bold');
                doc.setFontSize(10);
                doc.text(eje_x, eje_y, 'Capacidades');
                const capacidadesDivididas = doc.splitTextToSize(capacidades, 70);

                capacidadesDivididas.forEach((linea, indice) => {
                    const texto = (indice === 0) ? ': ' + linea : '  ' + linea;
                    doc.setFont('Helvetica', 'normal');
                    doc.setFontSize(10.5);
                    doc.text(50, eje_y, texto);
                    eje_y += 5;
                });

                eje_y += 10;
                doc.setFont('Helvetica', 'bold');
                doc.setFontSize(10);
                doc.text(20, eje_y, 'Especificaciones Técnicas :');
                doc.setFont('Helvetica', 'normal');
                doc.setFontSize(10);
                eje_y += 10;

                const especificacionesDivididas = doc.splitTextToSize(especificaciones, 160);
                especificacionesDivididas.forEach((fragmento, index) => {
                    if (eje_y > doc.internal.pageSize.height - 40) {
                        doc.addPage();
                        doc.addImage(plantilla, 'PNG', 1, 1, 208, 295);
                        eje_y = 40;
                    }
                    const prefijo = "- ";
                    doc.text(eje_x, eje_y, `${prefijo}${fragmento}`);
                    eje_y += 5;
                });

                eje_y += 5;
                doc.setFont('Helvetica', 'bold');
                doc.setFontSize(10);
                doc.text(eje_x, eje_y, 'Plazo de entrega');
                doc.setFont('Helvetica', 'normal');
                doc.setFontSize(10.5);
                doc.text(50, eje_y, ': ' + diasEntrega);

                
                eje_y += 5;
                doc.setFont('Helvetica', 'bold');
                doc.setFontSize(10);
                doc.text(eje_x, eje_y, 'Forma de Pago');
                doc.setFont('Helvetica', 'normal');
                doc.setFontSize(10.5);
                doc.text(50, eje_y, ': ' + formaPago);
                
                eje_y += 5;
                doc.setTextColor(255, 0 ,0)
                doc.setFont('Helvetica', 'bold');
                doc.setFontSize(10);
                doc.text(eje_x, eje_y, 'Garantia');
                doc.setFont('Helvetica', 'normal');
                doc.setFontSize(10.5);
                doc.text(50, eje_y, ': ' + garantia);
                
                doc.setTextColor(0, 0 ,0)
                eje_y += 20;
                doc.setFont('Helvetica', 'bold');
                doc.setFontSize(14);

                const texto = `Precio Unitario  :  ${moneda.toUpperCase()} ${parseFloat(importe).toFixed(2)} + IGV`;
                const anchoTexto = doc.getTextWidth(texto);
                const anchoRectangulo = anchoTexto + 20;
                const eje_x2 = (doc.internal.pageSize.width - anchoRectangulo) / 2;
                doc.setFillColor(255, 255, 0); // Color de fondo amarillo (RGB)
                doc.rect(eje_x2, eje_y - 7.5, anchoRectangulo, 12, 'F'); // Ajusta las dimensiones y posición del rectángulo según sea necesario
                doc.setTextColor(0, 0, 0); // Color del texto (negro)
                doc.text(eje_x2 + 10, eje_y, texto);

                nextPageCallback();
            };
        };

        const addAllProducts = (rows, index) => {
            if (index >= rows.length) {
                addPriceUnit();
                return;
            }
            
            addProductData(rows[index], index, () => {
                addAllProducts(rows, index + 1);
            });
        };

        const addPriceUnit = () => {
            let precioProducto = total;

            const precioFormateado = parseFloat(precioProducto).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
                useGrouping: true
            });

            doc.setFont('Helvetica', 'bold');
            doc.setFontSize(18);
            eje_y += 25;

            let simboloMoneda = '';
            if (moneda == "Soles") {
                simboloMoneda = 'S/.';
            } else if (moneda == "Dólares") {
                simboloMoneda = '$';
            }

            doc.setTextColor(0, 0, 0); // Color del texto (negro)
            doc.setFillColor(255, 255, 0);

            if (igvEnabled) {
                const texto = `Precio Total  :  ${simboloMoneda} ${precioFormateado}`;
                const anchoTexto = doc.getTextWidth(texto);
                const eje_x2 = (doc.internal.pageSize.width - anchoTexto) / 2;

                const igvText = "INCLUIDO IGV";
                const anchoTexto2 = doc.getTextWidth(igvText);
                const eje_x3 = (doc.internal.pageSize.width - anchoTexto2) / 2;
                const anchoRectangulo = anchoTexto + 20;
                doc.rect(eje_x2 - 10, eje_y - 7, anchoRectangulo, 18, 'F');
                doc.text(eje_x2, eje_y, texto);
                doc.setFontSize(14);
                doc.text(eje_x3+5, eje_y + 8, igvText);
            }else{
                const texto = `Precio Total  :  ${simboloMoneda} ${precioFormateado} + IGV`;
                const anchoTexto = doc.getTextWidth(texto);
                const eje_x2 = (doc.internal.pageSize.width - anchoTexto) / 2;
                const anchoRectangulo = anchoTexto + 20;
                doc.rect(eje_x2 - 10, eje_y - 8, anchoRectangulo, 12, 'F');
                doc.text(eje_x2, eje_y, texto);
            }

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
            var texto = 'NUESTRAS CUENTAS :';
            var textWidth = doc.getStringUnitWidth(texto) * doc.internal.getFontSize() / doc.internal.scaleFactor;
            var textHeight = doc.internal.getLineHeight();
            var startX = 20;
            var startY = eje_r + 2; // Ajustar según el tamaño de la fuente
            doc.text(texto, startX, startY);
            doc.line(startX, startY + 1, startX + textWidth, startY + 1);

            doc.setTextColor(0,0,0);//Color de texto
            doc.setFontSize(12);//Tamaño de texto
            doc.setFont('Helvetica', 'bold');//estilos de texto
            eje_r += 8; // vale 148
            doc.text(20, eje_r, 'Deposito a Cuenta de Ahorros');

            doc.setTextColor(0,0,255);//Color de texto
            doc.setFontSize(10.5);//Tamaño de texto
            doc.setFont('Helvetica', 'bold');//estilos de texto
            eje_r += 5; // vale 153
            doc.text(20, eje_r, 'BBVA');

            doc.setTextColor(0,0,0);//Color de texto
            doc.setFontSize(10.5);//Tamaño de texto
            doc.setFont('Helvetica', 'normal');//estilos de texto
            doc.text(32, eje_r, ': SOLES: 0011 0267 0201320316  |  CCI: 011 267 000201320316 27');

            doc.setTextColor(255,0,0);//Color de texto
            doc.setFontSize(10.5);//Tamaño de texto
            doc.setFont('Helvetica', 'bold');//estilos de texto
            eje_r += 5; // vale 158
            doc.text(20, eje_r, 'BCP');

            doc.setTextColor(0,0,0);//Color de texto
            doc.setFontSize(10.5);//Tamaño de texto
            doc.setFont('Helvetica', 'normal');//estilos de texto
            doc.text(32, eje_r, ': SOLES: 475-2156367-0-62  |  DOLARES: 475-2156380-1-04');

            doc.setTextColor(0,0,0);//Color de texto
            doc.setFontSize(11);//Tamaño de texto
            doc.setFont('Helvetica', 'normal');//estilos de texto
            eje_r += 5; // vale 163
            doc.text(20, eje_r, 'Orden de compra irrevocable');

            doc.setTextColor(0,0,0);//Color de texto
            doc.setFontSize(11);//Tamaño de texto
            doc.setFont('Helvetica', 'normal');//estilos de texto
            eje_r += 5; // vale 168
            doc.text(20, eje_r, 'Servicio gratuito dentro del tiempo de garantia');

            doc.setTextColor(0,0,0);//Color de texto
            doc.setFontSize(12);//Tamaño de texto
            doc.setFont('Helvetica', 'normal');//estilos de texto
            eje_r += 5; // vale 173
            doc.text(20, eje_r, 'A la espera de su orden');

            doc.setTextColor(0, 0, 0); // Color de texto
            doc.setFontSize(12); // Tamaño de texto
            doc.setFont('Helvetica', 'bold'); // Estilos de texto

            const anchoPagina = doc.internal.pageSize.width;
            const margenDerecho = 20;
            const anchoTextoNombre = doc.getTextWidth(nombreCompleto);
            const eje_x_left = anchoPagina - anchoTextoNombre - margenDerecho;

            // Calcular el ancho del texto de los roles
            const rolesWidth = doc.getTextWidth(roles);
            const eje_x_roles = eje_x_left + ((anchoTextoNombre / 2) - (rolesWidth / 2));

            eje_r += 160; // vale 173
            doc.text(eje_x_left, eje_r, nombreCompleto);
            eje_r += 5; // vale 173
            doc.setFontSize(10);
            doc.text(eje_x_roles, eje_r, roles);

            accordions.forEach((accordion, index) => {
            // Si el acordeón está abierto, simula un clic para cerrarlo
            if (accordion && accordion.querySelector('i').classList.contains('fa-arrow-up-long')) {
            accordion.click();
            }
        });

            const blob = doc.output('blob');
            const url = URL.createObjectURL(blob);
            window.open(url);
        };

        addAllProducts(document.querySelectorAll('.relative table tbody tr'), 0);
    }, 300); 
};

document.addEventListener('input', function(event) {
    const moneda = document.getElementById('moneda').options[document.getElementById("moneda").selectedIndex].text;
    const tipoCambio = document.getElementById('tipoCambio').value === null ? 1 : document.getElementById('tipoCambio').value;
    
    if (event.target.classList.contains('inputImporte')) {
        let total = 0;
        const rows = document.querySelectorAll('tr'); // Selecciona todas las filas de la tabla

        rows.forEach(function(row) {
            const importeCell = row.querySelector('.inputImporte');
            const monedaCell = row.children[14]; // Supongo que la celda 15 es la columna 14 (index 0 basado)
            if (importeCell && monedaCell) {
                if(moneda == "Soles"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "$") {
                            total += value * parseFloat(tipoCambio);
                        } else {
                            total += value;
                        }
                    }
                }else if(moneda == "Dólares"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "s/") {
                            total += value / parseFloat(tipoCambio);
                        } else {
                            total += value;
                        }
                    }
                }
            }
        });

        form.subtotal = total
        form.total = total

        document.getElementById('subtotal').value = total.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
    }
});

const searchQuery = ref('');
const selectedCategoria = ref('');
const selectedSubcategoria = ref('');
const filteredProductos = ref([]);

watchEffect(() => {
    filteredProductos.value = props.tbproductos.filter(tbproducto => {
    const searchLowerCase = searchQuery.value.toLowerCase();
    return (
        (tbproducto.modelo.toLowerCase().includes(searchLowerCase) ||
        tbproducto.tbcategoria.nombre.toLowerCase().includes(searchLowerCase) ||
        tbproducto.tbsubcategoria.nombre.toLowerCase().includes(searchLowerCase)) &&
        (selectedCategoria.value === '' || tbproducto.tbcategoria_id === selectedCategoria.value) &&
        (selectedSubcategoria.value === '' || tbproducto.tbsubcategoria_id === selectedSubcategoria.value)
    );
    });
});

const filteredSubcategorias = computed(() => {
    if (selectedCategoria.value === '') {
    return [];
    }
    const categoria = props.tbcategorias.find(categoria => categoria.id === selectedCategoria.value);
    return categoria ? categoria.tbsubcategorias : [];
});

watchEffect(() => {
    const categoriaSeleccionada = props.tbcategorias.find(categoria => categoria.id === selectedCategoria.value);
    if (categoriaSeleccionada) {
    selectedSubcategoria.value = categoriaSeleccionada.tbsubcategorias.length > 0 ? categoriaSeleccionada.tbsubcategorias[0].id : '';
    } else {
    selectedSubcategoria.value = '';
    }
});

var listarClientes = props.clientes;
console.log(listarClientes);

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
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-4 sm:gap-x-8 mb-3 sm:py-0 py-2">
                                <!-- cliente -->
                                <div class="w-full">
                                    <InputLabel for="cliente_id" class="text-xs">Cliente</InputLabel>
                                    <div class="relative">
                                        <TextInput v-model="searchTerm" autocomplete="off" type="text" id="cliente_id" @input="onInput" placeholder="Ingresa razon social"
                                            @keydown="onKeydown"
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
                                <!-- tenor -->
                                <!-- <div class="flex flex-col items-start">
                                    <InputLabel for="tenor_id" class="block text-xs font-medium text-gray-700">Descripcion</InputLabel>
                                    <div class="flex w-full">
                                        <select id="tenor_id" v-model="form.tenor_id" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg">
                                            <option value="" disabled selected>Seleccione una descripcion</option>
                                            <option v-for="tenor in tenors" :key="tenor.id" :value="tenor.id">{{ tenor.name }}</option>
                                        </select>
                                        <Button @click.prevent="() => openModal2(1)" class="bg-green-600 py-1 text-white mt-1 w-10 h-[42px] sm:h-[38px] rounded-r-lg">
                                            <i class="fas fa-plus mx-2"></i>
                                        </Button>
                                    </div>
                                </div> -->
                                 <!-- fecha -->
                                 <div>
                                    <InputLabel for="fecha" class="block text-xs font-medium text-gray-700">Fecha</InputLabel>
                                    <TextInput type="date" id="fecha" v-model="form.fecha" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.fecha" class="mt-2"/>
                                </div>
                                <div>
                                    <div>
                                        <InputLabel for="moneda" class="block text-xs font-medium text-gray-900">Moneda</InputLabel>
                                        <select id="moneda" v-model="form.moneda" required
                                                class="h-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="" disabled selected>Selecciona una moneda</option>
                                            <option value="soles s/">Soles</option>
                                            <option value="dolares $">Dólares</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <InputLabel class="text-xs" for="tipoCambio" :value="'tipo Cambio (' + (form.moneda === 'soles s/' ? 'S/' : '$') + '):'"></InputLabel>
                                    <TextInput id="tipoCambio" v-model="form.tipoCambio" type="number" class="w-full bg-green-400 uppercase" disabled></TextInput>
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
                                                <th scope="col" class="px-6 py-3 text-center border-b-2 border-white hidden">Categoria</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Foto</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Especificaciones</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Marca</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Capacidades</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio List.</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Min.</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Max.</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Importe</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Garantia</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap">D. Entrega</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap">F. Pago</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap hidden">Moneda</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Quitar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(tbproducto, i) in tbproductosAgregados" :key="i">
                                                <td class="px-6 py-3 text-center border-r border-b hidden">{{ tbproducto.tbcategoria.nombre }}</td>
                                                <td class="px-3 py-4 text-center border-r border-b">{{ tbproducto.modelo }}</td>
                                                <td class="px-4 py-3 text-center border-r border-b">
                                                    <img @click="openModal('/productos_img/' + tbproducto.foto)" :src="'/productos_img/' + tbproducto.foto" alt="Foto" class="w-10 h-10 cursor-pointer object-cover rounded-md">
                                                </td>
                                                <td class="px-6 py-4 text-center border-r border-b whitespace-nowrap">
                                                    <div class="accordions">
                                                        <dl>
                                                            <dt @click="toggleAccordion(i)" class="cursor-pointer normal-case">
                                                                Especificaciones
                                                                <i :class="{'fa-solid fa-arrow-up-long ml-2': isActive(i), 'fa-solid fa-arrow-down-long ml-2': !isActive(i)}"></i>
                                                            </dt>
                                                            <dd v-if="isActive(i)" class="ml-4">
                                                                <ul class="list-disc px-6 py-4 text-left">
                                                                    <li v-for="(item, index) in tbproducto.especificaciones.split('\n')" :key="index">{{ item }}</li>
                                                                </ul>
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3 text-center border-r border-b">{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }}</td>
                                                <td class="px-6 py-3 text-left border-r border-b whitespace-nowrap" contenteditable="true">
                                                    <ul class="list-disc px-4 py-2 text-left -translate-y-1" >
                                                        <li v-for="(capacidad, i) in tbproducto.capacidades.split('\n')" :key="i">{{ capacidad }}</li>
                                                    </ul>
                                                </td>
                                                <td class="px-6 py-4 text-center border-r whitespace-nowrap border-b">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_list ? tbproducto.precio_list : '0').toFixed(2) }}</td>
                                                <td class="px-6 py-4 text-center border-r whitespace-nowrap border-b">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_min ? tbproducto.precio_min : '0').toFixed(2) }} </td>
                                                <td class="px-6 py-4 text-center border-r whitespace-nowrap border-b">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_max ? tbproducto.precio_max : '0').toFixed(2) }} </td>
                                                <td class="px-3 py-3 text-center border-r whitespace-nowrap border-b"><input class="text-center dark:bg-gray-800 bg-white text-black border-white dark:border-gray-800 dark:text-white w-16" type="number" v-model="tbproducto.cantidad"></td>
                                                <td class="px-3 py-3 text-center border-r border-b inputImporte" contenteditable="true">{{ subtotalfinal = parseFloat(tbproducto.precio_min * tbproducto.cantidad).toFixed(2) }}</td>
                                                <td class="px-3 py-4 text-center border-r border-b capitalize"  placeholder="" contenteditable="true">{{ tbproducto.garantia }}</td>
                                                <td class="px-3 py-4 text-center border-r border-b normal-case" placeholder="hola" contenteditable="true">
                                                    <span class="placeholder-text">1 Día</span>
                                                </td>
                                                <td class="px-3 py-4 text-center border-r border-b normal-case" placeholder="" contenteditable="true">
                                                    <span class="placeholder-text">Al contado</span>
                                                </td>
                                                <td class="px-3 py-4 text-center border-r border-b normal-case hidden">
                                                    <span class="placeholder-text uppercase">{{ tbproducto.moneda }}</span>
                                                </td>
                                                <td class="px-3 py-3 text-center border-r border-b"><button @click.prevent="quitarProducto(i)"><i class="bi bi-trash3 text-red-500"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-4 sm:gap-x-8 sm:py-0 py-1">
                                <!-- garantia -->
                                <!-- <div>
                                    <InputLabel for="garantia" class="block ml-2 text-xs font-medium text-gray-900">Garantia</InputLabel>
                                    <select id="garantia" v-model="form.garantia" required
                                            class="mt-2.5 h-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected >Selecciona una opcion</option>
                                        <option value="3 meses">3 meses</option>
                                        <option value="6 meses">6 meses</option>
                                        <option value="1 año">1 año</option>
                                        <option value="sin garantia">Sin garantia</option>
                                    </select>
                                </div> -->
                                <!-- Moneda -->
                                
 
                                <!-- Forma pago -->
                                <!-- <div>
                                    <InputLabel for="forma_pago" class="block text-xs font-medium text-gray-700">Forma de pago</InputLabel>
                                    <select id="forma_pago" v-model="form.forma_pago"
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
                                    <select id="dias_entrega" v-model="form.dias_entrega"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Selecciona días de entrega</option>
                                        <option v-for="dia in 31" :key="dia" :value="dia">{{ dia }} día{{ dia > 1 ? 's' : '' }}</option>
                                    </select>
                                </div> -->
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="igvCheckbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" v-model="igvEnabled">
                                        <label for="igvCheckbox" class="ms-2 text-xs font-medium text-black dark:text-white">Aplicar IGV (18%)</label>
                                    </div>
                                    <InputLabel class="ml-6 py-1 hidden" for="igv" :value="'IGV (18%) (' + (form.moneda === 'soles s/' ? 'S/' : '$') + '):'" v-if="igvEnabled"></InputLabel>
                                    <TextInput id="igv" v-model="form.igv" type="text" class="mt-2 w-full uppercase" :disabled="!igvEnabled"></TextInput>
                                </div>
                                <div>
                                    <InputLabel class="text-xs" for="subtotal" :value="'Subtotal (' + (form.moneda === 'soles s/' ? 'S/' : '$') + '):'"></InputLabel>
                                    <TextInput id="subtotal" v-model="form.subtotal" type="text" class="mt-2 w-full uppercase"></TextInput>
                                </div>
                                <div>
                                    <InputLabel class="text-xs" for="total" :value="'Total (' + (form.moneda === 'soles s/' ? 'S/' : '$') + '):'"></InputLabel>
                                    <TextInput id="total" v-model="form.total" type="text" class="mt-2 w-full bg-green-400 uppercase" disabled></TextInput>
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
 
        <div v-if="modal3" class="fixed inset-0 overflow-y-auto z-1 pt-[110px] overflow-hidden bg-gray-200/40 flex justify-center items-center md:px-0 px-2" style="backdrop-filter: blur(2px);" @click.self="toggleModal3">
            <div class=" min-w-[calc(100vw-60px)] md:ml-[50px] md:px-6 max-h-[90%]">
                <div class="modal-content bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-2xl p-2">
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
                                <select v-model="selectedCategoria" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="" disabled>Seleccione una Categoría</option>
                                    <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id" :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                </select>
                            </div>
                            <div class="flex flex-col">
                                <InputLabel class="block text-md font-medium text-gray-700">Subcategoría</InputLabel>
                                <select v-model="selectedSubcategoria" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="" disabled>Seleccione una Subcategoría</option>
                                    <option v-for="tbsubcategoria in filteredSubcategorias" :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
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
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white hidden">Categoria</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Modelo</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Foto</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Especificaciones</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Marca</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Capacidades</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Precio Lis.</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Precio Min.</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Precio Max.</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Garantia</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Agregar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tbproducto in filteredProductos" :key="tbproducto.id" class="bg-white text-black dark:bg-gray-700 dark:text-white">
                                    <td class="px-6 py-3 text-center border-r border-b hidden">{{ tbproducto.tbcategoria.nombre }}</td>
                                    <td class="px-6 py-3 text-center border-r border-b whitespace-nowrap">{{ tbproducto.modelo }}</td>
                                    <td class="px-6 py-3 text-center border-r border-b">
                                        <img @click="openModal('/productos_img/' + tbproducto.foto)" :src="'/productos_img/' + tbproducto.foto" alt="Foto" class="w-10 h-10 cursor-pointer object-cover rounded-md">
                                    </td>
                                    <td class="px-6 py-4 text-center border-r border-b whitespace-nowrap">
                                        <div class="accordions">
                                            <dl>
                                                <dt @click="toggleAccordion(i)" class="cursor-pointer normal-case">
                                                    Especificaciones
                                                    <i :class="{'fa-solid fa-arrow-up-long ml-2': isActive(i), 'fa-solid fa-arrow-down-long ml-2': !isActive(i)}"></i>
                                                </dt>
                                                <dd v-if="isActive(i)" class="ml-4">
                                                    <ul class="list-disc px-6 py-4 text-left">
                                                        <li v-for="(item, index) in tbproducto.especificaciones.split('\n')" :key="index">{{ item }}</li>
                                                    </ul>
                                                </dd>
                                            </dl>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-center border-r border-b">{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }}</td>
                                    <td class="px-6 py-3 text-left border-r border-b whitespace-nowrap">
                                        <ul class="list-disc px-2 py-2 text-left -translate-y-1">
                                            <li v-for="(capacidad, index) in tbproducto.capacidades.split('\n')" :key="index">{{ capacidad }}</li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 text-center border-r border-b">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_list ? tbproducto.precio_list : '0').toFixed(2) }}</td>
                                    <td class="px-6 py-4 text-center border-r border-b">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_min ? tbproducto.precio_min : '0').toFixed(2) }} </td>
                                    <td class="px-6 py-4 text-center border-r border-b">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_max ? tbproducto.precio_max : '0').toFixed(2) }} </td>
                                    <td class="px-3 py-4 text-center border-r border-b">{{ tbproducto.garantia }}</td>
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

<!-- <script>
export default {
    props: {
        clientes: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            searchTerm: '',
            searchTermCodigoCli: '',
            filteredClientes: [],
            selectedIndex: -1,
            activeAccordion: [],
            form: {
                cliente_id: ''
            }
        };
    },
    methods: {
        onInput() {
            this.selectedIndex = -1;
            if (this.searchTerm.length > 0) {
                this.filteredClientes = this.clientes.filter(cliente =>
                    cliente.razonSocial.toLowerCase().includes(this.searchTerm.toLowerCase())
                );
            } else {
                this.filteredClientes = [];
            }
        },
        onKeydown(event) {
            if (this.filteredClientes.length > 0) {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    this.selectedIndex = (this.selectedIndex + 1) % this.filteredClientes.length;
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    this.selectedIndex = (this.selectedIndex - 1 + this.filteredClientes.length) % this.filteredClientes.length;
                } else if (event.key === 'Enter') {
                    event.preventDefault();
                    if (this.selectedIndex >= 0) {
                        this.selectCliente(this.filteredClientes[this.selectedIndex]);
                    }
                }
            }
        },
        updateSelection(index) {
            this.selectedIndex = index;
        },
        selectCliente(cliente) {
            this.searchTerm = cliente.razonSocial;
            this.searchTermCodigoCli = cliente.id;
            this.form.cliente_id = this.searchTermCodigoCli;
            this.filteredClientes = [];
        },
        toggleAccordion(index) {
            const indexPosition = this.activeAccordion.indexOf(index);
            if (indexPosition === -1) {
                this.activeAccordion.push(index);
            } else {
                this.activeAccordion.splice(indexPosition, 1);
            }
        },
        isActive(index) {
            return this.activeAccordion.includes(index);
        },
    },
};
</script> -->