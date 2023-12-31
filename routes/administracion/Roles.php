<?php

$app->group('/roles', function () use ($app) {
    $app->any('',       'controller.administracion.Roles:vistaRoles');
    $app->any(':nuevo', 'controller.administracion.Roles:vistaNuevoRol')->add(requierePermiso('Roles.Crear'));
    $app->group('/{rolID}', function () use ($app) {
        $app->any('', 'controller.administracion.Roles:vistaDetalleRol')->add(requierePermiso('Roles.Detalle'));
        $app->group('/permisos', function () use ($app) {
            $app->any('', 'controller.administracion.Roles:vistaDetalleRolPermisos')->add(requierePermiso('Roles.Permisos'));
        });
    })->add(recursoRol());
})->add(requierePermiso('Roles'))
->add(requiereUsuario());

$app->group('/api/roles', function () use ($app) {
    $app->get('', 'controller.administracion.Roles:listarRoles');
    $app->post('', 'controller.administracion.Roles:crearRol')->add(requierePermiso('Roles.Crear'));
    $app->group('/{rolID}', function () use ($app) {
        $app->put('', 'controller.administracion.Roles:actualizarRol')->add(requierePermiso('Roles.Editar'));
        $app->delete('', 'controller.administracion.Roles:eliminarRol')->add(requierePermiso('Roles.Borrar'));
        $app->group('/permisos', function () use ($app) {
            $app->get('', 'controller.administracion.Roles:listarRolPermisos')->add(requierePermiso('Roles.Permisos'));
            $app->post('', 'controller.administracion.Roles:guardarRolPermisos')->add(requierePermiso('Roles.Permisos.Editar'));
        });
    })->add(recursoRol());
})->add(requierePermiso('Roles'))
->add(requiereUsuario());