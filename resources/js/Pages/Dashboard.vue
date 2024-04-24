<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref } from 'vue';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJs, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';
ChartJs.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);
 
const props = defineProps({
    data: { type: Array }
});
 
const tbproductos = ref([]);
const tbcategorias = ref([]);
const charData = ref([]);
const chartOptions = ref([]);
const colors = ref([]);
 
const randomColor = () => `rgb(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)})`;
 
props.data.forEach((row) => {
    tbproductos.value.push(row.nombre); // Suponiendo que 'nombre' es el nombre del producto
    tbcategorias.value.push(row.count);
    colors.value.push(randomColor());
});
 
chartOptions.value = { responsive: true };
charData.value = {
    labels: tbproductos.value,
    datasets: [
        { label: 'Categorías', data: tbcategorias.value, backgroundColor: colors.value }
    ]
};
 
</script>
 
<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">
                Inicio
            </h2>
        </template>
 
        <div class="py-5">
            <div class="grid grid-cols-1 gap-y-3 sm:grid-cols-3 sm:gap-x-6 mb-3">
            <div class="max-w-md mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div>
                        <p class="text-center">Grafico de Inventario</p>
                    </div>
                    <Pie :data="charData" :options="chartOptions"></Pie>
                </div>
            </div>
            </div>
        </div>
    </AppLayout>
</template>