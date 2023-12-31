<?php

class FondosModel extends Model {
    function __construct() {
        parent::__construct(DB_POSTGRES);
    }


    public function listarFondosQuery($filtros = array(), $ordenes = array()) {
        
        $query = "SELECT fondo_id, 
                    fondo_nombre, 
                    activo,
                    ins_fecha,
                    upd_fecha 
                FROM " .SCHEMA. " .fondos
                WHERE del_fecha IS NULL";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'fondo_id' => array(
                'columna' => 'fondo_id',
            ),
            'key' => array(
                'columna' => 'upper(fondo_nombre)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
            'activo' => array(
                'columna' => 'activo',
            ),
        ));


        return $query;
    }

    public function listarFondos($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarFondosQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function totalFondos($filtros = array()) {
        return $this->totalRecurso(array($this, 'listarFondosQuery'), $filtros);
    }


    public function detallefondo($id) {
        $query = $this->listarFondosQuery(['fondo_id' => $id]);
        $result = $this->prepare($query);
        $result->execute();
        return $result->fetch();
    }



public function crearFondo ($dto = array()) {
    $query = "SELECT  " .SCHEMA. ".fn_fondo_ins (
        upper(:key),
        :evento_cuenta_id
    )";

    $this->bindValue(":key", $dto['key']);
    $this->bindValue(":evento_cuenta_id", $dto['usuario']);

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetch(pdo::FETCH_NUM)[0];  
}



public function listarFondosColumnasQuery($filtros = array(), $ordenes = array()) {
  
        $query= "select 
                    fc.fondo_columna_id, 
                    fc.fondo_id,
                    f.fondo_nombre,
                    fc.columna_id,
                    c.nombre,
                    fc.orden 
                from  " .SCHEMA. ".fondo_columnas fc 
                left join " .SCHEMA. ".fondos f on (F.fondo_id = FC.fondo_id)
                left join " .SCHEMA. ".columnas c on (C.columna_id=FC.columna_id)
                where fc.activo = true ";

    $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
        'id' => array(
            'columna' => 'fc.fondo_columna_id',
        ),
        'fondo_id' => array(
            'columna' => 'fc.fondo_id',
        ),
        'fondo_key' => array(
            'columna' => 'f.fondo_nombre',
        ),
        'columna_id' => array(
            'columna' => 'fc.columna_id',
        ),
        'columna_key' => array(
            'columna' => 'c.nombre',
        ),
        'orden' => array(
            'columna' => 'fc.orden ',
            'orden' => true,
        ),

    ));  

    

    return $query;
}
 



public function detalleColumna($id) {
    $query = $this->listarColumnasQuery(['id' => $id]);
    $result = $this->prepare($query);
    $result->execute();
    return $result->fetch();
}

 
public function listarFondosColumnas($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
        return $this->listarRecurso(array($this, 'listarFondosColumnasQuery'), $inicio, $cantidad, $filtros, $ordenes);
    }

    public function listarColumnasQuery($filtros = array(), $ordenes = array()) {
        $query = "SELECT  
                    columna_id ,
		            nombre
                FROM  " .SCHEMA. ".columnas
                WHERE activo = true";

        $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
            'id' => array(
                'columna' => 'columna_id',
            ),
            'key' => array(
                'columna' => 'upper(nombre)',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'
            ),
        ));
        return $query;
    }
 
public function listarColumnas ($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
    return $this->listarRecurso(array($this, 'listarColumnasQuery'), $inicio, $cantidad, $filtros, $ordenes);
}



public function actualizarFondo ($dto = array()) {
    $query = "SELECT  " .SCHEMA. ".fn_fondo_upd (
        upper(:key),
        :fondo_id,
        :evento_cuenta_id
    )";

    $this->bindValue(":fondo_id", $dto['fondo_id']);
    $this->bindValue(":key", $dto['key']); 
    $this->bindValue(":evento_cuenta_id", $dto['usuario']);

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetch(pdo::FETCH_NUM)[0];
}

