<template>
    <div class="w-full bg-[#F7BE15]">
        <div class="flex justify-between items-center h-[50px]">
            <div class="p-3 cursor-pointer hover:bg-yellow-300" @click="clickHamburger">
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