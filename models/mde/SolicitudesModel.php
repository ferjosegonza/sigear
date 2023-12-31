<?php

class SolicitudesModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }
    
    public function listarSolicitudesQuery($filtros = array(), $ordenes = array()) {
        
        $query = "SELECT S.solicitud_id as solicitud_id,
                    (S.solicitante_apellidos ||' '|| S.solicitante_nombres) as solicitante,
                    S.tipo_solicitudes_id as tipo_solicitud_id,
                    S.tipo_solicitud as tipo_solicitud,
                    s.institucion_id as institucion_id,
                    S.institucion as institucion,
                    S.vinculooley,
                    s.vinculo,
                    s.vinculo_id,
                    S.tiene_info as tiene_info,
                    S.num_ley,
                    S.num_tramite,
                    S.estado as estado,
                    s.estado_id as estado_id,
                    S.observaciones as observaciones,
                    S.personal_asignado,
                    S.personal_asignado_id,
                    S.persona_buscada_nombre,
                    S.persona_buscada_apellido,
                    S.persona_buscada_dni,
                    s.solicitante_id as solicitante_id,
                    S.periodo_camara as periodo_camara,
                    S.fecha_creacion_f as fecha_creacion_f,
                    S.fecha_creacion as fecha_creacion,
                    upper(S.nombre_completo_up) as nombre_completo_up,
                    S.id_documentacion_solicitada as id_documentacion_solicitada,
                    S.doc_solicitada as doc_solicitada,
                    S.tipo_documentacion as tipo_documentacion
                    from " .SCHEMA. ".VW_SOLICITUDES S
                    where 1=1";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'estado_id' => array(
                'columna' => 'S.estado_id',
                'buscador' => true,
                'orden' => true,
            ),
            'fecha_creacion' => array(
                'columna' => 'S.fecha_creacion',
                'buscador' => true,
                'orden' => true,
            ),
            'personal_asignado_id' => array(
                'columna' => 'S.personal_asignado_id',
                'buscador' => true
            ),
            'solicitud_id' => array(
                'columna' => 'S.solicitud_id',
            ),
            'tipo_solicitud' => array(
                'columna' => 'UPPER(S.tipo_solicitud)',
                'buscador' => true,
                'formato' => 'strtodb',
                'orden' => true,
            ),
            'key' => array(
                'columna' => "upper(S.solicitante_apellidos ||' '|| S.solicitante_nombres)",
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
            'solicitante_id' => array(
                    'columna' => 'S.solicitante_id',),
            'nombre_completo_up' => array(
                'columna' => "upper(S.nombre_completo_up)",
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
            'institucion_id' => array(
                'columna' => 'S.institucion_id',),
            'institucion' => array(
                'columna' => "upper(S.institucion)",
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
            'vinculooley' => array(
                'columna' => 'upper(S.vinculooley)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
            'tiene_info' => array(
                'columna' => "upper(S.tiene_info)",
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
            'num_tramite' => array(
                'columna' => 'upper(S.num_tramite)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
        ));

        return $query;
    }

 
    public function listarSolicitudes($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarSolicitudesQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalSolicitudes($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarSolicitudesQuery'), $filtros);
    }


    public function listarTipoSolicitudesQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT tipo_solicitudes_id, descripcion
                    from " .SCHEMA. ".tipo_solicitudes
                    where 1=1";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'tipo_solicitudes_id' => array(
                'columna' => 'tipo_solicitudes_id',
            ),
            'descripcion' => array(
                'columna' => 'UPPER(descripcion)',
                'buscador' => true,
                'formato' => 'strtodb'
            ),
        ));

        return $query;
    }


    public function listarTipoSolicitudes($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarTipoSolicitudesQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }


    public function listarDocumentacionSOlicitadaQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT id_documentacion_solicitada, descripcion
                    FROM " .SCHEMA. ".documentacion_solicitada
                    where 1=1";        
 
        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id_documentacion_solicitada' => array(
                'columna' => 'id_documentacion_solicitada',
            ),
            'descripcion' => array(
                'columna' => 'UPPER(descripcion)',
                'buscador' => true,
                'formato' => 'strtodb'
            ),
        ));

        return $query;
    }

    public function listarDocumentacionSolicitada($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarDocumentacionSOlicitadaQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }



    public function listarNacionalidadQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT id_pais, nacionalidad
                    FROM " .SCHEMA. ".paises
                    where 1=1";        
 
        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id_pais' => array(
                'columna' => 'id_pais',
            ),
            'nacionalidad' => array(
                'columna' => 'UPPER(nacionalidad)',
                'buscador' => true,
                'formato' => 'strtodb'
            ),
        ));

        return $query;
    }

    public function listarNacionalidad($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarNacionalidadQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }


    public function crearSolicitud ($dto = array()) {
        $query = "SELECT " .SCHEMA. ".fn_solicitudes_ins (
            :solicitante_id,
            :tipo_solicitud_id,
            :institucion_id,
            :buscada_nombre,
            :buscada_apellido,
            :buscada_dni,
            :observaciones,
            :numero_ley,
            :tiene_info,
            :numero_tramite,
            :personal_id,
            :periodo_camara,
            :vinculo_id,
            :ins_usuario,
            :id_doc_solicita,
            :tipo_doc
        )";
