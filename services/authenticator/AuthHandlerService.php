<?php

class AuthHandlerService extends Service {
    private UsuariosModel $usuariosM;

    public function __construct($container) {
        parent::__construct($container);
        $this->usuariosM = new UsuariosModel();
    }

    public function login(Request $request, Response $response, $args) {
        $dto['user'] = $request->getParam("user", "");
        $dto['password'] = $request->getParam("password", "");

        // normalizar
        $dto['user'] = trim($dto['user']);
        $dto['password'] = trim($dto['password']);

        // validar
        $errores = [];
        if (empty($dto['user'])) $errores['user'] = 'Usuario requerido';
        if (empty($dto['password'])) $errores['password'] = 'Contraseña requerida';
        if (!empty($errores)) return ['success'=>false, 'data'=>['user'=>$dto['user']], 'error'=>'usuario y password son requeridos'];

        $browserInfo = getBrowserInfo();
        $evento = [
            'cuenta_key' => $dto['user'],
            'client_ip' => getClientIP(),
            'payload' => [
                'clientBrowser' => $browserInfo['browserName'],
                'clientBrowserVersion' => $browserInfo['browserVersion'],
                'clientOs' => $browserInfo['osName'],
                'clientDevice' => $browserInfo['device']
            ]
        ];

        // Chequear que cuenta_key ingresada no este bloqueada
        if ($this->usuariosM->obtenerUsuarioBloqueadoPorKey($dto['user'])) {
            $this->usuariosM->registrarEventoLogin('usuario_bloqueado', $evento);
            return ['success'=>false, 'error'=> 'Demasiados intentos de inicio de sesión, su usuario ha sido bloqueado por una hora'];
        }

        // Chequear que client ip no este bloqueada
        if (!empty($args['app_login']) && $this->usuariosM->obtenerIpBloqueada($evento['client_ip'])) {
            $this->usuariosM->registrarEventoLogin('ip_bloqueada', $evento);
            return ['success'=>false, 'error'=> 'Demasiados intentos de inicio de sesión, su ip ha sido bloqueada por una hora'];
        }

        // Buscamos al usuario
        $usuario = $this->usuariosM->obtenerUsuarioPorKey($dto['user']);

        // Verificamos el hash
        $passwordValido = false;
        if ($usuario && $usuario['password_hash']) {
            // Si el usuario existe y tiene credenciales, chequeamos su password
            $passwordValido = password_verify($dto['password'], $usuario['password_hash']);
        } else {
            // sino verificamos contra un hash generico para tener un tiempo
            // de respuesta equilibrado
            password_verify($dto['password'], '$2y$12$3BGJYxOQkelajZmN/roxmO1zjv1IL83YomaECBNNrZy0SoN.8i392');
        }

        // Chequear que exista el usuario
        if (empty($usuario)) {
            $this->usuariosM->registrarEventoLogin('usuario_inexistente', $evento);
            return ['success'=>false, 'error'=> 'Credenciales Inválidas'];
        }
        $evento['cuenta_id'] = $usuario['cuenta_id'];

        // Chequear que usuario tenga credenciales
        if (!$usuario['password_hash']) {
            $this->usuariosM->registrarEventoLogin('usuario_sin_credenciales', $evento);
            return ['success'=>false, 'error'=> 'Credenciales Inválidas'];
        }

        // Chequear que password sea correcto
        if (!$passwordValido) {
            $this->usuariosM->registrarEventoLogin('password_invalido', $evento);
            return ['success'=>false, 'error'=> 'Credenciales Inválidas'];
        }

         // Chequear que usuario este activo
        if ($usuario['status'] === 'I') {
            $this->usuariosM->registrarEventoLogin('usuario_inactivo', $evento);
            return ['success'=>false, 'error'=> 'Usuario Inactivo'];
        }

        // Chequear que las credenciales esten activas
        if ($usuario['password_status'] === 'I') {
            // TODO deberia iniciar un proceso de restablecer contraseña...
        }

        // Login exitoso!
        $this->usuariosM->registrarEventoLogin('login_exitoso', $evento);

        $this->usuariosM->limpiarEventosLoginFallidosUsuario($usuario);

        return ['success'=>true, 'usuario'=>[
            'cuenta_id' => $usuario['cuenta_id'],
            'cuenta_key' => $usuario['cuenta_key'],
            'cuil' => $usuario['cuil'],
            'dni' => $usuario['dni'],
            'sexo' => $usuario['sexo'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'fecha_nacimiento' => $usuario['fecha_nacimiento'],
            'imagen' => $usuario['imagen'],
            'email' => $usuario['email'],
            'email_verificado' => $usuario['email_verificado'],
            'telefono' => $usuario['telefono'],
            'telefono_verificado' => $usuario['telefono_verificado'],
            'status' => $usuario['status'],
            'roles' => [],
            'permisos' => []
        ]];
    }

    public function logout($usuario) {
        #TODO guardar evento de logout ????
    }

}