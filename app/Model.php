<?php
abstract class Model extends Container {
    protected $_db;
    protected $_schema;
    private $bounded_variables = array();

    public function __construct($conexion) {
        global $container;
        parent::__construct($container);
        $hashConexion = md5(serialize($conexion));
        if (!$this->container->has($hashConexion)) {
            $this->container[$hashConexion] = function($c) use ($conexion) {
                return new Database($conexion);
            };
        }
        $this->_db = $this->container->get($hashConexion);
        $this->_schema = $conexion['db_schema'];
    }

    public function motor() {
        return $this->_db->motor();
    }

    public function bindValue($param, $value, $type = PDO::PARAM_STR){
        $this->bounded_variables[$param] = (object) array('type' => $type, 'value' => $value);
    }

    public function bindFecha($parameter, $variable, $formato) {
        if (is_null($variable)) {
            return $this->bindValue($parameter, $variable, PDO::PARAM_STR);
        }
        return $this->bindValue($parameter, getISO8601FromDate(date_create_from_format($formato, $variable)), PDO::PARAM_STR);
    }

    public function bindBoolean($parameter, $variable) {
        if (is_null($variable)) {
            return $this->bindValue($parameter, $variable, PDO::PARAM_STR);
        }

        return $this->bindValue($parameter, $variable, PDO::PARAM_BOOL);
    }

    public function getBoundedParams() {
        return $this->bounded_variables;
    }

    public function resetBoundedParams() {
        $this->bounded_variables = array();
    }

    public function debugParams(){
        $vars = array();
        foreach($this->bounded_variables as $key => $val){
            $vars[$key] = $val->value;
            if(is_null($vars[$key])){
                continue;
            }
            switch($val->type){
                case PDO::PARAM_STR: $type = 'string'; break;
                case PDO::PARAM_BOOL: $type = 'boolean'; break;
                case PDO::PARAM_INT: $type = 'integer'; break;
                case PDO::PARAM_NULL: $type = 'null'; break;
                default: $type = FALSE;
            }
            if($type !== FALSE) {
                settype($vars[$key], $type);
            }
        }
        if(is_numeric(key($vars))) {
            ksort($vars);
        }
        return $vars;
    }

    public function prepare($query) {
        return New DBStatement($this->_db->prepare($query), $this);
    }

    public function beginTransaction() {
        return $this->_db->beginTransaction();
    }

    public function inTransaction() {
        return $this->_db->inTransaction();
    }

    public function commit() {
        $result = 1;
        if ($this->_db->inTransaction()) {
            $result = $this->_db->commit();
        }
        return $result;
    }

    public function rollBack() {
        return $this->_db->rollBack();
    }

    // listados
    protected function listarRecurso($fnListar = null, $inicio = null, $cantidad = null, $filtros = array(), $ordenes = array(), $debug = false, $fetchType = PDO::FETCH_ASSOC) {
        if (is_null($fnListar)) {
            throw new Exception('fnListar no puede ser null');
        }
        $query = call_user_func($fnListar, $filtros, $ordenes);
        if (!is_null($inicio) && !is_null($cantidad) && $cantidad != "-1") {
            switch ($this->motor()) {
                case ORACLE:
                    $query = "SELECT T2.* FROM ( SELECT ROWNUM AS nro, T1.* FROM ( " . $query . " ) T1 ) T2
                    WHERE T2.nro BETWEEN :pagInicio AND :pagFinal";
                    $this->bindValue(":pagInicio", intval($inicio + 1));
                    $this->bindValue(":pagFinal", intval($inicio + $cantidad));
                    break;
                case SQLSERVER:
                    // FORZAMOS ORDER BY SI NO HAY NINGUNO DEFINIDO
                    // EL MISMO ES REQUERIDO PARA ESTA FORMA DE PAGINAR
                    if (strpos($query, 'ORDER BY') === false) {
                        $query .= " ORDER BY 1 ";
                    }
                    $query .= " OFFSET :pagInicio ROWS FETCH NEXT :pagCantidad ROWS ONLY ";
                    $this->bindValue(":pagInicio", intval($inicio), PDO::PARAM_INT);
                    $this->bindValue(":pagCantidad", intval($cantidad), PDO::PARAM_INT);
                    break;
                case MYSQL:
                    $query .= " LIMIT :pagCantidad OFFSET :pagInicio ";
                    $this->bindValue(":pagCantidad", intval($cantidad), PDO::PARAM_INT);
                    $this->bindValue(":pagInicio", intval($inicio), PDO::PARAM_INT);
                    break;
                case POSTGRES:
                    $query .= " LIMIT :pagCantidad OFFSET :pagInicio ";
                    $this->bindValue(":pagCantidad", intval($cantidad), PDO::PARAM_INT);
                    $this->bindValue(":pagInicio", intval($inicio), PDO::PARAM_INT);
                    break;
            }
        }
        $result = $this->prepare($query);
        if ($debug) {
            debug($result->debugQuery());
        }
        $result->execute();
        $data = $result->fetchAll($fetchType);
        $result->closeCursor();
        $result = null;
        return $data;
    }

