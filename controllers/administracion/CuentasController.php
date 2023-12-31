<?php

class CuentasController extends Controller {
    private CuentasModel $pm;

    public function __construct($container) {
        parent::__construct($container);
        $this->pm = new CuentasModel();
        $this->rm = new RolesModel();
    }

    public function vistaCuentas(Request $request, Response $response, $args) {
        $this->render($response,'administracion/cuentas/cuentas', [
            'solapaNombre' => 'USUARIOS',
            'appBackTo' =>'/administracion-botonera',
        ]);
    }

    public function vistaDetalleCuenta(Request $request, Response $response, $args) {
        $recurso = $request->getAttribute('recurso_cuenta');

        $this->render($response,'administracion/cuentas/cuentas_detalle', [
            'modalTitle' => 'EDITANDO USUARIO: "'.$recurso['cuenta_key'].'"',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "cuenta" => $recurso,
            "documento_tipos" => $this->pm->listarDocumentoTipos(null,null,[],[['descripcion']]),
        ]);
    }

    public function vistaNuevoCuenta(Request $request, Response $response, $args) {
        $this->render($response,'administracion/cuentas/cuentas_detalle', [
            'modalTitle' => 'AGREGAR USUARIO',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "documento_tipos" => $this->pm->listarDocumentoTipos(null,null,[],[['descripcion']]),
        ]);
    }

    public function vistaDetalleCuentaRoles(Request $request, Response $response, $args) {
        $recurso = $request->getAttribute('recurso_cuenta');
        $recurso['roles'] = $this->pm->listarCuentaRoles(null,null,['cuenta_id'=>$recurso['cuenta_id']],[['rol_key'=>'asc']]);

        $this->render($response,'administracion/cuentas/roles', [
            'modalTitle' => 'EDITANDO ROLES DEL USUARIO: "'.$recurso['cuenta_key'].'"',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "cuenta" => $recurso,
            "roles" => $this->rm->listarRoles(null,null,[],[['descripcion']]),
        ]);
    }

    //-----------------API------------------------
    public function listarCuentas(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());

        $lista = $this->pm->listarCuentas($start, $length, $filtros, $ordenes);
        $total = $this->pm->totalCuentas();
        $totalWithFilter = is_null($filtros) ? $total : $this->pm->totalCuentas($filtros);

        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function crearCuenta(Request $request, Response $response, $args ){
        $data = [
            'key' => trim($request->getParam("usuario")),
            'apellido' => $request->getParam("apellido"),
            'nombre' => $request->getParam("nombre"),
            'documento_tipo_id' => $request->getParam("documento_tipo"),
            'documento' => $request->getParam("documento"),
            'email' => $request->getParam("email"),
            'password' => $request->getParam("password"),
            'hash_function' => 'BCRYPT',
            'hash_factor' => 12,
            "status" => 'A',
            "empresas" => $request->getParam('empresas',[]),
            "usuario" => $this->getUsuario()->getIdCuenta()
        ];

        $data['password_hash'] = password_hash($data["password"], PASSWORD_BCRYPT, ['cost'=>12]);

        if(!empty($this->pm->listarCuentas(null,null,['cuenta_key'=>'[EQ:'.$data['key'].']'])))
            return $response->withAppError('Ya existe un cuenta con ese nombre de usuario');

        try {
            $this->pm->beginTransaction();

            $data['cuenta_id'] = $this->pm->crearCuenta($data);
            $this->pm->crearUsuarioPerfil($data);
            $this->pm->crearCuentaCredencial($data);

            $this->pm->commit();
        } catch(Exception $e){
            if($this->pm->inTransaction()) $this->pm->rollBack();
            throw $e;
        }

        return $response->withJson($data);
    }

    public function actualizarCuenta(Request $request, Response $response, $args ){
        $data = $request->getAttribute('recurso_cuenta');

        $data['cuenta_key'] = trim($request->getParam("usuario"));
        $data['apellido'] = $request->getParam("apellido");
        $data['nombre'] = $request->getParam("nombre");
        $data['documento_tipo_id'] = $request->getParam("documento_tipo");
        $data['documento'] = $request->getParam("documento");
        $data['email'] = $request->getParam("email");
        $data['status'] = $request->getParam("status", $data['cuenta_status']);
        $data['usuario'] = $this->getUsuario()->getIdCuenta();

        $data['password'] = $request->getParam("password");
        if(empty($data['password']))
            $data['password_hash'] = (new UsuariosModel())->obtenerUsuario($data['cuenta_id'])['password_hash'];
        else
            $data['password_hash'] = password_hash($data["password"], PASSWORD_BCRYPT, ['cost'=>12]);

        if(!empty($this->pm->listarCuentas(null,null,['cuenta_id'=>'[NEQ:'.$data['cuenta_id'].']','cuenta_key'=>'[EQ:'.$data['cuenta_key'].']'])))
            return $response->withAppError('Ya existe un cuenta con ese nombre');

        try {
            $this->pm->beginTransaction();
            $this->pm->actualizarCuenta($data);
            $this->pm->actualizarUsuarioPerfil($data);
            $this->pm->actualizarCuentaCredencial($data);

            $this->pm->commit();
        } catch(Exception $e){
            if($this->pm->inTransaction()) $this->pm->rollBack();
            throw $e;
        }
        return $response->withJson($data);
    }

    public function eliminarCuenta(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_cuenta');
        $recurso['usuario'] = $this->getUsuario()->getIdCuenta();
        return $this->pm->eliminarCuenta($recurso);
    }

    public function guardarCuentaRoles(Request $request, Response $response, $args ){
        $recurso = $request->getAttribute('recurso_cuenta');
        $roles_asignados = $request->getParam("roles", []);
        $roles_actuales = $this->pm->listarCuentaRoles(null,null,['cuenta_id'=>$recurso['cuenta_id']]);

        $agregar = [];
        $borrar = [];

        if(empty($roles_actuales)) $agregar = array_column($roles_asignados,'id');
        if(empty($roles_asignados)) $borrar = array_column($roles_actuales,'rol_id');

        if(!empty($roles_actuales) && (!empty($roles_asignados))){
            $asignados = array_column($roles_asignados,'id');
            $actuales = array_column($roles_actuales,'rol_id');
            $agregar = array_diff($asignados, $actuales);
            $borrar = array_diff($actuales, $asignados);
        }

        try {
            $this->pm->beginTransaction();
            $usuario = $this->getUsuario()->getIdCuenta();
            foreach ($agregar as $key => $value) {
                $rol = [
                    'cuenta_id' => $recurso['cuenta_id'],
                    'rol_id' => $value,
                    'usuario' => $usuario
                ];
                $this->pm->crearCuentaRol($rol);
            }

            foreach ($borrar as $key => $value) {
                $rol = current(array_filter($roles_actuales, function($actual)use($value){return ($actual['rol_id'] == $value);}));
                $rol['usuario'] =  $usuario;
                $this->pm->eliminarCuentaRol($rol);
            }

            $this->pm->commit();
        } catch (Exception $e) {
            if ($this->pm->inTransaction()) $this->pm->rollBack();
            throw $e;
        }
    }

}
