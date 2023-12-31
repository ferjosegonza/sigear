<?php

class SolicitantesModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }

    public function listarSolicitantesQuery($filtros = array(), $ordenes = array()) {

        $query = "SELECT s.solicitante_id, 
                            s.apellidos, 
                            s.nombres, 
                            s.documento, 
                            s.telefono, 
                            s.email,
                            -- ts.tipo_solicitante_id, 
                            -- ts.descripcion,
                            concat(s.apellidos, ' ', s.nombres) AS apellido_nombre
                    FROM " .SCHEMA. ".solicitantes s
                    -- LEFT JOIN " .SCHEMA. ".tipo_solicitante ts ON (s.tipo_solicitante_id = ts.tipo_solicitante_id)
                    WHERE s.registro_activo = true ";


        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'solicitante_id' => array(
                'columna' => 's.solicitante_id',
            ),
            'documento' => array(
                'columna' => 's.documento',
            ),
            'key' => array(
                'columna' => 'upper(s.apellidos)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb',
            ),
            'apellido_nombre' => array(
                'columna' =>  "upper(s.apellidos ||' '|| s.nombres)",
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb',
            ),
        ));

        return $query;
    }

    public function listarSolicitantes($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarSolicitantesQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalSolicitantes($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarSolicitantesQuery'), $filtros);
    }

    public function crearSolicitante ($dto = array()) {
        $query = "SELECT " .SCHEMA. ".fn_solicitante_ins (
            :apellido,
            :nombre,
            :documento,
            :telefono,
            :email,
            :evento_cuenta_id
        )";

        $this->bindValue(":apellido", $dto['apellidos']);
        $this->bindValue(":nombre", $dto['nombres']);
        $this->bindValue(":documento", $dto['documento']);
        $this->bindValue(":telefono", $dto['telefono']);
        $this->bindValue(":email", $dto['email']);
     /*   $this->bindValue(":tipo_solicitante_id", $dto['tipo_solicitante_id']);*/
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function actualizarSolicitante ($dto = array()) {
        $query = "SELECT " .SCHEMA. ".fn_solicitante_upd (
            :p_solicitante_id,
            :p_apellidos,
            :p_nombres,
            :p_documento,
            :p_telefono,
            :p_email,     
            :p_usuario 
        )";

        $this->bindValue(":p_solicitante_id", $dto['id']);
        $this->bindValue(":p_apellidos", $dto['apellidos']);
        $this->bindValue(":p_nombres", $dto['nombres']);
        $this->bindValue(":p_documento", $dto['documento']);
        $this->bindValue(":p_telefono", $dto['telefono']);
        $this->bindValue(":p_email", $dto['email']);
    /*    $this->bindValue(":p_tipo_solicitante_id", $dto['tipo_solicitante']);*/
        $this->bindValue(":p_usuario", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function detalleSolicitante($id) {
        $query = $this->listarSolicitantesQuery(['solicitante_id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function eliminarSolicitante ($dto = array()) {
        $query = "SELECT " .SCHEMA. ".fn_solicitante_del (
            :id,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['solicitante_id']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    //************LISTAR TIPOS DE SOLICITANTES**************************** */
/*    public function listarSolicitantesTiposQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT tipo_solicitante_id, descripcion, activo
                    FROM gpa.tipo_solicitante
                    WHERE activo = true";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'tipo_solicitante_id',
            ),
            'descripcion' => array(
                'columna' => 'descripcion',
                'orden' => true,
            ),
            'activo' => array(
                'columna' => 'activo',
            ),
        ));
        return $query;
    }

    public function listarSolicitantesTipos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarSolicitantesTiposQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }*/

 /* public function listarSolicitantesQuery2($filtros = array(), $ordenes = array()) {
        $query = "  SELECT solicitante_id,
                        apellidos || ' ' || nombres AS apellido_nombre,
                        documento,
                        telefono,
                        email,
                        ins_usuario,
                        ins_fecha,
                        upd_usuario,
                        upd_fecha,
                        del_usuario,
                        del_fecha,
                        registro_activo,
                        tipo_solicitante_id
                FROM gpa.solicitantes
                WHERE 1=1 and registro_activo= true";
        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'solicitante_id' => array(
                'columna' => 'solicitante_id',
            ),
            'apellido_nombre' => array(
                'columna' => 'unaccent(upper(apellido_nombre))',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'documento' => array(
                'columna' => 'documento',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'telefono' => array(
                'columna' => 'telefono',
            ),
            'email' => array(
                'columna' => 'email',
                'buscador' => false,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'tipo_solicitante_id' => array(
                'columna' => 'tipo_solicitante_id',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
        ));

        return $query;
    } */




    public function acSolicitantes($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    (" . $idCampo . ") AS ID,
                    UPPER(" . $valorCampo . ") AS VALUE
                FROM (" . $this->listarSolicitantesQuery($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

}