<template>
    <div class="w-full bg-[#F7BE15] fixed top-0 z-[100000]">
        <div class="flex pr md:pr-8 pr-4 justify-between items-center h-[50px]">
            <div class="flex justify-center items-center w-[50px] h-full cursor-pointer hover:bg-yellow-300" @click="clickHamburger">
                <i class='bx bx-menu'></i>
            </div>
            <div class="flex pr-1 gap-6 justify-between items-center h-[50px]">
                <Link :href="route('hojasservicios.index')" class="cursor-pointer">
                    <div class="flex gap-2 md:gap-1">
                        <div class="border-dotted md:border-0 border-4 md:w-7 w-10 h-10 flex items-center border-white">
                            <img src="../../../public/img/hoja_servicio.png" alt="" class="ml-1">
                        </div>
                        <span class="hidden sm:flex whitespace-nowrap font-bold items-center">Hoja de servicio</span>
                    </div>
                </Link>
                <Link :href="route('profile.show')" class="flex items-center gap-2 cursor-pointer">
                    <img class="p-1 w-8 h-8 rounded-full ring-2 ring-gray-100" :src="$page.props.auth.user.profile_photo_url">
                    <h1 class="font-bold sm:flex hidden md:text-base text-sm uppercase text-gray-800 leading-tight">
                        {{ $page.props.auth.user.name }}</h1>
                </Link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        dataOpenSideBar: Boolean,
        clickHamburger: Function,
    }
}
</script>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
</script>