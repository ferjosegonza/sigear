<?php

//==============================================================================
// RUTAS
//==============================================================================
$app->any('/', 'controller.App:botonera')->add(requiereUsuario());

$app->any('/api/docs', 'controller.App:swagger');
$app->get('/api/docs/json', 'controller.App:swaggerJson');

$app->group('/sso', function () use ($app) {
    $app->get('/login', 'controller.App:renderLogin')->add(requiereAnonimo());
    $app->get('/logout', 'controller.App:logout')->add(requiereUsuario());
    $app->any('/actualizar', 'controller.App:actualizarSesion')->add(requiereUsuario());
});

$app->group('/api/sso', function () use ($app) {
    $app->post('/login', 'controller.App:login')->add(requiereAnonimo());

    $app->get('/token', 'controller.App:getToken')->add(requiereAnonimo());
    $app->get('/refresh', 'controller.App:refreshToken');
});

$app->group('/debug', function () use ($app) {
    $app->get('/token', 'controller.App:debugToken');
    $app->get('/sesion', 'controller.App:debugSesion');//->add(requiereUsuario());
});