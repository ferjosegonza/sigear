<?php

class UsuariosModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }

    public function listarUsuariosQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT
                    u.id as cuenta_id,
                    u.key as cuenta_key,
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
                    uc.password_hash,
                    uc.hash_function,
                    uc.hash_factor,
                    uc.status as password_status
                FROM seguridad.vw_usuarios u
                LEFT JOIN seguridad.cuenta_credenciales uc on uc.cuenta_id = u.id and uc.delete_time is null
                WHERE u.delete_time is null  ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'cuenta_id' => array(
                'columna' => 'u.id',
            ),
            'key' => array(
                'columna' => 'upper(u.key)',
                'formato' => 'strtodb'
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

    public function listarUsuarios($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarUsuariosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalUsuarios($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarUsuariosQuery'), $filtros);
    }

    public function obtenerUsuario($id) {
        $query = $this->listarUsuariosQuery(['cuenta_id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function obtenerUsuarioPorKey($key) {
        $query = $this->listarUsuariosQuery(['key' => $key]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }


    //==============================================================================
    // USUARIOS BLOQUEADOS
    //==============================================================================

    public function listarUsuariosBloqueadosQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT
                        ub.cuenta_key as key,
                        ub.cuenta_id as id,
                        u.descripcion,
                        u.documento_tipo_id,
                        u.documento_tipo,
                        u.documento,
                        u.sexo,
                        u.nombre,
                        u.apellido,
                        u.fecha_nacimiento,
                        u.imagen,
                        u.email,
                        u.email_verificado,
                        u.telefono,
                        u.telefono_verificado,
                        u.status,
                        u.create_time,
                        u.update_time
                    FROM seguridad.vw_usuarios_bloqueados ub
                    LEFT JOIN seguridad.vw_usuarios u ON u.id = ub.cuenta_id
                    WHERE 1=1   ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'ub.cuenta_id',
            ),
            'key' => array(
                'columna' => 'ub.cuenta_key',
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

    public function listarUsuariosBloqueados($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarUsuariosBloqueadosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalUsuariosBloqueados($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarUsuariosBloqueadosQuery'), $filtros);
    }

    public function obtenerUsuarioBloqueado($id) {
        $query = $this->listarUsuariosBloqueadosQuery(['id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function obtenerUsuarioBloqueadoPorKey($key) {
        $query = $this->listarUsuariosBloqueadosQuery(['key' => $key]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    // =========================================================================
    // IP Bloqueadas
    // =========================================================================
    public function listarIpBloqueadasQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT
                    ib.client_ip
                FROM seguridad.vw_client_ips_bloqueadas ib
                WHERE 1=1 ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'client_ip' => array(
                'columna' => 'ib.client_ip'
            )
        ));

        return $query;
    }

    public function listarIpBloqueadas($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarIpBloqueadasQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalIpBloqueadas($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarIpBloqueadasQuery'), $filtros);
    }

    public function obtenerIpBloqueada($clientIp) {
        $query = $this->listarUsuariosBloqueadosQuery(['client_ip' => $clientIp]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    // =========================================================================
    // LOGINS
    // =========================================================================

    private function listarEventosLoginQuery($selectList, $filtros = array(), $ordenes = array()) {
        $query = "SELECT
                    el.id,
                    el.evento_login_tipo_id,
                    elt.nombre as evento_login_tipo_nombre,
                    elt.descripcion as evento_login_tipo,
                    el.client_ip,
                    el.cuenta_key,
                    el.cuenta_id,
                    el.timestamp,
                    el.payload
                FROM seguridad.eventos_login el
                JOIN seguridad.eventos_login_tipos elt ON elt.id = el.evento_login_tipo_id
                WHERE 1=1 ";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'el.id'
            ),
            'tipo_id' => array(
                'columna' => 'el.tipo_id'
            ),
            'tipo_nombre' => array(
                'columna' => 'elt.nombre'
            ),
            'client_ip' => array(
                'columna' => 'el.client_ip'
            ),
            'cuenta_key' => array(
                'columna' => 'el.cuenta_key'
            ),
            'cuenta_id' => array(
                'columna' => 'el.cuenta_id'
            ),
            'timestamp' => array(
                'columna' => 'el.timestamp'
            )
        ));

        return $query;
    }

    public function listarEventosLogin($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarEventosLoginQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalEventosLogin($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarEventosLoginQuery'), $filtros);
    }

    public function obtenerLogin($id) {
        $query = $this->listarUsuariosBloqueadosQuery(['id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }

    public function registrarEventoLogin($tipoNombre, $dto = array()) {
        $query = "SELECT seguridad.fn_evento_login_ins(
            :evento_login_tipo_nombre,
            :cuenta_key,
            :client_ip,
            :cuenta_id,
            :payload,
            :fallido
        )";

        $this->bindValue(":evento_login_tipo_nombre", $tipoNombre);
        $this->bindValue(":cuenta_key", $dto['cuenta_key']);
        $this->bindValue(":cuenta_id", $dto['cuenta_id']);
        $this->bindValue(":client_ip", $dto['client_ip']);

        $this->bindValue(":fallido", in_array($tipoNombre, [
            'usuario_inexistente',
            'usuario_sin_credenciales',
            'password_invalido'
        ]) ? 'T' : 'F');

        $result = $this->prepare($query);
        $dto['payload'] = json_encode($dto['payload'], JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
        $result->bindParam(":payload", $dto['payload'], PDO::PARAM_STR, strlen($dto['payload']));
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function limpiarEventosLoginFallidosUsuario($dto = array()) {
        $query = "SELECT seguridad.fn_cuenta_logins_fallidos_limpiar (
            :key,
            :evento_cuenta_id
        )";

        $this->bindValue(":key", $dto['cuenta_key']);
        $this->bindValue(":evento_cuenta_id", $dto['cuenta_id']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }
}