<?php

/**
 * Excepcion utilizada con el objetivo de pasar mensajes dentro del codigo.
 * La idea es que siempre sea una excepcion capturada.
 */
class MessageException extends BaseException {
    public function __construct($mensaje, $metadata = array()) {
        parent::__construct($mensaje, 500, $metadata);
    }
}