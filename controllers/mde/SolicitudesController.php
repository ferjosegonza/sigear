<?php

use FontLib\Table\Type\name;
use Sabberworm\CSS\Value\Value;

class SolicitudesController extends Controller {
    private SolicitudesModel $sm;
    private InstitucionesModel $im;
    private InformesModel $ifm;
    private SolicitantesModel $som;
    private AdjuntosModel $am;

    public function __construct($container) {
        parent::__construct($container);
        $this->sm = new SolicitudesModel();
        $this->im = new InstitucionesModel();
        $this->som = new SolicitantesModel();
        $this->am = new AdjuntosModel();
        $this->ifm = new InformesModel();
    }

    public function vistaSolicitudes(Request $request, Response $response, $args) {
        $this->render($response,'mde/solicitudes', [
            'solapaNombre' => 'SOLICITUDES',
            //'appBackTo' =>'/administracion-botonera',
        ]);
    }

    public function vistaDetalleSolicitudes(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_solicitud');
        
        $this->render($response,'mde/solicitudes_detalle', [
            'solapaNombre' => 'EDITANDO SOLICITUD',
            'appBackTo' =>'/administracion-botonera',
           /* "modal" => [
                "class" => "modal--rounded modal--full-width",
            ],*/
            "solicitud" => $recurso,
            "lista_solicitantes" => $this->sm->listarSolicitantes(null,null,[],[['descripcion']]),
            "lista_instituciones" => $this->im->listarInstituciones(null,null,[],[['descripcion']]),
            "lista_estados" => $this->sm->listarEstados(null,null,[],[['descripcion']]),
            "lista_personal" => $this->sm->listarPersonal(null,null,[],[['descripcion']]),
            "lista_vinculos" => $this->sm->listarVinculos(null,null,[],[['descripcion']]),
            "lista_tipo_solicitudes" => $this->sm->listarTipoSolicitudes(null,null,[],[['descripcion']]),
           // "historicos" => $this->sm->listarMovimientos(null,null,[],[['descripcion']]),
            "historicos" => $this->sm->listarMovimientos(null, null, ['solicitud_id'=> $recurso['solicitud_id']], null),
            "adjuntos" => $this->am->listarAdjuntos(null, null, ['id_tabla'=> $recurso['solicitud_id'], 'tabla'=> 'SOLICITUDES'], null),
            "informes" => $this->ifm->listarInformes(null, null, ['id_solicitud'=> $recurso['solicitud_id']], null),
            "lista_documentacion_solicitada"=>$this->sm->listarDocumentacionSolicitada(null,null,[],[['descripcion']]),
            "listar_nacionalidad"=>$this->sm->listarNacionalidad(null,null,[],[['descripcion']])
        ]);




    }

   /* public function listarMovimientos(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());
        $lista = $this->sm->listarMovimientos($start, $length, $filtros, $ordenes);
    }*/

    public function vistaNuevoSolicitudes(Request $request, Response $response, $args) {
        $recurso = $request->getAttribute('recurso_solicitud');
        $this->render($response,'mde/solicitudes_detalle', [
            'modalTitle' => 'AGREGAR SOLICITUD:',
           /* "modal" => [
                "class" => "modal--rounded modal--full-width",
                ],*/
                "solicitud" => $recurso,
                "lista_solicitantes" => $this->sm->listarSolicitantes(null,null,[],[['descripcion']]),
                "lista_instituciones" => $this->im->listarInstituciones(null,null,[],[['descripcion']]),
                "lista_estados" => $this->sm->listarEstados(null,null,[],[['descripcion']]),
                "lista_vinculos" => $this->sm->listarVinculos(null,null,[],[['descripcion']]),
                "lista_personal" => $this->sm->listarPersonal(null,null,[],[['descripcion']]),
                "lista_tipo_solicitudes" => $this->sm->listarTipoSolicitudes(null,null,[],[['descripcion']]),
                "lista_documentacion_solicitada"=>$this->sm->listarDocumentacionSolicitada(null,null,[],[['descripcion']]),
                "listar_nacionalidad"=>$this->sm->listarNacionalidad(null,null,[],[['descripcion']]),

        ]);
    }

