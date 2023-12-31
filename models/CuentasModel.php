<?php

class CuentasModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }

    public function listarCuentasQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT
                    u.id as cuenta_id,
                    u.key as cuenta_key,
                    u.usuario_perfil_id,
                    u.descripcion,
                    u.apellido,
                    u.nombre,
                    u.documento_tipo_id,
                    u.documento_tipo,
                    u.documento,
                    u.sexo,
                    u.fecha_nacimiento,
                    u.imagen,
                    u.email,
                    u.email_verificado,
                    u.telefono,
                    u.telefono_verificado,
                    u.status as cuenta_status,
                    u.create_time,
                    u.update_time,
                    uc.id as password_id,
                    uc.hash_function,
                    uc.hash_factor,
                    uc.status as password_status
                FROM seguridad.vw_usuarios u
                LEFT JOIN seguridad.cuenta_credenciales uc on uc.cuenta_id = u.id and uc.delete_time is null
                WHERE u.delete_time is null ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'cuenta_id' => array(
                'columna' => 'u.id',
            ),
            'cuenta_key' => array(
                'columna' => 'u.key',
            ),
            'apellido' => array(
                'columna' => 'u.apellido',
            ),
            'nombre' => array(
                'columna' => 'u.nombre',
            ),
            'email' => array(
                'columna' => 'u.email',
            ),
            'cuenta_status' => array(
                'columna' => 'u.status',
            ),
        ));
        return $query;
    }

    public function listarCuentas($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarCuentasQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalCuentas($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarCuentasQuery'), $filtros);
    }

    public function detalleCuenta($idCuenta) {
        $query = $this->listarCuentasQuery(['cuenta_id' => $idCuenta]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function crearCuenta ($dto = array()) {
        $query = "SELECT seguridad.fn_cuenta_ins (
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

    public function actualizarCuenta ($dto = array()) {
        $query = "SELECT seguridad.fn_cuenta_upd (
            :id,
            :key,
            :descripcion,
            :status,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['cuenta_id']);
        $this->bindValue(":key", $dto['cuenta_key']);
        $this->bindValue(":descripcion", $dto['descripcion']);
        $this->bindValue(":status", $dto['status']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function eliminarCuenta ($dto = array()) {
        $query = "SELECT seguridad.fn_cuenta_del(
            :id,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['cuenta_id']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function crearUsuarioPerfil ($dto = array()) {
        $query = "SELECT seguridad.fn_usuario_perfil_ins (
            :cuenta_id,
            :apellido,
            :nombre,
            :documento_tipo_id,
            :documento,
            :sexo,
            :fecha_nacimiento,
            :imagen,
            :email,
            :email_verificado,
            :telefono,
            :telefono_verificado,
            :direccion,
            :latitud,
            :longitud,
            :evento_cuenta_id
        )";

        $this->bindValue(":cuenta_id", $dto['cuenta_id']);
        $this->bindValue(":apellido", $dto['apellido']);
        $this->bindValue(":nombre", $dto['nombre']);
        $this->bindValue(":documento_tipo_id", $dto['documento_tipo_id']);
        $this->bindValue(":documento", $dto['documento']);
        $this->bindValue(":sexo", $dto['sexo']);
        $this->bindValue(":fecha_nacimiento", $dto['fecha_nacimiento']);
        $this->bindValue(":imagen", $dto['imagen']);
        $this->bindValue(":email", $dto['email']);
        $this->bindValue(":email_verificado", $dto['email_verificado']??true);
        $this->bindValue(":telefono", $dto['telefono']);
        $this->bindValue(":telefono_verificado", $dto['telefono_verificado']??true);
        $this->bindValue(":direccion", $dto['direccion']);
        $this->bindValue(":latitud", $dto['latitud']);
        $this->bindValue(":longitud", $dto['longitud']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function actualizarUsuarioPerfil ($dto = array()) {
        $query = "SELECT seguridad.fn_usuario_perfil_upd (
            :id,
            :cuenta_id,
            :apellido,
            :nombre,
            :documento_tipo_id,
            :documento,
            :sexo,
            :fecha_nacimiento,
            :imagen,
            :email,
            :email_verificado,
            :telefono,
            :telefono_verificado,
            :direccion,
            :latitud,
            :longitud,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['usuario_perfil_id']);
        $this->bindValue(":cuenta_id", $dto['cuenta_id']);
        $this->bindValue(":apellido", $dto['apellido']);
        $this->bindValue(":nombre", $dto['nombre']);
        $this->bindValue(":documento_tipo_id", $dto['documento_tipo_id']);
        $this->bindValue(":documento", $dto['documento']);
        $this->bindValue(":sexo", $dto['sexo']);
        $this->bindValue(":fecha_nacimiento", $dto['fecha_nacimiento']);
        $this->bindValue(":imagen", $dto['imagen']);
        $this->bindValue(":email", $dto['email']);
        $this->bindValue(":email_verificado", $dto['email_verificado']);
        $this->bindValue(":telefono", $dto['telefono']);
        $this->bindValue(":telefono_verificado", $dto['telefono_verificado']);
        $this->bindValue(":direccion", $dto['direccion']);
        $this->bindValue(":latitud", $dto['latitud']);
        $this->bindValue(":longitud", $dto['longitud']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function crearCuentaCredencial ($dto = array()) {
        $query = "SELECT seguridad.fn_cuenta_credencial_ins (
            :cuenta_id,
            :password_hash,
            :hash_function,
            :hash_factor,
            :status,
            :evento_cuenta_id
        )";

        $this->bindValue(":cuenta_id", $dto['cuenta_id']);
        $this->bindValue(":password_hash", $dto['password_hash']);
        $this->bindValue(":hash_function", $dto['hash_function']);
        $this->bindValue(":hash_factor", $dto['hash_factor']);
        $this->bindValue(":status", $dto['status']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function actualizarCuentaCredencial ($dto = array()) {
        $query = "SELECT seguridad.fn_cuenta_credencial_upd (
            :id,
            :cuenta_id,
            :password_hash,
            :hash_function,
            :hash_factor,
            :status,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['password_id']);
        $this->bindValue(":cuenta_id", $dto['cuenta_id']);
        $this->bindValue(":password_hash", $dto['password_hash']);
        $this->bindValue(":hash_function", $dto['hash_function']);
        $this->bindValue(":hash_factor", $dto['hash_factor']);
        $this->bindValue(":status", $dto['status']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    //****************************************************************************************

    public function listarDocumentoTiposQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT
                    id,
                    descripcion,
                    status,
                    create_time,
                    update_time
                FROM seguridad.documento_tipos
                WHERE delete_time is null ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'id',
            ),
            'descripcion' => array(
                'columna' => 'descripcion',
                'orden' => true,
            ),
            'status' => array(
                'columna' => 'status',
            ),
        ));
        return $query;
    }

    public function listarDocumentoTipos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarDocumentoTiposQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalDocumentoTipos($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarDocumentoTiposQuery'), $filtros);
    }

    public function detalleDocumentoTipo($id) {
        $query = $this->listarDocumentoTiposQuery(['id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    //==============================================================================================

    public function listarCuentaRolesQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT
                        cr.id,
                        cr.cuenta_id,
                        cr.rol_id,
                        r.key as rol_key,
                        cr.create_time,
                        cr.update_time
                    FROM seguridad.cuenta_roles cr
                    LEFT JOIN seguridad.roles r on r.id = cr.rol_id and r.delete_time is null
                    WHERE 1=1 ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'cr.id',
            ),
            'cuenta_id' => array(
                'columna' => 'cr.cuenta_id',
            ),
            'rol_id' => array(
                'columna' => 'cr.rol_id',
                'orden' => true,
            ),
            'rol_key' => array(
                'columna' => 'r.key',
            ),
        ));
        return $query;
    }

    public function listarCuentaRoles($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarCuentaRolesQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalCuentaRoles($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarCuentaRolesQuery'), $filtros);
    }

    public function detalleCuentaRol($id) {
        $query = $this->listarCuentaRolesQuery(['id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function obtenerCuentaRoles($id) {
        $query = $this->listarCuentaRolesQuery(['cuenta_id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    public function crearCuentaRol($dto = array()) {
        $query = "SELECT seguridad.fn_cuenta_rol_ins (
            :cuenta_id,
            :rol_id,
            :evento_cuenta_id
        )";

        $this->bindValue(":cuenta_id", $dto['cuenta_id']);
        $this->bindValue(":rol_id", $dto['rol_id']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function eliminarCuentaRol($dto = array()) {
        $query = "SELECT seguridad.fn_cuenta_rol_del (
            :id,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['id']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    //==============================================================================================

    public function listarCuentaPermisosQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT
                        c.id as cuenta_id,
                        p.id as permiso_id,
                        p.key as permiso_key,
                        r.id as rol_id,
                        r.key as rol_key
                    FROM seguridad.cuentas c
                    JOIN seguridad.cuenta_roles cr on cr.cuenta_id = c.id
                    JOIN seguridad.rol_permisos rp on rp.rol_id = cr.rol_id
                    JOIN seguridad.roles r on r.id = rp.rol_id and r.delete_time is null
                    JOIN seguridad.permisos p on p.id = rp.permiso_id and p.delete_time is null
                    WHERE 1=1 ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'cuenta_id' => array(
                'columna' => 'c.id',
            ),
            'rol_id' => array(
                'columna' => 'r.id',
                'orden' => true,
            ),
            'rol_key' => array(
                'columna' => 'r.key',
            ),
            'permiso_id' => array(
                'columna' => 'p.id',
                'orden' => true,
            ),
            'permiso_key' => array(
                'columna' => 'p.key',
            ),
        ));
        return $query;
    }

    public function listarCuentaPermisos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarCuentaPermisosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalCuentaPermisos($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarCuentaPermisosQuery'), $filtros);
    }

    public function obtenerCuentaPermisos($id) {
        $query = $this->listarCuentaPermisosQuery(['cuenta_id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    //==============================================================================================

}