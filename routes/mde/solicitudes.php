<?php

$app->group('/solicitudes', function () use ($app) {
    $app->any('', 'controller.mde.Solicitudes:vistaSolicitudes');
    $app->any(':nuevo', 'controller.mde.Solicitudes:vistaNuevoSolicitudes')->add(requierePermiso('Solicitudes.Crear'));
    $app->group('/{solicitudesID}', function () use ($app) {
      $app->any('', 'controller.mde.Solicitudes:vistaDetalleSolicitudes')->add(requierePermiso('Solicitudes.Detalle'));
    })->add(recursoSolicitudes());
})->add(requierePermiso('Solicitudes'))
->add(requiereUsuario());

$app->group('/api/solicitudes', function () use ($app) {
  $app->get('', 'controller.mde.Solicitudes:listarSolicitudes');
  //$app->get('/adjunto/{archivoID}', 'controller.mde.Solicitudes:obtenerArchivos');
  //https://desa-gpa.sigap.com.ar/api/solicitudes/adjunto/644379272e71f.pdf
  $app->post('', 'controller.mde.Solicitudes:crearSolicitud')->add(requierePermiso('Solicitudes.Crear'));
  // $app->post('/descripcionSearch', 'controller.mde.Solicitudes:acDescripcionSearch');
  $app->post('/auto_solicitante', 'controller.mde.Solicitudes:acSolicitante');  
  $app->group('/{solicitudesID}', function () use ($app) {
    $app->post('', 'controller.mde.Solicitudes:actualizarSolicitud')->add(requierePermiso('Solicitudes.Editar'));
    $app->delete('', 'controller.mde.Solicitudes:eliminarSolicitud')->add(requierePermiso('Solicitudes.Borrar'));
})->add(recursoSolicitudes());
})->add(requierePermiso('Solicitudes'))
->add(requiereUsuario());

$app->group('/mostrarArchivo', function () use ($app) {
  $app->group('/{adjuntoID}', function () use ($app) {
      $app->post('', 'controller.mde.Solicitudes:mostrarArchivo');
  });
});


/* $app->group('/mostrarArchivo', function () use ($app) {
  $app->group('/{adjuntoID}', function () use ($app) {
      $app->post('', function ($request, $response, $args) use ($app) {
          $adjuntoID = $request->getAttribute('adjuntoID');
          $documento = $request->getAttribute('documento');
          $id_solicitud = $request->getAttribute('id_solicitud');

          $controller = new controller\mde\SolicitudesController($app);
          return $controller->mostrarArchivo($request, $response, [
              'adjuntoID' => $adjuntoID,
              'documento' => $documento,
              'id_solicitud' => $id_solicitud,
              'variable1' => $variable1,
              'variable2' => $variable2
          ]);
      });
  });
}); */



$app->group('/filtros/solicitudes', function () use ($app) {
    $app->post('/tipo', 'controller.mde.Solicitudes:acTipo');
    $app->post('/institucion', 'controller.mde.Solicitudes:acInstituciones');
    $app->post('/vinculooley', 'controller.mde.Solicitudes:acVinculoOLey');
    $app->post('/tiene_info', 'controller.mde.Solicitudes:acTieneInfo');
    $app->post('/num_tramite', 'controller.mde.Solicitudes:acNumTramite');
    $app->post('/estado', 'controller.mde.Solicitudes:acEstados');
    $app->post('/asignado', 'controller.mde.Solicitudes:acAsignados');
    $app->post('/solicitante', 'controller.mde.Solicitudes:acSolicitanteSolicitudes');
})/*->add(requierePermiso('Solicitudes.Filtros'))*/
->add(requiereUsuario());