    // listados con clob
    //POR LAS DUDAS LA HAGO INDIVIDUAL, DESPUES SE VERA SI SE UNE A LA OTRA
    protected function listarRecursoClob($fnListar = null, $inicio = null, $cantidad = null, $filtros = array(), $ordenes = array(), $debug = false, $fetchType = PDO::FETCH_ASSOC, $clobFields = []) {
        if (is_null($fnListar)) {
            throw new Exception('fnListar no puede ser null');
        }
        $query = call_user_func($fnListar, $filtros, $ordenes);
        if (!is_null($inicio) && !is_null($cantidad) && $cantidad != "-1") {
            switch ($this->motor()) {
                case ORACLE:
                    $query = "SELECT T2.* FROM ( SELECT ROWNUM AS nro, T1.* FROM ( " . $query . " ) T1 ) T2
                    WHERE T2.nro BETWEEN :pagInicio AND :pagFinal";
                    $this->bindValue(":pagInicio", intval($inicio + 1));
                    $this->bindValue(":pagFinal", intval($inicio + $cantidad));
                    break;
                case SQLSERVER:
                    // FORZAMOS ORDER BY SI NO HAY NINGUNO DEFINIDO
                    // EL MISMO ES REQUERIDO PARA ESTA FORMA DE PAGINAR
                    if (strpos($query, 'ORDER BY') === false) {
                        $query .= " ORDER BY 1 ";
                    }
                    $query .= " OFFSET :pagInicio ROWS FETCH NEXT :pagCantidad ROWS ONLY ";
                    $this->bindValue(":pagInicio", intval($inicio), PDO::PARAM_INT);
                    $this->bindValue(":pagCantidad", intval($cantidad), PDO::PARAM_INT);
                    break;
                case MYSQL:
                    $query = " LIMIT :pagCantidad OFFSET :pagInicio ";
                    $this->bindValue(":pagCantidad", intval($cantidad), PDO::PARAM_INT);
                    $this->bindValue(":pagInicio", intval($inicio), PDO::PARAM_INT);
                    break;
                case POSTGRES:
                    $query .= " LIMIT :pagCantidad OFFSET :pagInicio ";
                    $this->bindValue(":pagCantidad", intval($cantidad), PDO::PARAM_INT);
                    $this->bindValue(":pagInicio", intval($inicio), PDO::PARAM_INT);
                    break;
            }
        }
        $result = $this->prepare($query);
        if ($debug) {
            debug($result->debugQuery());
        }
        $result->execute();
        $data = [];
        while ($row = $result->fetch($fetchType)) {
            foreach($clobFields as $clobField){
                if(!is_null($row[$clobField])) {
                    $row[$clobField] = ($row[$clobField]) ? stream_get_contents($row[$clobField]) : "";
                }
            }
            $data[] = $row;
        }
        $result->closeCursor();
        $result = null;
        return $data;
    }

