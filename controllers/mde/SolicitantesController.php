<?php

class SolicitantesController extends Controller {
    private SolicitantesModel $sm;

    public function __construct($container) {
        parent::__construct($container);
        $this->sm = new SolicitantesModel();

    }

    public function vistaSolicitantes(Request $request, Response $response, $args) {
        $this->render($response,'mde/solicitantes', [
            'solapaNombre' => 'SOLICITANTES',
            'appBackTo' =>'/administracion-botonera', 
        ]);
    }

    public function vistaDetalleSolicitante(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_solicitante');

        $this->render($response,'mde/solicitante_detalle', [
            'modalTitle' => 'EDITANDO SOLICITANTE: "'
                .$recurso['apellidos'].' '.$recurso['nombres'].'"',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "solicitante" => $recurso,
            /* "tipo_solicitantes" => $this->sm->listarSolicitantesTipos(null,null,[],[['descripcion']]),*/
        ]);
    }

    public function vistaNuevoSolicitante(Request $request, Response $response, $args) {
        $recurso = $request->getAttribute('recurso_solicitante');
        $returnToModal = $request->getQueryParam('return_to_modal');

        $this->render($response,'mde/solicitante_detalle', [
            'modalTitle' => 'AGREGAR SOLICITANTE',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "solicitante" => $recurso,
            // "returnToModal" => $returnToModal
            /* "tipo_solicitantes" => $this->sm->listarSolicitantesTipos(null,null,[],[['descripcion']]),*/
        ]);
    }


    //-----------------API------------------------
    public function listarSolicitantes(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());

        $lista = $this->sm->listarSolicitantes($start, $length, $filtros, $ordenes);
        $total = $this->sm->totalSolicitantes();
        $totalWithFilter = is_null($filtros) ? $total : $this->sm->totalSolicitantes($filtros);

        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function crearSolicitante(Request $request, Response $response, $args ){
        $data = [
            "apellidos" => $request->getParam("apellido"),
            "nombres" => $request->getParam("nombre"),
            "documento" => $request->getParam("documento"),
            "telefono" => $request->getParam("telefono"),
            "email" => $request->getParam("email"),
        /*    "tipo_solicitante_id" => $request->getParam("tipo_solicitante"),*/
            "usuario" => $this->getUsuario()->getIdCuenta()
        ];
        if(!empty($this->sm->listarSolicitantes(null,null,['key'=>'[EQ:'.$data['documento'].']'])))
            return $response->withAppError('Ya existe un solicitante con ese DNI');
        $data['id'] = $this->sm->crearSolicitante($data);
        return $response->withJson($data);
    }

    public function actualizarSolicitante(Request $request, Response $response, $args ){

        $recurso = $request->getAttribute('recurso_solicitante');

        $data = [
            "id" => $recurso['solicitante_id'],
            "apellidos" =>  $request->getParam("apellido"), 
            "nombres" => $request->getParam("nombre"), 
            "documento" => $request->getParam("documento"), 
            "telefono" => $request->getParam("telefono"), 
            "email" => $request->getParam("email"),
        /*    "tipo_solicitante" => $request->getParam("tipo_solicitante"),*/
            "usuario" => $this->getUsuario()->getIdCuenta()
        ];

        if(!empty($this->sm->listarSolicitantes(null,null,['solicitante_id'=>'[NEQ:'.$data['id'].']','documento'=>'[EQ:'.$data['documento'].']'])))
            return $response->withAppError('Ya existe un Solicitante con ese DNI');
        $this->sm->actualizarSolicitante($data);
        return $response->withJson($data);
    }

    public function eliminarSolicitante(Request $request, Response $response, $args ){
        $solicitante = $request->getAttribute('recurso_solicitante');
        $solicitante['usuario'] = $this->getUsuario()->getIdCuenta();
        $result = $this->sm->eliminarSolicitante($solicitante);
        // return $response->withJson($result);
        return $this->sm->eliminarSolicitante($solicitante);
    }

}