    //-----------------API------------------------
    public function listarSolicitudes(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());
        if (empty($ordenes)){
            $ordenes = [
                ['estado_id' => 'asc'],
                ['fecha_creacion' => 'asc']
            ];
        }
        $lista = $this->sm->listarSolicitudes($start, $length, $filtros, $ordenes);
        $total = $this->sm->totalSolicitudes();
        $totalWithFilter = is_null($filtros) ? $total : $this->sm->totalSolicitudes($filtros);

        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function listarEstados(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());
        $lista = $this->sm->listarEstados($start, $length, $filtros, $ordenes);
    }

    public function listarPersonal(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());
        $lista = $this->sm->listarPersonal($start, $length, $filtros, $ordenes);
    }

    public function listarVinculos(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());

        $lista = $this->sm->listarVinculos($start, $length, $filtros, $ordenes);
        $total = $this->sm->totalVinculos();
        $totalWithFilter = is_null($filtros) ? $total : $this->sm->totalVinculos($filtros);

        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function crearSolicitud(Request $request, Response $response, $args ){
       /* $data = $request->getParsedBody();
        $solicitante_id = $data['solicitante_id'];
      */
        $data = [
            "solicitante_id" => $request->getParam("solicitante_id"),
            "tipo_solicitud_id" => $request->getParam("tipo_solicitud_id"),
            "institucion_id" => $request->getParam("institucion_id"),
            "buscada_nombre" => $request->getParam("buscada_nombre"),
            "buscada_apellido" => $request->getParam("buscada_apellido"),
            "buscada_dni"=> $request->getParam("buscada_dni"),
            "observaciones" => $request->getParam("observaciones"),
            "numero_ley" => $request->getParam("numero_ley"),
            "tiene_info" => $request->getParam("tiene_info"),
            "tipo_estado_id" => $request->getParam("tipo_estado_id"),
            "numero_tramite" => $request->getParam("numero_tramite"),
            "personal_id" => $request->getParam("personal_id"),
            "periodo_camara" => $request->getParam("periodo_camara"),
            "vinculo_id" => $request->getParam("vinculo_id"),
            "ins_usuario" => $this->getUsuario()->getIdCuenta(),
            "id_doc_solicita" => $request->getParam("doc_solicita"),
            "tipo_doc" => $request->getParam("tipo_doc"),
        ];

    /*    if(!empty($this->sm->listarSolicitudes(null,null,['solicitud_id'=>'[NEQ:'.$data['solicitante_id'].']','documento'=>'[EQ:'.$data['buscada_dni'].']'])))
            return $response->withAppError('Ya existe un Solicitante con ese DNI');*/
        $data['solicitud_id'] = $this->sm->crearSolicitud($data);
        foreach ($_FILES as $key => $value) {
            $name = uniqid();
            $nombre = $this -> subirDocumento(DEVLOG_DIR."/upload", $name, $value);
            // UPLOAD_DIR
            $this->am->crearArchivo(["p_file" => $value['name'], "p_create_cuenta_id" => $this->getUsuario()->getIdCuenta(), "p_tabla" => "solicitudes", "p_id_tabla" => $data["solicitud_id"]]);
        }
        return $response->withJson($data);
    /*    $data['id'] = $this->sm->crearSolicitud($data);    --- aca estabas poniendo a $ data id y era solicitud_id por eso se rompia,creaba lasolicitud y se rompia despues
        return $response->withJson($data);*/
    }

    public function actualizarSolicitud(Request $request, Response $response, $args ){
        // $recurso = $request->getAttribute('recurso_solicitud');
            $data = [
                "solicitud_id" => $request->getParam("solicitud_id"),
                "solicitante_id" => $request->getParam("solicitante_id"),
                "tipo_solicitud_id" => $request->getParam("tipo_solicitud_id"),
                "institucion_id" => $request->getParam("institucion_id"),
                "buscada_nombre" => $request->getParam("buscada_nombre"),
                "buscada_apellido" => $request->getParam("buscada_apellido"),
                "buscada_dni"=> $request->getParam("buscada_dni"),
                "observaciones" => $request->getParam("observaciones"),
                "numero_ley" => $request->getParam("numero_ley"),
                "tiene_info" => $request->getParam("tiene_info"),
                "numero_tramite" => $request->getParam("numero_tramite"),
                "personal_id" => $request->getParam("personal_id"),
                "periodo_camara" => $request->getParam("periodo_camara"),
                "vinculo_id" => $request->getParam("vinculo_id"),
                "upd_usuario" => $this->getUsuario()->getIdCuenta(),
                "tipo_estado_id" => $request->getParam("estado"),
                "id_doc_solicita" => $request->getParam("doc_solicita"),
                "tipo_doc" => $request->getParam("tipo_doc")
            ];

        // if(!empty($this->sm->listarSolicitudes(null,null,['solicitud_id'=>'[NEQ:'.$data['id'].']','documento'=>'[EQ:'.$data['documento'].']'])))
           // return $response->withAppError('Ya existe un Solicitante con ese DNI');
        $this->sm->actualizarSolicitud($data);
        foreach ($_FILES as $key => $value) {
            // UPLOAD_DIR
            $id_recurso = $this->am->crearArchivo(["p_file" => $value['name'], "p_create_cuenta_id" => $this->getUsuario()->getIdCuenta(), "p_tabla" => "solicitudes", "p_id_tabla" => $data["solicitud_id"]]);
            // $name = uniqid();
            $this -> subirDocumento(DEVLOG_DIR."/upload", $id_recurso, $value);
            //C:\inetpub\wwwroot\sigear\uploads      en produccion lo esta guardando en el L:\php\sigear\upload

        }
        return $response->withJson($data);
    }

    private function subirDocumento($path, $filename, $file){
        $mime = mime_content_type($file['tmp_name']);
        $allowedMime = ["application/pdf", "image/jpeg", "image/jpg", "image/png"];

        $fileExt = strtolower(end(explode('.', $file['name'])));
        $allowedExt = ["pdf", "jpeg", "jpg", "png"];

        //PROCEDIMIENTO DE SUBIDA DE IMAGEN
        if($file['size'] <= 0) throw new Exception("EL TAMAÑO NO PUEDE SER MENOR O IGUAL A 0");
        if($file['size'] > 5000000) throw new Exception("EL TAMAÑO ES MUY GRANDE, MAXIMO 5MB");
        if(!in_array($mime, $allowedMime)) throw new Exception("SOLO SE PERMTEN LOS FORMATOS MIME: ". implode( ", ", $allowedMime));
        if(!in_array($fileExt, $allowedExt)) throw new Exception("SOLO SE PERMTEN LOS FORMATOS: ". implode( ", ", $allowedExt));
        if($file['error'] != 0) throw new Exception("EL ARCHIVO TIENE UN ERROR");
        if(!is_dir($path)) mkdir($path);
        $fileNewName = $filename . "." . $fileExt;
        $fileDestination = $path . "/" . $fileNewName;

        if(move_uploaded_file($file['tmp_name'], $fileDestination)){
            return $fileNewName;
        }else{
            throw new Exception("NO SE PUDO GUARDAR EL ARCHIVO EN EL SERVIDOR");
        }
    }

    public function eliminarSolicitud(Request $request, Response $response, $args ){
        $solicitud = $request->getAttribute('recurso_solicitud');
        $solicitud['usuario'] = $this->getUsuario()->getIdCuenta();
        return $this->sm->eliminarSolicitud($solicitud);
    }

    public function acSolicitante(Request $request, Response $response, $args ){
        $descripcion = $request->getParam('descripcion', null);
        $filtros = $request->getParam('filtros', []);
        $lista = $this->som->acSolicitantes("apellido_nombre", $descripcion, "solicitante_id", "apellido_nombre", $filtros, true);
        return $response->withJson($lista);
    }

    public function acTipo(Request $request, Response $response, $args ){
        $descripcion = $request->getParam('descripcion', null);
        $filtros = $request->getParam('filtros', []);
        $lista = $this->sm->acSolicitudes("tipo_solicitud", $descripcion, "tipo_solicitud", "tipo_solicitud", $filtros, true);
        return $response->withJson($lista);
    }

    public function acVinculoOLey(Request $request, Response $response, $args ){
        $descripcion = $request->getParam('descripcion', null);
        $filtros = $request->getParam('filtros', []);
        $lista = $this->sm->acVinculoOLey("vinculooley", $descripcion, "vinculooley", "vinculooley", $filtros, true);
        return $response->withJson($lista);
    }

    public function acTieneInfo(Request $request, Response $response, $args ){
        $descripcion = $request->getParam('descripcion', null);
        $filtros = $request->getParam('filtros', []);
        $lista = $this->sm->acTieneInfo("tiene_info", $descripcion, "tiene_info", "tiene_info", $filtros, true);
        return $response->withJson($lista);
    }

    public function acNumTramite(Request $request, Response $response, $args ){
        $descripcion = $request->getParam('descripcion', null);
        $filtros = $request->getParam('filtros', []);
        $lista = $this->sm->acNumTramite("num_tramite", $descripcion, "num_tramite", "num_tramite", $filtros, true);
        return $response->withJson($lista);
    }

    public function acEstados(Request $request, Response $response, $args ){
        $descripcion = $request->getParam('descripcion', null);
        $filtros = $request->getParam('filtros', []);
        $lista = $this->sm->acEstados("estado_id", $descripcion, "estado_id", "descripcion", $filtros, true);
        return $response->withJson($lista);
    }

    public function acAsignados(Request $request, Response $response, $args ){
        $descripcion = $request->getParam('descripcion', null);
        $filtros = $request->getParam('filtros', []);
        $lista = $this->sm->acAsignados("nombre_completo_up", $descripcion, "personal_asignado_id", "nombre_completo_up", $filtros, true);
        return $response->withJson($lista);
    }

    public function acSolicitanteSolicitudes(Request $request, Response $response, $args ){
        $descripcion = $request->getParam('descripcion', null);
        $filtros = $request->getParam('filtros', []);
        $lista = $this->sm->acSolicitudes("key", $descripcion, "solicitante_id", "solicitante", $filtros, true);
        return $response->withJson($lista);
    }

    public function acInstituciones(Request $request, Response $response, $args ){
        $descripcion = $request->getParam('descripcion', null);
        $filtros = $request->getParam('filtros', []);
        $lista = $this->sm->acInstituciones("institucion", $descripcion, "institucion_id", "institucion", $filtros, true);
        return $response->withJson($lista);
    }

    /* public function mostrarArchivo(Request $request, Response $response, $args ){
        $archivoId = $request->getAttribute("adjuntoID");

        $archivo = base64_encode(file_get_contents(ROOT_DIR.'/log/upload/'.$archivoId.'.pdf'));
        // debug($archivo);
        //  $archivo = $archivo.'.pdf';

        $this->render($response,'mde/mostrarArchivo', [
            'urlIframe' => $archivo,
        ]);
    } */

    public function mostrarArchivo(Request $request, Response $response, $args ){
        $archivoId = $request->getAttribute("adjuntoID");
        $documento = $request->getParam("documento");
        $id_solicitud = $request->getParam("id_solicitud");
        $extension = strtolower(substr($documento, -3));

      //  $contenido = base64_encode(file_get_contents(DEVLOG_DIR.'/upload/unidadespenitenciarias0050.pdf'));

        if (ENTORNO =='desarrollo'){
                if($extension === "pdf") {
                    $archivo = base64_encode(file_get_contents(ROOT_DIR.'/log/upload/'.$archivoId.'.pdf'));
                } elseif($extension === "jpg" || $extension === "png") {
                    $archivo = base64_encode(file_get_contents(ROOT_DIR.'/log/upload/'.$archivoId.'.'.$extension));
                } elseif(strtolower(substr($documento, -4)) === "jpeg") {
                    $archivo = base64_encode(file_get_contents(ROOT_DIR.'/log/upload/'.$archivoId.substr($documento, -5)));
                }
            }
        if (ENTORNO =='produccion'){
            if($extension === "pdf") {
                $archivo = base64_encode(file_get_contents(DEVLOG_DIR.'/upload/'.$archivoId.'.pdf'));
            } elseif($extension === "jpg" || $extension === "png") {
                $archivo = base64_encode(file_get_contents(DEVLOG_DIR.'/upload/'.$archivoId.'.'.$extension));
            } elseif(strtolower(substr($documento, -4)) === "jpeg") {
                $archivo = base64_encode(file_get_contents(DEVLOG_DIR.'/upload/'.$archivoId.substr($documento, -5)));
            }
        }


        // debug($archivo);
        //  $archivo = $archivo.'.pdf';

        $this->render($response,'mde/mostrarArchivo', [
            'urlIframe' => $archivo,
            'documento' => $documento,
            'id_solicitud' => $id_solicitud,
           // 'contenido' => $contenido,
        ]);
    }

}