/*
        if ($dto['tipo_solicitud_id'] == '1'){
            $institucion_id = NULL;
            $numero_ley = NULL;
            $numero_tramite = NULL;
            $tiene_info= $dto['tiene_info'];
        } else if ($dto['tipo_solicitud_id'] == '2'){
            $institucion_id =  $dto['institucion_id'];
            $numero_ley =  $dto['numero_ley'];
            $numero_tramite =  $dto['numero_tramite'];
            $tiene_info= NULL;
        } */
        if (($dto['tiene_info'] == '') or empty($dto['tiene_info']) or $dto['tipo_solicitud_id'] == '2') {
            $tiene_info = NULL; } else { $tiene_info =  $dto['tiene_info']; }

        if (($dto['vinculo_id'] == '') or empty($dto['vinculo_id']) or $dto['tipo_solicitud_id'] == '2') {
            $vinculo = NULL; } else { $vinculo =  $dto['vinculo_id']; }

        if (($dto['institucion_id'] == '') or empty($dto['institucion_id']) or $dto['tipo_solicitud_id'] == '1') {
            $institucion_id = NULL; } else { $institucion_id =  $dto['institucion_id']; }

        if (($dto['numero_ley'] == '') or empty($dto['numero_ley']) or $dto['tipo_solicitud_id'] == '1') {
            $numero_ley = NULL; } else { $numero_ley =  $dto['numero_ley']; }

        if (($dto['numero_tramite'] == '') or empty($dto['numero_tramite']) or $dto['tipo_solicitud_id'] == '1') {
            $numero_tramite = NULL; } else { $numero_tramite =  $dto['numero_tramite']; }

        if (($dto['personal_id'] == '') or empty($dto['personal_id'])) {
                $personal_asignado = NULL; } else { $personal_asignado =  $dto['personal_id']; }

        if (($dto['id_doc_solicita'] == '') or empty($dto['id_doc_solicita']) or $dto['tipo_solicitud_id'] == '1') {
            $id_doc_solicita = NULL; } else { $id_doc_solicita =  $dto['id_doc_solicita']; }

        $this->bindValue(":solicitante_id", $dto['solicitante_id']);
        $this->bindValue(":tipo_solicitud_id", $dto['tipo_solicitud_id']);
        $this->bindValue(":institucion_id", $institucion_id);
        $this->bindValue(":buscada_nombre", $dto['buscada_nombre']);
        $this->bindValue(":buscada_apellido", $dto['buscada_apellido']);
        $this->bindValue(":buscada_dni", $dto['buscada_dni']);
        $this->bindValue(":observaciones", $dto['observaciones']);
        $this->bindValue(":numero_ley", $numero_ley);
        $this->bindValue(":tiene_info", $tiene_info);
        $this->bindValue(":numero_tramite", $numero_tramite);
        $this->bindValue(":personal_id", $personal_asignado);
        $this->bindValue(":periodo_camara", $dto['periodo_camara']);
        $this->bindValue(":vinculo_id", $vinculo);
        $this->bindValue(":ins_usuario", $dto['ins_usuario']);
        $this->bindValue(":id_doc_solicita", $id_doc_solicita);
        $this->bindValue(":tipo_doc", $dto['tipo_doc']);
        // $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function actualizarSolicitud ($dto = array()) {
        if (($dto['tiene_info'] == '') or empty($dto['tiene_info']) or $dto['tipo_solicitud_id'] == '2') {
            $tiene_info = NULL; } else { $tiene_info =  $dto['tiene_info']; }

        if (($dto['vinculo_id'] == '') or empty($dto['vinculo_id']) or $dto['tipo_solicitud_id'] == '2') {
            $vinculo = NULL; } else { $vinculo =  $dto['vinculo_id']; }

        if (($dto['institucion_id'] == '') or empty($dto['institucion_id']) or $dto['tipo_solicitud_id'] == '1') {
            $institucion_id = NULL; } else { $institucion_id =  $dto['institucion_id']; }

        if (($dto['numero_ley'] == '') or empty($dto['numero_ley']) or $dto['tipo_solicitud_id'] == '1') {
            $numero_ley = NULL; } else { $numero_ley =  $dto['numero_ley']; }

        if (($dto['numero_tramite'] == '') or empty($dto['numero_tramite']) or $dto['tipo_solicitud_id'] == '1') {
            $numero_tramite = NULL; } else { $numero_tramite =  $dto['numero_tramite']; }

        if (($dto['personal_id'] == '') or empty($dto['personal_id'])) {
                $personal_asignado = NULL; } else { $personal_asignado =  $dto['personal_id']; }

        if (($dto['id_doc_solicita'] == '') or empty($dto['id_doc_solicita']) or $dto['tipo_solicitud_id'] == '1') {
            $id_doc_solicita = NULL; } else { $id_doc_solicita =  $dto['id_doc_solicita']; }

        $tipo_estado_id= $dto['tipo_estado_id'];
        if (($dto['tipo_estado_id'] == '1') and ($dto['personal_id'] != null)){
            $tipo_estado_id = 2; }

        // me dice sole tipo_vinculo, personal_id, tiene_info aparecen vacios
        $query = "SELECT " .SCHEMA. ".fn_solicitudes_upd (
            :p_solicitud_id,
            :p_solicitante_id,
            :p_tipo_solicitud_id,
            :p_institucion_id,
            :p_buscada_nombre,
            :p_buscada_apellido,
            :p_buscada_dni,
            :p_observaciones,
            :p_num_ley,
            :p_tiene_info,
            :p_num_tramite,
            :p_personal_id,
            :p_periodo_camara,
            :p_tipo_vinculo,
            :p_upd_usuario,
            :p_tipo_estado_id,
            :p_id_doc_solicita,
            :p_tipo_doc
        )";

        $this->bindValue(":p_solicitud_id", $dto['solicitud_id']);
        $this->bindValue(":p_solicitante_id", $dto['solicitante_id']);
        $this->bindValue(":p_tipo_solicitud_id", $dto['tipo_solicitud_id']);
        $this->bindValue(":p_institucion_id", $institucion_id);
        $this->bindValue(":p_buscada_nombre", $dto['buscada_nombre']);
        $this->bindValue(":p_buscada_apellido", $dto['buscada_apellido']);
        $this->bindValue(":p_buscada_dni", $dto['buscada_dni']);
        $this->bindValue(":p_observaciones", $dto['observaciones']);
        $this->bindValue(":p_num_ley", $numero_ley);
        $this->bindValue(":p_tiene_info", $tiene_info);
        $this->bindValue(":p_num_tramite", $numero_tramite);
        $this->bindValue(":p_personal_id", $personal_asignado);
        $this->bindValue(":p_periodo_camara", $dto['periodo_camara']);
        $this->bindValue(":p_tipo_vinculo", $vinculo);
        $this->bindValue(":p_upd_usuario", $dto['upd_usuario']);
        $this->bindValue(":p_tipo_estado_id", $tipo_estado_id);
        $this->bindValue(":p_id_doc_solicita", $id_doc_solicita);
        $this->bindValue(":p_tipo_doc", $dto['tipo_doc']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    public function detalleSolicitud($id) {
        $historicos = null;
        $query = $this->listarSolicitudesQuery(['solicitud_id' => $id]);
        $result = $this->prepare($query);
        $result->execute();

        return $result->fetch();
    }

    public function eliminarSolicitud ($dto = array()) {
        $query = "SELECT " .SCHEMA. ".fn_solicitudes_del (
            :id,
            :evento_cuenta_id
        )";

        $this->bindValue(":id", $dto['solicitud_id']);
        $this->bindValue(":evento_cuenta_id", $dto['usuario']);

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch(pdo::FETCH_NUM)[0];
    }

    //************LISTAR TIPOS DE SOLICITANTES**************************** */
    public function listarSolicitantesQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT solicitante_id, 
                        -- tipo_solicitante_id,
                         CONCAT_WS(' ', apellidos, nombres) as descripcion,
                         documento,
                         registro_activo as activo
                    FROM " .SCHEMA. ".solicitantes
                    WHERE registro_activo = true";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'solicitante_id',
            ),
            'descripcion' => array(
                'columna' => 'descripcion',
                'orden' => true,
            ),
           /* 'apellidos' => array(
                'columna' => 'apellidos',
                'orden' => true,
            ),
            'nombres' => array(
                'columna' => 'nombres',
                'orden' => true,
            ),*/
            'documento' => array(
                'columna' => 'documento',
                'orden' => true,
            ),
            'activo' => array(
                'columna' => 'activo',
            ),
        ));
        return $query;
    }

    public function listarEstadosQuery($filtros = array(), $ordenes = array()){
        $query = "SELECT estado_id, 
                         descripcion,
                         activo
                    FROM " .SCHEMA. ".estados
                    WHERE activo = true";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'estado_id',
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

    public function listarVinculosQuery($filtros = array(), $ordenes = array()){
        $query = "SELECT vinculo_id, 
                         descripcion,
                         activo
                    FROM " .SCHEMA. ".vinculo
                    WHERE activo = true";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'vinculo_id' => array(
                'columna' => 'vinculo_id',
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

    public function listarSolicitantes($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarSolicitantesQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function listarEstados($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()){
        return $this->listarRecurso(array($this, 'listarEstadosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function listarVinculos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()){
        return $this->listarRecurso(array($this, 'listarVinculosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalVinculos($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarVinculosQuery'), $filtros);
    }

    public function listarPersonalQuery($filtros = array(), $ordenes = array()) {
        $query= "SELECT usuario_perfil_id as personal_id,
                        -- descripcion as descripcionCuenta
                        CONCAT_WS(' ', apellido, nombre) as descripcion 
                        from seguridad.vw_usuarios
                        where status = 'A' and delete_time is NULL";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'personal_id' => array(
                'columna' => 'personal_id',
                'buscador' => true,
                'orden' => true,
            ),
            'key' => array(
                'columna' => 'upper(descripcion)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
            /*'descripcionCuenta' => array(
                'columna' => 'upper(c.descripcion)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'), */
        ));
        return $query;
    }

    public function listarMovimientosQuery($filtros = array(), $ordenes = array()) {
        $query= "SELECT ins_fecha,
                        fecha_f,
                        usuario,
                        asignado,
                        operacion,
                        solicitud_id
                        from " .SCHEMA. ".vw_solicitud_historico
                        where 1=1 ";

        $query.= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'ins_fecha' => array(
                'columna' => 'ins_fecha',
                'buscador' => true,
                'orden' => true,
            ),
            'solicitud_id' => array(
                'columna' => 'solicitud_id',
                'buscador' => true,
                'orden' => true,
            ),
        ));
   
        return $query;
    }

    public function listarPersonal($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()){
        return $this->listarRecurso(array($this, 'listarPersonalQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function listarMovimientos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()){
        return $this->listarRecurso(array($this, 'listarMovimientosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function acSolicitudes($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    (" . $idCampo . ") AS ID,
                    UPPER(" . $valorCampo . ") AS VALUE
                FROM (" . $this->listarSolicitudesQuery($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    public function acAsignados($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    (" . $idCampo . ") AS ID,
                    UPPER(" . $valorCampo . ") AS VALUE
                FROM (" . $this->listarSolicitudesQuery($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    public function acInstituciones($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    (" . $idCampo . ") AS ID,
                    $valorCampo AS VALUE
                FROM (" . $this->listarSolicitudesQuery($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    public function acVinculoOLey($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    (" . $idCampo . ") AS ID,
                    UPPER(" . $valorCampo . ") AS VALUE
                FROM (" . $this->listarSolicitudesQuery($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    public function acTieneInfo($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    (" . $idCampo . ") AS ID,
                    UPPER(" . $valorCampo . ") AS VALUE
                FROM (" . $this->listarSolicitudesQuery($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    public function acNumTramite($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    (" . $idCampo . ") AS ID,
                    UPPER(" . $valorCampo . ") AS VALUE
                FROM (" . $this->listarSolicitudesQuery($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    public function acEstados($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    (" . $idCampo . ") AS ID,
                    UPPER(" . $valorCampo . ") AS VALUE
                FROM (" . $this->listarEstadosQuery($filtros, $ordenes) . ") t5
                ORDER BY ID ASC";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }



 /*   public function listarTipoInstitucion($filtros = array(), $ordenes = array()) {
        
        $query = "SELECT tipo_solicitudes_id, 
                    descripcion, 
                    activo
                    FROM gpa.tipo_solicitudes
                    where 1=1";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'tipo_solicitud' => array(
                'columna' => 'UPPER(descripcion)',
                'buscador' => true,
                'formato' => 'strtodb'
            ),           
                
        ));

        return $query;
    }

    public function acTipoSolicitud($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    (" . $idCampo . ") AS ID,
                    UPPER(" . $valorCampo . ") AS VALUE
                FROM (" . $this->listarTipoInstitucion($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }*/

    
    /* public function acSolicitante($key, $nombre_completo, $idCampo, $valorCampo, $filtros=[]) {
        $filtros = array_merge($filtros, [$key => "[LI:%" . $nombre_completo . "%]"]);
        $ordenes = [];
        $query = "SELECT DISTINCT
                    *,
                    /UPPER/(" . $idCampo . ") AS ID,
                    UPPER(" . $nombre_completo . ") AS VALUE
                FROM (" . $this->listarSolicitantesQuery($filtros, $ordenes) . ") t5
                ORDER BY VALUE ASC
        ";

        $result = $this->prepare($query);
        $result->execute();
        return $result->fetchAll();
    } */

}