<?php

class RolesModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }

    public function listarRolesQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT
                        id,
                        key,
                        descripcion,
                        status,
                        create_time,
                        update_time
                    FROM seguridad.roles
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
            'status' => array(
                'columna' => 'status',
            ),
        ));
        return $query;
    }

    public function listarRoles($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarRolesQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalRoles($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarRolesQuery'), $filtros);
    }

    public function detalleRol($id) {
        $query = $this->listarRolesQuery(['id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function crearRol ($dto = array()) {
        $query = "SELECT seguridad.fn_rol_ins (
            :key,
            :descripcion,
            :status,
            :evento_cuenta_id
        )";

        $this->bindValue(":key", $dto['key']);
        $this->bindValue(":descripcion", $dto['descripcion']);
        $this->bindValue(":status", $dto['status']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function actualizarRol ($dto = array()) {
        $query = "SELECT seguridad.fn_rol_upd (
            :id,
            :key,
            :descripcion,
            :status,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['id']);
        $this->bindValue(":key", $dto['key']);
        $this->bindValue(":descripcion", $dto['descripcion']);
        $this->bindValue(":status", $dto['status']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function eliminarRol ($dto = array()) {
        $query = "SELECT seguridad.fn_rol_del (
            :id,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['id']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    //============================================================================
    // PERMISOS
    //============================================================================

    public function listarRolPermisosQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT
                        rp.id,
                        rp.rol_id,
                        r.key as rol_key,
                        rp.permiso_id,
                        p.key as permiso_key,
                        rp.create_time,
                        rp.update_time
                    FROM seguridad.rol_permisos  rp
                    JOIN seguridad.roles r on r.id = rp.rol_id and r.delete_time is null
                    JOIN seguridad.permisos p on p.id = rp.permiso_id and p.delete_time is null
                    WHERE 1=1 ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'rp.id',
            ),
            'rol_id' => array(
                'columna' => 'rp.rol_id',
            ),
            'rol_key' => array(
                'columna' => 'r.key',
            ),
            'permiso_id' => array(
                'columna' => 'rp.permiso_id',
            ),
            'permiso_key' => array(
                'columna' => 'p.key',
            ),

        ));
        return $query;
    }

    public function listarRolPermisos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarRolPermisosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalRolPermisos($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarRolPermisosQuery'), $filtros);
    }

    public function crearRolPermiso($dto = array()) {
        $query = "SELECT seguridad.fn_rol_permiso_ins (
            :rol_id,
            :permiso_id,
            :evento_cuenta_id
        )";

        $this->bindValue(":rol_id", $dto['rol_id']);
        $this->bindValue(":permiso_id", $dto['permiso_id']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function eliminarRolPermiso($dto = array()) {
        $query = "SELECT seguridad.fn_rol_permiso_del (
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