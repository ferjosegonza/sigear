<?php

class AdjuntosModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }

    public function crearArchivo($dto){
        $query = "SELECT " .SCHEMA. ".recursos_ins(:p_file, :p_create_cuenta_id, :p_tabla, :p_id_tabla)";
        $this->bindValue(":p_file", $dto['p_file']);
        $this->bindValue(":p_create_cuenta_id", $dto['p_create_cuenta_id']);
        $this->bindValue(":p_tabla", $dto['p_tabla']);
        $this->bindValue(":p_id_tabla", $dto['p_id_tabla']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }
 
    public function listarAdjuntosQuery($filtros = array(), $ordenes = array()){
        $query = "SELECT id_recursos, 
                        file, 
                        active, 
                        create_time, 
                        create_cuenta_id, 
                        update_time, 
                        update_cuenta_id, 
                        delete_time, 
                        delete_cuenta_id, 
                        tabla,   
                        id_tabla 
                    FROM " .SCHEMA. ".recursos
                    WHERE delete_time is null ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'tabla' => array(
                'columna' => 'upper(tabla)',
            ),
            'id_tabla' => array(
                'columna' => 'id_tabla',
            ),
            'id_recursos' => array(
                'columna' => 'id_recursos',
            ),
        ));
        return $query;
    }

    public function listarAdjuntos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarAdjuntosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalAdjuntos($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarAdjuntosQuery'), $filtros);
    }

    public function detalleAdjuntos($id) {
        $query = $this->listarAdjuntosQuery(['id_recursos' => $id]);
        $result = $this->prepare($query);
        $result->execute();

        return $result->fetch();
    }

    public function eliminarAdjunto ($dto = array()) {
        $query = "SELECT " .SCHEMA. ".recursos_del(:p_id_recurso, :p_usuario)";

        $this->bindValue(":p_id_recurso", $dto['id_recursos']);
        $this->bindValue(":p_usuario", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

}