<?php

class PersonasModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }

    public function listarPersonasQuery($filtros = array(), $ordenes = array()) {
        $query = "  SELECT
                        p.id,
                        p.cuil,
                        p.documento,
                        p.pasaporte,
                        p.apellido,
                        p.nombre,
                        p.sexo_id,
                        s.key as sexo_key,
                        s.descripcion as sexo,
                        p.fecha_nacimiento,
                        p.fecha_fallecimiento,
                        p.foto,
                        p.entidad_recurso_id,
                        p.create_time,
                        p.create_cuenta_id,
                        p.update_time,
                        p.update_cuenta_id
                    from siac.personas p
                    left join siac.sexos s on s.id = p.sexo_id and s.delete_time is null
                    where p.delete_time is null ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'persona_id' => array(
                'columna' => 'p.id',
            ),
            'cuil' => array(
                'columna' => 'p.cuil',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'documento' => array(
                'columna' => 'p.documento',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'pasaporte' => array(
                'columna' => 'p.pasaporte',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'apellido' => array(
                'columna' => 'upper(p.apellido)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'nombre' => array(
                'columna' => 'upper(p.nombre)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'apellido_nombre' => array(
                'columna' => 'upper(p.apellido || p.nombre)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'sexo' => array(
                'columna' => 'upper(s.descripcion)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
            'sexo_id' => array(
                'columna' => 'p.sexo_id',
            ),
        ));
        return $query;
    }

    public function listarPersonas($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarPersonasQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalPersonas($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarPersonasQuery'), $filtros);
    }

    public function detallePersona($id) {
        $query = $this->listarPersonasQuery(['persona_id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function detallePersonaPorCuil($cuil) {
        $query = $this->listarPersonasQuery(['cuil' => $cuil]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function detallePersonaPorPasaporte($pasaporte) {
        $query = $this->listarPersonasQuery(['pasaporte' => $pasaporte]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function crearPersona($dto = array()) {
        $query = "SELECT siac.fn_persona_ins (
            :cuil,
            :documento,
            :pasaporte,
            :apellido,
            :nombre,
            :sexo_id,
            :fecha_nacimiento,
            :fecha_fallecimiento,
            :foto,
            :cuenta_id
        )";

        $this->bindValue(":cuil", $dto['cuil']);
        $this->bindValue(":documento", $dto['documento']);
        $this->bindValue(":pasaporte", $dto['pasaporte']);
        $this->bindValue(":apellido", $dto['apellido']);
        $this->bindValue(":nombre", $dto['nombre']);
        $this->bindValue(":sexo_id", $dto['sexo_id']);
        $this->bindValue(":fecha_nacimiento", getISO8601FromDate(date_create_from_format($dto['fecha_nacimiento'],'d/m/Y')));
        $this->bindValue(":fecha_fallecimiento", getISO8601FromDate(date_create_from_format($dto['fecha_fallecimiento'],'d/m/Y')));
        $this->bindValue(":foto", $dto['foto_persona']);
        $this->bindValue(":cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function actualizarPersona($dto = array()) {
        $query = "SELECT siac.fn_persona_upd (
            :persona_id,
            :cuil,
            :documento,
            :pasaporte,
            :apellido,
            :nombre,
            :sexo_id,
            :fecha_nacimiento,
            :fecha_fallecimiento,
            :foto,
            :entidad_recurso_id,
            :cuenta_id
        )";

        $this->bindValue(":persona_id", $dto['persona_id']);
        $this->bindValue(":cuil", $dto['cuil']);
        $this->bindValue(":documento", $dto['documento']);
        $this->bindValue(":pasaporte", $dto['pasaporte']);
        $this->bindValue(":apellido", $dto['apellido']);
        $this->bindValue(":nombre", $dto['nombre']);
        $this->bindValue(":sexo_id", $dto['sexo_id']);
        $this->bindValue(":fecha_nacimiento", getISO8601FromDate(date_create_from_format($dto['fecha_nacimiento'],'d/m/Y')));
        $this->bindValue(":fecha_fallecimiento", getISO8601FromDate(date_create_from_format($dto['fecha_fallecimiento'],'d/m/Y')));
        $this->bindValue(":foto", $dto['foto']);
        $this->bindValue(":entidad_recurso_id", $dto['entidad_recurso_id']);
        $this->bindValue(":cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function eliminarPersona($dto = array()) {
        $query = "SELECT siac.fn_persona_del (
            :persona_id,
            :cuenta_id
        )";

        $this->bindValue(":persona_id", $dto['persona_id']);
        $this->bindValue(":cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function acPersonas($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    *,
                    /*UPPER*/(" . $idCampo . ") AS ID,
                    UPPER(" . $valorCampo . ") AS VALUE
                FROM (" . $this->listarPersonasQuery($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC
        ";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    #==========================================================================================================

}