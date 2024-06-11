import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export function useModalRecomendacione() {

    const nameInput8 = ref(null);
    const modal8 = ref(false);
    const title8 = ref('');
    const operation8 = ref(1);
    const id8 = ref('');
    const form8 = useForm({
        descripcion: '',
    });

    const openModal8 = (op, descripcion, recomendacione) => {
        modal8.value = true;
        nextTick(() => nameInput8.value.focus());
        operation8.value = op;
        id8.value = recomendacione;
        if (op === 1) {
            title8.value = 'recomendaciones registrar';
        } else {
            title8.value = 'Actualizar recomendaciones';
            form8.descripcion = descripcion;
        }
    }

    const closeModal8 = () => {
        modal8.value = false;
        form8.reset();
    }

    const save8 = () => {
        if (operation8.value == 1) {
            form8.post(route('recomendaciones.store'), {
                onSuccess: () => { ok8('recomendaciones registrada') }
            });
        } else {
            form8.put(route('recomendaciones.update', id8.value), {
                onSuccess: () => { ok8('recomendaciones actualizado') }
            });
        }
    }

    const ok8 = (msj) => {
        form8.reset();
        closeModal8();
        const Toast = Swal.mixin({
            toast: true,
            position: "bottom-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "success",
            title: msj,
            customClass: {
                title: 'text-2xl font-bold tracking-widest ',
                icon: 'text-base font-bold tracking-widest ',
            },
        });
    };
    const deleteRecomendacione = (id, descripcion) => {
        const alerta = Swal.mixin({
            buttonsStyling: true
        });

        alerta.fire({
            title: '¿Estás seguro de eliminar :' + descripcion + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, eliminar',
            cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
            customClass: {
                title: 'text-xl font-bold tracking-widest ',
                icon: 'text-xl font-bold tracking-widest ',
                cancelButton: 'text-base tracking-widest ',
                confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                form8.delete(route('recomendaciones.destroy', id), {
                    onSuccess: () => {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "bottom-end",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: "success",
                            title: 'Éxito',
                            text: "registro recomendaciones eliminado exitosamente",
                            customClass: {
                                title: 'text-2xl font-bold tracking-widest ',
                                icon: 'text-base font-bold tracking-widest ',
                                text: 'bg-red-500 hover:bg-red-600 tracking-widest ',
                            },
                        });
                    }
                });
            }
        });
    }

    return{
        nameInput8,
        modal8,
        title8,
        operation8,
        id8,
        form8,
        openModal8,
        closeModal8,
        save8,
        deleteRecomendacione
    }
}
