import Swal from "sweetalert2";
 
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
 
 
export function show_alerta(msj, icono, foco = '') {
    if (foco !== '') {
        document.getElementById(foco).focus();
    }
    Toast.fire({
        title: msj,
        icon: icono
    });
}
 
export function show_confirmacion(titulo, texto) {
    return Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            icon: 'text-xs',
            popup: 'small-alert-popup text-sm',
            title: 'small-alert-title text-sm',
            htmlContainer: 'small-alert-text text-sm',
            confirmButton: 'small-alert-confirm-button text-xs',
            cancelButton: 'small-alert-cancel-button text-xs'
        }
    });
}