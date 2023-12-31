<?php

class RolesController extends Controller {
    private RolesModel $roles;
    private PermisosModel $permisos;

    public function __construct($container) {
        parent::__construct($container);
        $this->roles = new RolesModel();
        $this->permisos = new PermisosModel();
    }

    public function vistaRoles(Request $request, Response $response, $args) {
        $this->render($response,'administracion/roles/roles', [
            'solapaNombre' => 'ROLES',
            'appBackTo' =>'/administracion-botonera',
        ]);
    }

    public function vistaDetalleRol(Request $request, Response $response, $args) {
        $recurso = $request->getAttribute('recurso_rol');

        $this->render($response,'administracion/roles/roles_detalle', [
            'modalTitle' => 'EDITANDO ROL',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "rol" => $recurso
        ]);
    }

    public function vistaNuevoRol(Request $request, Response $response, $args) {
        $this->render($response,'administracion/roles/roles_detalle', [
            'modalTitle' => 'AGREGAR ROL',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
        ]);
    }

    public function vistaDetalleRolPermisos(Request $request, Response $response, $args) {
        $recurso = $request->getAttribute('recurso_rol');
        $recurso['permisos'] = $this->roles->listarRolPermisos(null,null,['rol_id'=>$recurso['id']],[['permiso_key'=>'asc']]);

        $this->render($response,'administracion/roles/permisos', [
            'modalTitle' => "EDITANDO PERMISOS DEL ROL: '".$recurso['key']."'",
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "rol" => $recurso,
            "permisos" => $this->permisos->listarPermisos(null,null,[],[['key'=>'asc']]),
        ]);
    }

    //-----------------API------------------------
    public function listarRoles(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());

        $lista = $this->roles->listarRoles($start, $length, $filtros, $ordenes);
        $total = $this->roles->totalRoles();
        $totalWithFilter = is_null($filtros) ? $total : $this->roles->totalRoles($filtros);

        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function crearRol(Request $request, Response $response, $args ){
        $data = [
            "key" => $request->getParam("nombre"),
            "descripcion" => $request->getParam("descripcion"),
            "status" => 'A',
            "usuario" => $this->getUsuario()->getIdCuenta()
        ];

        if(!empty($this->roles->listarRoles(null,null,['key'=>'[EQ:'.$data['key'].']'])))
            return $response->withAppError('Ya existe un rol con ese nombre');

        $data['id'] = $this->roles->crearRol($data);
        return $response->withJson($data);
    }

    public function actualizarRol(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_rol');
        $data = [
            "id" => $recurso['id'],
            "key" => $request->getParam("nombre", $recurso['key']),
            "descripcion" => $request->getParam("descripcion", $recurso['descripcion']),
            "status" => $request->getParam("status", $recurso['status']),
            "usuario" => $this->getUsuario()->getIdCuenta()
        ];

        if(!empty($this->roles->listarRoles(null,null,['id'=>'[NEQ:'.$data['id'].']','key'=>'[EQ:'.$data['key'].']'])))
            return $response->withAppError('Ya existe un rol con ese nombre');

        $this->roles->actualizarRol($data);
        return $response->withJson($data);
    }

    public function eliminarRol(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_rol');
        $recurso['usuario'] = $this->getUsuario()->getIdCuenta();
        return $this->roles->eliminarRol($recurso);
    }

    public function guardarRolPermisos(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_rol');
        $permisos_asignados = $request->getParam("permisos", []);
        $permisos_actuales = $this->roles->listarRolPermisos(null,null,['rol_id'=>$recurso['id']]);

        $agregar = [];
        $borrar = [];

        if(empty($permisos_actuales)) $agregar = array_column($permisos_asignados,'id');
        if(empty($permisos_asignados)) $borrar = array_column($permisos_actuales,'permiso_id');

        if(!empty($permisos_actuales) && (!empty($permisos_asignados))){
            $asignados = array_column($permisos_asignados,'id');
            $actuales = array_column($permisos_actuales,'permiso_id');
            $agregar = array_diff($asignados, $actuales);
            $borrar = array_diff($actuales, $asignados);
        }

        try {
            $this->roles->beginTransaction();
            $usuario = $this->getUsuario()->getIdCuenta();
            foreach ($agregar as $key => $value) {
                $permiso = [
                    'rol_id' => $recurso['id'],
                    'permiso_id' => $value,
                    'usuario' => $usuario
                ];
                $this->roles->crearRolPermiso($permiso);
            }

            foreach ($borrar as $key => $value) {
                $permiso = current(array_filter($permisos_actuales, function($actual)use($value){return ($actual['permiso_id'] == $value);}));
                $permiso['usuario'] =  $usuario;
                $this->roles->eliminarRolPermiso($permiso);
            }

            $this->roles->commit();
        } catch (Exception $e) {
            if ($this->roles->inTransaction()) $this->roles->rollBack();
            throw $e;
        }
    }

}
