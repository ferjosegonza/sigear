loadjs.ready("JQUERY", () => {
    
    $.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
        options.cache = false;
        
        if (originalOptions.method) {
            options.type = originalOptions.method;
        } else if (originalOptions.type) {
            options.method = originalOptions.type;
        } else {
            options.type = "POST";
            options.method = "POST";
        }
        
        // usar cabecera que viene por default 'X-Requested-With' = 'XMLHttpRequest'
        //jqXHR.setRequestHeader('X-Ajax', "true");
    });

    const getTime = function() {
        const d = new Date();
        return d.getTime();
    }
    
    let ACTIVE_AJAX_BUTTON = null;
    let LAST_BUTTON_CLICKED = null;
    let LAST_BUTTON_CLICKED_TIME = null;
    const setActiveAjaxButton = function() {
        if (!LAST_BUTTON_CLICKED) return;
        if (getTime() - LAST_BUTTON_CLICKED_TIME > 1000) return;

        if(ACTIVE_AJAX_BUTTON) {
            ACTIVE_AJAX_BUTTON.removeClass('btn--processing');
        }
        ACTIVE_AJAX_BUTTON = LAST_BUTTON_CLICKED;
        ACTIVE_AJAX_BUTTON.addClass('btn--processing');
    };
    
    const unsetActiveAjaxButton = function() {
        if (!ACTIVE_AJAX_BUTTON) return;
        ACTIVE_AJAX_BUTTON.removeClass('btn--processing');
        ACTIVE_AJAX_BUTTON = null;
    };
    
    $(document).ajaxStart(function() {
        APP.startProcessing();
        
        if (!ACTIVE_AJAX_BUTTON) {
            setActiveAjaxButton();
        };
    });
    
    $(document).ajaxStop(function() {
        APP.endProcessing();
        unsetActiveAjaxButton();
    });
    
    $(document).click(function(event) {
        if (event.target.nodeName !== 'BUTTON') {
            LAST_BUTTON_CLICKED = null;
            return;
        }
        LAST_BUTTON_CLICKED = event.target;
        LAST_BUTTON_CLICKED_TIME = getTime();
        
        if ($.active && !ACTIVE_AJAX_BUTTON) {
            setActiveAjaxButton();
        }
    });
    
    // indicar modulo se cargo correctamente
    loadjs.done('JQUERY_MODULO');
});