loadjs.ready("ROUTER", () => {
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
        
        var modal = document.createElement('div');
        modal.className = 'app-modal';
        if (isDetail)
            modal.className += ' app-modal--is-detail';
        APP.DOM.APP_MODALS_CONTAINER.appendChild(modal);

        $(modal).html(html);

        if (APP.VIEW_STATE_STACK.length <= 1) {
            history.replaceState({
                'appModal': true
            }, null);
        }
        // TODO aca armar el router
        history.pushState({'appModal': true}, null, '#hm-' + APP.VIEW_STATE_STACK.length);
        //history.pushState({'appModal': true}, null);
        //history.pushState({'appModal': true}, null, '#' + window.location.hash + "-hm" + VIEWS.length);
        
        APP.openView(modal, {
            onClose: function (viewState) {
                APP.DOM.APP_MODALS_CONTAINER.removeChild(viewState.containerElement);
                if (onClose !== null)
                    onClose(viewState);
            }, 
            keepHeader: isDetail
        });
        if (onOpen !== null)
            onOpen(modal);
    };

    window.abrirDetalle = function (html, options = {}) {
        options.isDetail = true;
        abrirModal(html, options);
    };

    // alias
    window.cerrarModal = function () {
        history.back();
    };

    // alias
    window.cerrarDetalle = function () {
        history.back();
    };
    
    
    
    // indicar modulo se cargo correctamente
    loadjs.done('ROUTER_MODULO');
});
