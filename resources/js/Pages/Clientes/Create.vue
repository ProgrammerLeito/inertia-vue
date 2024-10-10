<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextInputCliente from '@/Components/TextInputCliente.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextRuc from '@/Components/TextRuc.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import InputError from '@/Components/InputError.vue';
import {Link , useForm} from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import ModalResponsive from '@/Components/ModalResponsive.vue';
import { show_alerta } from '@/utils/alertasSwal';

const nameInput4 = ref(null);
const modal4 = ref(false);
const title4 = ref('');
const operation4 = ref(1);
const id4 = ref('');

const { tbprovincias } = defineProps({
    tbprovincias: {
        type: Object,
        required: true
    }
});

const form4 = useForm ({
    prov_nombre: '',

});
const form = useForm({
  numeroDocumento: '',
  razonSocial: '',
  direccion: '',
  distrito: '',
  provincia: '',
  departamento:'',
  estado:'',
  cli_direccion2:'',
  cli_observacion:'',
  tbprovincia_id: '',
});

const errors = ref({
  numeroDocumento: '',
});

const consultarReniec = async () => {
  try {
    const response = await fetch(`/consultar-reniec?numero=${form.numeroDocumento}`);

    if (response.ok) {
      const empresa = await response.json();
      form.razonSocial = empresa.razonSocial;
      form.direccion = empresa.direccion;
      form.distrito = empresa.distrito;
      form.provincia = empresa.provincia;
      form.departamento = empresa.departamento;
      form.estado = empresa.estado;
      errors.value.numeroDocumento = ''; // Limpiar errores si la consulta es exitosa
    } else if (response.status === 404) {
      errors.value.numeroDocumento = 'No se encontró información para el DNI ingresado.';
    } else {
      throw new Error('Error en la consulta a la API de Reniec.');
    }
  } catch (error) {
    console.error(error);
  }
};

const submitForm = () => {
    form.post(route('clientes.store'), {
        onSuccess: () => {
            show_alerta('El cliente se ha registrado correctamente', 'success')
        },
        onError: (errors) => {
            if(errors.response && errors.response.status) {
                show_alerta('Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.', 'error');
                console.error('Error HTTP:', errors.response.status);
            } else {
                show_alerta('Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.', 'error');
                console.error('Error desconocido:', errors);
            }
        }
    });
}


const openModal4 = (op,prov_nombre,tbprovincia)=>{
    modal4.value = true;
    nextTick( () => nameInput4.value.focus());
    operation4.value = op;
    id4.value = tbprovincia;
    if(op ===1){
        title4.value = 'Registrar ciudad';
    }
    else{
        title4.value = 'Actualizar ciudad';
        form4.prov_nombre=prov_nombre;
    }
}

const closeModal4 = () =>{
    modal4.value = false;
    form4.reset();
}

const provincias = ref([]);

const save4 = () => {
    if (operation4.value == 1) {
        form4.post(route('tbprovincias.store'), {
            onSuccess: () => { ok4('La ciudad ha sido registrada correctamente', 'success') }
        });
    }
    $.ajax({
        url: '/fn_obtenerProvinciasRegistradas',
        method: 'GET',
        success: function(response) {
            provincias.value = response;
        }
    });
}

const ok4 = (msj, icono) => {
    form4.reset();
    closeModal4();
    show_alerta(msj, icono)
};

const searchTerm = ref('');
const searchTermCodigoCli = ref('');
const filteredClientes = ref([]);
const selectedIndex = ref(-1);

