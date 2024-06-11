<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref, watchEffect } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const searchQuery = ref('');
const filteredCotizaciones = ref([]);
const { cservicios } = defineProps({
    cservicios: {
        type: Object
    }
});

const form = useForm({
    id: ''
});

watchEffect(() => {
    filteredCotizaciones.value = cservicios.data.filter(cservicio => {
        const searchLower = searchQuery.value.toLowerCase();
        return (cservicio.n_informe && cservicio.n_informe.toLowerCase().includes(searchLower)) ||
            (cservicio.n_cotizacion && cservicio.n_cotizacion.toLowerCase().includes(searchLower)) ||
            (cservicio.descripcion && cservicio.descripcion.toLowerCase().includes(searchLower)) ||
            (cservicio.razonSocial && cservicio.razonSocial.toLowerCase().includes(searchLower)) ||
            (cservicio.requiere && cservicio.requiere.toLowerCase().includes(searchLower)) ||
            cservicio.id.toString().includes(searchLower);
    });
});

const deleteTservicio = (id, razonSocial) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar la cotizacion de: ' + razonSocial + '?',
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
            form.delete(route('c_servicos.destroy', id), {
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
                        text: "cotizacion de servicio eliminado exitosamente.",
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

const formPage = useForm({});
const onPageClick = (event) => {
    formPage.get(route('c_servicos.index', { page: event }));
}
const previousPage = () => {
    const prevPage = props.cservicios.current_page - 1;
    formPage.get(route('c_servicos.index', { page: prevPage }));
};

const nextPage = () => {
    const nextPage = props.cservicios.current_page + 1;
    formPage.get(route('c_servicos.index', { page: nextPage }));
};

const goToPage = (page) => {
    formPage.get(route('c_servicos.index', { page }));
};

const total_pages = cservicios.last_page;
const current_page = cservicios.current_page;
const countPerPage = cservicios.data.length;
const totalCount = cservicios.total;



const openCtgModalOrCodigoSunat = async (cservicio) => {
    const initialOptions = {
        title: `Cotización del cliente: ${cservicio.razonSocial}`,
        input: 'radio',
        inputOptions: {
            'estado': 'Cambiar Estado',
            'codigoSunat': 'Ingresar Código SUNAT'
        },
        inputValidator: (value) => {
            if (!value) {
                return 'Debes seleccionar una opción';
            }
        },
        confirmButtonText: 'Continuar',
        showCancelButton: true,
        customClass: {
            title: 'text-2xl font-bold tracking-widest ',
            input: 'text-base tracking-widest ',
            inputOptions: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
        confirmButtonColor: '#009846',
        cancelButtonColor: '#3085d6',
    };

    const { value: selectedOption } = await Swal.fire(initialOptions);

    if (selectedOption === 'estado') {
        await openCtgModal(cservicio);
    } else if (selectedOption === 'codigoSunat') {
        await openCodigoSunat(cservicio);
    }
};

const openCtgModal = async (cservicio) => {
    const modalTitle = `Cotización del cliente: ${cservicio.razonSocial}`;

    const options = {
        title: modalTitle,
        input: 'select',
        inputOptions: {
            'Por Enviar': 'Por Enviar',
            'Enviado': 'Enviado',
            'Aceptado': 'Aceptado',
            'Rechazado': 'Rechazado',
            'Finalizado': 'Finalizado',
        },
        customClass: {
            title: 'text-2xl font-bold tracking-widest ',
            input: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
        inputPlaceholder: 'Selecciona una opción',
        showCancelButton: true,
        confirmButtonColor: '#009846',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Asignar',
        showLoaderOnConfirm: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debes seleccionar un tipo de estado de envío';
            }
        },
        preConfirm: (value) => {
            return Inertia.post(route('cservicios.cambiar_estado'), {
                cservicio_id: cservicio.id,
                descripcion: value
            }).then(response => {
                if (response && response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Estado asignado exitosamente',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(response ? response.statusText : 'Error desconocido');
                }
            }).catch(error => {
                Swal.showValidationMessage(`Solicitud fallida: ${error.message}`);
            });
        }
    };

    await Swal.fire(options);
};

const openCodigoSunat = async (cservicio) => {
    const modalTitle = `Ingresar codigo Sunat: ${cservicio.razonSocial}`;

    const options = {
        title: modalTitle,
        input: 'text',
        inputPlaceholder: 'Ingresa el código SUNAT',
        customClass: {
            title: 'text-2xl font-bold tracking-widest ',
            input: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
        showCancelButton: true,
        confirmButtonColor: '#009846',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Asignar',
        showLoaderOnConfirm: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debes ingresar un código SUNAT';
            }
        },
        preConfirm: (value) => {
            return Inertia.post(route('cservicios.codigoSunat'), {
                cservicio_id: cservicio.id,
                codesunat: value
            }).then(response => {
                if (response && response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Código SUNAT asignado exitosamente',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(response ? response.statusText : 'Error desconocido');
                }
            }).catch(error => {
                Swal.showValidationMessage(`Solicitud fallida: ${error.message}`);
            });
        }
    };

    await Swal.fire(options);
};
const openModal = (cservicio) => {
    if (cservicio) {
        Swal.fire({
            title: 'DETALLES DE LA COTIZACION POR SERVICIO',
            width: 800,
            html: `<hr/><br/>
            <div style="text-align: left;" class="text-justify p-1 uppercase">
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1 text-base"><strong>N° COTIZACION</strong>: ${cservicio.n_cotizacion}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1 text-base"><strong>ESTADO</strong>: ${cservicio.descripcion}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1 text-base"><strong>CLIENTE</strong>: ${cservicio.razonSocial} </p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1 text-base"><strong>N° INFORME</strong>: ${cservicio.n_informe}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1 text-base"><strong>FECHA REGISTRO</strong>: ${cservicio.fecha}</p>
                <p class="py-1 grid gap-1 grid-cols-2 grid-rows-1 text-base"><strong>REGISTRADO POR</strong>: ${cservicio.foto}</p>
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
</script>
<template>
    <AppLayout title="Cotizaciones por  Servicio">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Cotizaciones por Servicio</h1>
        </template>
        
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 uppercase text-sm justify-between">
                        <Link :href="route('balanza')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-folder-plus mx-1"></i> Cotizar por servicio
                        </Link>
                    </div>
                    <div class="py-1 mb-5">
                        <div class="grid grid-cols-1 gap-y-2 sm:grid-cols-1 sm:gap-x-2">
                            <div class="flex flex-col">
                                <InputLabel for="table-search"
                                    class="block text-md font-medium text-gray-700 dark:text-white">
                                    Buscar</InputLabel>
                                <div class="relative mt-1">
                                    <div
                                        class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                        </svg>
                                    </div>
                                    <input v-model="searchQuery" type="text" id="table-search"
                                        class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Buscar Cotizacion por Servicio">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="relative overflow-x-auto shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                                <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N° Cot</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N° Inf</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">C | Factura</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Cliente</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Requerimiento</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Emision</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Neto</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Total</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-xs">
                                    <tr v-for="(cservicio, i) in filteredCotizaciones" :key="cservicio.id" class="bg-white text-black border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ cservicio.n_cotizacion }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ cservicio.n_informe }}</td>
                                        <td class="py-4 text-center fa-fade">{{ cservicio.codesunat ?cservicio.codesunat : 'Sin codigo' }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ cservicio.razonSocial }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ cservicio.requiere }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ cservicio.fecha }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ cservicio.precio }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">{{ cservicio.total }}</td>
                                        <td class="px-6 py-3 text-center dark:border-white border-b">
                                            <div :class="{
                                                'bg-blue-600': cservicio.descripcion === 'Por Enviar',
                                                'bg-yellow-600': cservicio.descripcion === 'Enviado',
                                                'bg-green-600': cservicio.descripcion === 'Aceptado',
                                                'bg-red-600': cservicio.descripcion === 'Rechazado',
                                                'bg-indigo-600': cservicio.descripcion === 'Finalizado',
                                            }" class="inline-block px-2 py-1 rounded">
                                                <b>{{ cservicio.descripcion }}</b>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-center whitespace-nowrap dark:border-white border-b">
                                            <Link :href="route('c_servicos.edit', cservicio.id)"
                                                class=" bg-green-500 p-1 dark:hover:bg-white py-1 px-2  dark:hover:text-green-500 rounded">
                                            <i class="bi bi-pencil-square"></i>
                                            </Link>
                                            <Button @click="$event => deleteTservicio(cservicio.id, cservicio.razonSocial)"
                                                class="ml-1 bg-red-600 dark:hover:bg-white dark:hover:text-red-600 py-1 px-2 font-extrabold dark:text-white rounded cursor-pointer text-white">
                                                <i class="bi bi-trash3"></i>
                                            </Button>
                                            <button @click="openCtgModalOrCodigoSunat(cservicio)"
                                                    class="text-center ml-1 text-white bg-blue-500 hover:bg-blue-600 py-1 px-2 dark:hover:bg-white dark:hover:text-blue-600 rounded-md">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <button @click="openModal(cservicio)"
                                                class="text-center ml-1 text-white bg-blue-500 hover:bg-blue-600 py-1 px-2 rounded-md"><i
                                                    class="bi bi-eye"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                        <div class="hidden sm:block">
                            <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                                <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                                <p class="text-gray-700 dark:text-white font-semibold">Total de Cotizaciones por Servicio: {{ totalCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:text-end text-center">
                            <nav aria-label="Page navigation example mt-4">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <li>
                                        <button @click="previousPage" :disabled="!cservicios.prev_page_url"
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
                                        <button @click="nextPage" :disabled="!cservicios.next_page_url"
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
        </div>
    </AppLayout>
</template>
