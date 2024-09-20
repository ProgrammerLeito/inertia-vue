<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

// Agrega cualquier otra importación necesaria

const isDarkMode = ref(false); // Estado para controlar el modo oscuro

// Función para cambiar el modo oscuro
const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    document.documentElement.classList.toggle('dark', isDarkMode.value);
    localStorage.setItem('modoOscuro', isDarkMode.value);
};

// Verificar si el modo oscuro está activado en localStorage al cargar el componente
onMounted(() => {
    const modoOscuroLocalStorage = localStorage.getItem('modoOscuro') === 'true';
    isDarkMode.value = modoOscuroLocalStorage;
    document.documentElement.classList.toggle('dark', modoOscuroLocalStorage);
});

//Operacion para poder salir cerrar la sesion
const logout = () => {
    router.post(route('logout')); // Realiza la llamada para cerrar sesión
};
</script>

<template>
        <div class="min-h-[calc(100vh-50px)] bg-gray-800" id="side-bar" :class="dataOpenSideBar==true?'side-bar-visible':'side-bar-close'">
            <div class="bg-gray-700 text-white dark:text-white h-[60px] flex justify-center items-center">
                <!-- <link href='https://webbeta.balinsa.com/font/alphamalemodern.ttf' rel="stylesheet" type="text/css"> -->
                <!-- <link href='http://127.0.0.1:8000/font/alphamalemodern.ttf' rel="stylesheet" type="text/css"> -->
                <Link rel="stylesheet" :href="route('dashboard')">
                    <span v-show="dataOpenSideBar" class="gap-1 self-center text-2xl font-semibold whitespace-nowrap dark:text-white flex justify-center items-center">
                        <div class="border-dashed border-4 w-12 h-12 p-2 flex justify-center items-center rounded-full border-white">
                        <span class="text-4xl flex justify-center items-center" id="B_logo">B</span></div><span id="nombre_Logo">ALINSA</span>
                    </span>
                </Link>
                <div v-if="$page.props.auth.user.profile_photo_url">
                    <img v-show="!dataOpenSideBar" class="p-1 w-8 h-8 rounded-full ring-2 ring-gray-100" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                </div>
                <div v-else>
                    <img src="/img/hombre.png" v-show="!dataOpenSideBar" class="p-1 w-8 h-8 rounded-full ring-2 ring-gray-100" alt="">
                </div>
            </div>
            <div class="flex flex-col justify-between h-[cal(100vh-3rem)] bg-gray-800">
                <div class="menu-man text-left px-2 whitespace-nowrap">
                    <div :class="{'profile flex justify-center items-center text-center p-5': dataOpenSideBar, 'flex justify-center items-center text-center': !dataOpenSideBar }">
                        <div class="flex flex-col justify-center items-center gap-4 text-white" v-show="dataOpenSideBar">
                            <!-- Verifica si hay una imagen de perfil subida -->
                            <div v-if="$page.props.auth.user.profile_photo_url" class="shrink-0 me-3">
                                <img class="p-1 w-24 h-24 rounded-full ring-2 ring-gray-300" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>
                            <!-- Si no hay imagen subida, muestra la imagen predeterminada -->
                            <div v-else>
                                <img src="/img/hombre.png" class="p-1 w-24 h-24 rounded-full ring-2 ring-gray-300" alt="">
                            </div>
                            <div>
                                <Link :href="route('profile.show')"><h5 class="text-xl font-medium leading-tight">{{ $page.props.auth.user.name }}</h5></Link>
                                <p class="text-gray-500">{{ $page.props.auth.user.roles[0]?.name }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Scrool para sidebar -->
                    <div :class="{'scroll-sidebar h-[calc(100vh-415px)] overflow-x-auto': dataOpenSideBar, 'h-[calc(100vh-205px)] overflow-hidden': !dataOpenSideBar}">
                        <Link :href="route('clientes.index')" :active="route().current('clientes.*')" class="py-3 rounded-sm text-gray-400 px-2 flex space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"><span class='bi bi-people'><span v-show="dataOpenSideBar" class="ml-2">Listar Clientes</span></span></Link>
                        <!-- <details class="py-3 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-telephone'><span v-show="dataOpenSideBar" class="ml-2">Visitas y Llamadas</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Control de llamadas Diarias</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                        <Link :href="route('tbproductos.index')" :active="route().current('tbproductos.*')" class="py-3 rounded-sm text-gray-400 px-2 flex space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"><span class=""><i class="fa-solid fa-cart-plus"></i><span v-show="dataOpenSideBar" class="ml-2">Catalago de Productos</span></span></Link>
                        <!-- <details class="py-3 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-cart'><span v-show="dataOpenSideBar" class="ml-2">Productos</span></span></summary>
                            <Link :href="route('tbproductos.index')" :active="route().current('tbproductos.index')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Catalago de Productos</span></Link>
                        </details> -->
                        <Link :href="route('hojasservicios.index')" :active="route().current('hojasservicios.*')" class="py-3 rounded-sm text-gray-400 px-2 flex space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"><span class=""><i class="fa-solid fa-file-powerpoint ml-1"></i><span v-show="dataOpenSideBar" class="ml-2.5">Cuadro de Pendientes</span></span></Link>
                        <details class="py-3 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-gear-wide-connected'><span v-show="dataOpenSideBar" class="ml-2">Servicio Tecnico</span></span></summary>
                            <Link :href="route('hservicios.index')" :active="route().current('hservicios.index')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Listar Informe Tecnico</span></Link>
                            <Link :href="route('hservicios.create')" :active="route().current('hservicios.create')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Registrar Informe Tecnico</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Manuales y Calibracion</span> <img src="../../../public/img/one_drive.png" class="w-4"></Link>
                        </details>
                        <details class="py-3 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-graph-up-arrow'><span v-show="dataOpenSideBar" class="ml-2">Cotizaciones</span></span></summary>
                            <Link :href="route('cventas.index')" :active="route().current('cventas.index')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Listar Cotizacion por Venta</span></Link>
                            <Link :href="route('c_servicos.index')" :active="route().current('c_servicos.index')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Listar Cotizacion por Servicio</span></Link>
                        </details>
                        <details class="py-3 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-bar-chart-line'><span v-show="dataOpenSideBar" class="ml-2">Prestamos o Alquiler</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Listar Equipos</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details>
                        <details class="py-3 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-patch-check'><span v-show="dataOpenSideBar" class="ml-2">Certificaciones</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Registrar Solicitud Certificacion</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Solicitudes Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones por Caducar</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Lista Certificados emitidos TW</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details>
                        <details class="py-3 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-gear'><span v-show="dataOpenSideBar" class="ml-2">Configuraciones</span></span></summary>
                            <Link :href="route('users.index')" :active="route().current('users.*')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Usuarios</span></Link>
                        </details>
                        <details class="py-3 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-inboxes'><span v-show="dataOpenSideBar" class="ml-2">Recursos</span></span></summary>
                            <Link :href="route('categories.index')" :active="route().current('productos.*')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white"><span class="ml-4">Inventario</span></Link>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Reuniones Semanales</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Ocurrencias</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                        </details>
                    </div>
                    <!-- Scrool para sidebar -->
                </div>
            </div>
            <div class="menu-man text-left px-2 justify-self-end whitespace-nowrap border-t border-gray-700 dark:border-gray-700">
                <div class="py-3 rounded-md cursor-pointer text-gray-400 hover:text-white" @click="logout">
                    <span class="px-2 flex space-x-2 gap-4">
                        <i class='bi bi-door-open-fill'></i>
                        <span v-show="dataOpenSideBar">Cerrar Sesión</span>
                    </span>
                </div>
            </div>
            <div class="modo_oscuro w-full flex justify-between px-4 py-2 hover:text-white">
                <div class="info h-8 flex items-center w-36 text-gray-300 hover:text-gray-100 dark:text-gray-400 gap-4 overflow-hidden cursor-pointer" @click="toggleDarkMode">
                    <i class='bx bx-moon h-6 w-6 text-xl text-gray-400 hover:text-white'></i>
                    <span class="font-normal text-base text-gray-400">Modo Oscuro</span>
                </div>
                <div v-show="dataOpenSideBar" @click="toggleDarkMode" class="w-14 h-8 flex items-center justify-center cursor-pointer">
                    <div class="base_swith w-10 h-5 rounded-full relative flex items-center" :class="{ 'bg-green-600': isDarkMode, 'bg-gray-700': !isDarkMode }" >
                    <div :class="{ 'bg-white': isDarkMode, 'bg-white': !isDarkMode }" 
                        :style="{ left: isDarkMode ? 'calc(80% - 0.75rem)' : '0.25rem' }"
                        class="circulo_swith duration-300 w-4 h-4 rounded-full bg-gray-100 absolute"></div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
export default {
  props: {
    dataOpenSideBar: Boolean
  },
  setup() {
    const isDarkMode = ref(false);
    const switchPosition = computed(() => isDarkMode.value ? 'calc(80% - 0.75rem)' : '0.25rem');

    const toggleDarkMode = () => {
      isDarkMode.value = !isDarkMode.value;
      document.documentElement.classList.toggle('dark', isDarkMode.value);
      localStorage.setItem('modoOscuro', isDarkMode.value);
    };

    onMounted(() => {
      const modoOscuroLocalStorage = localStorage.getItem('modoOscuro') === 'true';
      isDarkMode.value = modoOscuroLocalStorage;
      document.documentElement.classList.toggle('dark', modoOscuroLocalStorage);
    });

    return {
      isDarkMode,
      toggleDarkMode,
      switchPosition
    };
  }
};
</script>

<style>
.tooltip-text {
    font-size: 10px !important;
}

#side-bar {
    overflow: hidden;
    transition: 300ms;
}

.side-bar-visible {
    width: 250px !important;
}

.side-bar-close {
    width: 50px !important;
}
@media (max-width: 768px) {
    .side-bar-close {
        width: 0px !important;
    }
}

</style>

<style scoped>
#nombre_Logo {
    font-family: "AlphaMaleModern";
    letter-spacing: 3px;
}

#B_logo {
    font-family: "AlphaMaleModern";
    transform: translateX(2px) translateY(1px);
}
</style>