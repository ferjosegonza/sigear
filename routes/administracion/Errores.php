<?php

$app->group('/errores', function () use ($app) {
    $app->get('',            'controller.administracion.Errores:vistaErrores');
    $app->get('/ver',        'controller.administracion.Errores:vistaError');

})->add(requierePermiso('Errores'))
->add(requiereUsuario());


$app->group('/api/errores', function () use ($app) {
    $app->get('',            'controller.administracion.Errores:listarErrores')->add(requierePermiso('Errores'));
    $app->put('',            'controller.administracion.Errores:guardarError');

})->add(requiereUsuario());

