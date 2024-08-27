<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import Swal from 'sweetalert2';
import { onMounted , watch , computed , ref , watchEffect } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";

import jsPDF from 'jspdf';
import 'jspdf-autotable';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';

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

const form = useForm ({
    cliente_id: '',
    numeroDocumento: '',
    direccion: '',
    tenor_id:'',
    fecha:'',
    moneda:'',
    validez_cot:'3 dias',
    forma_pago:'Al contado',
    dias_entrega:'Inmediata',
    tipoCambio:'',
    subtotal: 0,
    igv: 0,
    total: 0,
    igvEnabled: false,
    precioTotalEnabled: false,
    precioUnitarioEnabled: false,
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

// Función para recortar el valor a dos decimales
const recortarADosDecimales = (valor) => {
const numero = parseFloat(valor);
if (!isNaN(numero)) {
    return numero.toFixed(2);
}
return valor;
};

// Verificar y recortar el valor cuando cambia
watch(() => form.igv, (nuevoValor) => {
    form.igv = recortarADosDecimales(nuevoValor);
});

watch(() => form.total, (nuevoValor) => {
    form.total = recortarADosDecimales(nuevoValor);
});

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
    form.direccion = cliente.direccion;
    form.numeroDocumento = cliente.numeroDocumento;
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
            const cantidadCell = row.children[9]; // Asegúrate de que este índice es correcto
            let valorInput = 1;
            if (cantidadCell) {
                // Intenta seleccionar el <input> dentro de esa celda
                const input = cantidadCell.querySelector('input');

                // Verifica si input no es null
                if (input) {
                    // Obtén el valor del <input>
                    valorInput = input.value;
                    // console.log("Valor del input en la celda 10:", valorInput);
                } else {
                    // console.log('No se encontró el <input> en la celda 10.');
                }
            } else {
                // console.log('No se encontró la celda en la posición 10.');
            }
            if (importeCell && monedaCell) {
                if(moneda == "Soles"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "$") {
                            total += (value * parseFloat(tipoCambio)) * valorInput;
                        } else {
                            total += value * valorInput;
                        }
                    }
                }else if(moneda == "Dólares"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "s/") {
                            total += (value / parseFloat(tipoCambio)) * valorInput;
                        } else {
                            total += value * valorInput;
                        }
                    }
                }
            }
        });

        form.subtotal = parseFloat(total).toFixed(2);
        form.total = parseFloat(total).toFixed(2);

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
            const cantidadCell = row.children[9];
            const monedaPrimerProducto = monedaCell.textContent;
            if (tbproductosAgregados.value.length == 1){
                form.tipoCambio = "3.80";
                if (monedaPrimerProducto == "$"){
                    form.moneda = "dolares $"
                }else{
                    form.moneda = "soles s/"
                }
                form.moneda
            }else{
                console.log("aldskasdasd")
            }
            let valorInput = 1;
            if (cantidadCell) {
                // Intenta seleccionar el <input> dentro de esa celda
                const input = cantidadCell.querySelector('input');

                // Verifica si input no es null
                if (input) {
                    // Obtén el valor del <input>
                    valorInput = input.value;
                    // console.log("Valor del input en la celda 10:", valorInput);
                } else {
                    // console.log('No se encontró el <input> en la celda 10.');
                }
            } else {
                // console.log('No se encontró la celda en la posición 10.');
            }
            if (importeCell && monedaCell) {
                if(moneda == "Soles"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "$") {
                            total += (value * parseFloat(tipoCambio)) * valorInput;
                        } else {
                            total += value * valorInput;
                        }
                    }
                }else if(moneda == "Dólares"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "s/") {
                            total += (value / parseFloat(tipoCambio)) * valorInput;
                        } else {
                            total += value * valorInput;
                        }
                    }
                }
            }
        });

        form.subtotal = parseFloat(total).toFixed(2);
        form.total = parseFloat(total).toFixed(2);

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
    const moneda = document.getElementById('moneda').options[document.getElementById("moneda").selectedIndex].text;
    const tipoCambio = document.getElementById('tipoCambio').value === null ? 1 : document.getElementById('tipoCambio').value;
    
    let total = 0;
    const rows = document.querySelectorAll('tr'); // Selecciona todas las filas de la tabla

    rows.forEach(function(row) {
        const importeCell = row.querySelector('.inputImporte');
        const monedaCell = row.children[14]; // Supongo que la celda 15 es la columna 14 (index 0 basado)
        const cantidadCell = row.children[9]; // Asegúrate de que este índice es correcto
        let valorInput = 1;
        if (cantidadCell) {
            // Intenta seleccionar el <input> dentro de esa celda
            const input = cantidadCell.querySelector('input');

            // Verifica si input no es null
            if (input) {
                // Obtén el valor del <input>
                valorInput = input.value;
                // console.log("Valor del input en la celda 10:", valorInput);
            } else {
                // console.log('No se encontró el <input> en la celda 10.');
            }
        } else {
            // console.log('No se encontró la celda en la posición 10.');
        }
        if (importeCell && monedaCell) {
            if(moneda == "Soles"){
                const value = parseFloat(importeCell.textContent);
                const moneda = monedaCell.textContent.trim();

                if (!isNaN(value)) {
                    if (moneda === "$") {
                        total += (value * parseFloat(tipoCambio)) * valorInput;
                    } else {
                        total += value * valorInput;
                    }
                }
            }else if(moneda == "Dólares"){
                const value = parseFloat(importeCell.textContent);
                const moneda = monedaCell.textContent.trim();

                if (!isNaN(value)) {
                    if (moneda === "s/") {
                        total += (value / parseFloat(tipoCambio)) * valorInput;
                    } else {
                        total += value * valorInput;
                    }
                }
            }
        }
    });

    form.subtotal = parseFloat(total).toFixed(2);
    form.total = parseFloat(total).toFixed(2);

    document.getElementById('subtotal').value = total.toFixed(2);
    document.getElementById('total').value = total.toFixed(2);
});
 
