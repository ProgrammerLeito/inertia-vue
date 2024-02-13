<script>
export default {
    name: 'SalidasIndex'
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'

defineProps({
    salidas: {
        type : Object,
        required: true
    },
    productos: {
        type : Object,
        required: true
    }
});

const deleteSalidas = id =>{
    if (confirm('Are you sure?')){
        Inertia.delete(route('salidas.destroy', id))
    }
}

</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">Salidas de Productos</h1>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-600">
                    <div class="flex justify-between">
                        <Link :href="route('categories.index')" class="text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded">
                            Regresar
                        </Link>
                        <Link :href="route('salidas.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 py-2 px-4 rounded">
                            Ingresar Salida
                        </Link>
                    </div>
                    <div class="mt-4">
                        <ul role="list" class="divide-y divide-gray-100">
                            <li class="flex justify-between gap-x-6 py-5" v-for="salida in salidas.data">
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ salida.empresa }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ salida.salida_id }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ salida.unidad_salida }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ salida.comentario_salida }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ salida.tecnico }}</p>
                                    </div>
                                </div>
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold leading-6 text-gray-900">{{ salida.fecha }}</p>
                                    </div>
                                </div>
                                <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                    <p class="text-md leading-6 text-gray-900">
                                        <Link class="py-2 px-4 text-green-500"><i class="bi bi-pencil-square"></i></Link>
                                        <Link class="py-2 px-4 text-red-500" @click="deleteSalidas(salida.id)"><i class="bi bi-trash3"></i></Link>
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