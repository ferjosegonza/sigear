<?php

$app->group('/permisos', function () use ($app) {
    $app->any('',       'controller.administracion.Permisos:vistaPermisos');
    $app->any(':nuevo', 'controller.administracion.Permisos:vistaNuevoPermiso')->add(requierePermiso('Permisos.Crear'));
    $app->group('/{permisoID}', function () use ($app) {
        $app->any('', 'controller.administracion.Permisos:vistaDetallePermiso')->add(requierePermiso('Permisos.Detalle'));
    })->add(recursoPermiso());
})->add(requierePermiso('Permisos'))
->add(requiereUsuario());

$app->group('/api/permisos', function () use ($app) {
    $app->get('', 'controller.administracion.Permisos:listarPermisos');
    $app->post('', 'controller.administracion.Permisos:crearPermiso')->add(requierePermiso('Permisos.Crear'));
    $app->group('/{permisoID}', function () use ($app) {
        $app->put('', 'controller.administracion.Permisos:actualizarPermiso')->add(requierePermiso('Permisos.Editar'));
        $app->delete('', 'controller.administracion.Permisos:eliminarPermiso')->add(requierePermiso('Permisos.Borrar'));
    })->add(recursoPermiso());
})->add(requierePermiso('Permisos'))
->add(requiereUsuario());