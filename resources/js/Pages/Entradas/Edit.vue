    <script>
    export default {
        name: 'EntradasEdit'
    }

    </script>

    <script setup>
    import { useForm } from '@inertiajs/vue3';
    import AppLayout from '@/Layouts/AppLayout.vue';
    import EntradasForm from '@/Components/Entradas/Form.vue';

    const props = defineProps({
        productos: {
            type: Object,
            required: true,
        },
        entradas: {
            type: Array,
            required: true,
        },
    })

    const form = useForm({
        cantidad: props.entradas.cantidad,
        fecha: props.entradas.fecha,
        producto_id: props.entradas.producto_id
    })

    </script>

    <template>
        <AppLayout title="Editar Producto">
            <template #header>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">Editar Producto</h1>
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"></div>
                    <div class="p-6 bg-white border-b border-gray-800">
                        <EntradasForm :updating="true" :form="form" :entradas="entradas" :productos="productos" @submit="form.put(route('entradas.update', entradas.id))"/>
                    </div>
                </div>
            </div>
        </AppLayout>
    </template>