loadjs.ready("YUP", () => {
    const DateSchema = yup.date;
    const invalidDate = new Date(''); // our failed to coerce value

    function parseDateFromFormats(formats, parseStrict = true) {
        if (!formats)
            throw new Error('se debe ingresar un formato valido');
        let next = this.clone();
        if (!next.hasOwnProperty("_validFormats"))
            next._validFormats = [];
        next._validFormats = next._validFormats.concat(formats);

        return next.transform(function (value, originalValue) {
            if (this.isType(value))
                return value;

            value = moment(originalValue, this._validFormats, parseStrict);
            return value.isValid() ? value.toDate() : invalidDate;
        });
    }

    yup.addMethod(yup.date, 'format', parseDateFromFormats);

    class MomentSchema extends DateSchema {
        constructor() {
            super();
            this._validFormats = [];

            this.withMutation(() => {
                this.transform(function (value, originalValue) {
                    if (moment.isMoment(value) && value.isValid())
                        return value;

                    value = moment(originalValue, this._validFormats, true);
                    return value.isValid() ? value.toDate() : invalidDate;
                });
            });
        }

        _validate(_value, options = {}) {
            return super._validate(_value === '' ? undefined : _value, options);
        }

        format(formats) {
            if (!formats)
                throw new Error('se debe ingresar un formato valido');
            let next = this.clone();
            next._validFormats = this._validFormats.concat(formats);
            return next;
        }
    }

    // expose moment schema
    yup.moment = function () {
        return new MomentSchema();
    };

    // traduccion al español
    yup.setLocale({
        mixed: {
            default: '${path} es invalido',
            required: '${path} es requerido',
            oneOf: '${path} debe ser uno de los siguientes valores: ${values}',
            notOneOf: '${path} no puede ser ninguno de los siguientes valores: ${values}',
            notType: ({ path, type, value, originalValue }) => {
                let t = type;
                switch (type) {
                    case "number": t = "número"; break;
                    case "string": t = "texto"; break;
                    case "object": t = "objeto"; break;
                    case "date": t = "fecha"; break;
                    case "boolean": t = "booleano"; break;
                    case "array": t = "arreglo"; break;
                }
                return `${path} debe ser de tipo \'${t}\'`
            }
        },
        number: {
            min: '${path} debe ser mayor o igual a ${min}',
            max: '${path} debe ser menor o igual a ${max}',
            lessThan: '${path} debe ser menor a ${less}',
            moreThan: '${path} debe ser mayor a ${more}',
            notEqual: '${path} debe ser distinto a ${notEqual}',
            positive: '${path} debe ser un número positivo',
            negative: '${path} debe ser un número negativo',
            integer: '${path} debe ser un número entero'
        },
        string: {
            length: '${path} debe contener exactamente ${length} caracteres',
            min: '${path} debe contener como mínimo ${min} caracteres',
            max: '${path} debe contener como máximo ${max} caracteres',
            matches: '${path} debe cumplir con la siguiente expresión regular "${regex}"',
            email: '${path} debe ser un email válido',
            url: '${path} debe ser una URL válida',
            trim: '${path} no puede contener caracteres vacios al principio o final',
            lowercase: '${path} debe contener solo minúsculas',
            uppercase: '${path} debe contener solo mayúsculas'
        }
    });

    window.validarEsquema = function (data, esquema) {
        let errors = null;
        try {
            esquema.validateSync(data, { abortEarly: false });
        } catch (schemaErr) {
            if (schemaErr.name !== "ValidationError") {
                throw schemaErr;
                return errors;
            }

            errors = {};
            if (schemaErr.inner.length > 0) {
                schemaErr.inner.forEach(function (err) {
                    errors[err.path] = {
                        tipo: err.type,
                        descripcion: err.errors[0]
                    };
                });
            }
        }
        return errors;
    };

    window.validarFormularioConYup = function (options = {}) {
        const event = options.event || null;
        if (!event) {
            APP.error("se requiere 'event'");
            return false;
        }
        const schema = options.schema || null;
        if (!schema) {
            APP.error("se requiere 'schema'");
        }
        const onValidate = options.onValidate || null;
        if (!onValidate) {
            APP.error("se requiere 'onValidate'");
        }
        const onError = options.onError || null;

        event.preventDefault();
        event.stopPropagation();

        const form = event.target;

        // limpiamos las validaciones previas
        if (form.hasClass("was-validated")) {
            form.removeClass("was-validated");

            const messages = form.querySelectorAll(".app-form-message");
            for (let i = 0; i < messages.length; i++) {
                messages[i].parentNode.removeChild(messages[i]);
            }
        }

        const validNodeNames = ["INPUT", "SELECT", "TEXTAREA"];
        const formData = {
            keys: [],
            dom: {},
            data: {}
        };
        for (let i = 0; i < form.elements.length; i++) {
            const element = form.elements[i];
            if (validNodeNames.indexOf(element.nodeName) < 0) {
                continue;
            }
            // prioridad al name
            let key = element.name;
            if (!key) {
                // sino id
                key = element.id;
            }
            if (!key) {
                if (!element.readOnly && !element.disabled) {
                    APP.error(element, "requiere name o id para ser validado");
                }
                continue;
            }
            formData.keys.push(key);
            formData.dom[key] = element;

            let value = element.value.trim();
            if (element.type === "checkbox") {
                const valueOnCheck = element.dataset.check || true;
                const valueOnUncheck = element.dataset.uncheck || false;
                value = element.checked ? valueOnCheck : valueOnUncheck;
            }

            if (formData.data[key]) {
                if (Array.isArray(formData.data[key])) {
                    formData.data[key].push(value);
                } else {
                    formData.data[key] = [formData.data[key], value];
                }
            } else {
                formData.data[key] = value;
            }

            // yup label
            if (element.hasAttribute("data-label") && element.dataset.label) {
                schema.fields[key] = schema.fields[key].label(element.dataset.label)
            }
        }
        formData.errors = validarEsquema(formData.data, schema);
        for (let i = 0; i < formData.keys.length; i++) {
            const key = formData.keys[i];
            const element = formData.dom[key];
            const parent = element.parentNode;
            const error = formData.errors ? formData.errors[key] : null;
            if (error) {
                element.addClass("is-invalid");
                element.removeClass("is-valid");
                if (parent.hasClass("input-group")) {
                    parent.addClass("is-invalid");
                    parent.removeClass("is-valid");
                }

                const message = document.createElement('div');
                message.addClass("app-form-message");
                message.addClass("invalid-feedback");
                message.innerHTML = error.descripcion;
                element.parentNode.appendChild(message);
            } else {
                element.addClass("is-valid");
                element.removeClass("is-invalid");
                if (parent.hasClass("input-group")) {
                    parent.addClass("is-valid");
                    parent.removeClass("is-invalid");
                }
                // ... valid feedback?
            }
        }

        form.addClass("was-validated");

        if (!formData.errors) {
            onValidate(formData);
            return;
        }
        if (onError) {
            onError(formData);
        }
    };

    // indicar modulo se cargo correctamente
    loadjs.done('YUP_MODULO');
});
