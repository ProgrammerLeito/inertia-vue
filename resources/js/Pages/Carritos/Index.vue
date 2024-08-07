<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref,onMounted,computed,watch } from 'vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import * as XLSX from 'xlsx';

const props=defineProps({
    carritos:{
        type:Object,
        required:true
    }
});

const form = useForm({
    materiales: '',
    cantidad: '',
    precio_u: '',
});

const precio_t = computed(() => {
    const cantidad = parseFloat(form.cantidad)
    const precio_u = parseFloat(form.precio_u)
    return isNaN(cantidad) || isNaN(precio_u) ? '0.00' : (cantidad * precio_u).toFixed(2)
})

watch(precio_t, (newVal) => {
    form.precio_t = newVal
})

const submitForm = () => {
    form.post(route('carritos.store'), {
        onSuccess: () => {
            form.cantidad = '';
            form.precio_t = '0.00';
            const Toast = Swal.mixin({
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: 'Éxito',
                text: "El producto se ha registrado en el carrito correctamente"
            });
        },
        onError: (errors) => {
            if (errors.response && errors.response.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.'
                });
                console.error('Error HTTP:', errors.response.status);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.'
                });
                console.error('Error desconocido:', errors);
            }
        }
    });
}

const form2 = useForm({
    id: '',
});

const deleteCarrito = (id, materiales) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar el producto del carrito definitivamente : ' + materiales + '?',
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
            form2.delete(route('carritos.destroy', id), {
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
                        text: "El producto del carrito se a eliminado exitosamente.",
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

onMounted(() => {
    const productoData = JSON.parse(localStorage.getItem('productoData'));
    if (productoData) {
        // form.marca = hservicioData.hmarca ? hservicioData.hmarca.nombre : 'Sin marca';
        form.materiales = productoData.insumo;
        form.precio_u = productoData.precio;
    }
});

var totalcompra = 0 

const generarArchivoExcel = () => {
    const encabezado = [['Lista de Productos a Comprar']];
    const datos = encabezado.concat([['Materiales', 'Cantidad', 'Precio Unitario', 'Precio Total']]);

    const tbodyRows = document.querySelectorAll('tbody tr');
    tbodyRows.forEach((row) => {
        const rowData = [];
        const cells = row.querySelectorAll('td:not(.hidden)');
        cells.forEach(cell => {
            rowData.push(cell.textContent.trim());
        });
        datos.push(rowData);
    });

    const worksheet = XLSX.utils.aoa_to_sheet(datos);

    // Aplica estilos a las celdas
    const rango = XLSX.utils.decode_range(worksheet['!ref']);
    for (let R = rango.s.r; R <= rango.e.r; ++R) {
        for (let C = rango.s.c; C <= rango.e.c; ++C) {
            const cell_address = { c: C, r: R };
            const cell_ref = XLSX.utils.encode_cell(cell_address);

            if (!worksheet[cell_ref]) continue;

            worksheet[cell_ref].s = {
                font: {
                    name: 'Arial',
                    sz: 12,
                    bold: R === 0,
                    color: { rgb: R === 0 ? 'FFFFFF' : '000000' }
                },
                fill: {
                    fgColor: { rgb: R === 0 ? '4F81BD' : 'FFFFFF' }
                },
                alignment: {
                    vertical: 'center',
                    horizontal: 'center'
                },
                border: {
                    top: { style: 'thin', color: { rgb: '000000' } },
                    bottom: { style: 'thin', color: { rgb: '000000' } },
                    left: { style: 'thin', color: { rgb: '000000' } },
                    right: { style: 'thin', color: { rgb: '000000' } }
                }
            };

            // Ajusta el ancho de las celdas específicas
            worksheet['!cols'] = worksheet['!cols'] || [];
            if (C === 1 || C === 2 || C === 3) {
                worksheet['!cols'][C] = { wpx: 95 };  // Ancho de la celda en píxeles
            }
            if (R === 0 && C === 0) {
                worksheet['!cols'][C] = { wpx: 250 };  // Ancho de la celda "Materiales" en píxeles
            }
        }
    }

    worksheet['!rows'] = worksheet['!rows'] || [];
    for (let R = rango.s.r; R <= rango.e.r; ++R) {
        worksheet['!rows'][R] = { hpx: 20 };  // Alto de la celda en píxeles
    }

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Productos');
    XLSX.writeFile(workbook, 'compras.xlsx');
}

</script>
<template>
    <AppLayout title="Lista de Compra de Productos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Productos a Comprar</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-green-600 py-1 rounded-t-md"></div>
                <div class="p-6 bg-white border-gray-100 shadow-lg shadow-gray-600 dark:bg-gray-800 tracking-widest dark:shadow-gray-800 rounded-b-lg">
                    <form @submit.prevent="submitForm" class="my-5 uppercase font-bol">
                        <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 sm:gap-x-8 mb-3">
                            <div>
                                <InputLabel for="materiales" value="materiales"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.materiales" type="text" id="materiales" required disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.materiales" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="cantidad" value="cantidad"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.cantidad" type="number" id="cantidad" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.cantidad" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="precio_u" value="precio unitario"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.precio_u" type="number" id="precio_u" required disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.precio_u" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="precio_t" value="precio total"
                                    class="cruz block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.precio_t" type="text" id="precio_t" required readonly disabled
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.precio_t" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 justify-end md:mt-0 mt-4">
                            <a href="https://api.whatsapp.com/send/?phone=51960269942&text=Hola+estoy+interesado+mas+informacion+a%3A+detalle&type=phone_number&app_absent=0" target="_blank" rel="noopener noreferrer" @click="generarArchivoExcel" class="text-sm font-bold py-2 px-5 bg-indigo-700 hover:bg-indigo-600 text-gray-50 rounded-md w-full md:w-auto flex gap-2 items-center justify-center">
                                <img src="img/whatsapp.png" alt="" class="h-5"> Ir a WhatsApp
                            </a>
                            <ButtonResponsive class="uppercase text-sm">
                                Guardar
                            </ButtonResponsive>
                        </div>
                    </form>
                    <div class="relative overflow-x-auto shadow-md md:rounded-lg rounded-md shadow-gray-200 dark:shadow-gray-500">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <!-- <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">#</th> -->
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">materiales</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">cantidad</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Unitario</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Precio Total</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-xs tracking-widest">
                                <p class="px-6 py-3 text-center dark:border-white border-b hidden">{{ totalcompra = 0 }}</p>
                                <tr v-for="carrito in carritos" :key="carrito.id"
                                    class="bg-white text-black  hover:text-white border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                    <!-- Mostrar los datos de cada hservicio -->
                                    <!-- <td class="px-6 py-4 text-center">{{ carrito.id }}</td> -->
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{carrito.materiales }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{carrito.cantidad }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b hidden">{{ totalcompra += ( parseFloat(carrito.precio_u) * parseInt(carrito.cantidad)) }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{carrito.moneda}} {{ carrito.precio_u }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{carrito.moneda}} {{ parseFloat(carrito.precio_u) * parseInt(carrito.cantidad)  }}</td>
                                    <td class="p-3 text-center whitespace-nowrap">
                                        <button @click="$event => deleteCarrito(carrito.id, carrito.materiales)" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 px-1.5 py-0.5 rounded-md">
                                            <i class='bx bxs-trash text-base text-white'></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="bg-white text-black hover:text-white border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                    <td class="px-6 py-3 text-center dark:border-white border-b"></td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b"></td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b text-md font-bold">Saldo Total:</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">S/. {{ totalcompra }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="carritos.length === 0" class="text-center py-2 dark:text-white">
                            No se encontraron datos.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>