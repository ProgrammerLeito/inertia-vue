import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export function useModalDocumento() {

    const nameInput7 = ref(null);
    const modal7 = ref(false);
    const title7 = ref('');
    const operation7 = ref(1);
    const id7 = ref('');
    const form7 = useForm({
        descripcion: '',
    });

    const openModal7 = (op, descripcion, documento) => {
        modal7.value = true;
        nextTick(() => nameInput7.value.focus());
        operation7.value = op;
        id7.value = documento;
        if (op === 1) {
            title7.value = 'documentos registrar';
        } else {
            title7.value = 'Actualizar documento';
            form7.descripcion = descripcion;
        }
    }

    const closeModal7 = () => {
        modal7.value = false;
        form7.reset();
    }

    const save7 = () => {
        if (operation7.value == 1) {
            form7.post(route('documentos.store'), {
                onSuccess: () => { ok7('documentos registrada') }
            });
        } else {
            form7.put(route('documentos.update', id7.value), {
                onSuccess: () => { ok7('documento actualizado') }
            });
        }
    }

    const ok7 = (msj) => {
        form7.reset();
        closeModal7();
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
    const deleteDocumento = (id, descripcion) => {
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
                form7.delete(route('documentos.destroy', id), {
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
                            text: "registro documento eliminado exitosamente",
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
        nameInput7,
        modal7,
        title7,
        operation7,
        id7,
        form7,
        openModal7,
        closeModal7,
        save7,
        deleteDocumento
    }
}
