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

export function useModalCondicione() {
    const nameInput5 = ref(null);
    const modal5 = ref(false);
    const title5 = ref('');
    const operation5 = ref(1);
    const id5 = ref('');
    const form5 = useForm({
        descripcion: '',
        categoria: ''
    });

    const openModal5 = (op, descripcion, categoria, condicione) => {
        modal5.value = true;
        nextTick(() => nameInput5.value.focus());
        operation5.value = op;
        id5.value = condicione;
        if (op === 1) {
            title5.value = 'Registrar condicione';
        } else {
            title5.value = 'Actualizar condiciones realizado';
            form5.descripcion = descripcion;
            form5.categoria = categoria;
        }
    }

    const closeModal5 = () => {
        modal5.value = false;
        form5.reset();
    }

    const save5 = () => {
        if (operation5.value == 1) {
            form5.post(route('condiciones.store'), {
                onSuccess: () => { ok5('condiciones registrada') }
            });
        } else {
            form5.put(route('condiciones.update', id5.value), {
                onSuccess: () => ok5('Trabajo realizado actualizado')
            });
        }
    }

    const ok5 = (msj) => {
        form5.reset();
        closeModal5();
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
    const deleteCondicione = (id, descripcion) => {
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
                form5.delete(route('condiciones.destroy', id), {
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
                            text: "registro condiciones eliminado exitosamente.",
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
        nameInput5,
        modal5,
        title5,
        operation5,
        id5,
        form5,
        openModal5,
        closeModal5,
        save5,
        deleteCondicione
    };
}
