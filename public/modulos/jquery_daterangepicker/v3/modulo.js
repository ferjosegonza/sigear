loadjs.ready("JQUERY_DATERANGEPICKER", () => {
    
    // configuramos las opciones por defecto
    $.fn.daterangepicker.defaultOptions = {
        "opens": "right",
        "autoUpdateInput": true,
        "drops": "down",
        "autoApply": true,
        "alwaysShowCalendars": true,
        "showCustomRangeLabel": false,
        "linkedCalendars": true,
        "timePicker24Hour": true,
        "timePickerIncrement": 15,
        "ranges": {
            "Hoy": [moment(), moment()],
            "Ayer": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            "Últimos 7 días": [moment().subtract(6, 'days'), moment()],
            "Últimos 30 días": [moment().subtract(29, 'days'), moment()],
            "Mes Actual": [moment().startOf('month'), moment().endOf('month')],
            "El mes pasado": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Seleccionar",
            "cancelLabel": "Cancelar",
            "fromLabel": "Desde",
            "toLabel": "Hasta",
            "customRangeLabel": "Rango Manual",
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ]
        },
    };

    // indicar modulo se cargo correctamente
    loadjs.done('JQUERY_DATERANGEPICKER_MODULO');
});