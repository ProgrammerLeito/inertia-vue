<template>
    <div class="w-full bg-[#F7BE15] fixed top-0 z-[100000]">
        <div class="flex justify-between items-center h-[50px]">
            <div class="flex justify-center items-center w-[50px] h-full cursor-pointer hover:bg-yellow-300" @click="clickHamburger">
                <i class='bx bx-menu'></i>
            </div>
            <div class="py-2">
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