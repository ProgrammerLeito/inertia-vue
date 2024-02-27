<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
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
    <div class="">
        <Head :title="title" />

        <Banner />
        
        <Header :dataOpenSideBar="openSidebar" :clickHamburger="toggleSidebar" />

        <div class="relative w-full flex md:pl-[50px] min-h-screen">
            <Sidebar class="absolute left-0 top-0 z-[100000]" :dataOpenSideBar="openSidebar"/>
            <div class="w-full relative">
                <!-- Page Heading -->
                <header v-if="$slots.header" class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>
        
                <!-- Page Content -->
                <main>
                    <slot />
                </main>
            </div>
        </div>
        
    </div>
</template>
