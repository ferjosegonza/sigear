<?php

$app->group('/instituciones', function () use ($app) {
    $app->any('', 'controller.mde.Instituciones:vistaInstituciones'); 
    $app->any(':nuevo', 'controller.mde.Instituciones:vistaNuevoInstitucion')->add(requierePermiso('Instituciones.Crear'));
    $app->group('/{institucionID}', function () use ($app) {
      $app->any('', 'controller.mde.Instituciones:vistaDetalleInstitucion')->add(requierePermiso('Instituciones.Detalle'));
    })->add(recursoInstitucion());
})->add(requierePermiso('Instituciones'))
->add(requiereUsuario());


$app->group('/api/instituciones', function () use ($app) {

  $app->get('', 'controller.mde.Instituciones:listarInstituciones');
  $app->post('', 'controller.mde.Instituciones:crearInstitucion')->add(requierePermiso('Instituciones.Crear'));
  $app->group('/{institucionID}', function () use ($app) {
    $app->put('', 'controller.mde.Instituciones:actualizarInstitucion')->add(requierePermiso('Instituciones.Editar'));
    $app->delete('', 'controller.mde.Instituciones:eliminarInstitucion')->add(requierePermiso('Instituciones.Borrar'));
})->add(recursoInstitucion()); 
})->add(requierePermiso('Instituciones'))
->add(requiereUsuario()); 