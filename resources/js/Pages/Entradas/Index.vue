<script>
export default {
    name: 'EntradasIndex'
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'

defineProps({
    entradas: {
        type : Object,
        required: true
    },
    productos: {
        type : Object,
        required: true
    }
});

const deleteEntradas = id =>{
    if (confirm('Are you sure?')){
        Inertia.delete(route('entradas.destroy', id))
    }
}

</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">Entradas de Productos</h1>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-600">
                    <div class="flex justify-between">
                        <Link :href="route('categories.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded">
                            Regresar
                        </Link>
                        <Link :href="route('entradas.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded">
                            Ingresar Entrada
                        </Link>
                    </div>
                    <div class="mt-4">
                        <ul role="list" class="divide-y divide-gray-100">
                            <li class="flex justify-between gap-x-6 py-5" v-for="entrada in entradas.data">
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ entrada.producto?.insumo  }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ entrada.cantidad }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ entrada.fecha }}</p>
                                    </div>
                                </div>
                                <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                    <p class="text-md leading-6 text-gray-900">
                                        <Link class="py-2 px-4 text-green-500" :href="route('entradas.edit', entrada.id)"><i class="bi bi-pencil-square"></i></Link>
                                        <Link class="py-2 px-4 text-red-500" @click="deleteEntradas(entrada.id)"><i class="bi bi-trash3"></i></Link>
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>