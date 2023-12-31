<?php

define("FLASH_SESSION_KEY", "hcdn_flash");

function flash_listarMensajes() {
    if (!isset($_SESSION[FLASH_SESSION_KEY])) {
        return array();
    }
    $mensajes = $_SESSION[FLASH_SESSION_KEY];

    return $mensajes;
}

/*
 * Tipos soportados son los mismos de bootstrap 4:
 * - primary (por defecto)
 * - secondary
 * - success
 * - danger
 * - warning
 * - info
 * - light
 * - dark
 */
function flash_nuevoMensaje($mensaje, $tipo = 'primary') {
    $mensajes = flash_listarMensajes();

    $flash = array(
        "tipo" => $tipo,
        "mensaje" => $mensaje,
    );

    array_push($mensajes, $flash);
    $_SESSION[FLASH_SESSION_KEY] = $mensajes;
}

function flash_limpiarMensajes() {
    unset($_SESSION[FLASH_SESSION_KEY]);
}

function flash_obtenerMensajes() {
    $mensajes = flash_listarMensajes();
    flash_limpiarMensajes();
    return $mensajes;
}


