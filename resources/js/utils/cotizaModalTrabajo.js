import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export function loaddat(url) {
    return new Promise((resolve, reject) => {
        fetch(url)
            .then(response => response.json())
            .then(data => resolve(data))
            .catch(error => reject(error));
    });
}

export function useModalTrabajo() {
    const nameInput4 = ref(null);
    const modal4 = ref(false);
    const title4 = ref('');
    const operation4 = ref(1);
    const id4 = ref('');
    const form4 = useForm({
        descripcion: '',
        ctrabajo_id: ''
    });

    const openModal4 = (op, descripcion, ctrabajo, trealizado) => {
        modal4.value = true;
        nextTick(() => nameInput4.value.focus());
        operation4.value = op;
        id4.value = trealizado;
        if (op === 1) {
            title4.value = 'Registrar trabajo realizados';
        } else {
            title4.value = 'Actualizar trabajo realizado';
            form4.descripcion = descripcion;
            form4.ctrabajo_id = ctrabajo;
        }
    }

    const closeModal4 = () => {
        modal4.value = false;
        form4.reset();
    }

    const save4 = () => {
        if (operation4.value == 1) {
            form4.post(route('trealizados.store'), {
                onSuccess: () => { ok4('trabajo realizados registrada') }
            });
        } else {
            form4.put(route('trealizados.update', id4.value), {
                onSuccess: () => ok4('Trabajo realizado actualizado')
            });
        }
    }

    const ok4 = (msj) => {
        form4.reset();
        closeModal4();
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
    const deleteTrealizado = (id, descripcion) => {
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
                form4.delete(route('trealizados.destroy', id), {
                    onSuccess: () => {
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
                            title: 'Éxito',
                            text: "registro trealizados eliminado exitosamente.",
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

    return {
        nameInput4,
        modal4,
        title4,
        operation4,
        id4,
        form4,
        openModal4,
        closeModal4,
        save4,
        deleteTrealizado
    };
}
