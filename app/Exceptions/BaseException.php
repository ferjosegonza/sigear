<?php

/**
 * Excepcion base personalizada.
 */
abstract class BaseException extends Exception {
    protected $metadata = array();

    public function __construct($mensaje, $code = 500, $metadata = array()) {
        parent::__construct($mensaje, $code);
        $this->metadata = $metadata;
    }

    public function getMetadata() {
        return $this->metadata;
    }

    public function withMetadata(array $metadata) {
        $this->metadata = array_merge($this->metadata, $metadata);
        return $this;
    }
}