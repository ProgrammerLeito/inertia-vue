<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { defineProps , onMounted, ref, watch, watchEffect, computed } from 'vue';
import Swal from 'sweetalert2';

const tbproductosAgregados = ref([]);

const tbproductosCombinados = computed(() => {
    return [
        ...tbproductosAgregados.value,
        ...props.tbproductosEditAgregados,
    ];
});


const props = defineProps({
    cventa: {
        type: Object,
        required: true
    },
    clientes: {
        type: Object,
        required: true
    },
    tbproductos: {
        type: Object
    },
    tbcategorias:{
        type: Object
    },
    tbmarcas: {
        type: Object
    },
    tbproductosEditAgregados :{
        type: Array,
        required: true
    }

});

const form = useForm({
    cliente_id: props.cventa.cliente_id,
    tenor_id: props.cventa.tenor_id,
    fecha: props.cventa.fecha,
    moneda: props.cventa.moneda,
    garantia: props.cventa.garantia,
    forma_pago: props.cventa.forma_pago,
    dias_entrega: props.cventa.dias_entrega,
    subtotal: props.cventa.subtotal,
    igv: props.cventa.igv,
    tipoCambio: props.cventa.tipoCambio,
    total: props.cventa.total
});

const searchTerm = ref('');
const searchTermCodigoCli = ref('');
const filteredClientes = ref([]);
const selectedIndex = ref(-1);
const activeAccordion = ref([]);
const modal3 = ref(false);
const searchQuery = ref('');
const selectedCategoria = ref('');
const selectedSubcategoria = ref('');
const filteredProductos = ref([]);
const modalImageUrl = ref('');
const modalOpen = ref(false);
 
const openModal = (imageUrl) => {
    modalImageUrl.value = imageUrl;
    modalOpen.value = true;
};

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

const toggleModal3 = () => {
    modal3.value = !modal3.value;
};

const agregarProducto = (producto) => {
    producto.cantidad = 1;
    tbproductosCombinados.value.push(producto);
    console.log(tbproductosCombinados);
    form.subtotal = tbproductosCombinados.value.reduce((acc, curr) => acc + (curr.precio_min * curr.cantidad), 0);
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

        document.getElementById('subtotal').value = total.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
        
    }, 300);
};

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

    console.log("Cliente seleccionado:", form.cliente_id);
};

// Método para quitar un producto
const quitarProducto = (index) => {
    tbproductosCombinados.value.splice(index, 1);
    form.subtotal = tbproductosCombinados.value.reduce((acc, curr) => acc + (curr.precio_min * curr.cantidad), 0);
    form.igv = form.subtotal * 0.18;
    form.total = form.subtotal + form.igv;
};

// Método para enviar el formulario
const submitForm = () => {
    // Aquí enviarías los datos del formulario al servidor para actualizar la cotización
};

onMounted(() => {
    const cliente = props.clientes.find(cliente => cliente.id === props.cventa.cliente_id);
    if (cliente) {
        searchTerm.value = cliente.razonSocial;
        searchTermCodigoCli.value = cliente.id;
        form.cliente_id = cliente.id;
        filteredClientes.value = []; // No mostrar la lista de opciones
    }
});

const isActive = (index) => {
    return activeAccordion.value.includes(index);
};

const toggleAccordion = (index) => {
    const indexPosition = activeAccordion.value.indexOf(index);
    if (indexPosition === -1) {
        activeAccordion.value.push(index);
    } else {
        activeAccordion.value.splice(indexPosition, 1);
    }
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

                // console.log('Tipo de cambio ingresado:', tipoCambio);
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

                // console.log('Tipo de cambio ingresado:', tipoCambio);
            } else {
                form.moneda = '';
            }
        });
    }
});

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

        form.subtotal = total
        form.total = total

        document.getElementById('subtotal').value = total.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
    }
});

// Watcher para actualizar el subtotal, el IGV y el total cuando se cambia la cantidad de productos
watch(tbproductosCombinados, (newProductos) => {
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

        document.getElementById('subtotal').value = total.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
        
    }, 50);
}, { deep: true });

</script>

