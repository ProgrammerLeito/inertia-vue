<script>
export default {
    name: 'ProductosIndex',
    data() {
        return {
            searchQuery: '',
            modalOpen: false, // Estado del modal
            modalImageUrl: '', // URL de la imagen para mostrar en el modal
            nombreCategoria: '',
        };
    },
    computed: {
        filteredProductos() {
            // Filtrar productos según la búsqueda
            return this.productos.data.filter(producto => {
                const normalizedQuery = this.searchQuery.toLowerCase();
                return producto.insumo.toLowerCase().includes(normalizedQuery) ||
                    producto.producto_id.toString().includes(normalizedQuery); // Suponiendo que 'id' es un número
            });
        },
    },
    mounted() {
        // Recuperar el nombre de la categoría desde localStorage al montar el componente
        this.nombreCategoria = localStorage.getItem('nombre_categoria') || 'No definido';
    },
    methods: {
        // Método para abrir el modal con la imagen seleccionada
        openModal(imageUrl) {
            this.modalImageUrl = imageUrl; // Establece la URL de la imagen
            this.modalOpen = true; // Abre el modal
        },
        // Constante para manejar la selección automática del producto
        guardarProductoId(producto_id, name) {
            // Guardar el producto_id en localStorage
            localStorage.setItem('producto_id', producto_id);
            localStorage.setItem('nombre_categoria', name);
        },
        redirectToSalidas(productoId) {
            // Redirigir a la página de salidas con el mismo enlace que el botón de visualización de salidas
            this.$inertia.visit(route('salidas.index', { producto_id: productoId }));
        },
        redirectToCreate(producto) {
            localStorage.setItem('productoData', JSON.stringify(producto));
            this.$inertia.visit(route('carritos.index'));
        }
    }
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import ModalCategoria from '@/Components/ModalCategoria.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2';
import {useForm} from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from "axios";
import { jsPDF } from 'jspdf';
import 'jspdf-autotable';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';

const props = defineProps({
    productos: {
        type : Object,
        required: true
    }
});

const form = useForm ({
    insumo: '',
    marca: '',
    modelo: '',
    cantidad: '',
    unidad_medida: '',
    fecha: '',
    comprador: '',
    comentario: '',
    stock: '',
    ultima_entrada: '',
    precio: '',
    category_id: '',
});

const deleteProducto = (id, insumo) => {
    show_confirmacion('¿Estas seguro?', `Estas seguro de eliminar definitivamente el producto ${insumo} de la base de datos`)
    .then((result) => {
        if (result.isConfirmed) {
            form.delete(route('productos.destroy', id), {
                onSuccess: () => {
                    show_alerta('El producto se ha eliminado definitivamente', 'success')
                }
            });
        }
    });
}

const formatDate = (dateString) => {
    const options = { month: 'short', day: '2-digit', year: 'numeric' };

    // Obtener la diferencia de zona horaria en minutos
    const offset = new Date().getTimezoneOffset();
    // Convertir minutos a milisegundos
    const offsetMilliseconds = offset * 60 * 1000;

    // Crear el objeto Date en la zona horaria local sumando el offset
    const date = new Date(new Date(dateString).getTime() + offsetMilliseconds);
    return date.toLocaleDateString('es-ES', options);
};

const formPage = useForm({});
 
const onPageClick = (event) => {
    formPage.get(route('productos.index', { page: event }));
};
 
 
const previousPage = () => {
    const prevPage = props.productos.current_page - 1;
    formPage.get(route('productos.index', { page: prevPage }));
};
 
const nextPage = () => {
    const nextPage = props.productos.current_page + 1;
    formPage.get(route('productos.index', { page: nextPage }));
};
 
const goToPage = (page) => {
    formPage.get(route('productos.index', { page }));
};
 
const total_pages = props.productos.last_page;
const current_page = props.productos.current_page;
const countPerPage = props.productos.data.length;
const totalCount = props.productos.total;

const obtenerDatosSalidas = async () => {
    try {
        const response = await axios.get('/fn_ObtenerDatosSalidas', {
            params: {
                fechaHoy: form.fechaHoy,
                fechaHasta: form.fechaHasta
            }
        });
        return response.data;
    } catch (error) {
        console.error('Error al obtener las salidas:', error);
        return [];
    }
};

const save = async () => {
    const salidas = await obtenerDatosSalidas();
    
    if (salidas.length > 0) {
        await printPDF(salidas);
        ok('PDF generado correctamente');
    } else {
        Swal.fire({
            title: 'No hay datos para las fechas seleccionadas',
            icon: 'warning',
            timer: 1500,
            showConfirmButton: false
        });
    }
};

let timerInterval;

const printPDF = async () => {
    Swal.fire({
        title: '¡Atención!',
        html: 'El historial de salidas se esta generando, espere un momento.',
        timer: 999999999, // Establece un valor grande para que parezca indefinido
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    })
    // Asegúrate de que obtenerDatosSalidas esté definida antes de llamarla aquí
    const data = await obtenerDatosSalidas();

    const doc = new jsPDF('landscape');

    const headerText = 'LISTA DE SALIDAS';
    const tableHeaders = ['N°', 'EMPRESA', 'PRODUCTO', 'TÉCNICO', 'UNIDAD SALIDA', 'UNIDAD DEVOLUCIÓN', 'FECHA', 'HORA SALIDA'];

    // Mapear los datos para la tabla
    const tableData = data.map((item, i) => [
        i + 1,
        item.empresa || '',
        item.producto_nombre || '',
        item.tecnico || '',
        item.unidad_salida || '',
        item.unidad_devolucion || '',
        formatDate(item.fecha) || '', // Asegúrate de que formatDate esté definida y sea la función correcta
        item.hora_salida || '',
    ]);

    let eje_y = 10;

    // Añadir el contenido al PDF
    doc.setFontSize(14);
    doc.setFont('courier', 'bold');
    doc.text(headerText, 15, eje_y += 7);

    doc.autoTable({
        head: [tableHeaders],
        body: tableData,
        startY: eje_y += 10,
        theme: 'grid',
        styles: {
            fontSize: 8,
            cellPadding: 2,
            valign: 'middle',
            halign: 'center',
            textColor: '#000000',
        },
        headStyles: {
            fillColor: '#40c63a',
            textColor: '#ffffff',
        },
        bodyStyles: {
            fillColor: '#eeefef',
            textColor: '#000000',
        },
        columnStyles: {
            0: { fontStyle: 'bold' },
        },
    });

    Swal.close();

    const blob = doc.output('blob');
    const url = URL.createObjectURL(blob);
    window.open(url);
};

const openModalSalidas = () => {
    modal.value = true;
};
 
const closeModal = () =>{
    modal.value = false;
    form.reset();
}
 
const ok = () =>{
    form.reset();
    closeModal();
}

const setCurrentDate = () => {
    const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Lima' }).split('/').reverse().join('-');
    form.fechaHoy = today; // Asigna la fecha actual al campo de registro
    form.fechaHasta = today; // Asigna la fecha actual al campo de registro
};

onMounted(() => {
    setCurrentDate();
});

const nameInput = ref(null);
const modal = ref(false);

</script>

<template>
    <AppLayout title="Inventario de Productos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Productos</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link :href="route('productos.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap items-center flex justify-center gap-2 w-full text-center" v-if="$page.props.user.permissions.includes('Crear Producto')">
                            <i class="bi bi-clipboard-plus mx-1"></i>Registrar Producto
                        </Link>
                        <Link :href="route('salidas.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full items-center flex justify-center gap-2 text-center" v-if="$page.props.user.permissions.includes('Listar Salidas')">
                            <i class="fa solid fa-list-ul mx-1"></i>Listar Salidas
                        </Link>
                        <Link :href="route('entradas.index')" class="text-white bg-indigo-700 items-center flex justify-center gap-2 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center" v-if="$page.props.user.permissions.includes('Listar Entradas')">
                            <i class="bi bi-list-check mx-1"></i>Listar Entradas
                        </Link>
                        <button @click.prevent="openModalSalidas" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center items-center flex justify-center gap-2" v-if="$page.props.user.permissions.includes('Imprimir Salidas')">
                            <i class="bx bx-printer text-xl"></i>Imprimir Salidas
                        </button>
                    </div>
                    <div class="md:mt-2 mt-4">
                        <div class="font-semibold text-center dark:text-white">Categoria || {{ nombreCategoria }}</div>
                    </div>
                    <div class="mt-4 overflow-auto">
                        <div class="pb-4 bg-white dark:bg-gray-800">
                            <label for="table-search" class="sr-only">Buscar</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input v-model="searchQuery" type="text" id="table-search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar el producto">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto scroll-dataTableLEO shadow-md md:rounded-lg rounded-md shadow-gray-200 dark:shadow-gray-500">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2 hidden">ID</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Codigo</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Foto</th>
                                        <th scope="col" class="px-6 py-3 text-left dark:border-white border-b-2">Producto</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Comprador</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Categoria</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Marca</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Modelo</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Stock</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cantidad Ultima Entrada</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Fecha</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Comentario</th> -->
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Aproximado</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white text-black dark:bg-gray-700 dark:text-white border-b dark:hover:bg-gray-900 hover:bg-gray-300 cursor-pointer" @dblclick="redirectToSalidas(producto.producto_id); guardarProductoId(producto.producto_id)" v-for="(producto, i) in filteredProductos">
                                        <td class="px-6 py-4 text-center hidden">{{ producto.producto_id }}</td>
                                        <td class="px-6 py-4 text-center">{{ i + 1 }}</td>
                                        <td class="px-0 py-0 font-semibold text-center">
                                            <img @click="openModal('/productos_inventario/' + producto.imagen_producto)" :src="'/productos_inventario/' + producto.imagen_producto" alt="Foto del Producto" style="width: 70px; height: 70px; cursor: pointer; object-fit: cover;" class="rounded-md py-1 mx-auto">
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-left">{{ producto.insumo }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.comprador }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.cantidad }}   {{ producto.unidad_medida }}</td>
                                        <!-- <td class="px-6 py-4 text-left">{{ producto?.name }}</td> -->
                                        <td class="px-6 py-4 text-center">{{ producto.marca }}</td>
                                        <td class="px-6 py-4 text-center">{{ producto.modelo }}</td>
                                        <td class="px-6 py-4 font-semibold text-center">{{ parseInt(producto.stock) + parseInt(producto.total_entradas) + parseInt(producto.total_devolucion) - parseInt(producto.total_salidas) }}    {{ producto.unidad_medida }}</td>
                                        <!-- <td class="px-6 py-4 text-center">{{ producto.ultima_cantidad_entrada == "0" ? producto.ultima_entrada : producto.ultima_cantidad_entrada }}</td> -->
                                        <td class="px-6 py-4 text-center">{{ formatDate(producto.entrada_maxima == null ? producto.fecha : producto.entrada_maxima) }}</td>
                                        <td class="px-6 py-4 text-center">S/. {{ parseFloat(producto.precio).toFixed(2) }}</td>
                                        <!-- <td class="px-6 py-4 text-left">{{ producto.comentario }}</td> -->
                                        <!-- <td class="text-center whitespace-nowrap">
                                            <button @click="redirectToCreate(producto)" class="bg-blue-600 hover:bg-blue-400 text-white px-2.5 py-[7px] rounded-lg mx-4"><i class="fas fa-shopping-cart"></i></button>
                                            <Link class="py-2 px-3 rounded-lg text-white bg-green-600 hover:bg-green-700" :href="route('productos.edit', producto.producto_id)" v-if="$page.props.user.permissions.includes('Acciones Productos')"><i class="bi bi-pencil-square"></i></Link>
                                            <ButtonDelete @click="$event => deleteProducto(producto.producto_id,producto.insumo)" v-if="$page.props.user.permissions.includes('Acciones Productos')">
                                                <i class="bi bi-trash3 py-2 px-3 rounded-lg text-white bg-red-600 hover:bg-red-700"></i>
                                            </ButtonDelete>
                                        </td> -->
                                        <td class="px-2 py-5 text-center whitespace-nowrap" style="display: flex; justify-content: center; align-items: center;">
                                            <button @click="redirectToCreate(producto)" class="inline-flex items-center justify-center mx-2 bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded-md">
                                                <i class='fas fa-shopping-cart text-sm text-white'></i>
                                            </button>
                                            <Link :href="route('productos.edit', producto.producto_id)" v-if="$page.props.user.permissions.includes('Acciones Productos')" class="inline-flex items-center justify-center bg-amber-400 hover:bg-amber-500 px-2 py-[0.1px] rounded-md mx-2">
                                                <i class='bx bxs-edit text-lg text-white'></i>
                                            </Link>
                                            <button @click="$event => deleteProducto(producto.producto_id,producto.insumo)" v-if="$page.props.user.permissions.includes('Acciones Productos')" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 px-2 py-[0.1px] rounded-md mx-2">
                                                <i class='bx bxs-trash text-lg text-white'></i>
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
                    <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                        <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                            <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                            <p class="text-gray-700 dark:text-white font-semibold">Total de Productos: {{ totalCount }}</p>
                        </div>
                        <div class="mt-4 sm:text-end text-center">
                            <nav aria-label="Page navigation example mt-4">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <li>
                                        <button @click="previousPage" :disabled="!productos.prev_page_url"
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-700 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            Prev
                                        </button>
                                    </li>
                                    <li v-for="page in total_pages" :key="page">
                                        <button @click="goToPage(page)"
                                            :class="{ 'text-blue-600 border-blue-300 dark:text-gray-900 bg-blue-50 hover:bg-blue-100 hover:text-blue-700': page === current_page, 'text-gray-900 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': page !== current_page }"
                                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                            {{ page }}
                                        </button>
                                    </li>
                                    <li>
                                        <button @click="nextPage" :disabled="!productos.next_page_url"
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-700 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
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
        <div class="fixed right-0 top-[110px] bottom-[-60px] md:left-[50px] left-[-2px] overflow-y-auto z-[10000] bg-gray-200/40" v-if="modalOpen">
            <div class="flex justify-center items-center flex-col h-full max-h-[90%] py-10 m-auto rounded-lg" style="backdrop-filter: blur(5px);" @click="modalOpen = false">
                <div class="w-full max-w-sm max-h-[90%] h-full bg-gray-50 rounded-t-lg">
                    <img :src="modalImageUrl" alt="Imagen ampliada" class="max-w-sm w-full h-full object-contain">
                </div>
                <div class="bg-gray-50 p-2 w-full max-w-sm flex justify-end rounded-b-lg">
                    <button @click="modalOpen = false" type="button" class="w-full justify-center rounded-md border border-transparent shadow-sm px-14 py-0 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
        <ModalCategoria :show="modal" @close="closeModal">
            <div class="p-4">
                <h2 class="text-base font-bold text-gray-900 dark:text-white text-center uppercase mb-4">Imprimir Salidas</h2>
                <div class="grid grid-cols-2 gap-x-8">
                    <div>
                        <InputLabel for="fechaHoy" value="Fecha Hoy:" class="mb-2 text-base font-bold"></InputLabel>
                        <TextInput id="fechaHoy" ref="fechaHoy" v-model="form.fechaHoy" type="date" class="w-full"
                            placeholder="Nombre de la categoria"></TextInput>
                        <InputError :message="form.errors.fechaHoy" class="mt-2"></InputError>
                    </div>
                    <div>
                        <InputLabel for="fechaHasta" value="Fecha Hasta:" class="mb-2 text-base font-bold"></InputLabel>
                        <TextInput id="fechaHasta" ref="nameInput" v-model="form.fechaHasta" type="date" class="w-full"
                            placeholder="Nombre de la categoria"></TextInput>
                        <InputError :message="form.errors.fechaHasta" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-3 flex justify-center">
                    <PrimaryButton :disabled="form.processing" @click="save" class="flex justify-center gap-2">
                        <i class="bx bx-printer text-xl"></i>Imprimir
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form.processing" @click="closeModal">
                        Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalCategoria>
    </AppLayout>
</template>