public function eliminarFondo ($dto = array()) {
    $query = "SELECT  " .SCHEMA. ".fn_fondo_del (
        :id,
        :evento_cuenta_id
    )";

    $this->bindValue(":id", $dto['fondo_id']);
    $this->bindValue(":evento_cuenta_id", $dto['usuario']);

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetch(pdo::FETCH_NUM)[0];
}


public function crearFondoColumnas($dto = array()) {
    $query = "SELECT  " .SCHEMA. ".fn_fondo_columnas_ins (
        :fondo_id,
        :columna_id,
        :orden        
    )";

    $this->bindValue(":fondo_id", $dto['fondo_id']);
    $this->bindValue(":columna_id", $dto['columna_id']);
    $this->bindValue(":orden", $dto['orden']);
    

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetch(pdo::FETCH_NUM)[0];
}

public function eliminarFondoColumna($dto = array()) {
    $query = "SELECT  " .SCHEMA. ".fn_fondo_columnas_del (
        :id_columna_fondo
      
    )";
    
    $this->bindValue(":id_columna_fondo", $dto['fondo_columna_id']);
   // $this->bindValue(":id_fondo", $dto['columna_id']);
    //$this->bindValue(":evento_cuenta_id", $dto['usuario']);
    $result = $this->prepare($query);
    $result->execute();
    return $result->fetch(pdo::FETCH_NUM)[0];
}


public function acDescripcion($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
    $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
    $ordenes = [];
    $query = "SELECT DISTINCT
                (" . $idCampo . ") AS ID,
                UPPER(" . $valorCampo . ") AS VALUE
            FROM (" . $this->listarFondosQuery($filtros, $ordenes) . ") t5
            ORDER BY VALUE ASC";

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetchAll();
}


public function insertarRepositorio($dto, $fondoId, $usuario, $nodo, $archivoTif, $idExcel) {
    $query = 'select  '.SCHEMA. '.insertarRepo(:fila, :fondo_id, :usuario, :nodo, :archivoTif, :id_excel)';

    $this->bindValue(":fondo_id", $fondoId,PDO::PARAM_INT);
    $this->bindValue(":usuario", $usuario);
    $this->bindValue(":fila", join("','",$dto));
    /*   if ($nodo = null)
        {
            $nodo = 0;
        }*/
    $this->bindValue(":nodo", $nodo);
    $this->bindValue(":archivoTif", $archivoTif);
    $this->bindValue(":id_excel", $idExcel);


    $result = $this->prepare($query);
    $result->execute();
    $campos = $result->fetch(pdo::FETCH_NUM)[0];
}

