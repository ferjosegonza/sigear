<?php

/**
 * PERMISO
 */
function recursoPermiso() {
    return function(Request $request, Response $response, callable $next) {
        $id = $request->getArgument('permisoID', null);

        // si no es valido el id, cortamos solicitud
        if (is_null($id) || !ctype_digit($id) || intval($id) <= 0) {
            return $response->withError(400, array('permisoID' => 'permisoID inválido'));
        }

        // si no existe cortamos solicitud
        $m = new PermisosModel();
        $recurso = $m->detallePermiso($id);
        if (empty($recurso)) {
            return $response->withAppError('PERMISO NO EXISTE');
        }

        // continuamos solicitud con el recurso como atributo
        return $next($request->withAttribute('recurso_permiso', $recurso), $response);
    };
}

/**
 * ROL
 */
function recursoRol() {
    return function(Request $request, Response $response, callable $next) {
        $id = $request->getArgument('rolID', null);

        // si no es valido el id, cortamos solicitud
        if (is_null($id) || !ctype_digit($id) || intval($id) <= 0) {
            return $response->withError(400, array('rolID' => 'rolID inválido'));
        }

        // si no existe cortamos solicitud
        $m = new RolesModel();
        $recurso = $m->detalleRol($id);
        if (empty($recurso)) {
            return $response->withAppError('ROL NO EXISTE');
        }

        // continuamos solicitud con el recurso como atributo
        return $next($request->withAttribute('recurso_rol', $recurso), $response);
    };
}

/**
 * CUENTA
 */
function recursoCuenta() {
    return function(Request $request, Response $response, callable $next) {
        $id = $request->getArgument('cuentaID', null);

        // si no es valido el id, cortamos solicitud
        if (is_null($id) || !ctype_digit($id) || intval($id) <= 0) {
            return $response->withError(400, array('cuentaID' => 'cuentaID inválido'));
        }

        // si no existe cortamos solicitud
        $m = new CuentasModel();
        $recurso = $m->detalleCuenta($id);
        if (empty($recurso)) {
            return $response->withAppError('CUENTA NO EXISTE');
        }

        // continuamos solicitud con el recurso como atributo
        return $next($request->withAttribute('recurso_cuenta', $recurso), $response);
    };
}


/*
* Fondos
*/
function recursoFondo() {
    return function(Request $request, Response $response, callable $next) {
        $id = $request->getArgument('fondoID', null);

        // si no es valido el id, cortamos solicitud
        if (is_null($id) || !ctype_digit($id) || intval($id) <= 0) {
            return $response->withError(400, array('fondoID' => 'fondoID inválido'));
        }

        // si no existe cortamos solicitud
        $m = new FondosModel();
        $recurso = $m->detalleFondo($id);
        
        if (empty($recurso)) {
            return $response->withAppError('FONDO NO EXISTE');
        }

        // continuamos solicitud con el recurso como atributo
        return $next($request->withAttribute('recurso_fondo', $recurso), $response);
    };
}

function recursoSolicitante() {
    return function(Request $request, Response $response, callable $next) {
        $id = $request->getArgument('solicitanteID', null);

        // si no es valido el id, cortamos solicitud
        if (is_null($id) || !ctype_digit($id) || intval($id) <= 0) {
            return $response->withError(400, array('solicitanteID' => 'solicitanteID inválido'));
        }

        // si no existe cortamos solicitud
        $sm = new SolicitantesModel();
        $recurso = $sm->detalleSolicitante($id);
        
        if (empty($recurso)) {
            return $response->withAppError('SOLICITANTE NO EXISTE');
        }

        // continuamos solicitud con el recurso como atributo
        return $next($request->withAttribute('recurso_solicitante', $recurso), $response);
    };
}

function recursoSolicitudes() {
    return function(Request $request, Response $response, callable $next) {
        $id = $request->getArgument('solicitudesID', null);

        // si no es valido el id, cortamos solicitud
        if (is_null($id) || !ctype_digit($id) || intval($id) <= 0) {
            return $response->withError(400, array('solicitudesID' => 'solicitudesID inválido'));
        }

        // si no existe cortamos solicitud
        $sm = new SolicitudesModel();
        $recurso = $sm->detalleSolicitud($id);

        if (empty($recurso)) {
            return $response->withAppError('SOLICITUD NO EXISTE');
        }

        // continuamos solicitud con el recurso como atributo
        return $next($request->withAttribute('recurso_solicitud', $recurso), $response);
    };
}

function recursoInstitucion() {
    return function(Request $request, Response $response, callable $next) {
        $id = $request->getArgument('institucionID', null);

        // si no es valido el id, cortamos solicitud
        if (is_null($id) || !ctype_digit($id) || intval($id) <= 0) {
            return $response->withError(400, array('institucionID' => 'institucionID inválido'));
        }

        // si no existe cortamos solicitud
        $im = new InstitucionesModel();
        $recurso = $im->detalleInstituciones($id);
        
        if (empty($recurso)) {
            return $response->withAppError('INSTITUCIÓN NO EXISTE');
        }

        // continuamos solicitud con el recurso como atributo
        return $next($request->withAttribute('recurso_institucion', $recurso), $response);
    };
}

function recursoColumna() {
    return function(Request $request, Response $response, callable $next) {
        $id = $request->getArgument('columnaID', null);

        // si no es valido el id, cortamos solicitud
        if (is_null($id) || !ctype_digit($id) || intval($id) <= 0) {
            return $response->withError(400, array('columnaID' => 'columnaID inválido'));
        }

        // si no existe cortamos solicitud
        $m = new FondosModel();
        $recurso = $m->detalleColumna($id);
        if (empty($recurso)) {
            return $response->withAppError('FONDO NO EXISTE');
        }

        // continuamos solicitud con el recurso como atributo
        return $next($request->withAttribute('recurso_columna', $recurso), $response);
    };
}

function recursoAdjunto() {
    return function(Request $request, Response $response, callable $next) {
        $id = $request->getArgument('adjuntoID', null);

        // si no es valido el id, cortamos solicitud
        if (is_null($id) || !ctype_digit($id) || intval($id) <= 0) {
            return $response->withError(400, array('adjuntoID' => 'adjuntoID inválido'));
        }

        // si no existe cortamos solicitud
        $am = new AdjuntosModel();
        $recurso = $am->detalleAdjuntos($id);

        if (empty($recurso)) {
            return $response->withAppError('ADJUNTO NO EXISTE');
        }

        // continuamos solicitud con el recurso como atributo
        return $next($request->withAttribute('recurso_adjunto', $recurso), $response);
    };
}
