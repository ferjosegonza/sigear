loadjs.ready("DATATABLE", () => {
    // log defaults
    //APP.debug($.fn.dataTable.defaults);

    /**
     * --config custom--
     * header: false
     * footer: false
     * titulo: "TITULO TABLA"
     */
    $.fn.dataTable.ext.errMode = 'none';

    $.extend($.fn.dataTable.defaults, {
        aLengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]], // lengthMenu   // tamaños de cantidad de paginas
        iDisplayLength: 50, // pageLength   // cantidad de paginas por defecto

        bLengthChange: false, // lengthChange // muestra selector de cantidad de paginas
        bInfo: true, // info   // muestra cantidad de registros en la tabla
        bPaginate: true, // paging  // muestra paginado

        bDeferRender: true, // deferRender  //
        bSort: false, // ordering     // mostrar orden en columnas
        bProcessing: true, // processing   // mostrar cartel de procesando datos de tabla
        bFilter: false, // searching    // mostrar buscador general de la tabla

        bServerSide: true, // serverSide   // modo de proceso ServerSide

        bScrollCollapse: true, // scrollCollapse // usado en combinacion con scrollY

        dom: "<'row'<'col-sm-12'tr>><'datatable__footer' i l p>",
        oLanguage: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ning&uacuten dato disponible en esta tabla",
            "sInfo": "Mostrando del _START_ al _END_ de _TOTAL_ registros",
            "sInfoEmpty": "No hay registros",
            "sInfoFiltered": "(Total de registros: _MAX_)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sThousands": ".",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "&uacute;ltimo",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });

    // override DataTable with custom function
    const originalDataTable = $.fn.DataTable;
    $.fn.DataTable = function (config) {
        // generamos y/o seleccionamos elementos de tabla
        const eTable = this.get(0);
        let eTableContainer = eTable.findAncestor("table-container");
        let eTableContainerHeader, eTableFiltros, eTableTitulo, eTableAcciones, eTableContainerBody, eTableBuscador;

        if (!eTableContainer) {
            eTableContainer = document.createElement('div');
            eTableContainer.addClass("table-container");

            eTableContainerHeader = document.createElement('div');
            eTableContainerHeader.addClass("table__header");
            eTableContainer.appendChild(eTableContainerHeader);

            eTableFiltros = document.createElement('div');
            eTableFiltros.addClass("filtros");
            eTableContainerHeader.appendChild(eTableFiltros);

            eTableTitulo = document.createElement('div');
            eTableTitulo.addClass("title");
            eTableContainerHeader.appendChild(eTableTitulo);

            eTableAcciones = document.createElement('div');
            eTableAcciones.addClass("actions");
            eTableContainerHeader.appendChild(eTableAcciones);

            eTableBuscador = document.createElement('div');
            eTableBuscador.addClass("table__search");
            eTableBuscador.innerHTML = `
                <input type="text" placeholder="Buscar...">
                <button title="Cerrar Buscador"><i class="fas fa-times"></i></button>
            `;
            eTableContainerHeader.appendChild(eTableBuscador);

            eTableContainerBody = document.createElement('div');
            eTableContainerBody.addClass("table__body");
            eTableContainer.appendChild(eTableContainerBody);

            eTable.parentNode.insertBefore(eTableContainer, eTable);
            eTableContainerBody.appendChild(eTable);
        } else {
            eTableContainerHeader = eTableContainer.querySelector(".table__header");
            eTableFiltros = eTableContainer.querySelector(".filtros");
            eTableTitulo = eTableContainer.querySelector(".title");
            eTableAcciones = eTableContainer.querySelector(".actions");
            eTableBuscador = eTableContainer.querySelector(".table__search");
            eTableContainerBody = eTableContainer.querySelector(".table__body");
        }

        if (config.titulo !== undefined) {
            if (eTableTitulo === null) {
                APP.warn(eTable.id, "parametro de configuracion 'titulo' requiere que tabla posea un elemento .title");
            } else {
                eTableTitulo.innerText = config.titulo;
            }
        }

        if (config.header === false && eTableContainerHeader !== null) {
            eTableContainerHeader.addClass("hidden");
        }

        if (config.footer === false) {
            config.info = false;
            config.paging = false;
        }

        if (typeof config.local !== "undefined") {
            config.serverSide = false;
        }
        let MODO_SERVER = false;
        if (config.serverSide) {
            MODO_SERVER = true;
        }

        let MODO_BUSCADOR = false;
        if (config.searching) {
            MODO_BUSCADOR = true;
        }
        config.searching = true; // activamos buscador interno de datatable para que siempre este activo

        const WITH_BOTTOM = (config.info !== false && config.paging !== false);

        if (config.scrollY === "" || config.scrollY === false) {
            config.scrollCollapse = false;
            eTableContainer.addClass("table-container--sin-scroll");
        } else if (config.scrollY === undefined) {
            const tableHeight = getTableScrollHeight(eTableContainer, WITH_BOTTOM);
            if (tableHeight !== null) {
                config.scrollY = tableHeight + "px";
            } else {
                config.scrollCollapse = false;
            }
        }

        const SERVER_PARAMS_HOOKS = [];
        const setServerParamHook = function (hook) {
            SERVER_PARAMS_HOOKS.push(hook);
        };
        if (typeof config.ajax !== "undefined") {
            // defaults
            config.ajax.cache = false;

            if (config.ajax.method) {
                config.ajax.type = config.ajax.method;
            } else if (config.ajax.type) {
                config.ajax.method = config.ajax.type;
            } else {
                config.ajax.method = "POST";
                config.ajax.type = "POST";
            }

            // agregar parametros para enviar al server
            config.fnServerParams = function (data) {
                const d = data;

                // primera vez de los filtros
                if (config.filtros) d.filtros = config.filtros;

                // primera vez de los ordenes
                if (config.ordenes && typeof config.ordenes === 'object') {
                    d.ordenes = [];
                    Object.keys(config.ordenes).forEach(function (key) {
                        let value = config.ordenes[key].toLowerCase();
                        if (value === 'asc' || value === 'desc') {
                            let o = {};
                            o[key] = value;
                            d.ordenes.push(o);
                        }
                    });
                }

                // corremos funciones
                SERVER_PARAMS_HOOKS.forEach(h => h(d));


                // limpiamos search y colums que no utilizamos actualmente
                delete d.search;
                delete d.columns;
                delete d.orders;

                return d;
            };
        }

        // callbacks (https://datatables.net/reference/option)
        // -- initComplete
        let INIT_COMPLETED = false;
        const INIT_COMPLETE_HOOKS = [];
        const setInitCompleteHook = function (hook) {
            if (INIT_COMPLETED) {
                hook();
            } else {
                INIT_COMPLETE_HOOKS.push(hook);
            }
        };
        config.initComplete = function (settings, json) {
            INIT_COMPLETE_HOOKS.forEach(h => h());
            INIT_COMPLETED = true;
        };

        // instanciamos datatable
        const t = originalDataTable.apply(this, arguments);

        // guardamos referencia a la tabla en APP
        APP.DATATABLES = APP.DATATABLES || [];
        APP.DATATABLES[eTable] = t;

        const eTableScrollHead = eTableContainerBody.querySelector(".dataTables_scrollHead");
        const eTableScrollBody = eTableContainerBody.querySelector(".dataTables_scrollBody");

        let lastXHR = null;
        t.on('error.dt', function (e, settings, techNote, message) {
            APP.endLoading();
            APP.endProcessing();

            // en caso de request abortada retornamos
            if (lastXHR && lastXHR.statusText == 'abort') {
                return;
            }

            if (!lastXHR || techNote != '7') {
                swal({
                    icon: "error",
                    title: "DATATABLE ERROR " + techNote,
                    text: message
                });
                return;
            }

            swal({
                icon: "error",
                text: lastXHR.responseJSON.error.message
            });
            lastXHR = null;
        });

        t.on('xhr.dt', function (e, settings, json, xhr) {
            lastXHR = xhr;
        });

        // ---------------------------------------------------------------
        // UTILS
        // ---------------------------------------------------------------
        t.recargar = function () {
            t.ajax.reload(null, false);
            if (typeof config.onRecarga === "function")
                config.onRecarga.apply(t);
        }.bind(t);

        t.setearTitulo = function (titulo) {
            eTableTitulo.innerText = titulo;
        }.bind(t);

        // ---------------------------------------------------------------
        // FILTROS
        // ---------------------------------------------------------------
        t._filtros = {
            data: config.filtros || {},
            dom: {
                contenedor: eTableFiltros,
                table_headers: [],
                botones: []
            }
        };

        // insertar datos de filtros a xhr
        setServerParamHook(function (d) {
            d.filtros = t._filtros.data;
        });

        const removerFiltro = function (idFiltro, recargar = true) {
            if (t._filtros.data.hasOwnProperty(idFiltro)) {
                delete t._filtros.data[idFiltro];
                if (idFiltro === "buscador") {
                    t.search("");
                }

                removerBotonFiltro(idFiltro);

                if (recargar) {
                    if (MODO_SERVER) {
                        t.recargar();
                    } else {
                        t.draw();
                    }
                }
            }
        };

        const removerBotonFiltro = function (idFiltro) {
            let btn = t._filtros.dom.botones[idFiltro];
            if (btn !== undefined) {
                t._filtros.dom.contenedor.removeChild(btn);
                delete t._filtros.dom.botones[idFiltro];
            }
        };

        t.agregarBotonFiltro = function (idFiltro, etiqueta) {
            let btn = t._filtros.dom.botones[idFiltro];
            if (btn !== undefined) {
                t._filtros.dom.contenedor.removeChild(btn);
            } else {
                btn = document.createElement('button');
                btn.addEventListener("click", function () {
                    removerFiltro(idFiltro);
                }, false);
                t._filtros.dom.botones[idFiltro] = btn;
            }

            btn.innerHTML = '<i class="fas fa-times"></i>' + etiqueta;
            t._filtros.dom.contenedor.insertBefore(btn, t._filtros.dom.contenedor.firstChild);
        }.bind(t);

        t.filtros = function () {
            return t._filtros.data;
        }.bind(t);

        t.filtro = function (idFiltro, defaultValue = null) {
            let v = t._filtros.data[idFiltro];
            if (!v) {
                return defaultValue;
            }
            return v;
        }.bind(t);

        t.setearFiltro = function (idFiltro, value) {
            t._filtros.data[idFiltro] = value;
            t.recargar();
        }.bind(t);

        t.setearFiltros = function (filtros) {
            const ids = Object.keys(filtros);
            if (ids) {
                ids.forEach((idFiltro) => {
                    t._filtros.data[idFiltro] = filtros[idFiltro];
                });
                t.recargar();
            }
        }.bind(t);

        t.removerFiltro = function (idFiltro) {
            removerFiltro(idFiltro);
        }.bind(t);

        t.removerFiltros = function (filtros) {
            let ids = null;
            if (Array.isArray(filtros)) {
                ids = filtros;
            } else {
                ids = Object.keys(filtros);
            }
            if (ids) {
                let recargar = false;
                ids.forEach((idFiltro) => {
                    if (t._filtros.data.hasOwnProperty(idFiltro)) {
                        removerFiltro(idFiltro, false);
                        recargar = true;
                    }
                });
                if (recargar) {
                    if (MODO_SERVER) {
                        t.recargar();
                    } else {
                        t.draw();
                    }
                }
            }
        }.bind(t);

        t.activarFiltro = function (idColumna, configFiltro) {
            if (eTableFiltros === null)
                return APP.error(eTable.id, "no posee un elemento .filtros");
            if (typeof idColumna !== "string")
                return APP.error("El parametro 'idColumna' debe ser un string");
            if (typeof configFiltro !== "object")
                return APP.error("El parametro 'configFiltro' debe ser un objeto conteniendo la configuracion del filtro");

            let modoFiltro = null;
            if (configFiltro.autocompletar !== undefined)
                modoFiltro = "AUTOCOMPLETAR";
            else if (configFiltro.select !== undefined)
                modoFiltro = "SELECT";
            else if (configFiltro.input !== undefined)
                modoFiltro = "INPUT";
            else if (configFiltro.select_estatico !== undefined)
                modoFiltro = "SELECT_ESTATICO";
            else if (configFiltro.daterangepicker !== undefined)
                modoFiltro = "DATERANGEPICKER";
            else if (configFiltro.datepicker !== undefined)
                modoFiltro = "DATEPICKER";

            if (modoFiltro === null)
                return APP.error("No se pudo determinar el modo del filtro.");

            const idFiltro = configFiltro.parametro;
            if (idFiltro === undefined)
                return APP.error(`Key 'parametro' en 'configFiltro' es obligatoria.`);
            if (typeof idFiltro !== "string")
                return APP.error(`Key 'parametro' en 'configFiltro' debe ser un string.`);

            const th = eTableContainer.querySelector(`th#${idColumna}`);
            if (th === null)
                return APP.error(`No se encontro un th con id '${idColumna}'`);

            t._filtros.dom.table_headers[idFiltro] = th;

            // agregamos clase y divFiltro al th
            const filtroDiv = document.createElement('div');
            filtroDiv.className = "filtro";

            th.addClass("columna-filtrable");
            th.appendChild(filtroDiv);

            // insertamos html necesario
            let inputElement = null;
            let inputButton = null;
            let filterContent = null;

            switch (modoFiltro) {
                case "AUTOCOMPLETAR":
                    filtroDiv.innerHTML += `
                    <input type="text" class="form-control" placeholder="filtrar..." />
                    `;
                    inputElement = filtroDiv.querySelector("input");
                    break;

                case "INPUT":
                    filtroDiv.innerHTML += `
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="filtrar...">
                        <div class="input-group-append">
                            <button class="btn"><i class="fas fa-search"></i></button>
                        </div>
                    </div>`;
                    inputElement = filtroDiv.querySelector("input");
                    inputButton = filtroDiv.querySelector("button");
                    break;

                case "SELECT":
                case "SELECT_ESTATICO":
                    filtroDiv.innerHTML += `
                    <div class="filtro__content"></div>
                    `;
                    filtroDiv.addClass("filtro--scrollable");
                    filterContent = filtroDiv.querySelector(".filtro__content");
                    break;

                case "DATERANGEPICKER":
                case "DATEPICKER":
                    filtroDiv.addClass("filtro--hidden");
                    filtroDiv.innerHTML += `
                    <input type="text" class="form-control text-center" placeholder="filtrar..." />
                    `;
                    inputElement = filtroDiv.querySelector("input");
                    break;
            }

            // configuramos listener
            const closeFiltrable = function (eventClose) {
                if (!th.is(eventClose.target) && !th.contains(eventClose.target)) {
                    document.removeEventListener("click", closeFiltrable);
                    th.removeClass("expandido");
                    if (inputElement !== null)
                        inputElement.value = null; // limpiar input tras cerrar el filtro
                }
            };

            th.addEventListener("click", function (eventOpen) {
                if (eventOpen.target.hasClass("expandido")) {
                    document.removeEventListener("click", closeFiltrable);
                    eventOpen.target.removeClass("expandido");
                    if (inputElement !== null)
                        inputElement.value = null; // limpiar input tras cerrar el filtro
                } else {
                    eventOpen.target.addClass("expandido");
                    if (inputElement !== null)
                        inputElement.focus();

                    setTimeout(function () {
                        document.addEventListener("click", closeFiltrable);
                    }, 0);
                }
            }, false);

            // TODO: agregar datepicker
            switch (modoFiltro) {
                case "AUTOCOMPLETAR":
                    // TODO: validar que exista autocomplete!!
                    $(inputElement).autocomplete({

                        // TODO: pisamos valores por defecto??
                        autoFocus: true,
                        minLength: 1,

                        source: function (request, response) {
                            $.post({
                                url: configFiltro.autocompletar,
                                dataType: "json",
                                data: {
                                    descripcion: inputElement.value,
                                    filtros: t._filtros.data
                                },
                                success: response
                            });
                        },
                        select: function (event, ui) {
                            // limpiar input y cerrar filtro tras seleccionar
                            document.removeEventListener("click", closeFiltrable);
                            th.removeClass("expandido");
                            setTimeout(function () {
                                inputElement.value = null;

                            }, 0);

                            // seteamos filtro y recargamos tabla
                            t._filtros.data[idFiltro] = ui.item.id;
                            t.recargar();

                            // agregamos icono para remover el filtro facilmente
                            t.agregarBotonFiltro(idFiltro, ui.item.value);
                        }
                    });
                    break;

                case "SELECT":
                    $.post({
                        url: configFiltro.select,
                        dataType: "json",
                        data: {
                            filtros: t._filtros.data
                        },
                        success: function (json) {
                            // render options as buttons
                            let html = '';
                            json.forEach(option => {
                                const etiqueta = option.label || option.value;
                                html += `<button class="btn__filter__select" data-id="${option.id}" data-etiqueta="${etiqueta}">${option.value}</button>`;
                            });
                            filterContent.innerHTML = html;
                            const buttons = filterContent.getElementsByClassName("btn__filter__select");

                            // set listener
                            $(buttons).on("click", function (event) {
                                // limpiar input y cerrar filtro tras seleccionar
                                document.removeEventListener("click", closeFiltrable);
                                th.removeClass("expandido");

                                // seteamos filtro y recargamos tabla
                                t._filtros.data[idFiltro] = event.target.dataset.id;
                                t.recargar();

                                // agregamos icono para remover el filtro facilmente
                                t.agregarBotonFiltro(idFiltro, event.target.dataset.etiqueta);
                            });
                        }
                    });
                    break;

                case "SELECT_ESTATICO":
                    const json = configFiltro.select_estatico;

                    // render options as buttons
                    let html = '';
                    json.forEach(option => {
                        const etiqueta = option.label || option.value;
                        html += `<button class="btn__filter__select" data-id="${option.id}" data-etiqueta="${etiqueta}">${option.value}</button>`;
                    });
                    filterContent.innerHTML = html;
                    const buttons = filterContent.getElementsByClassName("btn__filter__select");

                    // set listener
                    $(buttons).on("click", function (event) {
                        // limpiar input y cerrar filtro tras seleccionar
                        document.removeEventListener("click", closeFiltrable);
                        th.removeClass("expandido");

                        // seteamos filtro y recargamos tabla
                        t._filtros.data[idFiltro] = event.target.dataset.id;
                        t.recargar();

                        // agregamos icono para remover el filtro facilmente
                        t.agregarBotonFiltro(idFiltro, event.target.dataset.etiqueta);
                    });
                    break;

                case "INPUT":
                    $(inputButton).on("click", function (event) {
                        const valor = $(inputElement).val();

                        // validar si es necesario
                        if (typeof configFiltro.esValido === "function" && !configFiltro.esValido(valor)) {
                            return;
                        }

                        // limpiar input y cerrar filtro tras seleccionar
                        document.removeEventListener("click", closeFiltrable);
                        th.removeClass("expandido");

                        // seteamos filtro y recargamos tabla
                        t._filtros.data[idFiltro] = valor;
                        t.recargar();

                        // agregamos icono para remover el filtro facilmente
                        t.agregarBotonFiltro(idFiltro, valor);
                    });
                    break;

                case "DATERANGEPICKER":
                    var options = configFiltro.daterangepicker;
                    var forceOptions = {
                        "singleDatePicker": false
                    };
                    for (var k in forceOptions) {
                        options[k] = forceOptions[k];
                    }
                    $(inputElement).daterangepicker(options, function (start, end, label) {
                        // limpiar input y cerrar filtro tras seleccionar
                        document.removeEventListener("click", closeFiltrable);
                        th.removeClass("expandido");

                        // seteamos filtro y recargamos tabla
                        t._filtros.data[idFiltro] = {
                            desde: options["format"] ? start.format(options["format"]) : start.toISOString(),
                            hasta: options["format"] ? end.format(options["format"]) : end.toISOString()
                        };
                        t.recargar();

                        // agregamos icono para remover el filtro facilmente
                        if (!label) {
                            label = start.format("DD/MM/YY") + " - " + end.format("DD/MM/YY");
                        }
                        t.agregarBotonFiltro(idFiltro, label);
                    });
                    break;

                case "DATEPICKER":
                    var options = configFiltro.datepicker;
                    var forceOptions = {
                        "singleDatePicker": true,
                        "ranges": null
                    };
                    for (var k in forceOptions) {
                        options[k] = forceOptions[k];
                    }
                    $(inputElement).daterangepicker(options, function (start, end) {
                        // limpiar input y cerrar filtro tras seleccionar
                        document.removeEventListener("click", closeFiltrable);
                        th.removeClass("expandido");

                        // seteamos filtro y recargamos tabla
                        t._filtros.data[idFiltro] = options["format"] ? start.format(options["format"]) : start.toISOString();
                        t.recargar();

                        // agregamos icono para remover el filtro facilmente
                        t.agregarBotonFiltro(idFiltro, start.format("DD/MM/YY"));
                    });
                    break;
            }
        }.bind(t);

        // ---------------------------------------------------------------
        // ORDENES DE TABLA
        // ---------------------------------------------------------------
        t._ordenes = {
            data: [],
            dom: {
                table_headers: []
            }
        };

        const validarOrdenValue = function (value) {
            switch (value.toLowerCase()) {
                case 'asc':
                    return 'asc';
                case 'desc':
                    return 'desc';
                default:
                    return null;
            }
        };

        const addOrden = function (idOrden, value, top = false) {
            let o = {};
            o[idOrden] = value;
            if (top) {
                t._ordenes.data.unshift(o);
            } else {
                t._ordenes.data.push(o);
            }
        };

        if (config.ordenes && typeof config.ordenes === 'object') {
            Object.keys(config.ordenes).forEach(function (key) {
                let value = validarOrdenValue(config.ordenes[key]);
                if (value) {
                    addOrden(key, value);
                }
            });
        }

        // insertar datos de ordenes a xhr
        setServerParamHook(function (d) {
            d.ordenes = t._ordenes.data;
        });

        const obtenerIndiceOrden = function (idOrden) {
            return t._ordenes.data.findIndex(function (item) {
                return item.hasOwnProperty(idOrden);
            });
        }.bind(t);

        const obtenerOrdenActual = function (idOrden) {
            let id = obtenerIndiceOrden(idOrden);
            if (id < 0) {
                return null;
            }
            return t._ordenes.data[id][idOrden];
        }.bind(t);

        const setearOrdenClass = function (idOrden, value) {
            let th = t._ordenes.dom.table_headers[idOrden];
            if (!th) {
                return;
            }
            let ordenDiv = th.querySelector('.orden');
            if (!ordenDiv) {
                return;
            }

            ordenDiv.removeClass('no-sort');
            ordenDiv.removeClass('sort-desc');
            ordenDiv.removeClass('sort-asc');

            switch (value) {
                case 'asc':
                    ordenDiv.title = "Columna ordenada en forma ascendente";
                    ordenDiv.addClass('sort-asc');
                    break;

                case 'desc':
                    ordenDiv.title = "Columna ordenada en forma descendente";
                    ordenDiv.addClass('sort-desc');
                    break;

                default:
                    ordenDiv.title = "Columna sin orden";
                    ordenDiv.addClass('no-sort');
            }
        };

        t.ordenes = function () {
            return t._ordenes.data;
        }.bind(t);

        t.setearOrden = function (idOrden, value) {
            value = validarOrdenValue(value);
            if (!value) return;

            let id = obtenerIndiceOrden(idOrden);
            if (id >= 0) {
                if (t._ordenes.data[id][idOrden] === value) {
                    return;
                }
                t._ordenes.data[id][idOrden] = value;
                // esto lo comento para que mueva la prioridad del sort
                //if (t._ordenes.data[id][idOrden] === value) {
                //    return;
                //}
                // t._ordenes.data.splice(id, 1); // si queremos remover
            } else {
                addOrden(idOrden, value);
            }

            setearOrdenClass(idOrden, value);
            t.recargar();
        }.bind(t);

        const removerOrdenTabla = function (idOrden) {
            let id = obtenerIndiceOrden(idOrden);
            if (id < 0) {
                return;
            }
            t._ordenes.data.splice(id, 1);
            setearOrdenClass(idOrden, null);
        }.bind(t);

        t.removerOrden = function (idOrden) {
            removerOrdenTabla(idOrden);
            t.recargar();
        }.bind(t);

        t.removerOrdenes = function (arrIds) {
            arrIds.forEach(id => {
                removerOrdenTabla(id);
            });
            t.recargar();
        }.bind(t);

        t.limpiarOrdenes = function () {
            let keys = t._ordenes.data.map(item => {
                return Object.keys(item)[0];
            });
            t.removerOrdenes(keys);
        }.bind(t);

        let ACTION_ORDEN_AGREGADA = false;
        t.activarOrden = function (idColumna, configOrden) {
            if (typeof idColumna !== "string")
                return APP.error("El parametro 'idColumna' debe ser un string");
            if (typeof configOrden !== "object")
                return APP.error("El parametro 'configOrden' debe ser un objeto conteniendo la configuracion del orden");

            const th = eTableContainer.querySelector(`th#${idColumna}`);
            if (th === null)
                return APP.error(`No se encontro un th con id '${idColumna}'`);

            t._ordenes.dom.table_headers[configOrden.parametro] = th;

            // agregamos clase y divOrden al th
            const ordenDiv = document.createElement('div');

            switch (obtenerOrdenActual(configOrden.parametro)) {
                case 'asc':
                    ordenDiv.title = "Columna ordenada en forma ascendente";
                    ordenDiv.className = "orden sort-asc";
                    break;
                case 'desc':
                    ordenDiv.title = "Columna ordenada en forma descendente";
                    ordenDiv.className = "orden sort-desc";
                    break;
                default:
                    ordenDiv.title = "Columna sin orden";
                    ordenDiv.className = "orden no-sort";
            }

            th.addClass("columna-ordenable");
            th.appendChild(ordenDiv);

            ordenDiv.innerHTML += `
                <i class="on-no-sort fas fa-minus"></i>
                <i class="on-sort-asc fas fa-arrow-down"></i>
                <i class="on-sort-desc fas fa-arrow-up"></i>
            `;

            ordenDiv.addEventListener("click", function () {
                switch (obtenerOrdenActual(configOrden.parametro)) {
                    case 'asc':
                        t.setearOrden(configOrden.parametro, 'desc');
                        break;
                    default:
                        t.setearOrden(configOrden.parametro, 'asc');
                        break;
                }
            }, false);

            if (!ACTION_ORDEN_AGREGADA) {
                t.agregarAccion({
                    descripcion: 'Limpiar Ordenes de Tabla',
                    icono: 'fas fa-broom',
                    accion: function () {
                        t.limpiarOrdenes();
                    }
                });
            }
            ACTION_ORDEN_AGREGADA = true;
        };

        // ---------------------------------------------------------------
        // ACCIONES
        // ---------------------------------------------------------------
        t._acciones = {
            dom: {
                contenedor: eTableAcciones
            }
        };

        t.agregarAccion = function (configAccion) {
            if (eTableAcciones === null)
                return APP.error(eTable.id, "no posee un elemento .actions");
            btn = document.createElement('button');
            btn.title = configAccion.descripcion;
            if (configAccion.btnId) {
                btn.id = configAccion.btnId;
            }
            if (configAccion.btnClass) {
                btn.addClass(configAccion.btnClass);
            }
            if (configAccion.iconoText) {
                btn.innerHTML = configAccion.iconoText;
                btn.addClass("btn--text");
            } else if (configAccion.iconoHtml) {
                btn.innerHTML = configAccion.iconoHtml;
            } else {
                btn.innerHTML = '<i class="' + configAccion.icono + '"></i>';
            }

            btn.addEventListener("click", function (event) {
                configAccion.accion(event);
            }, true);

            t._acciones.dom.contenedor.appendChild(btn);
        }.bind(t);

        // ---------------------------------------------------------------
        // ON DATATABLE EVENTS
        // ---------------------------------------------------------------

        const TABLE_FILTERS = [];
        let PREDRAWING = false;
        t.on('preDraw', function (e, settings, json) {

            if (!PREDRAWING) {
                PREDRAWING = true;
                const ids = Object.keys(t._filtros.dom.table_headers);
                ids.forEach(function (id) {
                    const th = t._filtros.dom.table_headers[id];
                    let elements = th.getElementsByClassName("filtro");
                    for (let i = 0; i < elements.length; i++) {
                        TABLE_FILTERS[id] = elements[i];
                        th.removeChild(elements[i]);
                    }
                });
                //APP.info("pre");
                // return false;
            }
            //return true;
        });

        t.on('draw.dt', function (e, settings, json) {
            PREDRAWING = false;

            const ids = Object.keys(t._filtros.dom.table_headers);
            ids.forEach(function (id) {
                const th = t._filtros.dom.table_headers[id];
                const element = TABLE_FILTERS[id];
                if (element) {
                    th.appendChild(element);
                }
            });

            //APP.info("draw");
        });




        // ---------------------------------------------------------------
        // MODO BUSCADOR
        // ---------------------------------------------------------------
        if (MODO_BUSCADOR && eTableBuscador) {
            const inputElement = eTableBuscador.querySelector("input");
            const closeElement = eTableBuscador.querySelector("button");
            if (inputElement && closeElement) {
                const close = function () {
                    inputElement.removeEventListener("input", onInput);
                    document.removeEventListener("click", closeOnLostFocus);
                    document.removeEventListener("keydown", onKey);
                    closeElement.removeEventListener("click", close);
                    eTableBuscador.removeClass("expandido");
                    inputElement.value = null; // limpiar input tras cerrar el filtro
                };
                const closeOnLostFocus = function (eventClose) {
                    if (!eTableBuscador.is(eventClose.target) && !eTableBuscador.contains(eventClose.target)) {
                        close();
                    }
                };
                const onKey = function (event) {
                    if (event.which === 13 || event.keyCode === 13) {
                        event.preventDefault();
                        close();
                    }
                };
                const onInput = function (event) {
                    if (!event.target.value) {
                        t.removerFiltro("buscador");
                    } else {
                        t._filtros.data["buscador"] = event.target.value;
                        t.search(event.target.value);
                        if (MODO_SERVER) {
                            t.recargar();
                        } else {
                            t.draw();
                        }

                        // agregamos icono para remover el filtro facilmente
                        t.agregarBotonFiltro("buscador", event.target.value);
                    }
                };

                t.agregarAccion({
                    descripcion: 'Buscar',
                    icono: 'fas fa-search',
                    accion: function () {
                        eTableBuscador.addClass("expandido");
                        inputElement.focus();
                        setTimeout(function () {
                            inputElement.addEventListener("input", onInput);
                            closeElement.addEventListener("click", close);
                            document.addEventListener("keydown", onKey);
                            document.addEventListener("click", closeOnLostFocus);
                        }, 0);
                    }
                });
            }
        }

        // ---------------------------------------------------------------
        // MODO LOCAL
        // ---------------------------------------------------------------
        if (typeof config.local !== "undefined") {
            // TODO: aca chequear que esten source y sourceToRow

            t._local = {
                items: config.local.itemsIniciales !== undefined ? config.local.itemsIniciales : [],
                itemToRow: config.local.itemToRow
            };

            const obtenerIndice = function (id) {
                return t._local.items.findIndex(function (item) {
                    return item.id === id;
                });
            };

            t.borrarItems = function () {
                t._local.items.length = 0;
                t.recargar();
            }.bind(t);

            t.agregarItem = function (item) {
                if (typeof item !== "object")
                    return APP.error("item debe ser un objeto");
                if (!item.hasOwnProperty('id'))
                    return APP.error("item debe contener la propiedad id");

                const indice = obtenerIndice(item.id);
                if (indice !== -1)
                    return APP.error("ya existe un item con el mismo id", item.id);

                t._local.items.push(item);
                t.recargar();
                return true;
            }.bind(t);

            t.setearItems = function (items) {
                t._local.items = items;
                t.recargar();
                return true;
            }.bind(t);

            t.editarItem = function (id, data) {
                if (id === null)
                    return APP.error("id del item es requerido");
                if (typeof data !== "object")
                    return APP.error("data debe ser un objeto");

                const indice = obtenerIndice(id);
                if (indice < 0) {
                    APP.warn("no existe item con id:", id);
                    return false;
                }

                const item = t._local.items[indice];
                Object.assign(item, data);
                t.recargar();
                return true;
            }.bind(t);

            t.items = function () {
                return t._local.items;
            }.bind(t);

            t.obtenerItem = function (id) {
                if (id === null)
                    return APP.error("id del item  es requerido");

                const indice = obtenerIndice(id);
                if (indice < 0) {
                    return false;
                }

                return t._local.items[indice];
            }.bind(t);

            t.removerItem = function (id = null) {
                if (id === null)
                    return APP.error("id del item es requerido");

                const indice = obtenerIndice(id);
                if (indice < 0) {
                    APP.warn("no existe item con id:", id);
                    return false;
                }
                const itemRemovido = t._local.items[indice];
                t._local.items.splice(indice, 1);
                t.recargar();
                return itemRemovido;
            }.bind(t);

            // override server side recargar
            t.recargar = function () {
                let data = [];
                let items = t._local.items;
                // aca podriamos filtrar los datos primero
                // aca mapeamos a arreglo de columnas
                items.forEach(function (item) {
                    data.push(t._local.itemToRow(item));
                });
                this.clear();
                this.rows.add(data);
                this.draw();
                if (typeof config.onRecarga === "function")
                    config.onRecarga.apply(t);
            }.bind(t);


            // tras iniciar recargamos tabla para dibujar los items iniciales
            setInitCompleteHook(function () {
                t.recargar();
            });
        }
        // ---------------------------------------------------------------
        // ---------------------------------------------------------------

        return t;
    };


    // indicar modulo se cargo correctamente
    loadjs.done('DATATABLE_MODULO');
});



function getTableScrollHeight(idOrTableContainer, withBottom = true) {
    var tContainer = idOrTableContainer;
    if (typeof tContainer !== "object") {
        tContainer = document.getElementById(idOrTableContainer).findAncestor("table-container");
    }
    if (!tContainer)
        return null;

    var hContainer = tContainer.offsetHeight;

    var tHeader = tContainer.getElementsByClassName("table__header")[0];
    var hHeader = 0;
    if (tHeader) {
        hHeader = tHeader.offsetHeight;
    }

    var tHead = tContainer.getElementsByTagName("thead")[0];
    var hHead = 0;
    if (tHead) {
        hHead = tHead.offsetHeight;
    }

    var hBottom = 0;
    if (withBottom) {
        // 60 px fixed bottom
        hBottom = 50;
    }

    var result = hContainer - hHeader - hHead - hBottom;
    // min height
    if (result < 70) {
        result = 70;
    }
    return result;
}