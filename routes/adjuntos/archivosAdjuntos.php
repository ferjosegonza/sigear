<?php

$app->group('/solicitud-adjuntos', function () use ($app) {
    $app->any(':nuevo', 'controller.adjuntos.Adjuntos:vistaNuevoAdjunto');//->add(requierePermiso('SolicitudAdjuntos.Crear'));
/*    $app->get('/documentos/{fileId:.*}', 'controller.adjuntos.SolicitudAdjuntos:obtenerArchivo')->add(cacheTiempo(3600));
    $app->group('/{adjuntoID}', function () use ($app) {
        $app->any('', 'controller.adjuntos.SolicitudAdjuntos:vistaDetalleAdjunto')->add(requierePermiso('SolicitudAdjuntos.Detalle'));
    })->add(recursoAdjunto()); */
}) // ->add(requierePermiso('SolicitudAdjuntos'))
->add(requiereUsuario());

$app->group('/api/solicitud-adjuntos', function () use ($app) {
    /*
    $app->get('', 'controller.adjuntos.SolicitudAdjuntos:listarAdjuntos');
    $app->post('', 'controller.adjuntos.SolicitudAdjuntos:crearAdjunto')->add(requierePermiso('SolicitudAdjuntos.Crear')); */
    $app->group('/{adjuntoID}', function () use ($app) {
        //$app->get('', 'controller.mde.Solicitudes:obtenerArchivos');
        // $app->get('', 'controller.adjuntos.Adjuntos:obtenerArchivos');
        // $app->get('', 'controller.adjuntos.SolicitudAdjuntos:detalleAdjunto')->add(requierePermiso('SolicitudAdjuntos.Detalle'));
        // $app->post('/actualizar', 'controller.adjuntos.SolicitudAdjuntos:actualizarAdjunto')->add(requierePermiso('SolicitudAdjuntos.Editar'));
        $app->delete('', 'controller.adjuntos.Adjuntos:eliminarAdjunto');// ->add(requierePermiso('SolicitudAdjuntos.Borrar'));/*
    })->add(recursoAdjunto());
})// ->add(requierePermiso('SolicitudAdjuntos'))
->add(requiereUsuario());
/*
$app->group('/filtros/solicitud-adjuntos', function () use ($app) {
    $app->post('/descripcion', 'controller.adjuntos.SolicitudAdjuntos:acDescripcion');
})->add(requierePermiso('SolicitudAdjuntos.Filtros'))
->add(requiereUsuario());
*/