<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 font-bold rounded md:w-min whitespace-nowrap w-full text-center">
        <slot />
    </button>
</template>
