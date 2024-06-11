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

export function useModalObservacione() {
    const nameInput6 = ref(null);
    const modal6 = ref(false);
    const title6 = ref('');
    const operation6 = ref(1);
    const id6 = ref('');
    const form6 = useForm({
        descripcion: '',
        categoria: ''
    });

    const openModal6 = (op, descripcion, categoria, observacione) => {
        modal6.value = true;
        nextTick(() => nameInput6.value.focus());
        operation6.value = op;
        id6.value = observacione;
        if (op === 1) {
            title6.value = 'observaciones condicione';
        } else {
            title6.value = 'Actualizar observaciones realizado';
            form6.descripcion = descripcion;
            form6.categoria = categoria;
        }
    }

    const closeModal6 = () => {
        modal6.value = false;
        form6.reset();
    }

    const save6 = () => {
        if (operation6.value == 1) {
            form6.post(route('observaciones.store'), {
                onSuccess: () => { ok6('observaciones registrada') }
            });
        } else {
            form6.put(route('observaciones.update', id6.value), {
                onSuccess: () => ok6('observaciones actualizado')
            })
        }
    }

    const ok6 = (msj) => {
        form6.reset();
        closeModal6();
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
    const deleteObservacion = (id, descripcion) => {
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
                form6.delete(route('observaciones.destroy', id), {
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
                            text: "registro observaciones eliminado exitosamente",
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
        nameInput6,
        modal6,
        title6,
        operation6,
        id6,
        form6,
        openModal6,
        closeModal6,
        save6,
        deleteObservacion
    };
}
