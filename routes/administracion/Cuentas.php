<?php

$app->group('/cuentas', function () use ($app) {
    $app->any('',       'controller.administracion.Cuentas:vistaCuentas');
    $app->any(':nuevo', 'controller.administracion.Cuentas:vistaNuevoCuenta')->add(requierePermiso('Cuentas.Crear'));
    $app->group('/{cuentaID}', function () use ($app) {
        $app->any('', 'controller.administracion.Cuentas:vistaDetalleCuenta')->add(requierePermiso('Cuentas.Detalle'));
        $app->any('/roles', 'controller.administracion.Cuentas:vistaDetalleCuentaRoles')->add(requierePermiso('Cuentas.Roles'));
    })->add(recursoCuenta());
})->add(requierePermiso('Cuentas'))
->add(requiereUsuario());

$app->group('/api/cuentas', function () use ($app) {
    $app->get('', 'controller.administracion.Cuentas:listarCuentas');
    $app->post('', 'controller.administracion.Cuentas:crearCuenta')->add(requierePermiso('Cuentas.Crear'));
    $app->group('/{cuentaID}', function () use ($app) {
        $app->put('', 'controller.administracion.Cuentas:actualizarCuenta')->add(requierePermiso('Cuentas.Editar'));
        $app->delete('', 'controller.administracion.Cuentas:eliminarCuenta')->add(requierePermiso('Cuentas.Borrar'));
        $app->group('/roles', function () use ($app) {
            $app->post('', 'controller.administracion.Cuentas:guardarCuentaRoles')->add(requierePermiso('Cuentas.Roles.Editar'));
        });
    })->add(recursoCuenta());
})->add(requierePermiso('Cuentas'))
->add(requiereUsuario());