<?php

$app->group('/solicitantes', function () use ($app) {
    $app->any('', 'controller.mde.Solicitantes:vistaSolicitantes');
    $app->any(':nuevo', 'controller.mde.Solicitantes:vistaNuevoSolicitante')->add(requierePermiso('Solicitante.Crear'));
    // 21/02/2023 comenté la línea de abajo porque es igual a la de arriba salvo por /nuevo y :nuevo
    // $app->any('/nuevo', 'controller.mde.Solicitantes:vistaNuevoSolicitante')->add(requierePermiso('Solicitante.Crear'));
    $app->group('/{solicitanteID}', function () use ($app) {
      $app->any('', 'controller.mde.Solicitantes:vistaDetalleSolicitante')->add(requierePermiso('Solicitante.Detalle'));
    })->add(recursoSolicitante());
})->add(requierePermiso('Solicitantes'))
->add(requiereUsuario());


$app->group('/api/solicitantes', function () use ($app) {

  $app->get('', 'controller.mde.Solicitantes:listarSolicitantes');
  $app->post('', 'controller.mde.Solicitantes:crearSolicitante')->add(requierePermiso('Solicitante.Crear'));
  $app->group('/{solicitanteID}', function () use ($app) {
    $app->put('', 'controller.mde.Solicitantes:actualizarSolicitante')->add(requierePermiso('Solicitante.Editar'));
    $app->delete('', 'controller.mde.Solicitantes:eliminarSolicitante')->add(requierePermiso('Solicitante.Borrar'));
})->add(recursoSolicitante());
})->add(requierePermiso('Solicitantes'))
->add(requiereUsuario());