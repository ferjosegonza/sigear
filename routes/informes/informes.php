<?php

/** 
 * VISTAS INFORMES 
 */

$app->group('/informes', function () use ($app) {
    
    $app->any('',           'controller.informes.Informes:vistaInformes');
    $app->any(':nuevo',     'controller.informes.Informes:vistaNuevoInforme')->add(requierePermiso('Informes.Crear'));

    $app->group('/{informeID}', function () use ($app) {
        $app->any('', 'controller.informes.Informes:vistaDetalleInforme')->add(requierePermiso('Informes.Detalle'));
        $app->any('/confeccionar', 'controller.informes.Informes:vistaConfeccionarInforme')->add(requierePermiso('Informes.Crear'));
    });

})->add(requierePermiso('Informes'))->add(requiereUsuario()); 

/** 
 * API NFORMES 
 */
$app->group('/api/informes', function () use ($app) {

    $app->get('', 'controller.informes.Informes:listarInformes');
    
    $app->post('', 'controller.informes.Informes:addInforme')->add(requierePermiso('Informes.Crear'));   

    $app->group('/{informeID}', function () use ($app) {
        $app->get('/imagenes', 'controller.informes.Informes:listarImagenes');
        
        $app->put('', 'controller.informes.Informes:updateInforme')->add(requierePermiso('Informes.Editar'));
        $app->delete('', 'controller.informes.Informes:deleteInforme')->add(requierePermiso('Informes.Borrar'));

        $app->get('/pdf/crear', 'controller.informes.Informes:crearPdfInforme');
      
    });
})->add(requierePermiso('Informes'))->add(requiereUsuario());
 