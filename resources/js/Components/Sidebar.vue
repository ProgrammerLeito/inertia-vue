<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex">
        <div class="h-full bg-gray-800" id="side-bar" :class="dataOpenSideBar==true?'side-bar-visible':'side-bar-close'">
            <div class="bg-gray-700 text-white dark:text-white h-[60px] flex justify-center items-center">
                <link href='http://127.0.0.1:8000/font/alphamalemodern.ttf' rel="stylesheet" type="text/css">
                <span v-show="dataOpenSideBar" class="gap-1 self-center text-2xl font-semibold whitespace-nowrap dark:text-white flex justify-center items-center">
                    <div class="border-dashed border-4 w-12 h-12 p-2 flex justify-center items-center rounded-full border-white">
                <span class="text-4xl flex justify-center items-center" id="B_logo">B</span></div><span id="nombre_Logo">ALINSA</span>
                </span>
                <!-- <div class="text-xl font-bold text-center flex items-center justify-center text-white h-full" v-show="dataOpenSideBar">Balinsa</div> -->
                <img src="/img/hombre.png" v-show="!dataOpenSideBar" class="p-1 w-8 h-8 rounded-full ring-2 ring-gray-100 dark:ring-gray-500" alt="">
            </div>

            <div class="flex flex-col justify-between h-[cal(100vh-3rem)] bg-gray-800">
                <div class="menu-man text-left px-2 whitespace-nowrap">
                    <div class="profile flex justify-center items-center text-center p-5">
                        <div class="text-center text-white" v-show="dataOpenSideBar">
                            <img src="/img/hombre.png" class="p-1 w-24 h-24 rounded-full ring-2 ring-gray-300 dark:ring-gray-500 mb-4" alt="">
                            <h5 class="text-xl font-medium leading-tight mb-2">Leonardo</h5>
                            <p class="text-gray-500">Admin</p>
                        </div>
                    </div>
                    <div class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <Link :href="route('dashboard')" :active="route().current('dashboard')" class="px-2 flex space-x-2"><span class='bi bi-people'></span><span v-show="dataOpenSideBar">Clientes</span></Link>
                    </div>
                    <div class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <Link href="" class="px-2 flex space-x-2"><span class='bi bi-telephone'></span><span v-show="dataOpenSideBar">Visitas y Llamadas</span></Link>
                    </div>
                    <div class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <Link href="" class="px-2 flex space-x-2"><span class='bi bi-cart'></span><span v-show="dataOpenSideBar">Productos</span></Link>
                    </div>
                    <div class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <Link href="" class="px-2 flex space-x-2"><span class='bi bi-gear-wide-connected'></span><span v-show="dataOpenSideBar">Servicio Tecnico</span></Link>
                    </div>
                    <div class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <Link href="" class="px-2 flex space-x-2"><span class='bi bi-graph-up-arrow'></span><span v-show="dataOpenSideBar">Cotizaciones</span></Link>
                    </div>
                    <div class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <Link href="" class="px-2 flex space-x-2"><span class='bi bi-bar-chart-line'></span><span v-show="dataOpenSideBar">Prestamos o Alquiler</span></Link>
                    </div>
                    <div class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <Link href="" class="px-2 flex space-x-2"><span class='bi bi-patch-check'></span><span v-show="dataOpenSideBar">Certificaciones</span></Link>
                    </div>
                    <div class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <Link href="" class="px-2 flex space-x-2"><span class='bi bi-gear'></span><span v-show="dataOpenSideBar">Configuraciones</span></Link>
                    </div>
                    <div class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <Link :href="route('categories.index')" :active="route().current('productos.*')" class="px-2 flex space-x-2"><span class='bi bi-inboxes'></span><span v-show="dataOpenSideBar">Recursos</span></Link>
                    </div>
                    <!-- <details class="py-3 rounded-sm cursor-pointer text-gray-400 hover:text-white">
                        <summary class="px-2 flex space-x-2"><span class='bi bi-feather'><span v-show="dataOpenSideBar">Cotizaciones</span></span></summary>
                        <p class="px-2 flex space-x-2 p-2 ms-4">Cotizaciones Pendientes</p>
                    </details> -->
                </div>
            </div>
            <div class="menu-man text-left px-2 justify-self-end whitespace-nowrap">
                <div class="py-3 rounded-md cursor-pointer text-gray-400 hover:text-white">
                    <a href="#" target="-blanck" class="px-2 flex space-x-2"><i class='bi bi-house-door' ></i><span v-show="dataOpenSideBar">Cerrar Sesion</span></a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        dataOpenSideBar:Boolean
    }
}
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