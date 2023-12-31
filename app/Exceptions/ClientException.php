<?php

/**
 * Excepcion que no se captura y llega al cliente como error http 417 por defecto.
 * El cliente vera el mensaje de error.
 */
class ClientException extends BaseException {
    public function __construct($mensaje, $status = 417, $metadata = array()) {
        if ($status < 400 || $status >= 500) {
            $status = 417;
        }
        parent::__construct($mensaje, $status, $metadata);
    }
}