loadjs.ready("MOMENT_TIMEZONE", () => {
    // indicar modulo se cargo correctamente
    moment.tz.setDefault("America/Buenos_Aires")

    loadjs.done('MOMENT_TIMEZONE_MODULO');
});
