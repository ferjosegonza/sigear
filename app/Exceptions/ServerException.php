<?php

/**
 * Excepcion que no se captura y llega al cliente como error http 500.
 * El cliente nunca ve el mensaje de error excepto que la app se encuentre en modo DEBUG.
 */
class ServerException extends BaseException {
    public function __construct($mensaje, $metadata = array()) {
        parent::__construct($mensaje, 500, $metadata);
    }
}