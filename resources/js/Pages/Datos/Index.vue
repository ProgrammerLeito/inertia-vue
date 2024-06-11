<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import ButtonDelete from '@/Components/ButtonDelete.vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import Swal from 'sweetalert2';
import { nextTick, onMounted, ref } from 'vue';
import FileInput from '@/Components/FileInput.vue';

const nameInput = ref(null);
const modal = ref(false);
const title = ref('');
const operation = ref(1);
const id = ref('');

const props =defineProps({
    datos: {
        type: Object,
        required: true
    },
    clientes: {
        type: Object
    }

});
const initialvalues = {
    nombre: '',
    cargo: '',
    telefono: '',
    correo: '',
    tarjeta: '',
    cliente_id: '',

};

const form = useForm(initialvalues)

const onSelectFoto = (e) => {
    const files = e.target.files;
    if (files.length) {
        form.tarjeta = files[0]
    }
}
const openModal = (op,dato) => {
    modal.value = true;
    nextTick(() => nameInput.value.focus());
    operation.value = op;
    id.value = dato;
    if (op === 1) {
        title.value = 'Registrar datos ';
    }
}

const closeModal = () => {
    modal.value = false;
    form.reset();
}

const save = () => {
    if (operation.value == 1) {
        form.post(route('datos.store'), {
            onSuccess: () => { ok('datos registrada') }
        });
    }
}
const ok = (msj) => {
    form.reset();
    closeModal();
    Swal.fire({
        title: msj,
        icon: 'success',
        timer: 1000,
        showConfirmButton: false
    });
};

