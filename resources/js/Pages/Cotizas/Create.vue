<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import Swal from 'sweetalert2';
import {useForm} from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { onMounted , watch , computed} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';

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
    tbproductos:{
        type:Object
    },
    tbmarcas:{
        type:Object
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
    if (form.moneda === 'dolares') {
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
    if (newValue === 'dolares') {
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
    } else if (newValue === 'sol' && form.tipoCambio) {
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
        title2.value = 'Registrar tenor';
    } else {
        title2.value = 'Actualizar tenor';
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
    Swal.fire({title: msj, icon: 'success'});
};
 

onMounted(() => {
    const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual y la formatea como yyyy-mm-dd
    form.fecha = today; 
});

const submitForm = () => {
    form.post(route('cotizas.store'), {
        onSuccess: () => { 
            // Restablecer el formulario después de un envío exitoso
            form.reset();
            // Mostrar un mensaje de éxito
            Swal.fire({
                title: 'Cotización guardada',
                text: 'La cotización se ha guardado exitosamente.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            // Redirigir a la página de índice de cotizaciones
            Inertia.visit(route('cotizas.index'));
        },
        onError: (errors) => {
            // Manejar errores de validación u otros errores del servidor
            console.error(errors);
        }
    });
};


</script>
 
<template>
    <AppLayout title="Cotizar por Venta">
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Crear Cotizacion por venta</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-xs  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
               
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
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
                           <div class="py-5">
                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2  max-h-80 overflow-y-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
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
                                            <tr v-for="(tbproducto, index) in tbproductosAgregados" :key="index">
                                                <td class="px-3 py-4 text-center">{{ tbproducto.modelo }}</td>
                                                <td class="px-6 py-3 text-center">
                                                    <img @click="openModal('/img/catalogo/' + tbproducto.foto)" :src="'/img/catalogo/' + tbproducto.foto" alt="Foto" class="w-10 h-10 cursor-pointer object-cover rounded-md">
                                                </td>
                                                <td class="px-3 py-3 text-center">{{ tbproducto.especificaciones }}</td>
                                                <td class="px-3 py-3 text-center">{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }}</td>
                                                <td class="px-3 py-3 text-center">{{ tbproducto.capacidades }}</td>
                                                <td class="px-3 py-3 text-center">s/{{ tbproducto.precio }}</td>
                                                <td class="px-3 py-3 text-blue-500 text-center"><input type="number" v-model="tbproducto.cantidad"></td>
                                                <td class="px-3 py-3  text-center">s/{{ tbproducto.precio * tbproducto.cantidad }}</td>
                                                <td class="px-3 py-3 text-center "><button @click="quitarProducto(index)">Quitar</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                           </div>
                            
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-4 sm:gap-x-8">
                               
                                <!-- Medida -->
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
                                            <option value="sol">Sol</option>
                                            <option value="dolares">Dólares</option>
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
                                    <InputLabel class="text-xs" for="subtotal" :value="'Subtotal (' + (form.moneda === 'sol' ? 'S/' : '$') + '):'"></InputLabel>
                                    <TextInput v-model="form.subtotal" type="number" class="mt-2 w-full uppercase" disabled></TextInput>
                                </div>
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="igvCheckbox" v-model="igvEnabled">
                                        <label for="igvCheckbox" class="ml-2 text-blue-700">Aplicar IGV (18%)</label>
                                    </div>
                                    <InputLabel for="igv" :value="'IGV (18%) (' + (form.moneda === 'sol' ? 'S/' : '$') + '):'" v-if="igvEnabled"></InputLabel>
                                    <TextInput v-model="form.igv" type="number" class="mt-2 w-full uppercase" disabled></TextInput>
                                </div>
                                <div>
                                    <InputLabel class="text-xs" for="total" :value="'Total (' + (form.moneda === 'sol' ? 'S/' : '$') + '):'"></InputLabel>
                                    <TextInput v-model="form.total" type="number" class="mt-2 w-full bg-green-400 uppercase" disabled></TextInput>
                                </div>

                            </div>

                            <div class="mt-6 flex flex-wrap">
                                <Link  class="text-white uppercase ml-1 bg-green-700 hover:bg-green-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                    vista previa pdf
                                </Link>
                                <PrimaryButton class="text-white uppercase text-xs bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                    Generar Cotizacion
                                </PrimaryButton>
                                <Link :href="route('cotizas.index')" class="text-white uppercase ml-1 bg-red-700 hover:bg-red-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                    Cancelar
                                </Link>
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

        <div v-if="modal3" class="fixed inset-0 overflow-y-auto z-50 bg-gray-200/40 flex justify-center items-center">
            <div class="modal-content bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-2xl max-w-7xl w-full sm:max-w-4xl md:max-w-3xl lg:max-w-4xl xl:max-w-6xl">
                <button @click="toggleModal3" class="close absolute top-0.5 right-0.5 p-2 text-gray-500 hover:text-gray-700">
                    &times;
                </button>
                <div class="px-2 py-2 dark:text-white">
                    <h1 class="uppercase text-lg font-bold ">Catálogo de productos</h1>
                    <p class="text-sm">Seleccione los productos a cotizar</p>
                    <hr class="my-1">
                </div>
                <div class="py-3 px-2">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 max-h-80 overflow-y-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
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
                                <tr v-for="tbproducto in tbproductos" :key="tbproducto.id" class="bg-white text-black dark:bg-gray-700 dark:text-white">
                                    <td class="px-6 py-3 text-center">{{ tbproducto.modelo }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <img @click="openModal('/img/catalogo/' + tbproducto.foto)" :src="'/img/catalogo/' + tbproducto.foto" alt="Foto" class="w-10 h-10 cursor-pointer object-cover rounded-md">
                                    </td>
                                    <td class="px-6 py-3 text-center">{{ tbproducto.especificaciones }}</td>
                                    <td class="px-6 py-3 text-center">{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }}</td>
                                    <td class="px-6 py-3 text-center">{{ tbproducto.capacidades }}</td>
                                    <td class="px-6 py-3 text-center">s/{{ tbproducto.precio }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <button @click="agregarProducto(tbproducto)" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:bg-green-700">
                                            Agregar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
