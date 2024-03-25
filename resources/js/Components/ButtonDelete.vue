<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" lass="py-2 px-4 text-red-500">
        <slot />
    </button>
</template>
