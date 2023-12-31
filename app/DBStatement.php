<?php

class DBStatement  {
    private $model = null;
    private $stmt = null;

    public function __construct(PDOStatement $stmt, Model $model) {
        $this->stmt = $stmt;
        $this->model = $model;
    }

    // =============================================================================
    // Funcionalidades HCDN
    // =============================================================================
    public function debugParams(){
        return $this->model->debugParams();
    }

    public function debugQuery($reemplazarVariables = true) {
        $queryString = $this->stmt->queryString;
        if (!$reemplazarVariables) {
            return $queryString;
        }

        $vars = $this->debugParams();
        $params_are_numeric = is_numeric(key($vars));

        foreach($vars as $key => &$var){
            switch(gettype($var)){
                case 'string': $var = "'{$var}'"; break;
                case 'integer': $var = "{$var}"; break;
                case 'boolean': $var = $var ? 'TRUE' : 'FALSE'; break;
                case 'NULL': $var = 'NULL';
                default:
            }
        }

        if($params_are_numeric){
            $queryString = preg_replace_callback( '/\?/', function($match) use(&$vars) {
                return array_shift($vars);
            }, $queryString);
        }else{
            $queryString = strtr($queryString, $vars);
        }

        return $queryString;
    }

    // =============================================================================
    // Funcionalidades modificadas
    // =============================================================================

    /**
     * @param int|string $parameter
     * @param mixed $value
     * @return bool
     */
    public function bindValue($parameter, $value, int $type = PDO::PARAM_STR) {
        return $this->model->bindValue($parameter, $value, $type);
    }

    /** @return bool */
    public function execute(?array $bound_input_params = null) {
        if (!is_null($bound_input_params)) {
            throw new Exception("funcionalidad de execute params no implementada");
        }
        try {
            // hacer bind de parametros y values precargados
            $bp = $this->model->getBoundedParams();
            foreach($bp as $key => $val){
                $this->stmt->bindValue($key, $val->value, $val->type);
            }
            $ok = $this->stmt->execute();

            $this->model->resetBoundedParams();
        } catch (PDOException $e) {
            $data = array(
                'queryConVariables' => preg_replace('/\s+/', ' ', $this->debugQuery()),
                'query' => preg_replace('/\s+/', ' ', $this->debugQuery(false)),
                'params' => $this->debugParams(),
                'motor' => $this->model->motor()
            );
            $this->model->resetBoundedParams();
            throw new DBException($e, $data);
        }
        return $ok;
    }

    // =============================================================================
    // Funcionalidades de PDO
    // =============================================================================

    /**
     * @param mixed $driverdata
     * @return bool
     */
    public function bindColumn($column, &$param, int $type = 0, int $maxlen = 0, $driverdata = null) {
        return $this->stmt->bindColumn($column, $param, $type, $maxlen, $driverdata);
    }

    /**
     * @param mixed $driver_options
     * @return bool
     */
    public function bindParam($parameter, &$param, int $type = PDO::PARAM_STR, int $maxlen = 0, $driverdata = null) {
        return $this->stmt->bindParam($parameter, $param, $type, $maxlen, $driverdata);
    }

    /** @return bool */
    public function closeCursor() {
        return $this->stmt->closeCursor();
    }

    /** @return int|false */
    public function columnCount() {
        return $this->stmt->columnCount();
    }

    /** @return false|null */
    public function debugDumpParams() {
        return $this->stmt->debugDumpParams();
    }

    /** @return string|false|null */
    public function errorCode() {
        return $this->stmt->errorCode();
    }

    /** @return array|false */
    public function errorInfo() {
        return $this->stmt->errorInfo();
    }

    /** @return mixed */
    public function fetch(int $fetch_style = PDO::FETCH_ASSOC, int $cursor_orientation = PDO::FETCH_ORI_NEXT, int $cursor_offset = 0) {
        return $this->stmt->fetch($fetch_style, $cursor_orientation, $cursor_offset);
    }

    /**
     * @param mixed $fetch_argument
     * @return array|false
     */
    public function fetchAll(int $fetch_style = PDO::FETCH_ASSOC, $fetch_argument=null, array $ctor_args=[]) {
        switch (func_num_args()) {
            case 0:
            case 1:
                return $this->stmt->fetchAll($fetch_style);
            case 2:
                return $this->stmt->fetchAll($fetch_style, $fetch_argument);
            case 3:
                return $this->stmt->fetchAll($fetch_style, $fetch_argument, $ctor_args);
        }
    }

    /** @return mixed */
    public function fetchColumn(int $column_number = 0) {
        return $this->stmt->fetchColumn($column_number);
    }

    /** @return mixed */
    public function fetchObject(?string $class_name = "stdClass", ?array $ctor_args = null) {
        return $this->stmt->fetchObject($class_name, $ctor_args);
    }

    /** @return mixed */
    public function getAttribute(int $attribute) {
        return $this->stmt->getAttribute($attribute);
    }

    /** @return array|false */
    public function getColumnMeta(int $column) {
        return $this->stmt->getColumnMeta($column);
    }

    /** @return bool */
    public function nextRowset() {
        return $this->stmt->nextRowset();
    }

    /** @return int|false */
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function setAttribute(int $attribute, $value) {
        return $this->stmt->setAttribute($attribute, $value);
    }

    /** @return bool */
    public function setFetchMode(int $mode, $param1=null, $param2=null) {
        switch (func_num_args()) {
            case 1:
                return $this->stmt->setFetchMode($mode);
            case 2:
                return $this->stmt->setFetchMode($mode, $param1);
            case 3:
                return $this->stmt->setFetchMode($mode, $param1, $param2);
        }
    }
}