watch(() => form.tipoCambio, (newValue) => {
    const moneda = document.getElementById('moneda').options[document.getElementById("moneda").selectedIndex].text;
    const tipoCambio = document.getElementById('tipoCambio').value === null ? 1 : document.getElementById('tipoCambio').value;
    
    let total = 0;
    const rows = document.querySelectorAll('tr'); // Selecciona todas las filas de la tabla

    rows.forEach(function(row) {
        const importeCell = row.querySelector('.inputImporte');
        const monedaCell = row.children[14]; // Supongo que la celda 15 es la columna 14 (index 0 basado)
        const cantidadCell = row.children[9]; // Asegúrate de que este índice es correcto
        let valorInput = 1;
        if (cantidadCell) {
            // Intenta seleccionar el <input> dentro de esa celda
            const input = cantidadCell.querySelector('input');

            // Verifica si input no es null
            if (input) {
                // Obtén el valor del <input>
                valorInput = input.value;
                // console.log("Valor del input en la celda 10:", valorInput);
            } else {
                // console.log('No se encontró el <input> en la celda 10.');
            }
        } else {
            // console.log('No se encontró la celda en la posición 10.');
        }
        if (importeCell && monedaCell) {
            if(moneda == "Soles"){
                const value = parseFloat(importeCell.textContent);
                const moneda = monedaCell.textContent.trim();

                if (!isNaN(value)) {
                    if (moneda === "$") {
                        total += (value * parseFloat(tipoCambio)) * valorInput;
                    } else {
                        total += value * valorInput;
                    }
                }
            }else if(moneda == "Dólares"){
                const value = parseFloat(importeCell.textContent);
                const moneda = monedaCell.textContent.trim();

                if (!isNaN(value)) {
                    if (moneda === "s/") {
                        total += (value / parseFloat(tipoCambio)) * valorInput;
                    } else {
                        total += value * valorInput;
                    }
                }
            }
        }
    });

    form.subtotal = parseFloat(total).toFixed(2);
    form.total = parseFloat(total).toFixed(2);

    document.getElementById('subtotal').value = total.toFixed(2);
    document.getElementById('total').value = total.toFixed(2);
});
 
onMounted(() => {
    const today = new Date().toISOString().split('T')[0];
    form.fecha = today;
});
 
// Función para recolectar los datos de la tabla
const recolectarDatosTabla = () => {
    const datosTabla = [];
    
    const rows = document.querySelectorAll('.relative table tbody tr');

    rows.forEach(row => {
        const capacidadesSeleccionadas = [];

        const subcategoria = row.querySelector('td:nth-child(1)')?.innerText.trim() || '';
        const modelo = row.querySelector('td:nth-child(2)')?.innerText.trim() || '';
        
        // Extraer especificaciones del acordeón
        const especificacionesElement = row.querySelector('td:nth-child(4) ul');
        const especificaciones = especificacionesElement ? 
            Array.from(especificacionesElement.querySelectorAll('li')).map(li => li.innerText.trim()).join('\n') 
            : '';

        const marca = row.querySelector('td:nth-child(5)')?.innerText.trim() || '';
        
        row.querySelectorAll('td:nth-child(6) input[type="checkbox"]').forEach(checkbox => {
            if (checkbox.checked) {
                capacidadesSeleccionadas.push(checkbox.value);
            }
        });

        const precioList = parseFloat(row.querySelector('td:nth-child(7)')?.innerText.trim().replace(/[^0-9.,]/g, '') || 0);
        const precioMin = parseFloat(row.querySelector('td:nth-child(8)')?.innerText.trim().replace(/[^0-9.,]/g, '') || 0);
        const precioMax = parseFloat(row.querySelector('td:nth-child(9)')?.innerText.trim().replace(/[^0-9.,]/g, '') || 0);
        const cantidad = parseFloat(row.querySelector('td:nth-child(10) input')?.value.trim() || 0);
        const importe = parseFloat(row.querySelector('td:nth-child(11)')?.innerText.trim().replace(/[^0-9.,]/g, '') || (precioMin * cantidad).toFixed(2));
        const garantia = row.querySelector('td:nth-child(12) select')?.value.trim() || 'Sin Garantia';
        const diasEntrega = row.querySelector('td:nth-child(13) select')?.value.trim() || '1 dia';
        const formaPago = row.querySelector('td:nth-child(14) select')?.value.trim() || 'Al contado';
        const moneda = row.querySelector('td:nth-child(15)')?.innerText.trim() || '';
        const foto = row.querySelector('td:nth-child(16)')?.innerText.trim() || '';
        const requerimientos = row.querySelector('td:nth-child(17)')?.innerText.trim() || 'Entrega En Planta';

        if (capacidadesSeleccionadas.length > 0) {
            datosTabla.push({
                subcategoria_id: subcategoria,
                modelo: modelo,
                especificaciones: especificaciones,
                marca: marca,
                capacidades: capacidadesSeleccionadas.join('\n'),
                precio_list: precioList.toFixed(2),
                precio_min: precioMin.toFixed(2),
                precio_max: precioMax.toFixed(2),
                cantidad: cantidad,
                importe: importe.toFixed(2),
                garantia: garantia,
                dias_entrega: diasEntrega,
                forma_pago: formaPago,
                moneda: moneda,
                foto: foto,
                requerimientos: requerimientos
            });
        }
    });

    return datosTabla;
};

