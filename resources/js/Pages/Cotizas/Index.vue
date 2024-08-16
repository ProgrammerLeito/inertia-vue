<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue'
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";
import jsPDF from 'jspdf';
import 'jspdf-autotable';
 
const props = defineProps({
    cventas: {
        type: Object,
        required: true
    },
    clientes: {
        type: Object,
        required: true
    }
});

const searchQuery = ref('');

// Computed property for filtered cventas
const filteredCVentas = computed(() => {
    if (!searchQuery.value) return props.cventas.data; // Access cventas via props

    const query = searchQuery.value.toLowerCase().trim();

    return props.cventas.data.filter(cventa => { // Access cventas data via props
        const nCotizacionMatch = cventa.n_cotizacion.toLowerCase().includes(query);
        const clienteMatch = cventa.cliente && cventa.cliente.razonSocial.toLowerCase().includes(query);
        const tecnicoMatch = cventa.tecnico.toLowerCase().includes(query);
        const estadoMatch = cventa.estado.toLowerCase().includes(query);

        return nCotizacionMatch || clienteMatch || tecnicoMatch || estadoMatch;
    });
});

const form = useForm({
    id: ''
})
 
const deleteCotizacion = (id, cliente_id, cventa) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    // Verificar si cventa y cventa.cliente están definidos antes de acceder a razonSocial
    const razonSocial = cventa && cventa.cliente ? cventa.cliente.razonSocial : 'Cliente desconocido';
    
    alerta.fire({
        title: `¿Estás seguro de eliminar la cotización de ${razonSocial}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('cventas.destroy', id), {
                onSuccess: () => {
                    alerta.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Cotización eliminada exitosamente',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                },
                onError: () => {
                    alerta.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al eliminar la cotización',
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
 
const openCtgModal = async (cventa) => {
    const modalTitle = `Cotizacion del cliente: ${cventa.cliente ? cventa.cliente.razonSocial : 'Sin cliente'}`;
 
    const options = {
        title: modalTitle,
        input: 'select',
        inputOptions: {
            'Por Enviar': 'Por Enviar',
            // 'Enviado': 'Enviado',
            'Aceptado': 'Aceptado',
            'Rechazado': 'Rechazado',
            // 'Finalizado': 'Finalizado',
        },
        customClass: {
            title: 'text-2xl font-bold tracking-widest ',
            input: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
        inputPlaceholder: 'Selecciona una opcion',
        showCancelButton: true,
        confirmButtonColor: '#009846',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Asignar',
        showLoaderOnConfirm: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debes seleccionar un tipo de estado de envio';
            }
        },
        preConfirm: async (value) => {
            const response = await Inertia.post(route('cventas.cambiar_estado'), { cventa_id: cventa.id, estado: value });
            if (response && response.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Calificación asignada exitosamente',
                    timer: 1000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            } else {
                throw new Error(response ? response.statusText : 'Error desconocido');
            }
        }
    };
 
    const result = await Swal.fire(options);
};
 
const openModal = (cventa) => {
    if (cventa) {
        Swal.fire({
            title: 'DETALLES DE LA COTIZACION POR VENTA',
            width: 800,
            html: `<hr/><br/>
            <div style="text-align: left;" class="text-justify p-1 uppercase">
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>N° COTIZACION</strong>: ${cventa.n_cotizacion}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>ESTADO</strong>: ${cventa.estado}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>CLIENTE</strong>: ${cventa.cliente ? cventa.cliente.razonSocial : 'Sin cliente'} </p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>FECHA REGISTRO</strong>: ${cventa.fecha}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1"><strong>REGISTRADO POR</strong>: ${cventa.tecnico}</p>
            </div><br/><hr/>
            `,
            confirmButtonText: 'Cerrar',
            customClass: {
                title: 'text-sm sm:text-xs lg:text-2xl  xl:text-2xl font-extrabold bg-green-600  overflow-hidden text-white rounded-t-md  py-3 tracking-widest ',
                content: 'text-xs sm:text-xs lg:text-base xl:text-base  tracking-widest ',
                confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
            },
        });
    }
};
 
const formPage = useForm({});
const onPageClick = (event) => {
    formPage.get(route('cventas.index', { page: event }));
}
const previousPage = () => {
    const prevPage = props.cventas.current_page - 1;
    formPage.get(route('cventas.index', { page: prevPage }));
};

const nextPage = () => {
    const nextPage = props.cventas.current_page + 1;
    formPage.get(route('cventas.index', { page: nextPage }));
};

const goToPage = (page) => {
    formPage.get(route('cventas.index', { page }));
};

const total_pages = props.cventas.last_page;
const current_page = props.cventas.current_page;
const countPerPage = props.cventas.data.length;
const totalCount = props.cventas.total;

const displayedPages = () => {
    if (total_pages <= 3) {
        return Array.from({ length: total_pages }, (_, i) => i + 1);
    } else if (current_page <= 2) {
        return [1, 2, '...', total_pages];
    } else if (current_page > total_pages - 3) {
        return [1, '...', total_pages - 3, total_pages - 2, total_pages - 1, total_pages];
    } else {
        return [1, '...', current_page - 1, current_page, current_page + 1, total_pages];
    }
};


let timerInterval;

$(document).on('dblclick', ".previsualizarPfd", function(event) {
    
    Swal.fire({
        title: '¡Atención!',
        html: 'La cotización se esta generando, espere un momento.',
        timer: 999999999, // Establece un valor grande para que parezca indefinido
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    })
    const $this = $(this);
    const cventaId = $this.data('id');
    const tbagregadoId = $this.data('id');

    // Verificar si la solicitud ya ha sido realizada
    if ($this.data('clicked')) {
        // console.log('La solicitud ya ha sido realizada para este elemento.');
        return; // Salir de la función si la solicitud ya se ha realizado
    }

    // Marcar el elemento como clickeado
    $this.data('clicked', true);

    // Crear promesas para las consultas
    const consultaDatosCot = axios.post('/consultarDatosCot', { id: cventaId }, {
        headers: {
            'X-Inertia': false // Esto indica que no estás esperando una respuesta de Inertia.
        }
    });

    const consultaDatosProductosCot = axios.post('/consultarDatosProductosCot', { id: tbagregadoId }, {
        headers: {
            'X-Inertia': false // Esto indica que no estás esperando una respuesta de Inertia.
        }
    });

    // Ejecutar ambas consultas en paralelo
    Promise.all([consultaDatosCot, consultaDatosProductosCot])
        .then(responses => {
            // Respuestas de las consultas
            const datosCotizacion = responses[0].data;
            const datosProductosAgregados = responses[1].data;

            const arregloCotizacion = datosCotizacion.cventas
            const arregloProductosAgregados = datosProductosAgregados.tbproductos_agregados

            const razonSocial = arregloCotizacion.razonSocial;
            const ruc = arregloCotizacion.numeroDocumento;
            const direccion = arregloCotizacion.direccion;
            const numeroCotizacion = arregloCotizacion.n_cotizacion;
            const nombreCompleto = arregloCotizacion.tecnico;
            const valorTipoCambio = arregloCotizacion.tipoCambio;
            const forma_pago = arregloCotizacion.forma_pago;
            const validez_cot = arregloCotizacion.validez_cot;
            const dias_entrega = arregloCotizacion.dias_entrega;
            let monedaTipoCambio = arregloCotizacion.moneda;
            if (monedaTipoCambio == "soles s/") {
                monedaTipoCambio = 'Soles';
            } else if (monedaTipoCambio == "dolares $") {
                monedaTipoCambio = 'Dólares';
            }
            let simboloMoneda = "";
            if (monedaTipoCambio == "Soles") {
                simboloMoneda = 'S/';
            } else if (monedaTipoCambio == "Dólares") {
                simboloMoneda = 'US$';
            }
            const numeroCotizacionFormateado = numeroCotizacion.toString().padStart(8, '0');

            const fechaEncabezadoCotizacion = new Date();
            const añoCotizacion = fechaEncabezadoCotizacion.getFullYear();
            const datosTabla = arregloProductosAgregados;

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
                const fechaTexto = arregloCotizacion.fecha;
                // Dividir la cadena de fecha en componentes
                const [year, month, day] = fechaTexto.split('-');
                // Crear un objeto Date con los componentes separados
                const fecha = new Date(year, month - 1, day); // Restar 1 al mes porque los meses en Date comienzan desde 0
                // console.log("fecha", fecha);

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
                            { content: `Validez de la Cotización     : ${validez_cot}\n\nForma de Pago                    : ${forma_pago}\n\nPlazo de Entrega                 : ${dias_entrega} ${parseInt(dias_entrega) == 1 ? "dia" : dias_entrega == "Inmediata" ? "" : "dias"}\n\nAsistencia al correo de área de ventas industriasbalinsa@gmail.com\n\nTipo de Cambio                   : ${valorTipoCambio}`, styles: { halign: 'left' , fontStyle: 'bold'} }
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

            clearInterval(timerInterval);
            Swal.close();
            const blob = doc.output('blob');
            const url = URL.createObjectURL(blob);
            window.open(url);
        })
        .catch(error => {
            console.error('Error al consultar datos:', error);
        })
        .finally(() => {
            // Restablecer el estado después de un breve periodo de tiempo
            setTimeout(() => $this.data('clicked', false), 1000); // Ajusta el tiempo según sea necesario
        });
});

</script>
 
<template>
    <AppLayout title="Cotizaciones por Venta">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Cotizaciones</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between" v-if="$page.props.user.permissions.includes('Registrar Cotizacion')">
                        <Link :href="route('cventas.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-folder-plus mx-1"></i> Registrar cotizacion
                        </Link>
                    </div>
                    <div>
                        <div class="py-1">
                            <div class="flex lg:flex-row flex-col md:gap-4 gap-3 md:my-0 my-2 w-full py-1.5">
                                <div class="flex flex-col justify-end w-full">
                                    <div class="flex w-full">
                                        <span
                                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                            <i class="fa-solid fa-file-lines text-base"></i>
                                        </span>
                                        <input v-model="searchQuery" type="text" id="table-search"
                                            class="md:max-w-xs w-full outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-primary-600 focus:border-primary-600 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 placeholder-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            autocomplete="off"
                                            placeholder="Buscar campos de la cotización">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="relative overflow-x-auto scroll-dataTableLEO shadow-md md:rounded-lg rounded-md shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 hidden">C | Factura</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cliente</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Referencia</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Emision</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Neto</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Total</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Asesor</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-xs">
                                    <tr v-for="(cventa, index) in filteredCVentas" :key="cventa.id" :data-id="cventa.id" class="bg-white previsualizarPfd text-black border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-300 cursor-pointer">
                                        <td class="px-1 py-4 text-center hidden">{{ cventa.id }}</td>
                                        <td class="px-1 py-4 text-center">{{ cventa.n_cotizacion }}</td>
                                        <td class="px-1 py-4 text-center fa-fade font-semibold hidden">s|codigo</td>
                                        <td class="px-1 py-4 text-center">
                                            {{ cventa.cliente ? cventa.cliente.razonSocial : 'Sin cliente' }}
                                        </td>
                                        <!-- <td class="px-6 py-4 text-center">{{ cventa.tenor ? cventa.tenor.name : 'Sin codigo' }}
                                        </td> -->
                                        <td class="px-6 py-4 text-center whitespace-nowrap">{{ cventa.fecha }}</td>
                                        <!-- <td class="px-6 py-4 text-center">{{ cventa.moneda == "dolares $" ? "$" : "S/" }} {{ cventa.subtotal }}</td> -->
                                        <td class="px-6 py-4 text-center whitespace-nowrap">{{ cventa.moneda == "dolares $" ? "$" : "S/" }} {{ parseFloat(cventa.total * 1.18).toFixed(2) }}</td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">{{ cventa.tecnico }}</td>
                                        <td class="px-6 py-4 text-center text-white">
                                            <div :class="{
                                                'bg-blue-600': cventa.estado === 'Por Enviar',
                                                // 'bg-yellow-600': cventa.estado === 'Enviado',
                                                'bg-green-600': cventa.estado === 'Aceptado',
                                                'bg-red-600': cventa.estado === 'Rechazado',
                                                // 'bg-indigo-600': cventa.estado === 'Finalizado',
                                            }" class="inline-block px-2 py-1 rounded">
                                                <b>{{ cventa.estado }}</b>
                                            </div>
                                        </td>
                                        <td class="py-4 text-center whitespace-nowrap">
                                            <!-- <button @click="openCtgModal(cventa)"
                                                class="text-center mx-1 text-white bg-blue-500 hover:bg-blue-600 py-1 px-2 dark:hover:bg-white dark:hover:text-blue-600 rounded-md"><i
                                                    class="fas fa-star"></i>
                                            </button> -->
                                            <button @click="openCtgModal(cventa)" class="inline-flex mx-1 items-center justify-center bg-amber-400 hover:bg-amber-500 px-1.5 py-0.5 rounded-md">
                                                <i class='fas fa-star text-sm text-white'></i>
                                            </button>
                                            <button v-if="$page.props.user.permissions.includes('Acciones Cotizacion')" @click="$event => deleteCotizacion(cventa.id, cventa.cliente_id, cventa)" class="inline-flex mx-1 items-center justify-center bg-red-600 hover:bg-red-700 px-[7px] py-[3px] rounded-md">
                                                <i class='bi bi-trash3 text-sm text-white'></i>
                                            </button>
                                            <button @click="openModal(cventa)" class="inline-flex mx-1 items-center justify-center bg-blue-600 hover:bg-blue-700 px-[7px] py-[3px] rounded-md">
                                                <i class='bi bi-eye text-sm text-white'></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="filteredCVentas.length === 0" class="text-center py-2 dark:text-white">
                                No se encontraron datos.
                            </div>
                        </div>
                        <div class="flex flex-wrap justify-between">
                            <!-- <div class="mt-4 text-star">
                                <p class="text-gray-700 dark:text-white">Registros por página: {{ countPerPage }}
                                    Total de registros: {{ totalCount }}</p>
                            </div> -->
                            <div class="flex mt-4 text-end justify-center md:justify-end w-full">
                                <nav aria-label="Page navigation example mt-4">
                                    <ul class="inline-flex -space-x-px text-sm">
                                        <li>
                                            <button @click="previousPage" :disabled="!cventas.prev_page_url"
                                                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                Prev
                                            </button>
                                        </li>
                                        <li v-for="page in displayedPages()" :key="page">
                                            <button v-if="page !== '...'" @click="goToPage(page)"
                                                :class="{ 'text-blue-600 border-blue-300 dark:text-gray-800 bg-blue-50 hover:bg-blue-100 hover:text-blue-700': page === current_page, 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': page !== current_page }"
                                                class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                {{ page }}
                                            </button>
                                            <span v-else class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">...</span>
                                        </li>
                                        <li>
                                            <button @click="nextPage" :disabled="!cventas.next_page_url"
                                                class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                                Next
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>