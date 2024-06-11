import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export function loaddata(url) {
    return new Promise((resolve, reject) => {
        fetch(url)
            .then(response => response.json())
            .then(data => resolve(data))
            .catch(error => reject(error));
    });
}

export function useModalPlantilla() {
    const nameInput3 = ref(null);
    const modal3 = ref(false);
    const title3 = ref('');
    const operation3 = ref(1);
    const id3 = ref('');
    const form3 = useForm({
        descripcion: '',
    });

    const openModal3 = (op, plantilla) => {
        modal3.value = true;
        nextTick(() => nameInput3.value.focus());
        operation3.value = op;
        id3.value = plantilla ? plantilla.id : null;
        if (op === 1) {
            title3.value = 'Registrar plantilla';
        } else {
            title3.value = 'Actualizar plantilla';
            form3.descripcion = plantilla.descripcion;
        }
    };

    const closeModal3 = () => {
        modal3.value = false;
        form3.reset();
    };

    const save3 = () => {
        if (operation3.value === 1) {
            form3.post(route('plantillas.store'), {
                onSuccess: () => { ok3('Plantilla registrada') }
            });
        } else {
            form3.put(route('plantillas.update', id3.value), {
                onSuccess: () => { ok3('Plantilla actualizada') }
            });
        }
    };

    const ok3 = (msj) => {
        form3.reset();
        closeModal3();
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

    const deletePlantilla = (id, descripcion) => {
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
                form3.delete(route('plantillas.destroy', id), {
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
                            text: "Registro de plantilla eliminado exitosamente.",
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
    };

    return {
        nameInput3,
        modal3,
        title3,
        operation3,
        id3,
        form3,
        openModal3,
        closeModal3,
        save3,
        deletePlantilla
    };
}