<template>
    <AppLayout title="Editar Cotización por Venta">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Editar Cotización por Venta</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-xs shadow-lg bg-white dark:bg-gray-800 rounded-lg">
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
                            <div class="py-5">
                                <div class="relative overflow-x-auto scroll-dataTableLEO shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2  max-h-80 overflow-y-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
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
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Max.</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Importe</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Garantia</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap">D. Entrega</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap">F. Pago</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap hidden">Moneda</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 whitespace-nowrap">Incluye</th>
                                                <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Quitar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(tbproducto, i) in tbproductosCombinados" :key="i">
                                                <td class="px-3 py-4 text-center hidden text-gray-900 dark:text-white">{{ tbproducto.subcategoria_id ? tbproducto.subcategoria_id : tbproducto.tbsubcategoria.nombre }}</td>
                                                <td class="px-3 py-4 text-center text-gray-900 dark:text-white">{{ tbproducto.modelo }}</td>
                                                <td class="px-4 py-3 text-center text-gray-900 dark:text-white">
                                                    <img @click="openModal('/productos_img/' + tbproducto.foto)" :src="'/productos_img/' + tbproducto.foto" alt="Foto" class="w-10 h-10 cursor-pointer object-cover rounded-md">
                                                </td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap text-gray-900 dark:text-white">
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
                                                <td class="px-3 py-3 text-center text-gray-900 dark:text-white">{{ tbproducto.marca ? tbproducto.marca : tbproducto.tbmarca.nombre }}</td>
                                                <td class="px-6 py-3 text-left whitespace-nowrap text-gray-900 dark:text-white" contenteditable="true">
                                                    <div class="flex flex-col">
                                                        <label v-for="(capacidad, i) in tbproducto.capacidades.split('\n')" :key="i" class="flex items-center">
                                                            <input 
                                                                type="checkbox" 
                                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" 
                                                                :value="capacidad" 
                                                                ref="capacidadCheckboxes"
                                                                :checked="tbproducto.capacidades.split('\n').length === 1"
                                                            >
                                                            <span class="ml-2">{{ capacidad }}</span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap text-gray-900 dark:text-white">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_list ? tbproducto.precio_list : '0').toFixed(2) }}</td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap text-gray-900 dark:text-white">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_min ? tbproducto.precio_min : '0').toFixed(2) }} </td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap text-gray-900 dark:text-white">{{ tbproducto.moneda }} {{ parseFloat(tbproducto.precio_max ? tbproducto.precio_max : '0').toFixed(2) }} </td>
                                                <td class="px-3 py-3 text-center whitespace-nowrap text-gray-900 dark:text-white"><input class="text-center dark:bg-gray-800 bg-white text-black border-white dark:border-gray-800 dark:text-white w-16" type="number" v-model="tbproducto.cantidad"></td>
                                                <td class="px-3 py-3 text-center text-gray-900 dark:text-white inputImporte" contenteditable="true">{{ parseFloat(tbproducto.importe).toFixed(2) ? tbproducto.precio_min : tbproducto.importe}}</td>
                                                <td class="px-3 py-4 text-center capitalize">
                                                    <select v-model="tbproducto.garantia" class="form-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-32 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        <option v-for="(garantia, index) in garantias" :key="index" :value="garantia">
                                                            {{ garantia }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="px-6 py-3 text-left whitespace-nowrap forma-pago-cell">
                                                    <select v-model="tbproducto.dias_entrega" class="form-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-24 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        <option v-for="(dias, i) in diasEntrega" :key="i" :value="dias">
                                                            {{ dias }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="px-6 py-3 text-left whitespace-nowrap forma-pago-cell">
                                                    <select v-model="tbproducto.forma_pago" class="form-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-28 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        <option v-for="(pago, i) in formaPago" :key="i" :value="pago">
                                                            {{ pago }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="px-3 py-4 text-center normal-case hidden">
                                                    <span class="placeholder-text uppercase">{{ tbproducto.moneda }}</span>
                                                </td>
                                                <td class="px-3 py-4 text-center normal-case hidden"> {{ tbproducto.foto }} </td>
                                                <td class="px-3 py-3 text-center font-bold capitalize text-sm whitespace-nowrap" contenteditable="true">{{ tbproducto.requerimientos ? tbproducto.requerimientos : 'Entrega en Planta' }}</td>
                                                <td class="px-3 py-3 text-center"><button @click.prevent="quitarProducto(i)"><i class="bi bi-trash3 text-red-500"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="flex w-full gap-4 md:justify-end justify-start items-center md:pb-4 pb-2.5 pt-2">
                                <input type="checkbox" id="convertirPrecioEnabled" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" v-model="convertirPrecioEnabled">
                                <label for="convertirPrecioEnabled" class="text-base font-extrabold text-black dark:text-white">Convertir Precio</label>
                                <input checked type="checkbox" id="precioUnitarioEnabled" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" v-model="precioUnitarioEnabled">
                                <label for="precioUnitarioEnabled" class="text-base font-extrabold text-black dark:text-white">Incluir Precio Unitario</label>
                                <input checked type="checkbox" id="precioTotalCheckbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" v-model="precioTotalEnabled">
                                <label for="precioTotalCheckbox" class="text-base font-extrabold text-black dark:text-white">Incluir Precio Total</label>
                            </div>
                            <div class="grid grid-cols-1 gap-y-4 items-end sm:grid-cols-4 sm:gap-x-8 sm:py-0 py-1">
                                <div class="flex">
                                    <div>
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
                                        <input id="tipoCambio" v-model="form.tipoCambio" type="number" class="w-28 font-semibold text-center text-white bg-blue-600 uppercase rounded-r-md h-10" disabled/>
                                    </div>
                                </div>
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
                            <div class="flex justify-end mt-4">
                                <Button type="submit" class="bg-green-600 py-2 px-4 text-white uppercase text-xs font-semibold rounded-md hover:bg-green-700 mr-2">Actualizar</Button>
                                <Link :href="route('cventas.index')" class="text-white uppercase ml-1 bg-red-700 hover:bg-red-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                    Cancelar
                                </Link>
                            </div>
                        </form>
                        <portal-target name="modals"></portal-target>
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
                                    <!-- {{ tbcategorias }} -->
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
            <div class="flex justify-center  items-center h-full"  @click="modalOpen = false">
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
            tbproductosCombinados: [],
            selectedFormaPago: '15 dias' // Valor predeterminado para formaPago
        };
    },
    methods: {
        getSelectedCapacidades() {
            const checkboxes = this.$refs.capacidadCheckboxes;
            const selectedCapacidades = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);
            return selectedCapacidades;
        },
        async mounted() {
            // Obtener tbproductosAgregados y asignar las garantías y formas de pago seleccionadas
            this.tbproductosCombinados = await this.fetchTbproductos();

            // Asegurar que cada producto tenga un valor por defecto para formaPago si no está definido
            this.tbproductosCombinados.forEach(producto => {
                if (!producto.formaPago) {
                    producto.formaPago = this.formaPago[0]; // Establecer el primer valor de formaPago como predeterminado
                }
            });
        }
    }
}
</script>