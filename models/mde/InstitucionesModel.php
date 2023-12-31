<?php

class InstitucionesModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }
    
    public function listarInstitucionesQuery($filtros = array(), $ordenes = array()) {
        
        $query = "select institucion_id, 
                        descripcion, 
                        ins_fecha 
                        from " .SCHEMA. ".instituciones 
                        where del_fecha IS null ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'institucion_id' => array(
                'columna' => 'institucion_id',
            ),
         
            'key' => array(
                'columna' => 'upper(descripcion)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
         
        ));

        return $query;
    }

    public function listarInstituciones($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarInstitucionesQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }
    
    public function totalInstituciones($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarInstitucionesQuery'), $filtros);
    }
    
    public function crearInstitucion ($dto = array()) {
        $query = "SELECT " .SCHEMA. ".fn_instituciones_ins (
            :descripcion,
            :evento_cuenta_id
        )";

        if (!isset($dto['descripcion']) || empty($dto['descripcion'])) {
            throw new Exception('La propiedad "descripcion" en "$dto" no está definida o está vacía');
        }
    
        $this->bindValue(":descripcion", $dto['descripcion']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);
    
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];  
    }

    public function actualizarInstitucion ($dto = array()) {
        $query = "SELECT " .SCHEMA. ".fn_instituciones_upd (
            :p_institucion_id,
            :p_descripcion,
            :p_usuario
        )";

        if (!isset($dto['descripcion']) || empty($dto['descripcion'])) {
            throw new Exception('La propiedad "descripcion" en "$dto" no está definida o está vacía');
        }

        $this->bindValue(":p_institucion_id", $dto['id']);
        $this->bindValue(":p_descripcion", $dto['descripcion']);
        $this->bindValue(":p_usuario", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function detalleInstituciones($id) {
        $query = $this->listarInstitucionesQuery(['institucion_id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function eliminarInstitucion ($dto = array()) {
        $query = "SELECT " .SCHEMA. ".fn_instituciones_del (
            :id,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['institucion_id']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }
}