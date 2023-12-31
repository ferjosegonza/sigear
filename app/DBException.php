<?php

class DBException extends PDOException {
    private $metadata = array();

    public function __construct(PDOException $e, $metadata = array()) {
        $mensaje = $e->getMessage();
        $codigo = $e->getCode();

        if(substr($mensaje, 0, 9) === 'SQLSTATE[') {
            switch ($metadata['motor']) {
                case ORACLE:
                case MYSQL:
                    preg_match('/SQLSTATE\[.+\]: (.*): (\d+) (.+)/', $mensaje, $matches);
                    $codigo = $matches[2];
                    $mensaje = 'ERROR SQL: '.$matches[3];
                    break;
                case SQLSERVER:
                    preg_match('/SQLSTATE\[.+\]: (.+)/', $mensaje, $matches);
                    $codigo = null;
                    $mensaje = 'ERROR SQL: '.$matches[1];
                    break;
                case POSTGRES:
                    $this->parsePostgreSQL($mensaje, $codigo, $metadata);
                    break;
            }
        }

        if(is_null($codigo) || !ctype_digit($codigo)) {
            $codigo = 0;
        }

        parent::__construct($mensaje, $codigo, $e);

        $this->metadata = $metadata;
    }

    public function getMetadata() {
        return $this->metadata;
    }

    private function parsePostgreSQL(&$mensaje, &$codigo, &$metadata) {
        if (preg_match('/SQLSTATE\[.+\]:.* ERROR:  (.*)DETAIL:  (.*)CONTEXT:  (.*)/is', $mensaje, $matches)) {
            $mensaje = $matches[1];
            $metadata['detalle'] = $matches[2];
            $metadata['contexto'] = $matches[3];

            // https://www.postgresql.org/docs/current/errcodes-appendix.html
            switch ($codigo) {
                case 23505:
                    // unique_violation
                    preg_match('/.* \"(.*)\"/i', $mensaje, $m);
                    $metadata['constraint'] = $m[1];
                    break;
            }
        }
    }
}