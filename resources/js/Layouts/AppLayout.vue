<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Header from '@/Components/Header.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<script>

export default {
    components:{Sidebar},
    data(){
        return{
            openSidebar:false
        }
    },
    methods: {
        toggleSidebar(){
            this.openSidebar = !this.openSidebar
        }
    }
}
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />
        
        <Header class="" :dataOpenSideBar="openSidebar" :clickHamburger="toggleSidebar" />

        <div class="relative w-full flex md:pl-[50px] min-h-[calc(100vh-111px)] mt-[50px]">
            <Sidebar class="fixed left-0 top-[50px] z-[100000] shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)]" :dataOpenSideBar="openSidebar"/>
            <div class="w-full relative bg-gray-100 dark:bg-gray-700">
                <!-- Page Heading -->
                <header v-if="$slots.header" class="bg-white dark:bg-gray-800 z-[10000] fixed top-[50px] w-full dark:shadow-[0_1px_30px_2px_rgba(0,0,0,0.4)] shadow-[0_1px_30px_2px_rgba(0,0,0,0.2)]">
                    <div class="h-[60px] flex items-center mx-auto py-[17.5px] px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>
        
                <!-- Page Content -->
                <main class="mt-[60px]">
                    <slot />
                </main>
            </div>
        </div>
        <footer class="ml-auto bg-[#F7BE15] dark:bg-[#F7BE15] border-t border-gray-300 dark:border-gray-700">
            <div class="mx-auto">
                <div class="h-[60px] flex items-center justify-center"><h3 class="text-white dark:text-white text-center text-sm w-64 md:w-full"><b>Copyright © 2024 </b>Todos los derechos reservados. Diseñado y desarrollado por <b>Industrias Balinsa.</b></h3></div>
                <!-- <div class="h-20 flex items-center justify-center"><h3 class="text-gray-900 dark:text-gray-300 text-center text-sm w-64 md:w-full">Todos los derechos reservados. Diseñado y desarrollado por <b>Leonardo Garcia Jimenez</b></h3></div> -->
            </div>
        </footer>
    </div>
</template>
