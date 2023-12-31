<?php

$app->group('/fondos', function () use ($app) {
    $app->any('',       'controller.fondos.Fondos:vistaFondos');
    $app->any('/OCRporPagina',  'controller.fondos.Fondos:OCRporPagina');
    $app->any('/deTifaJPG',  'controller.fondos.Fondos:deTifaJPG');
    $app->any('/getPaginasOCR/{repositorioID}',  'controller.fondos.Fondos:getPaginasOCR');
    $app->any('/mostrarGaleria/{repositorioID}',  'controller.fondos.Fondos:mostrarGaleria');
    $app->any('/cargarImagen/{repositorioID}',  'controller.fondos.Fondos:cargarImagen');
    $app->any('/mostrarJPG',  'controller.fondos.Fondos:mostrarJPG');
    $app->any('/llamarScript',  'controller.fondos.Fondos:llamarScript');
    $app->any(':nuevo',       'controller.fondos.Fondos:vistaNuevoFondos')->add(requierePermiso('Fondos.Crear'));
    $app->any('/fondosExcel',  'controller.fondos.Fondos:importarExcel')->add(requierePermiso('Fondos.ImportarExcel'));
    $app->any('/repositorioPlana',  'controller.fondos.Fondos:vistaRepositorioPlana');
    $app->any('/repositorio',  'controller.fondos.Fondos:vistaRepositorio');
    $app->any('/detalleRepositorio/{repositorioID}',  'controller.fondos.Fondos:detalleRepositorio');
    $app->any('/arbol',  'controller.fondos.Fondos:vistaArbol');
    $app->any('/buscarRepo',  'controller.fondos.Fondos:buscarRepo');
    $app->any('/mostrarProbandoOCR', 'controller.fondos.Fondos:mostrarProbandoOCR');
    $app->group('/mostrar_progreso/{idExcelValue}', function () use ($app) {
      $app->any('', 'controller.fondos.Fondos:mostrarProgreso');
    });
    $app->group('/{fondoID}', function () use ($app) {
      $app->any('', 'controller.fondos.Fondos:vistaDetalleFondo')->add(requierePermiso('Fondos.Detalle'));
      $app->group('/columnas', function () use ($app) {
        $app->any('', 'controller.fondos.Fondos:vistaDetalleFondosColumna')->add(requierePermiso('Fondos.Columnas'));
    });
    })->add(recursoFondo());

})->add(requierePermiso('Fondos'))
->add(requiereUsuario());

$app->group('/api/fondos', function () use ($app) {
  $app->get('', 'controller.fondos.Fondos:listarFondos');
  $app->post('', 'controller.fondos.Fondos:crearFondo')->add(requierePermiso('Fondos.Crear'));
  $app->post('/procesarExcel',  'controller.fondos.Fondos:procesarExcel')->add(requierePermiso('Fondos.ImportarExcel'));
  $app->group('/repositorioEditar/{id_repo_editado}', function () use ($app) {
    $app->any('', 'controller.fondos.Fondos:repositorioEditar');
  });
  $app->group('/cargarEnBase/{idExcelValue}', function () use ($app) {
    $app->any('', 'controller.fondos.Fondos:cargarDatosEnBase');
  });
  $app->group('/cant_para_ocerrear/{idExcel}', function () use ($app) {
    $app->any('', 'controller.fondos.Fondos:cantidadParaOcerrear');
  });
  $app->group('/ocrIndividual/{idExcelNuevo}', function () use ($app) {
    $app->any('', 'controller.fondos.Fondos:ocrIndividual');
  });
  $app->get('/repositorio',  'controller.fondos.Fondos:listarRepositorio');
  $app->get('/repositorioPlana',  'controller.fondos.Fondos:listarRepositorioPlana');
  $app->group('/{fondoID}', function () use ($app) {
    $app->put('', 'controller.fondos.Fondos:actualizarFondo')->add(requierePermiso('Fondos.Editar'));
    $app->delete('', 'controller.fondos.Fondos:eliminarFondo')->add(requierePermiso('Fondos.Borrar'));
    $app->group('/columnas', function () use ($app) {
      $app->get('', 'controller.fondos.Fondos:listarFondoColumnas')->add(requierePermiso('Fondos.Columnas'));
      $app->post('', 'controller.fondos.Fondos:guardarFondoColumnas')->add(requierePermiso('Fondos.Columnas.Editar'));
  });
})->add(recursoFondo());
})->add(requierePermiso('Fondos'))
->add(requiereUsuario());

/*
$app->group('/filtros/fondos', function () use ($app) {
  $app->any('/fondoNombre', 'controller.fondos.Fondos:acFondo');
})/*->add(requierePermiso('Solicitudes.Filtros'))*/
//->add(requiereUsuario());



$app->group('/filtros/fondos', function () use ($app) {
  $app->any('/fondoNombre', 'controller.fondos.Fondos:acFondo');
  $app->any('/apellido', 'controller.fondos.Fondos:acApellido');
  $app->any('/asunto', 'controller.fondos.Fondos:acAsunto');
  $app->any('/nodoArbol', 'controller.fondos.Fondos:acnodoArbol');
  
  //$app->post('/fondoNombre', 'controller.fondos.Fondos:acDescripcion');
})/*->add(requierePermiso('Solicitudes.Filtros'))*/
->add(requiereUsuario());

