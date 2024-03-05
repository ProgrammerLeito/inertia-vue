    <script>
    export default {
        name: 'ProductosEdit'
    }

    </script>

    <script setup>
    import { useForm } from '@inertiajs/vue3';
    import AppLayout from '@/Layouts/AppLayout.vue';
    import SalidasForm from '@/Components/Salidas/Form.vue';

    const props = defineProps({
        productos: {
            type: Object,
            required: true,
        },
        salidas: {
            type: Array,
            required: true,
        },
    })

    const form = useForm({
        empresa: props.salidas.empresa,
        unidad_salida: props.salidas.unidad_salida,
        comentario_salida: props.salidas.comentario_salida,
        tecnico: props.salidas.tecnico,
        fecha: props.salidas.fecha,
        producto_id: props.salidas.producto_id
    })

    </script>

    <template>
        <AppLayout title="Editar Producto">
            <template #header>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">Editar Producto</h1>
            </template>

            <div class="py-2 md:py-4 max-h-[calc(100vh-185px)] overflow-auto">
                <div class="h-full mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"></div>
                    <div class="p-6 bg-white border-b border-gray-800">
                        <SalidasForm :updating="true" :form="form" :salidas="salidas" :productos="productos" @submit="form.put(route('salidas.update', salidas.id))"/>
                    </div>
                </div>
            </div>
        </AppLayout>
    </template>