public function listarRepositorioPlanaquery($filtros = array(), $ordenes = array()) {

    $query = "SELECT rp.repositorio_id as repositorio_id, 
    rp.fondo_id as fondo_id, 
    f.fondo_nombre,
    COALESCE(rp.subfondo, '') as subfondo, 
    COALESCE(rp.seccion_mesa, '') as seccion_mesa, 
    COALESCE(rp.subseccion, '') as subseccion, 
    COALESCE(rp.serie_libreria, '') as serie_libreria, 
    COALESCE(rp.subserie_cabinet, '') as subserie_cabinet, 
    COALESCE(rp.paginas, '') as paginas, 
    COALESCE(rp.caja, '') as caja, 
    COALESCE(rp.carpeta, '') as carpeta, COALESCE(rp.legajo, '') as legajo, 
    COALESCE(rp.asunto, '') as asunto, COALESCE(rp.tomo, '') as tomo, 
    COALESCE(rp.observacion, '') as observacion, COALESCE(rp.lugar, '') as lugar, 
    COALESCE(rp.indice, '') as indice, COALESCE(rp.fecha, '') as fecha, 
    COALESCE(rp.fecha_extrema, '') as fecha_extrema,  
    COALESCE(rp.fecha_entrada, '') as fecha_entrada, 
    COALESCE(rp.fecha_revicion, '') as fecha_revicion, 
    COALESCE(rp.cant_fojas, '') as cant_fojas, COALESCE(rp.numeracion_folio, '') as numeracion_folio, 
    COALESCE(rp.tipo_documental, '') as tipo_documental, 
    COALESCE(rp.estado_conservacion_id, '') as estado_conservacion_id, 
    COALESCE(rp.observacion_archivista, '') as observacion_archivista, 
    COALESCE(rp.apellido, '') as apellido, COALESCE(rp.nombre, '') as nombre, 
    COALESCE(rp.foto, '') as foto, COALESCE(rp.punto_acceso_por_materia, '') as punto_acceso_por_materia, 
    COALESCE(rp.punto_acceso_por_lugar, '') as punto_acceso_por_lugar, 
    COALESCE(rp.punto_acceso_por_persona, '') as punto_acceso_por_persona, 
    rp.dato_json, 
    rp.ins_fecha, 
    rp.ins_usuario,
    rp.nodo_arbol_id, 
    ef.descripcion as nodo_id,
    COALESCE(rp.archivo_tif, '') as archivo_tif,
    rp.pasado_a_repo as pasado_a_repo
    FROM   " .SCHEMA. ".repositorio_plana rp
    LEFT JOIN  " .SCHEMA. ".estructura_fondos ef on (rp.nodo_arbol_id = ef.estructura_fondo_id)
    left join  " .SCHEMA. ".fondos f on (rp.fondo_id = CAST(F.FONDO_ID AS CHARACTER VARYING))
    where 1=1 and rp.pasado_a_repo = FALSE"; 

    $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
        'repositorio_id' => array(
            'columna' => 'pr.repositorio_id',
        ),
    ));



    return $query;
}


public function listarRepositorioPlana ($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
    return $this->listarRecurso(array($this, 'listarRepositorioPlanaquery'), $inicio, $cantidad, $filtros, $ordenes);
}


public function totalRepositorioPlana($filtros = array()) {
    return $this->totalRecurso(array($this, 'listarRepositorioPlanaquery'), $filtros);
}