const submitForm = async (event) => {
    if (event) {
        event.preventDefault();
    }

    $("#generate-quote-button").hide();
    $("#loading-button").show();
    try {
        // Desplegar todos los acordeones
        const accordions = document.querySelectorAll('.accordions dt');
        accordions.forEach((accordion, index) => {
            if (!accordion.classList.contains('active')) {
                accordion.click();
            }
        });

        setTimeout(() => {
            // Recolectar los datos de la tabla
            const datosTabla = recolectarDatosTabla();
            const nombreClienteError = form.cliente_id;

            if (!nombreClienteError) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                Toast.fire({
                    icon: 'error',
                    text: 'Debe seleccionar un cliente.'
                });
                $("#generate-quote-button").show();
                $("#loading-button").hide();
            }
            else if(datosTabla.length <= 0) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                Toast.fire({
                    icon: 'error',
                    text: 'Debe seleccionar al menos un Producto.'
                });
                $("#generate-quote-button").show();
                $("#loading-button").hide();
            }else{
                $.ajax({
                    url: '/fn_guardarCotizacion',
                    method: 'GET',
                    data: {
                        cliente_id: form.cliente_id,
                        numeroDocumento: form.numeroDocumento,
                        direccion: form.direccion,
                        tenor_id: form.tenor_id,
                        fecha: form.fecha,
                        moneda: form.moneda,
                        validez_cot: form.validez_cot,
                        forma_pago: form.forma_pago,
                        dias_entrega: form.dias_entrega,
                        tipoCambio: form.tipoCambio,
                        subtotal: form.subtotal,
                        igv: form.igv,
                        total: form.total,
                    },
                    success: function(response) {
                        if (response.success) {
                            $.ajax({
                                url: '/validarIdCot',
                                method: 'GET',
                                success: function(response) {
                                    const idCotizacion = response.id;
    
                                    datosTabla.forEach(producto => {
                                        producto.idCotizacion = idCotizacion;
                                    });
    
                                    axios.post(route('cventas.guardarProductosCotizacion'), {
                                        productos: datosTabla
                                    });
    
                                    Swal.fire({
                                        title: 'Cotización guardada',
                                        text: 'La cotización y los productos se han guardado exitosamente.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
    
                                    Inertia.visit(route('cventas.index'));
    
                                },
                                error: function(error) {
                                    console.error("ERROR AL EXTRAER ULTIMO ID ",error);
                                }
                            });
                        }
                    },
                    error: function(error) {
                        console.error("ERROR AL REGISTRAR COTIZACION ",error);
                    }
                });
            }

        }, 500);
    } catch (error) {
        console.error('Error al enviar el formulario:', error);

        Swal.fire({
            title: 'Error',
            text: 'Hubo un error al guardar la cotización.',
            icon: 'error',
            showConfirmButton: true
        });
    }
};

const { props } = usePage();
const user = props.auth.user;

const obtenerNombreCompleto = (user) => {
    if (user) {
        return `${user.name} ${user.apellidopat} ${user.apellidomat}`.toUpperCase();
    }
    return '';
};

const nombreCompleto = obtenerNombreCompleto(user);

