loadjs.ready("LAYOUT", () => {
    const ID_APP_MODALS_CONTAINER = "app-modals-container";

    APP.cargarDOM("APP_MODALS_CONTAINER", ID_APP_MODALS_CONTAINER, true);

    // history manipulation
    window.onpopstate = function (event) {
        if (event === undefined || event === null)
            return;

        if (event.state === undefined || event.state === null)
            return;

        if (event.state.appModal === true)
            APP.closeCurrentView();
    };

    window.abrirModal = function (html, options = {}) {
        if (APP.DOM.APP_MODALS_CONTAINER === null) {
            console.log("error abrirModal: se requiere APP_MODALS_CONTAINER");
            return;
        }
        if (typeof $() === 'undefined') {
            console.log("error abrirModal: se requiere JQuery");
            return;
        }
        
        let onOpen = options.onOpen || null;
        let onClose = options.onClose || null;
        let isDetail = options.isDetail || false;
        let keepSolapa = options.hasOwnProperty("keepSolapa") ? options.keepSolapa : true; 
        let isSolapa = options.isSolapa || false;
        
        let htmlIsModalCache = options.htmlIsModalCache || false;
        
        let modal;
        if (!htmlIsModalCache) {
            modal = document.createElement('div');
            modal.className = 'app-modal';
            if (isDetail) {
                modal.className += ' app-modal--is-detail';
            }
        } else {
            modal = html;
        }
        
        APP.DOM.APP_MODALS_CONTAINER.appendChild(modal);
        
        if (!isSolapa) {
            history.replaceState({
                'appModal': true
            }, null);
            history.pushState({'appModal': true}, null, '#hm-' + APP.VIEW_STATE_STACK.length);
        }
    
        APP.openView(modal, {
            onClose: function (viewState) {
                APP.DOM.APP_MODALS_CONTAINER.removeChild(viewState.containerElement);
                if (onClose !== null)
                    onClose(viewState);
            },
            keepHeader: isDetail,
            closeWithOverlay: options.closeWithOverlay || false,
            keepSolapa: keepSolapa,
            isSolapa: isSolapa
        });
        
        // despues de openView para que pueda leer el state correcto
        if (!keepSolapa) {
            APP.setearAppSolapas();
        }
        if (!htmlIsModalCache) {
            $(modal).html(html);
        }
        
        if (onOpen !== null)
            onOpen(modal);
        
        return modal;
    };

    window.abrirDetalle = function (html, options = {}) {
        options.isDetail = true;
        options.keepSolapa = false;
        return abrirModal(html, options);
    };

    // alias
    window.cerrarModal = function () {
        history.back();
    };

    // alias
    window.cerrarDetalle = function () {
        history.back();
    };
     
    // extension de APP
    APP.abrirSolapa = function(id) {
        if (!APP.CONFIG_SOLAPAS) {
            this.error('CONFIG_SOLAPAS indefinido');
            return;
        }
        
        let configSolapa = APP.CONFIG_SOLAPAS[id];
        if (!configSolapa) {
            this.error('CONFIG_SOLAPAS no posee id', id);
            return;
        }
        
        let onOpen = configSolapa.onOpen || null;
        let onClose = configSolapa.onClose || null;
                
        if (!configSolapa.inicial) {
            APP.startLoading();
            if (!configSolapa._loaded) {
                $.ajax({
                    url: configSolapa.url,
                    success: function (html) {
                        APP.closeCurrentView(true);
                        APP.CONFIG_SOLAPAS._activoStack.push(id);
                        
                        configSolapa._loaded = abrirModal(html, {
                            onClose: onClose,
                            onOpen: onOpen,
                            isDetail: true,
                            keepSolapa: true,
                            isSolapa: true,
                        });
                        
                        configSolapa._state = APP.state;
                        APP.endLoading();
                    },
                    error: function(xhr){
                        swalError(xhr);
                        APP.endLoading();
                    }
                });
            } else {
                APP.closeCurrentView(true);
                APP.CONFIG_SOLAPAS._activoStack.push(id);

                abrirModal(configSolapa._loaded, {
                    onClose: onClose,
                    onOpen: onOpen,
                    isDetail: true,
                    keepSolapa: true,
                    isSolapa: true,
                    htmlIsModalCache: true
                });
                APP.state = configSolapa._state;
                APP.endLoading();
            }
            return;
        }
        
        APP.closeCurrentView(true);
    },
    // ========================================================================
    // RENDER ESTADISTICAS
    // ========================================================================
    window.renderEstadisticas = function(stats, id) {
        const container = document.getElementById(id);
        if (!container) return;
        
        let html = "";
        for(let i=0; i<stats.length; i++) {
            const color = stats[i].color || "celeste";
            html += `
            <div class="app-estadistica color-${color}">
                <div class="app-estadistica__desc">${stats[i].descripcion.replace(/_/g, " ")}</div>
                <div class="app-estadistica__value">${stats[i].cantidad}</div>
            </div>`;
        }
        container.innerHTML = html;
    };
    
    window.layoutToggleEstadisticas = function(button) {
        const layout = button.findAncestor("app-layout__estadisticas-tabla");
        layout.toggleClass("stats-expanded");
        
        const table = layout.querySelector("table");
        if (table && APP.DATATABLES && APP.DATATABLES[table]) {
            APP.DATATABLES[table].columns.adjust({autowidth:true}).draw();
        }
    };
    
    // ========================================================================
    // RENDER ESTADISTICAS
    // ========================================================================
    window.abrirDetalleDelMaestro = function(idMasterDetail) {
        let detailElement = document.getElementById(idMasterDetail);
        if (!detailElement) {
            detailElement = document.querySelector(idMasterDetail);
        }
        if (!detailElement) {
            return;
        }
        detailElement.addClass("detail--active");
    };

    window.cerrarDetalleDelMaestro = function(idMasterDetail) {
        let detailElement = document.getElementById(idMasterDetail);
        if (!detailElement) {
            detailElement = document.querySelector(idMasterDetail);
        }
        if (!detailElement) {
            return;
        }
        detailElement.removeClass("detail--active");
    };
    
    // SCAN LAYOUT
    window.ScanLayout = function(config = {}) {
        const _layoutId = config.id;
        if (!_layoutId) {
            return APP.error('ScanLayout requiere layoutId');
        }
        const _layout = document.getElementById(_layoutId);
        if (!_layout) {
            return APP.error('No se encuentra layout con id', _layoutId);
        }
        const _scanFocusableElement = _layout.querySelector(".on_escaner");
        if (!_scanFocusableElement) {
            return APP.error('No se encuentra div.on_escaner dentro de layout');
        }
        const _inputScanElement = document.createElement('INPUT');
        _inputScanElement.setAttribute('style', 'position:absolute; opacity:0;');
        
        const _onParseEscaner = config.onParseEscaner;
        if (!_onParseEscaner) {
            return APP.error('ScanLayout requiere onParseEscaner');
        }

        const _scanGateway = config.hasOwnProperty('scanGateway') ? config.scanGateway : 'keyboard'; // input or keyboard
        const _scanEventTimeout = config.hasOwnProperty('scanEventTimeout') ? config.scanEventTimeout : 500; // milisegundos
        let _lastKeyTime = null;
        const _activarAlInicio = config.hasOwnProperty('activarAlInicio') ? config.activarAlInicio : true;
        let _modoActual = config.modoInicial || 'ESCANER';
        let _contentScanned = '';

        if (_scanGateway === 'input') {
            _scanFocusableElement.appendChild(_inputScanElement);
        }

        const _onInputFocusOut = function() {
            _inputScanElement.focus();
        }

        const _desactivarModoEscaner = function() {
           if (_scanGateway === 'input') {
                _inputScanElement.removeEventListener("keypress", _onKeyPress);
                _inputScanElement.value = "";

                _inputScanElement.removeEventListener("focusout", _onInputFocusOut);
                APP.unregisterOnFocus("inputEscanerFocus");
                _inputScanElement.blur();
            } else {
                document.removeEventListener("keypress", _onKeyPress);
            }

            // limpiamos buffer de caracteres
            _contentScanned = "";
        }

        const _getTime = function() {
            const d = new Date();
            return d.getTime();
        };
        
        const _onKeyPress = function(event) {
            const currentTime = _getTime();
            if (_lastKeyTime && currentTime - _lastKeyTime > _scanEventTimeout) {
                _inputScanElement.value = "";
                _contentScanned = "";
            }
            _lastKeyTime = currentTime;

            if (event.keyCode === 13) {
                if (_scanGateway === 'input') {
                    _contentScanned = _inputScanElement.value;
                }
                const value = _contentScanned.trim();

                // evitamos que se pueda seguir escaneando
                _desactivar();

                // TODO agregar funcion para normalizar por config
                // if(config.normalizar) value = config.normalizar(value)
                _onParseEscaner(value);
            } else {
                _contentScanned += event.key;
            }
        };

        const _desactivar = function() {
            _desactivarModoEscaner();

            document.activeElement.blur();
        }

        this.desactivar = function () {
            _desactivar();
        };

        this.intercambiarModo = function () {
            if(_modoActual === 'ESCANER') {
                this.activarModoManual();
            } else {
                this.activarModoEscaner();
            }
        };

        this.activarModoActual = function () {
            if(_modoActual === 'ESCANER') {
                this.activarModoEscaner();
            } else {
                this.activarModoManual();
            }
        };

        this.activarModoEscaner = function () {
            _modoActual = 'ESCANER';
            _contentScanned = "";

            if (_scanGateway === 'input') {
                _inputScanElement.value = "";
                setTimeout(function(){
                    _inputScanElement.focus(); // evitamos que foco quede sobre botones
                }, 0);
                _inputScanElement.addEventListener("focusout", _onInputFocusOut);
                APP.registerOnFocus("inputEscanerFocus", _onInputFocusOut);

                _inputScanElement.addEventListener("keypress", _onKeyPress);
            } else {
                document.addEventListener("keypress", _onKeyPress);
                setTimeout(function(){
                    _scanFocusableElement.focus(); // evitamos que foco quede sobre botones
                }, 0);
            }
            
            _layout.removeClass("modo__manual");
            _layout.addClass("modo__escaner");

            if (config.onModoEscaner) {
                config.onModoEscaner();
            }
        };

        this.activarModoManual = function () {
            _modoActual = 'MANUAL';
            document.removeEventListener("keypress", _onKeyPress);
            _contentScanned = "";

            _layout.removeClass("modo__escaner");
            _layout.addClass("modo__manual");

            if (config.onModoManual) {
                config.onModoManual();
            }
        };
        
        if (_activarAlInicio) {
            // activamos modo actual por defecto
            this.activarModoActual();
        }
    };
    
    
    // indicar modulo se cargo correctamente
    loadjs.done('LAYOUT_MODULO');
});
