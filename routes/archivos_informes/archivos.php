<?php

/** 
 * VISTAS ARCHIVOS 
 */
$app->group('/archivos', function () use ($app) {
        
    $app->any('/buscar', 'controller.archivos.Archivos:vistaBuscarArchivos');
    $app->any('/editor', 'controller.archivos.Archivos:vistaEditorImagen');

    $app->group('/{informeID}', function () use ($app) {
      
        $app->get('/{archivoID}', 'controller.archivos.Archivos:getImagenArchivo');
      
    });
})->add(requierePermiso('Informes.Crear'))->add(requiereUsuario()); 

/** 
 * API ARCHIVOS 
 */
$app->group('/api/archivos', function () use ($app) {

    $app->get('/buscar', 'controller.archivos.Archivos:listarArchivos');
    
    $app->post('/agregar', 'controller.archivos.Archivos:addAImagenInforme');

    $app->post('/update/orden', 'controller.archivos.Archivos:updateOrden');
    
    $app->post('', 'controller.archivos.Archivos:addArchivo');   

    $app->group('/tiff', function () use ($app) {
      
        $app->any('/{repositorioID}', 'controller.archivos.Archivos:getTiffFromRepositorio');

      
    });

    $app->group('/{archivoID}', function () use ($app) {
      
        $app->post('', 'controller.archivos.Archivos:updateImagenInforme');
        $app->delete('', 'controller.archivos.Archivos:deleteArchivo');
      
    });

    
})->add(requierePermiso('Informes.Crear'))->add(requiereUsuario());
 