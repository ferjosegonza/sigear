<?php

class InstitucionesController extends Controller {
    private InstitucionesModel $im;
    
    public function __construct($container) {
        parent::__construct($container);
        $this->im = new InstitucionesModel();

    }


    public function vistaInstituciones(Request $request, Response $response, $args) {
        $this->render($response,'mde/instituciones', [
            'solapaNombre' => 'INSTITUCIONES',
            'appBackTo' =>'/administracion-botonera',
        ]);
    }

    public function vistaDetalleInstitucion(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_institucion');

        $this->render($response,'mde/instituciones_detalle', [
            'modalTitle' => 'EDITANDO INSTITUCIÓN',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "institucion" => $recurso
        ]);
    }

    public function vistaNuevoInstitucion(Request $request, Response $response, $args) {
        $this->render($response,'mde/instituciones_detalle', [
            'modalTitle' => 'AGREGAR INSTITUCIÓN',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
                ]
        ]);
    }




    //-----------------API------------------------
    public function listarInstituciones(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());

        $lista = $this->im->listarInstituciones($start, $length, $filtros, $ordenes);
        $total = $this->im->totalInstituciones();
        $totalWithFilter = is_null($filtros) ? $total : $this->im->totalInstituciones($filtros);

        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function crearInstitucion(Request $request, Response $response, $args ){
        $data = [
            "descripcion" => $request->getParam("descripcion"),
            "usuario" => $this->getUsuario()->getIdCuenta()
        ];
        if(!empty($this->im->listarInstituciones(null,null,['key'=>'[EQ:'.$data['descripcion'].']'])))
            return $response->withAppError('Ya existe una Institución con ese nombre');
        $data['id'] = $this->im->crearInstitucion($data);
        return $response->withJson($data);
    }

    public function actualizarInstitucion(Request $request, Response $response, $args ){ 
        $recurso = $request->getAttribute('recurso_institucion');
        $data = [
            "id" => $recurso['institucion_id'],
            "descripcion" =>  $request->getParam("descripcion"),
            "usuario" => $this->getUsuario()->getIdCuenta()
        ];

        if(!empty($this->im->listarInstituciones(null,null,['key'=>'[EQ:'.$data['descripcion'].']'])))
            return $response->withAppError('Ya existe una Institución con ese nombre');

        $this->im->actualizarInstitucion($data);
        return $response->withJson($data);
    }

    public function eliminarInstitucion(Request $request, Response $response, $args ){
        $institucion = $request->getAttribute('recurso_institucion');
        $institucion['usuario'] = $this->getUsuario()->getIdCuenta();
        $result = $this->im->eliminarInstitucion($institucion);
        // return $response->withJson($result);
        return $this->im->eliminarInstitucion($institucion);
    }
}