const previewPDF2 = () => {
    const accordions = document.querySelectorAll('.accordions dt');
    accordions.forEach((accordion, index) => {
        if (!accordion.classList.contains('active')) {
            accordion.click();
        }
    });

    setTimeout(() => {
        const datosTabla = recolectarDatosTabla();
        const fechaEncabezadoCotizacion = new Date();
        const añoCotizacion = fechaEncabezadoCotizacion.getFullYear();
        const razonSocial = document.getElementById("cliente_id").value;
        const monedaTipoCambio = document.getElementById('moneda').options[document.getElementById("moneda").selectedIndex].text;
        const valorTipoCambio = document.getElementById("tipoCambio").value;

        const forma_pago = document.getElementById("forma_pago").options[document.getElementById("forma_pago").selectedIndex].text;
        const validez_cot = document.getElementById("validez_cot").options[document.getElementById("validez_cot").selectedIndex].text;
        const dias_entrega = document.getElementById("dias_entrega").options[document.getElementById("dias_entrega").selectedIndex].text;
        const direccion = document.getElementById("direccion").value;
        const ruc = document.getElementById("numeroDocumento").value;

        const numeroCotizacion = form.n_cotizacion
        const numeroCotizacionFormateado = numeroCotizacion.toString().padStart(8, '0');
        let simboloMoneda = "";

        if (monedaTipoCambio == "Soles") {
            simboloMoneda = 'S/';
        } else if (monedaTipoCambio == "Dólares") {
            simboloMoneda = 'US$';
        }

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

        // Función para formatear la moneda 
        function fn_formatearMonedaLocal(importe){
            const importeFormateado = parseFloat(importe).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
                useGrouping: true
            });

            return importeFormateado;
        }

        function convertirNumeroAPalabras(numero) {
            const unidades = ['cero', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
            const decenas = ['diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'];
            const decenasD = ['veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
            const centenas = ['cien', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

            function convertirParteEntera(numero) {
                if (numero < 10) return unidades[numero];
                else if (numero < 20) return decenas[numero - 10];
                else if (numero < 100) {
                    let unidad = numero % 10;
                    let decena = Math.floor(numero / 10);
                    return decenasD[decena - 2] + (unidad > 0 ? ` y ${unidades[unidad]}` : '');
                } else if (numero < 1000) {
                    let centena = Math.floor(numero / 100);
                    let resto = numero % 100;
                    if (centena === 1 && resto === 0) {
                        return 'cien';
                    } else {
                        return centenas[centena] + (resto > 0 ? ` ${convertirParteEntera(resto)}` : '');
                    }
                } else if (numero < 1000000) {
                    let miles = Math.floor(numero / 1000);
                    let resto = numero % 1000;
                    if (miles === 1) {
                        return `mil${resto > 0 ? ` ${convertirParteEntera(resto)}` : ''}`;
                    } else {
                        return `${convertirParteEntera(miles)} mil${resto > 0 ? ` ${convertirParteEntera(resto)}` : ''}`;
                    }
                } else {
                    return '';
                }
            }

            function convertirParteDecimal(numero) {
                return convertirParteEntera(numero);
            }

            const [parteEntera, parteDecimal] = numero.toString().split('.').map(Number);

            const palabrasEntero = convertirParteEntera(parteEntera);
            const palabrasDecimal = parseFloat(parteDecimal) != 0 && parteDecimal ? ` con ${convertirParteDecimal(parteDecimal)}` : "";
            const palabraMoneda = simboloMoneda === "S/" ? "Soles" : "Dólares Americanos";

            return `${palabrasEntero}${palabrasDecimal} ${palabraMoneda}`.trim();
        }

        const accordions = document.querySelectorAll('.accordions dt');
        accordions.forEach((accordion, index) => {
            if (accordion.classList.contains('active')) {
                accordion.click();
            }
        });

        const fechaFormateada = obtenerFechaFormateada();
    
        let subtotal = 0;
        datosTabla.forEach(product => {
            if(monedaTipoCambio == "Soles"){
                if (!isNaN(product.importe)) {
                    if (product.moneda === "$") {
                        product.importe = (product.importe * parseFloat(valorTipoCambio));
                    } else {
                        product.importe = product.importe;
                    }
                }
            }else if(monedaTipoCambio == "Dólares"){
                if (!isNaN(product.importe)) {
                    if (product.moneda === "s/") {
                        product.importe = (product.importe / parseFloat(valorTipoCambio));
                    } else {
                        product.importe = product.importe;
                    }
                }
            }
            product.subtotal = product.cantidad * product.importe;
            subtotal += product.subtotal;
        });
    
        let igv = subtotal * 0.18;
        let total = subtotal + igv;

        // ========== Inicia Función Dibujar Encabezado ==========

        function fn_dibujarEncabezado(texto){
            const anchoTexto = doc.getTextWidth(texto);
            const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
            doc.text(eje_x_left, eje_y, texto);
        }

        // ========== Termina Función Dibujar Encabezado ==========

        // ========== Inicia Función Dibujar Datos del Cliente ==========

        function fn_dibujarDatosClientes(inicioTabla){
            doc.autoTable({
                body: [
                    [
                        'Razón Social',
                        razonSocial
                    ],
                    [
                        'RUC',
                        ruc
                    ],
                    [
                        'Direccion',
                        direccion
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
                startY: inicioTabla,
                columnStyles: {
                    0: {
                        cellWidth: 40,
                        fontStyle: 'bold'
                    }
                },
            });
            
            doc.autoTable({
                body: [
                    [
                        'Moneda',
                        monedaTipoCambio,
                        'Asesor',
                        nombreCompleto
    
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
                        fontStyle: 'bold'
                    },
                    1: {
                        cellWidth: 55
                    },
                    2: {
                        cellWidth: 30,
                        fontStyle: 'bold'
                    }
                },
            });
        }

        // ========== Termina Función Dibujar Datos del Cliente ==========

        // ========== Inicia Función Dibujar Condiciones ==========

        function fn_dibujarCondiciones(){
            doc.autoTable({
                body: [
                    [
                        { content: 'CONDICIONES :', styles: { halign: 'left' , fontStyle: 'bold'} }
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
                startY: doc.lastAutoTable.finalY + 5
            });
    
            const lineYPosition = doc.lastAutoTable.finalY;
    
            doc.autoTable({
                body: [
                    [
                        { content: `Emitir una orden de compra a nombre de INDUSTRIAS BALINSA E.I.R.L con ruc: 20608165585\n\nNo se realizan cambios ni devoluciones\n\nOrden de compra irrevocable`, styles: { halign: 'left' , fontStyle: 'bold' } }
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
    
            const lineYPosition2 = doc.lastAutoTable.finalY;
    
            doc.autoTable({
                body: [
                    [
                        { content: `Los precios unitarios NO incluyen IGV`, styles: { halign: 'left' , fontStyle: 'bold', textColor: [255, 0, 0] } }
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
    
            const lineYPosition3 = doc.lastAutoTable.finalY;
    
            doc.autoTable({
                body: [
                    [
                        { content: `Validez de la Cotización     : ${validez_cot}\n\nForma de Pago                    : ${forma_pago}\n\nPlazo de Entrega                 : ${dias_entrega}\n\nAsistencia al correo de área de ventas industriasbalinsa@gmail.com\n\nTipo de Cambio                   : ${valorTipoCambio}`, styles: { halign: 'left' , fontStyle: 'bold'} }
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
            doc.line(10.1, lineYPosition3, doc.internal.pageSize.width - 10.1, lineYPosition3); 

            doc.autoTable({
                body: [
                    [
                        { content: 'Hacer deposito bancario a nombre de INDUSTRIAS BALINSA EIRL segun:', styles: { halign: 'left' , textColor: [ 0, 0, 0 ]} },
                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 9.5, 
                    cellPadding: 1,
                    lineWidth: 0.30,
                    lineColor: [255, 255, 255]
                },
                headStyles: { fillColor: [253, 202, 36], textColor: [0, 0, 0] },
                margin: {left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY + 5,
            });
        }

        // ========== Termina Función Dibujar Condiciones ==========

        // ========== Inicia Función Dibujar Productos ==========

        function fn_dibujarProductos(){
            doc.autoTable({
                head: [['Marca', 'Modelo', 'Capacidades', 'Especificaciones', 'Cantidad', 'Precio Unitario', 'SubTotal', 'Imagen']],
                body: [
                    ...datosTabla.map(product => {
                        let especificacionesList = product.especificaciones
                            .split('\n')
                            .map(line => `- ${line}`)
                            .join('\n');
                        
                        return [
                            product.marca,
                            product.modelo,
                            product.capacidades,
                            especificacionesList,
                            product.cantidad,
                            parseFloat(product.importe).toFixed(2),
                            parseFloat(product.subtotal).toFixed(2),
                            {
                                content: '', 
                                styles: { cellWidth: 20, minCellHeight: 20 },
                                image: product.foto
                            }
                        ];
                    }),
                ],
                rowPageBreak: 'avoid',
                didDrawCell: function (data) {
                    if (data.column.index === 7 && data.cell.section === 'body') {
                        if (data.row.index < datosTabla.length) {
                            const product = datosTabla[data.row.index];
                            if (product && product.foto) {
                                const imageUrl = "/productos_img/" + product.foto;

                                const containerWidth = 20;
                                const containerHeight = 20;

                                const reductionFactor = 0.8;

                                const imgWidth = containerWidth * reductionFactor;
                                const imgHeight = containerHeight * reductionFactor;

                                const x = data.cell.x + (containerWidth - imgWidth) / 2;
                                const y = data.cell.y + (containerHeight - imgHeight) / 2;

                                doc.addImage(imageUrl, 'JPEG', x, y, imgWidth, imgHeight);
                            }
                        }
                    }
                },
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 8, 
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                headStyles: { fillColor: [253, 202, 36], textColor: [0, 0, 0] },
                columnStyles: {
                    5: {
                        cellWidth: 25,
                    },
                    6: {
                        cellWidth: 25,
                    },
                    7: {
                        cellWidth: 20,
                    },
                },
                margin: {left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY + 5,
            });

            const anchoTablaTotal = 50
            const inicioTablaTotal = anchoPagina - anchoTablaTotal - 30;

            eje_y = doc.lastAutoTable.finalY + 5;
            doc.text(eje_x, eje_y, `SON :`);
            doc.setTextColor(255,0,0);
            const totalAPalabra = `${(convertirNumeroAPalabras(total.toFixed(2)).toUpperCase())}`;
            const totalAPalabraSplit = doc.splitTextToSize(totalAPalabra, 100);
            let comaIndex = totalAPalabraSplit.indexOf(',');
            if (comaIndex !== -1) {
                let primeraParte = totalAPalabraSplit.substring(0, comaIndex).trim();
                let segundaParte = totalAPalabraSplit.substring(comaIndex + 1).trim();
                doc.text(eje_x + 10, eje_y, primeraParte);
                doc.text(eje_x + 10, eje_y + 5, segundaParte);
            } else {
                doc.text(eje_x + 10, eje_y, totalAPalabraSplit);
            }
            doc.setTextColor(0,0,0);

            doc.autoTable({
                body: [
                [
                    { content: 'Sub Total', styles: { halign: 'right' , fontStyle: 'bold'} },
                    { content: `${fn_formatearMonedaLocal(subtotal)}`, styles: { halign: 'center' } }
                ],
                [
                    { content: 'IGV % 18', styles: { halign: 'right' , fontStyle: 'bold'} },
                    { content: `${fn_formatearMonedaLocal(igv)}`, styles: { halign: 'center' } }
                ],
                [
                    { content: `Total ${simboloMoneda}`, styles: { halign: 'right' , fontStyle: 'bold'} },
                    { content: `${fn_formatearMonedaLocal(total)}`, styles: { halign: 'center' } }
                ]
                ],
                startY: doc.lastAutoTable.finalY,
                margin: { left: inicioTablaTotal },
                columnStyles: {
                    0: { cellWidth: 25 },
                    1: { cellWidth: 25 },
                },
                styles: {
                    fontSize: 8, 
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                theme: 'grid'
            });
        }

        // ========== Termina Función Dibujar Productos ==========

        // ========== Inicia Función Dibujar Cuentas ==========

        function fn_dibujarCuentas(){
            doc.autoTable({
                head: [['Banco', 'Moneda', 'Tipo de Cuenta', 'Cuenta', 'Cuenta CCI']],
                body: [
                    [
                        { content: 'BCP', styles: { halign: 'left' , fontStyle: 'bold'} },
                        { content: 'SOLES', styles: { halign: 'left' , fontStyle: 'bold'} },
                        { content: 'AHORROS', styles: { halign: 'left' } },
                        { content: '4752156367062', styles: { halign: 'left' } },
                        { content: '00247500215636706225', styles: { halign: 'left' } },
                    ],
                    [
                        { content: 'BCP', styles: { halign: 'left' , fontStyle: 'bold'} },
                        { content: 'DOLARES', styles: { halign: 'left' , fontStyle: 'bold'} },
                        { content: 'AHORROS', styles: { halign: 'left' } },
                        { content: '4752156380104', styles: { halign: 'left' } },
                        { content: '00247500215638010428', styles: { halign: 'left' } },
                    ],
                    [
                        { content: 'BBVA', styles: { halign: 'left' , fontStyle: 'bold'} },
                        { content: 'SOLES', styles: { halign: 'left' , fontStyle: 'bold'} },
                        { content: 'AHORROS', styles: { halign: 'left' } },
                        { content: '0011 0267 0201320316', styles: { halign: 'left' } },
                        { content: '011 267 000201320316 27', styles: { halign: 'left' } },
                    ],
                    [
                        { content: 'BBVA', styles: { halign: 'left' , fontStyle: 'bold'} },
                        { content: 'DOLARES', styles: { halign: 'left' , fontStyle: 'bold'} },
                        { content: 'AHORROS', styles: { halign: 'left' } },
                        { content: '0011-0267-0201320324', styles: { halign: 'left' } },
                        { content: '01126700020132032421', styles: { halign: 'left' } },
                    ],
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 8, 
                    cellPadding: 2,
                    lineWidth: 0.30,
                    lineColor: [0, 0, 0]
                },
                headStyles: { fillColor: [253, 202, 36], textColor: [0, 0, 0] },
                margin: {left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY + 5,
            });

            doc.autoTable({
                body: [
                    [
                        { content: 'Posteriormente enviar ticket o certificado de deposito correspondiente a industriasbalinsa@gmail.com haciendo referencia al Nº de orden', styles: { halign: 'left' , textColor: [ 0, 0, 0 ]} },
                    ],
                    [
                        { content: 'Sin otro particular quedamos de ustedes a la espera de sus gratas ordenes.', styles: { halign: 'left' , textColor: [ 0, 0, 0 ]} },
                    ]
                ],
                rowPageBreak: 'avoid',
                theme: 'grid',
                styles: { 
                    fontSize: 8, 
                    cellPadding: 1,
                    lineWidth: 0.30,
                    lineColor: [255, 255, 255]
                },
                headStyles: { fillColor: [253, 202, 36], textColor: [0, 0, 0] },
                margin: {left: 10 , right: 10},
                startY: doc.lastAutoTable.finalY + 5,
            });
        }

        // ========== Termina Función Dibujar Cuentas ==========

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
        fn_dibujarEncabezado(`COTIZACION : N° ${añoCotizacion} - ${numeroCotizacionFormateado}`);
        doc.text(eje_x, eje_y, fechaFormateada);

        const inicioTabla = 50;

        fn_dibujarDatosClientes(inicioTabla);

        doc.setFontSize(8);
        doc.setFont('Helvetica', 'bold');
        fn_dibujarProductos();
        
        fn_dibujarCondiciones();

        doc.setFont('Helvetica', 'normal');
        fn_dibujarCuentas();

        // ========== Finaliza Construción de PDF ==========
    
        const blob = doc.output('blob');
        const url = URL.createObjectURL(blob);
        window.open(url);
    }, 300);
}

document.addEventListener('input', function(event) {
    const moneda = document.getElementById('moneda').options[document.getElementById("moneda").selectedIndex].text;
    const tipoCambio = document.getElementById('tipoCambio').value === null ? 1 : document.getElementById('tipoCambio').value;
    
    if (event.target.classList.contains('inputImporte')) {
        let total = 0;
        const rows = document.querySelectorAll('tr'); // Selecciona todas las filas de la tabla

        rows.forEach(function(row) {
            const importeCell = row.querySelector('.inputImporte');
            const monedaCell = row.children[14]; // Supongo que la celda 15 es la columna 14 (index 0 basado)
            const cantidadCell = row.children[9]; // Asegúrate de que este índice es correcto
            let valorInput = 1;
            if (cantidadCell) {
                // Intenta seleccionar el <input> dentro de esa celda
                const input = cantidadCell.querySelector('input');

                // Verifica si input no es null
                if (input) {
                    // Obtén el valor del <input>
                    valorInput = input.value;
                    // console.log("Valor del input en la celda 10:", valorInput);
                } else {
                    // console.log('No se encontró el <input> en la celda 10.');
                }
            } else {
                console.log('No se encontró la celda en la posición 10.');
            }
            if (importeCell && monedaCell) {
                if(moneda == "Soles"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "$") {
                            total += (value * parseFloat(tipoCambio))*valorInput;
                        } else {
                            total += value*valorInput;
                        }
                    }
                }else if(moneda == "Dólares"){
                    const value = parseFloat(importeCell.textContent);
                    const moneda = monedaCell.textContent.trim();
    
                    if (!isNaN(value)) {
                        if (moneda === "s/") {
                            total += (value / parseFloat(tipoCambio)) * valorInput;
                        } else {
                            total += value * valorInput;
                        }
                    }
                }
            }
        });

        form.subtotal = parseFloat(total).toFixed(2);
        form.total = parseFloat(total).toFixed(2);

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
        tbproducto.tbmarca.nombre.toLowerCase().includes(searchLowerCase) ||
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
                                <div class="hidden">
                                    <InputLabel for="direccion" value="Direccion"
                                        class="block text-xs font-medium text-black"/>
                                    <TextInput v-model="form.direccion" type="text" id="direccion"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.direccion" class="mt-2"></InputError>
                                </div>
                                <div class="hidden">
                                    <InputLabel for="numeroDocumento" value="numero Documento"
                                        class="block text-xs font-medium text-black"/>
                                    <TextInput v-model="form.numeroDocumento" type="text" id="numeroDocumento"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.numeroDocumento" class="mt-2"></InputError>
                                </div>
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
                                <div class="relative overflow-x-auto scroll-dataTableLEO shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2  max-h-80 overflow-y-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-black dark:text-white">
                                        <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-center border-b-2 border-white hidden">Categoria</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Foto</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Especificaciones</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Marca</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Capacidades</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">P. Proveedor</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Min.</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 hidden">Precio Max.</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Importe</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 hidden">Garantia</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap hidden">D. Entrega</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap hidden">F. Pago</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap hidden">Moneda</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap hidden">URL_IMG</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap hidden">Incluye</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Quitar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(tbproducto, i) in tbproductosAgregados" :key="i">
                                                <td class="px-6 py-3 text-center border-r border-b hidden">{{ tbproducto.tbsubcategoria.nombre }}</td>
                                                <td class="px-3 py-4 text-center border-r border-b">{{ tbproducto.modelo }}</td>
                                                <td class="px-4 py-3 text-center border-r border-b">
                                                    <img @click="openModal('/productos_img/' + tbproducto.foto)" :src="'/productos_img/' + tbproducto.foto" alt="Foto" class="w-10 h-10 cursor-pointer object-cover rounded-md">
                                                </td>
                                                <td class="px-6 py-4 text-center border-r border-b whitespace-nowrap">
                                                    <div class="accordions">
                                                        <dl>
                                                            <dt @click="toggleAccordion(i)" :class="{'cursor-pointer normal-case active': isActive(i), 'cursor-pointer normal-case': !isActive(i)}">
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
                                                    <div class="flex flex-col">
                                                        <label v-for="(capacidad, i) in tbproducto.capacidades.split('\n')" :key="i" class="flex items-center">
                                                            <input 
                                                                type="checkbox" 
                                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" 
                                                                :value="capacidad" 
                                                                :name="`capacidad-${tbproducto.modelo}-${i}`"
                                                                :checked="tbproducto.capacidades.split('\n').length === 1"
                                                            >
                                                            <span class="ml-2">{{ capacidad }}</span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-center border-r whitespace-nowrap border-b">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_list ? tbproducto.precio_list : '0').toFixed(2) }}</td>
                                                <td class="px-6 py-4 text-center border-r whitespace-nowrap border-b">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_min ? tbproducto.precio_min : '0').toFixed(2) }} </td>
                                                <td class="px-6 py-4 text-center border-r whitespace-nowrap border-b hidden">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_max ? tbproducto.precio_max : '0').toFixed(2) }} </td>
                                                <td class="px-3 py-3 text-center border-r whitespace-nowrap border-b"><input class="text-center dark:bg-gray-800 bg-white text-black border-white dark:border-gray-800 dark:text-white w-16" type="number" v-model="tbproducto.cantidad"></td>
                                                <td class="px-3 py-3 text-center border-r border-b inputImporte" contenteditable="true">{{ parseFloat(tbproducto.precio_min).toFixed(2) }}</td>
                                                <td class="px-3 py-4 text-center border-r border-b capitalize hidden">
                                                    <select v-model="tbproducto.garantia" class="form-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-32 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        <option v-for="(garantia, index) in garantias" :key="index" :value="garantia">
                                                            {{ garantia }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="px-6 py-3 text-left border-r border-b whitespace-nowrap forma-pago-cell hidden">
                                                    <select v-model="tbproducto.diasEntrega" class="form-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-24 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        <option v-for="(dias, i) in diasEntrega" :key="i" :value="dias">
                                                            {{ dias }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="px-6 py-3 text-left border-r border-b whitespace-nowrap forma-pago-cell hidden">
                                                    <select v-model="tbproducto.formaPago" class="form-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-28 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        <option v-for="(pago, i) in formaPago" :key="i" :value="pago">
                                                            {{ pago }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="px-3 py-4 text-center border-r border-b normal-case hidden">
                                                    <span class="placeholder-text uppercase">{{ tbproducto.moneda }}</span>
                                                </td>
                                                <td class="px-3 py-4 text-center border-r border-b normal-case hidden"> {{ tbproducto.foto }} </td>
                                                <td class="px-3 py-3 text-center font-bold border-r capitalize border-b text-sm whitespace-nowrap hidden" contenteditable="true">{{ tbproducto.requerimientos ? tbproducto.requerimientos : 'Entrega en Planta' }}</td>
                                                <td class="px-3 py-3 text-center border-r border-b">
                                                    <button @click.prevent="quitarProducto(i)" class="inline-flex bg-red-600 hover:bg-red-700 px-2 py-0.5 rounded-md">
                                                        <i class='bi bi-trash3 text-base text-white'></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- <div class="flex flex-wrap w-full gap-4 md:justify-end justify-start items-center md:pb-4 pb-2.5 pt-2">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="convertirPrecioEnabled" class="w-4 h-4 text-orange-500 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 dark:focus:ring-orange-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" v-model="convertirPrecioEnabled">
                                    <label for="convertirPrecioEnabled" class="text-base font-extrabold text-black dark:text-white whitespace-nowrap">Convertir Moneda</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input checked type="checkbox" id="precioUnitarioEnabled" class="w-4 h-4 text-yellow-500 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" v-model="precioUnitarioEnabled">
                                    <label for="precioUnitarioEnabled" class="text-base font-extrabold text-black dark:text-white whitespace-nowrap">Incluir Precio Unitario</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input checked type="checkbox" id="precioTotalCheckbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" v-model="precioTotalEnabled">
                                    <label for="precioTotalCheckbox" class="text-base font-extrabold text-black dark:text-white whitespace-nowrap">Incluir Precio Total</label>
                                </div>
                            </div> -->
                            <div class="grid grid-cols-1 gap-y-4 items-end sm:grid-cols-4 sm:gap-x-8 sm:py-2 py-1">
                                <div>
                                    <InputLabel for="validez_cot" class="block text-xs font-medium text-black dark:text-white">Validez de la Cotización</InputLabel>
                                    <select id="validez_cot" v-model="form.validez_cot" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="3 dias" selected>3 dias</option>
                                        <option value="7 dias">7 dias</option>
                                        <option value="15 dias">15 dias</option>
                                        <option value="Sin Vigencia">Sin Vigencia</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="forma_pago" class="block text-xs font-medium text-black dark:text-white">Forma de pago</InputLabel>
                                    <select id="forma_pago" v-model="form.forma_pago"required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="Al contado" selected>Al contado</option>
                                        <option value="Credito 15 dias">Credito 15 dias</option>
                                        <option value="Credito 30 dias">Credito 30 dias</option>
                                        <option value="Credito 60 dias">Credito 60 dias</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="dias_entrega" class="block text-xs font-medium text-black dark:text-white">Días de entrega</InputLabel>
                                    <select id="dias_entrega" v-model="form.dias_entrega" required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="Inmediata" selected>Inmediata</option>
                                        <option v-for="dia in 31" :key="dia" :value="dia">{{ dia }} día{{ dia > 1 ? 's' : '' }}</option>
                                    </select>
                                </div>
                                <div class="flex w-full">
                                    <div class="w-full">
                                        <InputLabel for="moneda" class="block text-xs font-medium text-gray-900">Moneda</InputLabel>
                                        <select id="moneda" v-model="form.moneda" required
                                                class="h-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-md">
                                            <option value="" disabled selected>Selecciona una moneda</option>
                                            <option value="soles s/">Soles</option>
                                            <option value="dolares $">Dólares</option>
                                        </select>
                                    </div>
                                    <div>
                                        <InputLabel class="text-xs" for="tipoCambio" :value="'tipo Cambio (' + (form.moneda === 'soles s/' ? 'S/' : '$') + '):'"></InputLabel>
                                        <input id="tipoCambio" v-model="form.tipoCambio" type="text" class="w-28 font-semibold text-center border-gray-400 dark:border-white border-[1.5px] text-white bg-amber-400 uppercase rounded-r-md h-10"/>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-y-4 items-end md:grid-cols-2 xl:grid-cols-4 sm:gap-x-8 sm:py-0 py-1">
                                
                                <div class="hidden">
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
                                    <!-- <button class="inline-block bg-green-700 text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center" @click.prevent="previewPDF">PREVISUALIZAR PDF</button> -->
                                    <button class="inline-block bg-green-700 text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center" @click.prevent="previewPDF2">PREVISUALIZAR PDF</button>
                                    <ButtonResponsive 
                                        id="generate-quote-button" 
                                        class="text-white uppercase text-xs bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center"
                                        @click="submitForm">
                                        Generar Cotizacion
                                    </ButtonResponsive>

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
                                    <Link :href="route('cventas.index')" class="inline-block bg-red-600 text-white font-bold py-2.5 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">
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
 
        <div v-if="modal3" class="fixed inset-0 overflow-y-auto z-1 pt-[110px] overflow-hidden bg-gray-200/40 flex justify-center items-center md:px-0 px-2" style="backdrop-filter: blur(2px);" @click.self="toggleModal3">
            <div class=" min-w-[calc(100vw-60px)] md:ml-[50px] md:px-6 max-h-[90%]">
                <div class="modal-content bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-2xl p-2">
                    <button @click="toggleModal3" class="close absolute top-0.5 right-0.5 p-2 text-gray-500 hover:text-gray-700">
                    &times;
                </button>
                <div class="px-4 flex justify-between py-2 dark:text-white">
                    <div class="items-start">
                        <h1 class="uppercase text-lg font-bold">Catálogo de productos</h1>
                        <p class="text-sm">Seleccione los productos a cotizar</p>
                    </div>
                    <div class="flex justify-end items-center">
                        <button @click="toggleModal3" class="inline-flex bg-red-600 hover:bg-red-700 px-2 py-0.5 rounded-md">
                            <i class='fa-solid fa-xmark text-xl text-white'></i>
                        </button>
                    </div>
                </div>
                <hr class="my-1 mx-2 border-gray-400 border-[1.5px]">
                <div class="py-0 px-2">
                    <div class="py-0">
                        <div class="flex lg:flex-row flex-col md:gap-4 gap-2.5 md:my-0 my-0 w-full py-0.5">
                            <div class="flex flex-col justify-end w-full mb-0.5">
                                <div class="flex flex-col md:flex-row md:items-center w-full md:h-9">
                                    <div class="h-10 text-sm flex items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-[#111B22] rounded-t-lg md:rounded-none md:rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max px-2">Filtrar por Categoria: </h4>
                                    </div>
                                    <select v-model="selectedCategoria" class="w-full h-10 outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-b-lg md:rounded-none md:rounded-r-lg">
                                        <option value="" disabled>Seleccione una Categoría</option>
                                        <option v-for="tbcategoria in tbcategorias" :key="tbcategoria.id" :value="tbcategoria.id">{{ tbcategoria.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex flex-col justify-end w-full mb-0.5">
                                <div class="flex flex-col md:flex-row md:items-center w-full md:h-9">
                                    <div class="h-10 text-sm flex items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-[#111B22] rounded-t-lg md:rounded-none md:rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max px-2">Filtrar por Subcategoria: </h4>
                                    </div>
                                    <select v-model="selectedSubcategoria" class="w-full h-10 outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-b-lg md:rounded-none md:rounded-r-lg">
                                        <option value="" disabled>Seleccione una Subcategoría</option>
                                        <option v-for="tbsubcategoria in filteredSubcategorias" :key="tbsubcategoria.id" :value="tbsubcategoria.id">{{ tbsubcategoria.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex flex-col justify-end w-full mt-1">
                                <div class="flex w-full">
                                    <span
                                        class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                        <i class="fa-solid fa-cart-shopping text-md"></i>
                                    </span>
                                    <input v-model="searchQuery" type="text" id="table-search"
                                        class="lg:max-w-xs w-full h-10 outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-primary-600 focus:border-primary-600 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        autocomplete="off"
                                        placeholder="Buscar por Productos">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-3 px-2">
                    <div class="relative overflow-x-auto scroll-dataTableLEO shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 max-h-80 overflow-y-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-black dark:text-white">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white hidden">Categoria</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Modelo</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Foto</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Especificaciones</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Marca</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">Capacidades</th>
                                    <th scope="col" class="px-6 py-3 text-center border-b-2 border-white">P. Proveedor</th>
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
 
        <div class="fixed inset-0 overflow-y-auto z-50 bg-gray-200/40" v-if="modalOpen" @click="modalOpen = false">
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
            formaPago: ['15 dias', '30 dias', '60 dias', 'Al contado'],
            diasEntrega: ['1 dia', '2 dias', '3 dias', '4 dias', '5 dias'],
            garantias: ['3 meses', '6 meses', '1 año', 'Sin Garantia'],
            tbproductosAgregados: [],
            selectedFormaPago: '15 dias' // Valor predeterminado para formaPago
        };
    },
    methods: {
        async mounted() {
            // Obtener tbproductosAgregados y asignar las garantías y formas de pago seleccionadas
            this.tbproductosAgregados = await this.fetchTbproductos();

            // Asegurar que cada producto tenga un valor por defecto para formaPago si no está definido
            this.tbproductosAgregados.forEach(producto => {
                if (!producto.formaPago) {
                    producto.formaPago = this.formaPago[0]; // Establecer el primer valor de formaPago como predeterminado
                }
            });
        }
    }
}
</script>