const onInput = () => {
    selectedIndex.value = -1;
    if (searchTerm.value.length > 0) {
        const provinciasAUtilizar = provincias.value.length > 0 ? provincias.value : tbprovincias;
        filteredClientes.value = provinciasAUtilizar.filter(tbprovincia =>
            tbprovincia.prov_nombre.toLowerCase().includes(searchTerm.value.toLowerCase())
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


const selectCliente = (tbprovincia) => {
    searchTerm.value = tbprovincia.prov_nombre;
    searchTermCodigoCli.value = tbprovincia.id;
    form.tbprovincia_id = searchTermCodigoCli.value;
    filteredClientes.value = [];
};

</script>

<template>
    <AppLayout title="Registrar Cliente">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Registrar Cliente</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm  shadow-lg bg-white dark:bg-gray-800 rounded-lg">
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">
                            <div class="grid lg:grid-cols-3 grid-cols-1 sm:grid-cols-2 gap-y-3 sm:gap-x-6 mb-3">
                                <div>
                                    <InputLabel for="numeroDocumento" value="Número de RUC" />
                                    <div class="w-full flex justify-end items-end">
                                        <TextRuc v-model="form.numeroDocumento" type="text" id="numeroDocumento" placeholder="Ingrese y consulte el RUC"
                                         class="mt-2 w-full"/>
                                        <button @click="consultarReniec" type="button" class="bg-blue-600 w-12 h-[42px] rounded-r-lg hover:bg-blue-700"><i class='bx bx-search-alt text-white font-medium text-xl'></i></button>
                                    </div>
                                    <InputError :message="$page.props.errors.numeroDocumento" />
                                </div>    
                                <div>
                                    <InputLabel value="razon Social" />
                                    <TextInput v-model="form.razonSocial" required type="text" placeholder="" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel value="Estado"/>
                                    <TextInput v-model="form.estado" required type="text" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel value="Distrito" />
                                    <TextInput v-model="form.distrito" required type="text" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel value="Provincia" />
                                    <TextInput v-model="form.provincia" required type="text"  class="mt-2 w-full" />
                                </div>
                                <div>
                                    <InputLabel value="Departamento" />
                                    <TextInput v-model="form.departamento" required type="text" class="mt-2 w-full"/>
                                </div>
                                <div class="w-full flex items-end justify-center">
                                    <div class="w-full">
                                        <InputLabel for="cliente_id">Ciudad</InputLabel>
                                        <div class="relative">
                                            <TextInputCliente v-model="searchTerm" autocomplete="off" type="text" id="cliente_id" @input="onInput" placeholder="Ingresa nombre de ciudad"
                                                @keydown="onKeydown" required
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg h-[41.6px]" />
                                            <div id="contenedorDeClientes"
                                                class="w-full z-50 max-h-60 border border-gray-300 rounded-lg absolute overflow-auto text-sm divide-y divide-gray-200 bg-white dark:bg-gray-800"
                                                v-show="filteredClientes.length > 0">
                                                <div v-for="(tbprovincia, index) in filteredClientes" :key="tbprovincia.id"
                                                    :class="['text-gray-800 text-sm dark:text-white font-medium cursor-pointer overflow-hidden whitespace-nowrap text-ellipsis dark:hover:bg-gray-700 hover:bg-gray-200 option p-2', { 'bg-gray-200 dark:bg-gray-700': index === selectedIndex }]"
                                                    @click="selectCliente(tbprovincia)" @mouseover="updateSelection(index)">
                                                    {{ tbprovincia.prov_nombre }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <Button @click.prevent="() => openModal4(1)" class="bg-green-600 text-white mt-1 py-1 w-10 h-[41.5px] sm:h-[41.6px] rounded-r-lg">
                                        <i class="fas fa-plus mx-2"></i>
                                    </Button>
                                </div>
                                <div>
                                    <InputLabel value="direccion fiscal" />
                                    <TextInput v-model="form.direccion" type="text" required placeholder="" class="mt-2 w-full"/>
                                </div>
                                <div>
                                    <InputLabel for="cli_direccion2" value="direccion anexa 1"/>
                                    <TextInput v-model="form.cli_direccion2" type="text" id="cli_direccion2" required placeholder="Ingrese Direccion Anexa 1" class="mt-2 w-full"/>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="cli_observacion" value="Observaciones"/>
                                    <textarea id="cli_observacion" v-model="form.cli_observacion" rows="4" class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="Escriba las observaciones..."></textarea>
                                <InputError :message="$page.props.errors.cli_observacion" class=""/>
                            </div>
                            <div class="d-flex mt-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <ButtonResponsive>
                                        GUARDAR
                                    </ButtonResponsive>
                                    <Link :href="route('clientes.index')" class="inline-block bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700 md:w-min whitespace-nowrap w-full text-center">
                                        Cancelar
                                    </Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <ModalResponsive :show="modal4" @close="closeModal4">
            <div class="p-4 uppercase">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white text-center uppercase mb-4">{{ title4 }}</h2>
                <div class="p-1">
                    <div class="w-full">
                        <InputLabel for="prov_nombre" value="ciudad:" class="mb-2"></InputLabel>
                        <TextInput id="prov_nombre" ref="nameInput4" v-model="form4.prov_nombre" type="text" class="w-full"
                                placeholder="ciudad"></TextInput>
                        <InputError :message="form4.errors.prov_nombre" class="mt-2"></InputError>
                    </div>
                </div>
                <div class="p-1 flex justify-center">
                    <PrimaryButton :disabled="form4.processing" @click="save4">
                        <i class="fa-solid fa-save mx-1"></i>{{ operation4 == 1 ? 'Registrar' : 'Actualizar' }}
                    </PrimaryButton>
                    <DangerButton class="ml-3" :disabled="form4.processing"
                            @click="closeModal4">
                            Cancelar
                    </DangerButton>
                </div>
            </div>
        </ModalResponsive>
    </AppLayout>
</template>
