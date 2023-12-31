<?php

class InformesModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }
    
    public function listarImagenesQuery($filtros = array(), $ordenes = array()){
        $query = "SELECT ia.* 
                    FROM " .SCHEMA. ".informe_archivos ia
                 WHERE ia.del_fecha is null ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id_imagen' => array(
                'columna' => 'ia.informe_archivo_id',
            ),
            'informeID' => array(
                'columna' => 'ia.informe_id',
            ),
            'repositorio' => array(
                'columna' => 'ia.repositorio_id',
            ),
            'orden' => array(
                'columna' => 'ia.orden',
                'orden' => true
            )
        ));

        return $query;
    }
    public function listarInformesQuery($filtros = array(), $ordenes = array()){
        $query = "SELECT i.* 
                    FROM " .SCHEMA. ".vw_informes i 
                    WHERE i.registro_activo = true ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id_informe' => array(
                'columna' => 'i.informe_id',
            ),
            'id_solicitud' => array(
                'columna' => 'i.solicitud_id',
            ),
            'estado' => array(
                'columna' => 'i.informe_estado_id',
            )
        ));
       
        return $query;
    }

    public function listarInformes($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarInformesQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalInformes($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarInformesQuery'), $filtros);
    }

    public function detalleInforme($id) {
        $query = $this->listarInformesQuery(['id_informe' => $id]);
        $result = $this->prepare($query);
        $result->execute();

        return $result->fetch();
    }

    public function listarImagenes($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarImagenesQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalImagenes($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarImagenesQuery'), $filtros);
    }

    public function detalleImagen($id) {
        $query = $this->listarInformesQuery(['id_imagen' => $id]);
        $result = $this->prepare($query);
        $result->execute();

        return $result->fetch();
    }

    public function estadosInforme() {
        $query = "SELECT * FROM ".SCHEMA.".informe_estados ";
        $result = $this->prepare($query);
        $result->execute();

        return $result->fetchAll();
    }

    public function addInforme ($solicitud, $titulo, $usuario) {
        $query = "CALL " .SCHEMA. ".sp_informe_ins(
                        :p_solicitud_id,
                        :p_descripcion, 
                        :p_usuario_id, 
                        :p_informe_id
                    )";

        $this->bindValue(":p_solicitud_id", $solicitud);
        $this->bindValue(":p_descripcion", $titulo);
        $this->bindValue(":p_usuario_id", $usuario);
        
        $result = $this->prepare($query);

        $result->bindParam(":p_informe_id", $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function updateInforme ($id, $titulo, $estado, $usuario) {
       
        $query = "CALL " .SCHEMA. ".sp_informe_upd(
                    :p_informe_id, 
                    :p_descripcion, 
                    :p_informe_estado_id, 
                    :p_usuario_id
                )";

        $this->bindValue(":p_informe_id", $id);
        $this->bindValue(":p_descripcion", $titulo);
        $this->bindValue(":p_informe_estado_id", $estado);
        $this->bindValue(":p_usuario_id", $usuario);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function deleteInforme ($id, $usuario) {
        $query = "CALL " .SCHEMA. ".sp_informe_del(:p_informe_id, :p_usuario_id)";

        $this->bindValue(":p_informe_id", $id);
        $this->bindValue(":p_usuario_id", $usuario);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

}