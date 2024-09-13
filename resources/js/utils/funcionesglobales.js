import { format, parse } from 'date-fns';
import { es } from 'date-fns/locale';

export function formatDate(dateString) {
    if (!dateString) return 'Fecha inválida';
    const parsedDate = parse(dateString, 'yyyy-MM-dd', new Date());
    if (isNaN(parsedDate)) return 'Fecha inválida';
    return format(parsedDate, 'EEEE dd/MM/yyyy', { locale: es });
}

export function trimLeadingZeros(value) {
    if (!value) return '';
    return value.replace(/^0+/, '');
}

export function getFirstLetter(value) {
    if (!value) return '';
    return value.charAt(0).toUpperCase();
}