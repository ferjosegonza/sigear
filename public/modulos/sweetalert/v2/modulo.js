loadjs.ready("SWEETALERT", () => {
    const originalSwal = swal;
    swal = function (param) {
        let payload = {};
        let modoHTML = false;

        if (typeof param === "object" && typeof param.html === "string") {
            modoHTML = true;
            const el = document.createElement("DIV");
            el.addClass("swal__html__container");
            el.innerHTML = param.html;
            param.content = el;
            delete param.html;



            const inputs = el.querySelectorAll("input, select, textarea");
            inputs.payload = {};
            inputs.forEach(function (input) {
                input.addClass("swal-content__input");

                if (input.name === "") {
                    APP.warn("swal input no posee atributo name", input);
                    return;
                }

                // setear valores iniciales
                payload[input.name] = input.value;

                input.addEventListener("input", function (event) {
                    // actualizar payload en cada update
                    payload[event.target.name] = event.target.value;
                    swal.setActionValue({
                        confirm: payload
                    });
                }, false);
            });
        }

        const p = originalSwal.apply(this, arguments);

        if (modoHTML) {
            // inicializar action value
            swal.setActionValue({
                confirm: payload
            });
        }

        return p;
    };

    // copiamos todas las propiedades
    for (let key in originalSwal)
        swal[key] = originalSwal[key];



    window.swalError = function(xhr) {
        // en caso de haber un loader lo finalizamos
        APP.endLoading();
        APP.endProcessing();

        // en caso de request abortada retornamos
        if (xhr.statusText == 'abort') {
            return;
        }

        let html = `<p>${xhr.responseJSON.error.message}</p>`;

        // metadata
        const listMetadata = typeof this.listMetadata === 'undefined' ? false : this.listMetadata;
        if (listMetadata && xhr.responseJSON.error.metadata) {
            const keys = Object.keys(xhr.responseJSON.error.metadata)
            if (keys) {
                html += `<ul style='list-style: none;padding: 0;margin: 0;text-align: left;'>`;
                keys.forEach(key => {
                    html += `<li>- ${xhr.responseJSON.error.metadata[key]}</li>`;
                });
                html += `</ul>`;
            }
        }

        // then function
        const fnThen = typeof this.fnThen === 'undefined' ? () => {} : this.fnThen;

        // mostramos swal con error
        swal({
            icon: "error",
            html: html,
            dangerMode: true,
            buttons:[false,'Aceptar']
        }).then(fnThen.bind({error: xhr.responseJSON.error}));
    };

    // indicar modulo se cargo correctamente
    loadjs.done('SWEETALERT_MODULO');
});

