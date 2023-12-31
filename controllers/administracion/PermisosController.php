<?php

class PermisosController extends Controller {
    private PermisosModel $pm;

    public function __construct($container) {
        parent::__construct($container);
        $this->pm = new PermisosModel();
    }

    public function vistaPermisos(Request $request, Response $response, $args) {
        $this->render($response,'administracion/permisos/permisos', [
            'solapaNombre' => 'PERMISOS',
            'appBackTo' =>'/administracion-botonera',
        ]);
    }

    public function vistaDetallePermiso(Request $request, Response $response, $args) {
        $recurso = $request->getAttribute('recurso_permiso');

        $this->render($response,'administracion/permisos/permisos_detalle', [
            'modalTitle' => 'EDITANDO PERMISO',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "permiso" => $recurso
        ]);
    }

    public function vistaNuevoPermiso(Request $request, Response $response, $args) {
        $this->render($response,'administracion/permisos/permisos_detalle', [
            'modalTitle' => 'AGREGAR PERMISO',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
        ]);
    }

    //-----------------API------------------------
    public function listarPermisos(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());

        $lista = $this->pm->listarPermisos($start, $length, $filtros, $ordenes);
        $total = $this->pm->totalPermisos();
        $totalWithFilter = is_null($filtros) ? $total : $this->pm->totalPermisos($filtros);

        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function crearPermiso(Request $request, Response $response, $args ){
        $data = [
            "key" => $request->getParam("nombre"),
            "descripcion" => $request->getParam("descripcion"),
            "usuario" => $this->getUsuario()->getIdCuenta()
        ];

        if(!empty($this->pm->listarPermisos(null,null,['key'=>'[EQ:'.$data['key'].']'])))
            return $response->withAppError('Ya existe un permiso con ese nombre');

        $data['id'] = $this->pm->crearPermiso($data);
        return $response->withJson($data);
    }

    public function actualizarPermiso(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_permiso');
        $data = [
            "id" => $recurso['id'],
            "key" => $request->getParam("nombre"),
            "descripcion" => $request->getParam("descripcion"),
            "usuario" => $this->getUsuario()->getIdCuenta()
        ];

        if(!empty($this->pm->listarPermisos(null,null,['id'=>'[NEQ:'.$data['id'].']','key'=>'[EQ:'.$data['key'].']'])))
            return $response->withAppError('Ya existe un permiso con ese nombre');

        $this->pm->actualizarPermiso($data);
        return $response->withJson($data);
    }

    public function eliminarPermiso(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_permiso');
        $recurso['usuario'] = $this->getUsuario()->getIdCuenta();
        return $this->pm->eliminarPermiso($recurso);
    }

}
