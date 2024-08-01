<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { defineProps , onMounted} from 'vue';

const tbproductosAgregados = [];
const props = defineProps({
    cventa: {
        type: Object,
        required: true
    },
    clientes: {
        type: Object,
        required: true
    },
    tenors: {
        type: Object,
        required: true
    },
    tbproductos: {
        type: Object
    },
    tbmarcas: {
        type: Object
    },
    $productosAgregados:{
        type:Object
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
    total: props.cventa.total
});

// Método para agregar un producto
const agregarProducto = (producto) => {
    tbproductosAgregados.push(producto);
};

// Método para quitar un producto
const quitarProducto = (index) => {
    tbproductosAgregados.splice(index, 1);
};

// Método para enviar el formulario
const submitForm = () => {
    // Aquí enviarías los datos del formulario al servidor para actualizar la cotización
};

// Método para cancelar la edición y regresar a la página anterior
const cancelEdit = () => {
    $inertia.visit(route('cotizaciones.index')); // Cambia 'route' por el nombre real de tu función de rutas
};
onMounted(() => {
    const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual y la formatea como yyyy-mm-dd
    form.fecha = today; 
});
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
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-8 mb-3">
                                <!-- cliente -->
                                <div>
                                    <InputLabel for="cliente_id" class="text-xs">Cliente</InputLabel>
                                    <select id="cliente_id" v-model="form.cliente_id" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Seleccione un cliente</option>
                                        <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">{{ cliente.razonSocial }}</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="fecha" class="block text-xs font-medium text-gray-700">Fecha</InputLabel>
                                    <TextInput type="date" id="fecha" v-model="form.fecha" required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"/>
                                    <InputError :message="$page.props.errors.fecha" class="mt-2"/>
                                </div>
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
                    
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                           </div>
                           <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-4 py-2 card sm:gap-x-8">
                                <div>
                                    <InputLabel for="subtotal" :value="'Subtotal (' + (form.moneda === 'sol' ? 'S/' : '$') + '):'"></InputLabel>
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
                                    <InputLabel for="total" :value="'Total (' + (form.moneda === 'sol' ? 'S/' : '$') + '):'"></InputLabel>
                                    <TextInput v-model="form.total" type="number" class="mt-2 w-full bg-green-400 uppercase" disabled></TextInput>
                                </div>
                            </div>
                            <div class="flex justify-end">
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
    </AppLayout>
</template>