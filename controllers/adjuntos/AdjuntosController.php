<?php

class AdjuntosController extends Controller {
    private AdjuntosModel $am;
/*    private AdjuntosModel $adm;
    private AdjuntoTiposModel $adt;
    private RecursoService $recurso;
    public Recurso $imagen;
    private RecursosModel $recursosm;
*/
    public function __construct($container) {
        parent::__construct($container);
        $this->am = new AdjuntosModel();
        /*$this->adt = new AdjuntoTiposModel();
        $this->recursosm = new RecursosModel();
        $this->recurso = $this->get('service.recursos.Recurso');
        $this->imagen = $this->recurso->imageRecurso('acreditaciones/acreditacion-adjuntos', [
            'url' => BASE_URL . '/acreditacion-adjuntos/documentos',
            'entidad' => 'acreditacion_adjuntos'
        ]);*/
    }

    public function vistaNuevoAdjunto(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_solicitud');

        $this->render($response,'adjuntos/adjuntos_detalle', [
            'solapaNombre' => 'ADJUNTAR ARCHIVOS:',
            /* "modal" => [
                "class" => "modal--rounded modal--full-width",
            ],
            "solicitud" => $recurso,
            "lista_solicitantes" => $this->sm->listarSolicitantes(null,null,[],[['descripcion']]),
            "lista_instituciones" => $this->im->listarInstituciones(null,null,[],[['descripcion']]),
            "lista_estados" => $this->sm->listarEstados(null,null,[],[['descripcion']]),
            "lista_personal" => $this->sm->listarPersonal(null,null,[],[['descripcion']]),
            "lista_vinculos" => $this->sm->listarVinculos(null,null,[],[['descripcion']]),
            "lista_tipo_solicitudes" => $this->sm->listarTipoSolicitudes(null,null,[],[['descripcion']]),
           // "historicos" => $this->sm->listarMovimientos(null,null,[],[['descripcion']]),
            "historicos" => $this->sm->listarMovimientos(null, null, ['solicitud_id'=> $recurso['solicitud_id']], null), */
        ]);

    }

    /* public function obtenerArchivos(Request $request, Response $response, $args ){
        $archivo = $request->getArgument('archivoID');
        $path = realpath(DEVLOG_DIR . "/upload/" . basename($archivo));

        $mime = mime_content_type($path);
        $file= new \GuzzleHttp\Psr7\LazyOpenStream($path, 'r');

        header_remove("Cache-Control");
        header_remove("Pragma");
        header_remove("Expires");

        return $response->withHeader('Content-Disposition', 'inline')
                        //->withHeader('Cache-Control', "public, max-age=604800, immutable")
                        ->withHeader('Cache-Control', "no-cache, no-store, must-revalidate")
                        ->withHeader('Content-Type', $mime)
                        ->withBody($file);
    }*/

    public function eliminarAdjunto(Request $request, Response $response, $args ){
        $adjunto = $request->getAttribute('recurso_adjunto');
        $adjunto['usuario'] = $this->getUsuario()->getIdCuenta();
        return $this->am->eliminarAdjunto($adjunto);
    }

}