const deleteDatos = (id, nombre) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });

    alerta.fire({
        title: '¿Estás seguro de eliminar los datos de: ' + nombre + '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
        customClass: {
            title: 'text-xl font-bold tracking-widest ',
            cancelButton: 'text-base tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('datos.destroy', id), {
                onSuccess: () => {
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
                        text: "Cliente eliminado exitosamente.",
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
const idHojasCliente= ref('');
const razonSocial = ref('');

onMounted(() => {
    const cliente_id = localStorage.getItem('cliente_id');
    razonSocial.value = localStorage.getItem('razonSocial');
    idHojasCliente.value = cliente_id; // Asigna el valor de servicio_id a idHojasServicio
});

onMounted(() => {
    const cliente_id = localStorage.getItem('cliente_id');
        if (cliente_id) {
            form.cliente_id = cliente_id;
        }
});
const formPage = useForm({});
const onPageClick = (event) => {
    formPage.get(route('datos.index', { page: event }));
}
const previousPage = () => {
    const prevPage = props.datos.current_page - 1;
    formPage.get(route('datos.index', { page: prevPage }));
};

const nextPage = () => {
    const nextPage = props.datos.current_page + 1;
    formPage.get(route('datos.index', { page: nextPage }));
};

const goToPage = (page) => {
    formPage.get(route('datos.index', { page }));
};

const total_pages = props.datos.last_page;
const current_page = props.datos.current_page;
const countPerPage = props.datos.data.length;
const totalCount = props.datos.total;
</script>
<template>
    <AppLayout title="Datos Del Cliente">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">
                Lista De Datos Del Cliente</h1>
        </template>
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <ButtonResponsive @click="() => openModal(1)" class="font-normal">
                            <i class="fas fa-plus mx-2 font-bold"></i>Registrar Datos
                        </ButtonResponsive>
                        <Link :href="route('clientes.index')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="fas fa-arrow-left mx-2"></i> Regresar
                        </Link>
                    </div>
                    <div class="md:mt-0 py-6 hidden">
                        <div class="font-semibold text-center dark:text-white">CLIENTE || {{ razonSocial }} </div>
                    </div>
                    <div
                        class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <th scope="col" class="px-6 py-2 text-center dark:border-white border-b-2">Nombre
                                    </th>
                                    <th scope="col" class="px-6 py-2 text-left dark:border-white border-b-2">Cargo
                                    </th>
                                    <th scope="col" class="px-6 py-2 text-center dark:border-white border-b-2">Teléfono
                                    </th>
                                    <th scope="col" class="px-6 py-2 text-center dark:border-white border-b-2">correo
                                    </th>
                                    <th scope="col" class="px-6 py-2 text-center dark:border-white border-b-2">tarjeta
                                    </th>
                                    <th scope="col" class="px-6 py-2 text-center dark:border-white border-b-2">acciones
                                    </th>
                                    <!-- Agrega más encabezados según tus necesidades -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="dato in datos.data" :key="dato.id"
                                class="bg-white text-black dark:bg-gray-700 dark:text-white border-b dark:hover:bg-gray-900 hover:bg-gray-300 cursor-pointer">
                                <td class="px-6 py-4 text-center">{{ dato.nombre }}</td>
                                    <td class="px-6 py-4 text-left whitespace-nowrap font-bold">{{ dato.cargo }}</td>
                                    <td class="px-6 py-4 text-center">{{ dato.telefono }}</td>
                                    <td class="px-6 py-4 text-center">{{ dato.correo }}</td>
                                    <td class="px-0 py-0 text-center flex justify-center">
                                        <img @click="openModal3('/storage/' + dato.tarjeta)"
                                            :src="'/storage/' + dato.tarjeta" alt="Foto"
                                            style="width: 70px; height: 70px; cursor: pointer; object-fit: cover;"
                                            class="rounded-md py-1"/>
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap">
                                        <Link :href="route('datos.edit', dato.id)" class="py-2 px-3 rounded-lg text-white bg-green-600 hover:bg-green-700"><i class="bi bi-pencil-square"></i></Link>
                                        <ButtonDelete @click="deleteDatos(dato.id, dato.nombre)" v-if="$page.props.user.permissions.includes('Acciones Productos')">
                                            <i class="bi bi-trash3 py-2 px-3 rounded-lg text-white bg-red-600 hover:bg-red-700"></i>
                                        </ButtonDelete>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-wrap md:justify-between sm:justify-between justify-center">
                        <div class="hidden sm:block">
                            <div class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star">
                                <p class="text-gray-700 dark:text-white font-semibold">Registros por página: {{ countPerPage }}</p>
                                <p class="text-gray-700 dark:text-white font-semibold">Total de Datos: {{ totalCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:text-end text-center">
                            <nav aria-label="Page navigation example mt-4">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <li>
                                        <button @click="previousPage" :disabled="!datos.prev_page_url"
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
                                        <button @click="nextPage" :disabled="!datos.next_page_url"
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
    </AppLayout>
    <ModalResponsive :show="modal" @close="closeModal">
        <div class="p-4 uppercase">
            <h2 class="text-xl font-medium text-gray-900 tracking-widest dark:text-white text-center uppercase mb-1">{{ title }}</h2>
            <hr>
            <div class="py-3 tracking-widest">
                <div class="w-full mt-1">
                    <InputLabel for="cliente_id" value="Cliente:" class="mb-2 text-xs"></InputLabel>
                    <select id="cliente_id" v-model="form.cliente_id" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="">Seleccione un Cliente</option>
                        <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">{{ cliente.razonSocial
                            }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3">
                <div class="w-full">
                    <InputLabel for="nombre" value="Nombre:" class="mb-2 text-xs"></InputLabel>
                    <TextInput id="nombre" ref="nameInput" v-model="form.nombre" type="text" class="w-full"
                        placeholder="Nombre"></TextInput>
                    <InputError :message="form.errors.nombre" class="mt-2"></InputError>
                </div>
                <div class="w-full">
                    <InputLabel for="cargo" value="Cargo:" class="mb-2 text-xs"></InputLabel>
                    <TextInput id="cargo" ref="nameInput" v-model="form.cargo" type="text" class="w-full"
                        placeholder="Cargo"></TextInput>
                    <InputError :message="form.errors.cargo" class="mt-2"></InputError>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3">
                <div class="w-full">
                    <InputLabel for="telefono" value="telefono:" class="mb-2 text-xs"></InputLabel>
                    <TextInput id="telefono" ref="nameInput" v-model="form.telefono" type="text" class="w-full"
                        placeholder="Telefono"></TextInput>
                    <InputError :message="form.errors.telefono" class="mt-2"></InputError>
                </div>
                <div class="w-full">
                    <InputLabel for="correo" value="correo:" class="mb-2 text-xs"></InputLabel>
                    <TextInput id="correo" ref="nameInput" v-model="form.correo" type="text" class="w-full"
                        placeholder="Correo"></TextInput>
                    <InputError :message="form.errors.correo" class="mt-2"></InputError>
                </div>
            </div>
            <div class="w-full py-2 my-2 tracking-widest">
                <InputLabel for="tarjeta" value="tarjeta:" class="block mb-2 text-xs font-medium text-gray-700" />
                <FileInput name="tarjeta" @change="onSelectFoto" />
                <InputError :message="$page.props.errors.tarjeta" class="mt-2" />
            </div>
            <div class="p-1 flex justify-center">
                <PrimaryButton :disabled="form.processing" @click="save">
                    <i class="fa-solid fa-save mx-1"></i>{{ operation == 1 ? 'Registrar' : 'Actualizar' }}
                </PrimaryButton>
                <DangerButton class="ml-3" :disabled="form.processing" @click="closeModal">
                    Cancelar
                </DangerButton>
            </div>
        </div>
    </ModalResponsive>
    <div class="fixed right-0 top-[110px] bottom-[-60px] left-[50px] overflow-y-auto z-[10000] bg-gray-200/40"
        v-if="modalOpen">
        <div class="flex justify-center items-center flex-col h-full max-h-[90%] py-10 m-auto rounded-lg"
            @click="modalOpen = false">
            <div class="w-full max-w-sm max-h-[90%] h-full bg-gray-50 rounded-t-lg">
                <img :src="modalImageUrl" alt="Imagen ampliada" class="max-w-sm w-full h-full object-contain">
            </div>
            <!-- Botón de cierre del modal -->
            <div class="bg-gray-50 p-2 w-full max-w-sm flex justify-end rounded-b-lg">
                <button @click="modalOpen = false" type="button"
                    class="w-full justify-center rounded-md border border-transparent shadow-sm px-14 py-0 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            modalOpen: false, // Estado del modal
            modalImageUrl: '' // URL de la imagen para mostrar en el modal
        };
    },
    methods: {
        openModal3(imageUrl) {
            console.log('Abrir modal con imagen:', imageUrl);
            this.modalImageUrl = imageUrl; // Establece la URL de la imagen
            this.modalOpen = true; // Abre el modal
        }

    }
}
</script>
