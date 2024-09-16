export function checkTableVisibility() {
    const tables = [
        { tbody: '#tbodyContenedorHojasServiciosBalanzas', table: '#construirTablaBalanzas' },
        { tbody: '#tbodyContenedorHojasServiciosTermometros', table: '#construirTablaTermometros' },
        { tbody: '#tbodyContenedorHojasServiciosPesas', table: '#construirTablaPesas' }
    ];

    tables.forEach(({ tbody, table }) => {
        const hasRows = $(tbody).find('tr').length > 0; // >0 desde aqui comienza a verificar si es que existen datos
        if (hasRows) {
            $(table).show();
        } else {
            $(table).hide();
        }
    });
}