public function listarRepositorioquery($filtros = array(), $ordenes = array()) {

    $query = "select repositorio_id, 
                fondo_id, 
                fondo_nombre, 
                COALESCE(subfondo,'') as subfondo, 
                COALESCE(seccion_mesa,'') as seccion_mesa, 
                COALESCE(subseccion, '') as subseccion, 
                COALESCE(serie_libreria, '') as serie_libreria, 
                COALESCE(subserie_cabinet, '') as subserie_cabinet, 
                paginas, 
                caja, 
                COALESCE(carpeta,'') as carpeta, 
                coalesce(legajo, '') as legajo, 
                coalesce(asunto,'') as asunto, 
                coalesce(asunto_grilla, '') as asunto_grilla,
                coalesce(tomo, '') as tomo, 
                coalesce(observacion, '') as observacion, 
                coalesce(lugar, '') as lugar, 
                indice, 
                coalesce(fecha, '') as fecha, 
                coalesce(fecha_extrema,'') as fecha_extrema, 
                fecha_entrada, 
                fecha_revicion, cant_fojas, 
                numeracion_folio,
                coalesce(tipo_documental, '') as tipo_documental, 
                estado_conservacion_id, 
                coalesce(observacion_archivista, '') as observacion_archivista, 
                coalesce(upper(apellido),'') as apellido, 
                coalesce(nombre,'') as nombre, 
                foto, punto_acceso_por_materia, 
                    punto_acceso_por_lugar, punto_acceso_por_persona, ins_usuario, ins_fecha, upd_usuario, upd_fecha, del_usuario, del_fecha, dato_json, 
                    archivo, ocr as ocr, nodo_arbol_id, COALESCE(nodo_detalle,'') as nodo_detalle
            from sigear.vw_repositorio_grilla
            WHERE 1 =1 ";

    $query .= $this->obtenerQueryFiltrosOrdenes($filtros, $ordenes, array(
        'repositorio_id' => array(
            'columna' => 'repositorio_id'),
        'fondo_id'=> array( // "FONDO", anda
            'columna' => 'fondo_id',
           // 'buscador' => true,
            'orden' => true),
        'fondo_nombre'=> array( // "FONDO", anda
            'columna' => 'fondo_nombre',
            'buscador' => true,
            'orden' => true,
            'formato' => 'strtodb'),
        'subfondo'=> array(
            'columna' => 'subfondo',
            'buscador' => true,
            'orden' => true,
            'formato' => 'strtodb'),
        'seccion_mesa'=> array( // "SECCION/MESA", anda
                'columna' => 'seccion_mesa',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
        'legajo'=> array( // "LEGAJO", anda
                'columna' => 'legajo',
                'buscador' => true,
                'orden' => true,
                'formato' => 'strtodb'),
        'nodo'=> array( // creo q es "NODO ARBOL"
                    'columna' => "COALESCE(nodo_detalle,'')",
                    'buscador' => true,
                    'orden' => true,
                    'formato' => 'strtodb') ,
        'apellido'=> array( // columna "APELLIDO Y NOMBRE", anda
                    'columna' => 'unaccent(upper(apellido))',
                    'buscador' => true,
                    'orden' => true,
                    'formato' => 'strtodb') ,
        'asunto'=> array( // asunto
                    'columna' => 'upper(asunto)',
                    'buscador' => true,
                    'orden' => true,
                    'formato' => 'strtodb') ,
        'fecha'=> array( // columna "FECHA_INGRESO", anda
                        'columna' => 'fecha',
                        'buscador' => true,
                        //'orden' => true,
                        'formato' => 'strtodb'),
        'observacion_archivista'=> array( // columna "OBSERVACION ARCHIVISTA", anda
                            'columna' => 'observacion_archivista',
                            'buscador' => true,
                            'orden' => true,
                            'formato' => 'strtodb'),

    ));

    //debug($query);

    return $query;

    

}


public function listarRepositorio ($inicio = null, $cantidad = null, $filtros = array(), $ordenes = array()) {
    return $this->listarRecurso(array($this, 'listarRepositorioquery'), $inicio, $cantidad, $filtros, $ordenes);    //true como ultimo parametro habilita debug
}

public function listarRepoFiltrado($fondo, $seccionInput, $subFondo, $asuntoInput, $apellidoNombreInput){}


public function totalRepositorio($filtros = array()) {
    return $this->totalRecurso(array($this, 'listarRepositorioquery'), $filtros);
}



public function cantidadColumnasFondo($id_fondo){
    $query = "select count(*)
        from  " .SCHEMA. ".fondo_columnas fc 
        where activo ='T' and  fondo_id = :p_fondo";

    $this->bindValue(":p_fondo", $id_fondo);

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetch(pdo::FETCH_NUM)[0];
}


public function fnArbol($recorrido, $idSector){
    if($recorrido == 'descendente'){
        $rec = 'desc';
    }else if($recorrido == 'ascendente'){
        $rec = 'asc';
    }else return new Exception('Se debe espcificar el sentido del recorrido');

    $query = "select * from  " .SCHEMA. ".fn_organigrama_".$rec."(:p_id_sector)";

    $result = $this->prepare($query); 
    $result->bindValue(":p_id_sector", $idSector);
    $result->execute(); 
    return $result->fetchall();
}

//public function traerRepoSinOCR($idExcel){
    public function traerRepo($idExcel){
    $query = "select repositorio_id, archivo from ".SCHEMA.".repositorio where idexcel = :id";
    $result= $this->prepare($query);
    $result->bindValue(":id", $idExcel);
    $result->execute();
    return $result->fetchAll();
}

public function traerRegistroRepo($repositorio_id){
    $query = "SELECT
                r.repositorio_id AS r_repositorio_id,
                r.fondo_id AS r_fondo_id,
                r.subfondo AS r_subfondo,
                r.seccion_mesa AS r_seccion_mesa,
                r.subseccion AS r_subseccion,
                r.serie_libreria AS r_serie_libreria,
                r.subserie_cabinet AS r_subserie_cabinet,
                r.paginas AS r_paginas,
                r.caja AS r_caja,
                r.carpeta AS r_carpeta,
                r.legajo AS r_legajo,
                r.asunto AS r_asunto,
                r.tomo AS r_tomo,
                r.observacion AS r_observacion,
                r.lugar AS r_lugar,
                r.indice AS r_indice,
                r.fecha AS r_fecha,
                r.fecha_extrema AS r_fecha_extrema,
                r.fecha_entrada AS r_fecha_entrada,
                r.fecha_revicion AS r_fecha_revicion,
                r.cant_fojas AS r_cant_fojas,
                r.numeracion_folio AS r_numeracion_folio,
                r.tipo_documental AS r_tipo_documental,
                r.estado_conservacion_id AS r_estado_conservacion_id,
                r.observacion_archivista AS r_observacion_archivista,
                r.apellido AS r_apellido,
                r.nombre AS r_nombre,
                r.foto AS r_foto,
                r.punto_acceso_por_materia AS r_punto_acceso_por_materia,
                r.punto_acceso_por_lugar AS r_punto_acceso_por_lugar,
                r.punto_acceso_por_persona AS r_punto_acceso_por_persona,
                r.ins_usuario AS r_ins_usuario,
                r.ins_fecha AS r_ins_fecha,
                r.upd_usuario AS r_upd_usuario,
                r.upd_fecha AS r_upd_fecha,
                r.del_usuario AS r_del_usuario,
                r.del_fecha AS r_del_fecha,
                r.dato_json AS r_dato_json,
                r.archivo AS r_archivo,
                r.ocr AS r_ocr,
                r.nodo_arbol_id AS r_nodo_arbol_id,
                r.idexcel AS r_idexcel,
                r.disc AS r_disc,
                f.fondo_id as f_fondo_id,
                f.fondo_nombre as f_fondo_nombre,
                f.activo as f_activo,
                f.ins_fecha as f_ins_fecha,
                f.ins_usuario as f_ins_usuario,
                f.upd_fecha as f_upd_fecha,
                f.upd_usuario as f_upd_usuario,
                f.del_fecha as f_del_fecha,
                f.del_usuario as f_del_usuario

            FROM ".SCHEMA.".repositorio r, ".SCHEMA.".fondos f
            WHERE r.repositorio_id = :id and r.fondo_id = f.fondo_id";
    $result= $this->prepare($query);
    $result->bindValue(":id", $repositorio_id);
    $result->execute();
    return $result->fetch();
}

public function insertOcrEnRepositorio($ocr, $id) {
    //$query = 'select  '.SCHEMA. '.insertocrenrepositorio(:ocr)';
    //fn_ocr_repositorio(p_repositorio_id integer, p_ocr character varying)

    $query = 'select '.SCHEMA. '.fn_ocr_repositorio(:id, :ocr)';

    // $this->bindValue(":usuario", $usuario);

    $result = $this->prepare($query);
    $result->bindValue(":id",$id);
    $result->bindValue(":ocr", $ocr);
    //$result->execute();
    //return $result->fetch(pdo::FETCH_NUM)[0];
    return $result;
}

public function idExcel(){
    $query = "select nextval('sigear.seq_id_excel')";
    $result = $this->prepare($query);
    $result->execute();
    return $result->fetch(pdo::FETCH_NUM)[0];
}


public function insertarRepositorioCompleto($idExcel){
    $query = 'select  '.SCHEMA. '.fn_pasar_repo_plana(:id)';
    $result = $this->prepare($query);
    $result->bindValue(":id",$idExcel);
    $result->execute();
    return $result->fetch(pdo::FETCH_NUM)[0];
}

public function marcarPasadosEnPlana($idExcel){
    $query = 'select  '.SCHEMA. '.fn_borrar_repo_plana(:id)';
    $result = $this->prepare($query);
    $result->bindValue(":id",$idExcel);
    $result->execute();
    return $result->fetch(pdo::FETCH_NUM)[0];
}

public function cantidadParaOcerrear($idExcel){
    $query = 'select  '.SCHEMA. '.fn_cantidadparaocerrear(:id)';
    $result = $this->prepare($query);
    $result->bindValue(":id",$idExcel);
    $result->execute();
    return $result->fetch();
}

public function OCRporPagina($index, $txt){
    $query = "select sigear.fn_ocr_x_pag_ins(123, 1, 'hola')";
    $result = $this->prepare($query);
    //$result->bindValue(":id",$idExcel);
    $result->execute();
    return $result->fetch();
}

public function dameNombreFondo($idFondo){
    $query = "select fondo_nombre from ".SCHEMA.".fondos where fondo_id = :id";
    $result= $this->prepare($query);
    $result->bindValue(":id", $idFondo);
    $result->execute();
    return $result->fetch();
}

public function listaJPG($repositorioID){
    $query = "SELECT repositorio_id, tiff_pagina
            FROM  " .SCHEMA. " .ocr_por_pagina
            WHERE repositorio_id = :repositorioID
            ORDER BY repositorio_id, tiff_pagina";

    $result = $this->prepare($query);
    $result->bindValue(":repositorioID", $repositorioID);
    $result->execute();
    return $result->fetchAll();
}

public function cantPaginas($repositorioID){
    $query = "SELECT MAX(tiff_pagina) AS max_tiff_pagina
            FROM " . SCHEMA . ".ocr_por_pagina
            WHERE repositorio_id = :repositorioID";

    $result = $this->prepare($query);
    $result->bindValue(":repositorioID", $repositorioID);
    $result->execute();
    return $result->fetch();
}

public function dameIdExcel($idRepositorio){
    $query = "select idexcel from ".SCHEMA.".repositorio where repositorio_id = :id";
    $result= $this->prepare($query);
    $result->bindValue(":id", $idRepositorio);
    $result->execute();
    return $result->fetch();
}

/******* filtros  ********************* */

public function acRepositorio($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
    $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
    $ordenes = [];
    $query = "SELECT DISTINCT
                (" . $idCampo . ") AS ID,
                UPPER(" . $valorCampo . ") AS VALUE
            FROM (" . $this->listarFondosQuery($filtros, $ordenes) . ") t5
            ORDER BY VALUE ASC";

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetchAll();
}

public function acApellido($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
    $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
    $ordenes = [];
    $query = "SELECT DISTINCT
                (" . $idCampo . ") AS ID,
                $valorCampo AS VALUE
            FROM (" . $this->listarRepositorioquery($filtros, $ordenes) . ") t5
            ORDER BY VALUE ASC";

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetchAll();
}

public function acAsunto($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
    $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
    $ordenes = [];
    $query = "SELECT DISTINCT
                (" . $idCampo . ") AS ID,
                $valorCampo AS VALUE
            FROM (" . $this->listarRepositorioquery($filtros, $ordenes) . ") t5
            ORDER BY VALUE ASC";

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetchAll();
}


public function acNodo($key, $descripcion, $idCampo, $valorCampo, $filtros=[]) {
    $filtros = array_merge($filtros, [$key => "[LI:%" . $descripcion . "%]"]);
    $ordenes = [];
    $query = "SELECT DISTINCT
                (" . $idCampo . ") AS ID,
                $valorCampo AS VALUE
            FROM (" . $this->listarRepositorioquery($filtros, $ordenes) . ") t5
            ORDER BY VALUE ASC";

    $result = $this->prepare($query);
    $result->execute();
    return $result->fetchAll();
}

public function repositorioEditar(){
    /*
    repositorio_id
fondo_id
subfondo
seccion_mesa
subseccion
serie_libreria
subserie_cabinet
paginas
caja
carpeta
legajo
asunto
tomo
observacion
lugar
indice
fecha
fecha_extrema
fecha_entrada
fecha_revicion
cant_fojas
numeracion_folio
tipo_documental
estado_conservacion_id
observacion_archivista
apellido
nombre
foto
punto_acceso_por_materia
punto_acceso_por_lugar
punto_acceso_por_persona
upd_usuario
upd_fecha
    */
}

}