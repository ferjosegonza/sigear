<?php

class PermisosModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }

    public function listarPermisosQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT 
                        id,
                        key,
                        descripcion,
                        create_time,
                        update_time
                    FROM seguridad.permisos
                    WHERE delete_time is null ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'id',
            ),
            'key' => array(
                'columna' => 'upper(key)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
        ));
        return $query;
    }

    public function listarPermisos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarPermisosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalPermisos($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarPermisosQuery'), $filtros);
    }

    public function detallePermiso($id) {
        $query = $this->listarPermisosQuery(['id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function crearPermiso ($dto = array()) {
        $query = "SELECT seguridad.fn_permiso_ins (
            :key,
            :descripcion,
            :evento_cuenta_id
        )";

        $this->bindValue(":key", $dto['key']);
        $this->bindValue(":descripcion", $dto['descripcion']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function actualizarPermiso ($dto = array()) {
        $query = "SELECT seguridad.fn_permiso_upd (
            :id,
            :key,
            :descripcion,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['id']);
        $this->bindValue(":key", $dto['key']);
        $this->bindValue(":descripcion", $dto['descripcion']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function eliminarPermiso ($dto = array()) {
        $query = "SELECT seguridad.fn_permiso_del (
            :id,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['id']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }
}