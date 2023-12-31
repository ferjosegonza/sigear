<?php

class ArchivosModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }
    
    public function listarArchivosQuery($filtros = array(), $ordenes = array()){
        
        if(isset($filtros['ocr'])){
            $query = "SELECT r.*, ocr.* 
                    FROM " .SCHEMA. ".vw_repositorio r 
                    JOIN " .SCHEMA. ".ocr_por_pagina ocr ON (r.repositorio_id = ocr.repositorio_id)
                    WHERE 1=1 ";
        } else {
            $query = "SELECT r.* 
                    FROM " .SCHEMA. ".vw_repositorio r 
                    WHERE 1=1 ";
        }
        

        $items = array(
            'asunto' => array(
                'columna' => 'upper(r.asunto)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'apellido' => array(
                'columna' => 'upper(r.apellido_nombre)',
                'orden' => true,
                'formato' => 'strtodb',
                'operador' => 'LIKE'
            ),
            'id_fondo' => array(
                'columna' => 'r.fondo_id',
            ),
            'id_nodo' => array(
                'columna' => 'r.nodo_id',
            ),
            'legajo' => array(
                'columna' => 'r.legajo',
            ),
            'seccion' => array(
                'columna' => 'upper(r.seccion_mesa)',
                'orden' => true,
                'formato' => 'strtodb',
                'operador' => 'LIKE'
            ),
            'repositorio' => array(
                'columna' => 'r.repositorio_id',
            )
        );

        if(isset($filtros['ocr'])){
            $items['ocr'] = array(
                'columna' => 'upper(ocr.texto_ocr)',
                'orden' => true,
                'formato' => 'strtodb',
                'operador' => 'LIKE'
            );
        }

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, $items);
        
        return $query;
    }

    public function listarArchivos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarArchivosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalArchivos($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarArchivosQuery'), $filtros);
    }

    public function detalleArchivo($id) {
        $query = $this->listarArchivosQuery(['id_archivo' => $id]);
        $result = $this->prepare($query);
        $result->execute();

        return $result->fetch();
    }

    public function estadosArchivo() {
        $query = "SELECT * FROM ".SCHEMA.".archivo_estados ";
        $result = $this->prepare($query);
        $result->execute();

        return $result->fetchAll();
    }

    public function addArchivo ($informeID, $repoID, $nombre, $orden, $usuario) {
        $query = "CALL " .SCHEMA. ".sp_informe_archivo_ins(
                :p_informe_id, 
                :p_repositorio_id, 
                :p_nombre, 
                :p_orden, 
                :p_usuario_id, 
                :p_informe_archivo_id
        )";

        $this->bindValue(":p_informe_id", $informeID);
        $this->bindValue(":p_repositorio_id", $repoID);
        $this->bindValue(":p_nombre", $nombre);
        $this->bindValue(":p_orden", $orden);
        $this->bindValue(":p_usuario_id", $usuario);
        
        $result = $this->prepare($query);

        $result->bindParam(":p_informe_archivo_id", $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4000);
        
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function updateArchivo ($id, $nombre, $orden, $usuario) {
       
        $query = "CALL " .SCHEMA. ".sp_informe_archivo_upd(
                    :p_informe_archivo_id, 
                    :p_nombre, 
                    :p_orden, 
                    :p_usuario_id
                )";

        $this->bindValue(":p_informe_archivo_id", $id);
        $this->bindValue(":p_nombre", $nombre);
        $this->bindValue(":p_orden", $orden);
        $this->bindValue(":p_usuario_id", $usuario);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function deleteArchivo ($id, $usuario) {
        $query = "CALL " .SCHEMA. ".sp_informe_archivo_del(:p_informe_archivo_id, :p_usuario_id)";

        $this->bindValue(":p_informe_archivo_id", $id);
        $this->bindValue(":p_usuario_id", $usuario);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

}