    protected function totalRecurso($fnListar = null, $filtros = array()) {
        if (is_null($fnListar)) {
            throw new Exception('fnListar no puede ser null');
        }
        $query = call_user_func($fnListar, $filtros);
        $query = "SELECT count(1) FROM ( " . $query . " ) T1";
        $result = $this->prepare($query);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_COLUMN);
        $result->closeCursor();
        $result = null;
        return $data;
    }

    public function obtenerQueryFiltros($filtros, $config) {
        if (empty($filtros)) {
            return '';
        }
        if (!is_array($filtros)) {
            throw new Exception('obtenerQueryFiltros "filtros" debe ser un array');
        }
        if (!is_array($config)) {
            throw new Exception('obtenerQueryFiltros "config" debe ser un array');
        }

        $query = '';

        foreach ($filtros as $parametro => $valor) {
            $filtroConfig = $config[$parametro];
            if (is_null($filtroConfig) || $parametro === 'buscador' || is_null($valor)) {
                continue;
            }
            if (!is_array($filtroConfig)) {
                throw new Exception('obtenerQueryFiltros $config["'.$parametro.'"] debe ser un array');
            }

            // columna
            $columna = $filtroConfig['columna'];
            if (is_null($columna)) {
                throw new Exception('obtenerQueryFiltros $config["'.$parametro.'"] requiere key "columna"');
            }

            // parseo de filtros dinamicos
            $listado = false;
            $modo = false;
            if(preg_match('/^\[(\w+)(?::(.+))?\]$/u', $valor, $matches)) {
                $modo = mb_strtoupper($matches[1]);
                $valorModo = $matches[2]; // puede venir null
                switch($modo) {
                    case 'EQ':
                        // Equality test
                        $operator = '=';
                        break;
                    case 'NEQ':
                        // Inequality test
                        $operator = '!=';
                        break;
                    case 'GT':
                        // Greater than test
                        $operator = '>';
                        break;
                    case 'GTE':
                        // Greater than or equal to test
                        $operator = '>=';
                        break;
                    case 'LT':
                        // Less than test.
                        $operator = '<';
                        break;
                    case 'LTE':
                        // Less than or equal to test.
                        $operator = '<=';
                        break;
                    case 'IN':
                        // Equivalent to any member of.
                        $operator = 'IN';
                        $listado = true;
                        break;
                    case 'NIN':
                        // Inequivalent to any member of.
                        $operator = 'NOT IN';
                        $listado = true;
                        break;
                    case 'NU':
                        // Tests for nulls
                        $operator = 'IS NULL';
                        $valorModo = null;
                        break;
                    case 'NNU':
                        // Tests for not nulls
                        $operator = 'IS NOT NULL';
                        $valorModo = null;
                        break;
                    case 'LI':
                        // Tests for not nulls
                        $operator = 'LIKE';
                        if ($this->motor() === POSTGRES) {
                            $operator = 'ILIKE';
                        }
                        break;
                    case 'NLI':
                        // Tests for not nulls
                        $operator = 'NOT LIKE';
                        if ($this->motor() === POSTGRES) {
                            $operator = 'NOT ILIKE';
                        }
                        break;
                    default:
                        $modo = false;
                }
                if ($modo) {
                    $valor = $valorModo;
                }
            }

            if (!$modo){
                // configurar operador
                $operator = '=';
                if ($filtroConfig['operador']) {
                    $operator = $filtroConfig['operador'];
                }
                if ($operator === 'LIKE') {
                    $valor = '%'. $valor . '%';
                }
            }

            // agregamos operador
            $query .= ' AND '. $columna . ' ' . $operator. ' ';

            if (is_null($valor)) {
                continue;
            }

            // aplicar formato si se encuentra definido
            if (!is_null($filtroConfig['formato'])) {
                if (!is_callable($filtroConfig['formato'])) {
                    throw new Exception('obtenerQueryFiltros $config["'.$parametro.'"]["formato"] no es un callable');
                }
                $valor = $filtroConfig['formato']($valor);
            }

            if ($listado) {
                $valor = empty(trim($valor)) ? array() : preg_split("/\\s*,\\s*/", $valor);
                $query .= " ( ";
                $last = count($valor) - 1;
                foreach ($valor as $key => $item) {
                    $query .= ':' . $parametro . '_' . $key . ' ';

                    if ($filtroConfig['tipo'] == 'fecha') {
                        $this->bindFecha(':' . $parametro . '_' . $key, $item, 'd/m/Y');
                    }elseif ($filtroConfig['tipo'] == 'boolean') {
                            $this->bindBoolean(':' . $parametro . '_' . $key, $item);
                    } else {
                        $this->bindValue(':' . $parametro . '_' . $key, $item);
                    }
                    if ($key < $last) {
                        $query .= ', ';
                    }
                }
                $query .= " ) ";
            } else {
                $query .= ':' . $parametro . ' ';
                $this->bindValue(':' . $parametro, strtoupper($valor));
            }
        }

        if (!empty($filtros['buscador'])) {
            $camposBusqueda = array();
            $i = 0;
            foreach ($config as $parametro => $filtroConfig) {
                if (!is_array($filtroConfig)) {
                    throw new Exception('obtenerQueryFiltros $config["'.$parametro.'"] debe ser un array');
                }
                if (!$filtroConfig['buscador']) {
                    continue;
                }
                // columna
                $columna = $filtroConfig['columna'];
                if (is_null($columna)) {
                    throw new Exception('obtenerQueryFiltros $config["'.$parametro.'"] requiere key "columna"');
                }

              
                //$camposBusqueda[] = $columna . ' LIKE :buscador'.$i;
                if ($this->motor() === POSTGRES) {
                    $camposBusqueda[] = $columna . ' ILIKE :buscador'.$i;
                } else {
                    $camposBusqueda[] = $columna . ' LIKE :buscador'.$i;
                }
                
                $i++;
            }
            if (!empty($camposBusqueda)) {
                $query .= ' AND (' . implode(' OR ', $camposBusqueda) . ') ';
                for ($i=0; $i < count($camposBusqueda); $i++) {
                    $this->bindValue(':buscador'.$i, '%'. strtodb($filtros['buscador']) . '%');
                }

            }
        }
        return $query;
    }

    public function obtenerQueryOrdenes($ordenes, $keyColumn) {
        if (empty($ordenes)) {
            return '';
        }
        if (!is_array($ordenes)) {
            throw new Exception('obtenerQueryOrdenes "ordenes" debe ser un array');
        }
        if (!is_array($keyColumn)) {
            throw new Exception('obtenerQueryOrdenes "keyColumn" debe ser un array');
        }

        // cambiamos claves por columnas y devolvemos cadenas formadas
        $ordenesMapeados = array_map(function($orden) use ($keyColumn) {
            $col = $keyColumn[key($orden)];
            return is_null($col) ? '' : $col .' '. strtoupper(current($orden));
        }, $ordenes);

        // filtramos cadenas nulas
        $ordenesFiltrados = array_filter($ordenesMapeados);

        // si todas las cadenas fueron nulas devolvemos cadena vacia
        if (empty($ordenesFiltrados)) {
            return '';
        }

        // devolvemos la query
        return ' ORDER BY ' . implode(', ', $ordenesFiltrados) . ' ';
    }

    public function obtenerQueryFiltrosOrdenes($filtros, $ordenes, $config) {
        $query = '';

        if (!is_array($config)) {
            throw new Exception('obtenerQueryFiltrosOrdenes "config" debe ser un array');
        }

        // generacion de filtros
        $configFiltros = array_filter($config, function($item) {
            return $item['filtro'] !== false;
        });


        $query .= $this->obtenerQueryFiltros($filtros, $configFiltros);

        // generacion de group by
        $configGroupBy = array_filter($config, function($item) {
            return $item['groupBy'] === true;
        });
        $configGroupByMapeado = array_map(function($groupBy) {
            $columna = $groupBy['columna'];
            if (is_null($columna)) {
                throw new Exception('obtenerQueryFiltrosOrdenes $config["'.key($groupBy).'"] requiere key "columna"');
            }
            return $columna;
        }, $configGroupBy);
        if (!empty($configGroupByMapeado)) {
            $query .= ' GROUP BY ' . implode(', ', $configGroupByMapeado) . ' ';
        }

        // generacion de ordenes
        $configOrdenes = array_filter($config, function($item) {
            return $item['orden'] !== false;
        });
        $configOrdenesMapeado = array_map(function($orden) {
            $columna = $orden['columna'];
            if (is_null($columna)) {
                throw new Exception('obtenerQueryFiltrosOrdenes $config["'.key($orden).'"] requiere key "columna"');
            }
            return $columna;
        }, $configOrdenes);
        $query .= $this->obtenerQueryOrdenes($ordenes, $configOrdenesMapeado);

        return $query;
    }
}
