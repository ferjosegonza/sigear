<?php
    /**
    * @OA\Info(
    *      title=APP_NAME,
    *      version=APP_VERSION,
    * ),
    * @OA\Server(
    *      url=BASE_URL,
    *      description="DESA",
    * )
    */
class AppController extends Controller {
    private AuthHandlerService $authHandler;
    private SessionHandlerService $sessionHandler;

    public function __construct($container) {
        parent::__construct($container);
        $this->sessionHandler = $this->get('service.authenticator.SessionHandler');
        $this->authHandler = $this->get('service.authenticator.AuthHandler');
    }

    // Utilizado como portada de la aplicacion
    public function botonera(Request $request, Response $response, $args) {
        $data = array(
            'portada' => true
        );
        $this->render($response, 'layout/botonera', $data);
    }

    // Endpoint Login
    public function renderLogin(Request $request, Response $response, $args) {
        $csrfToken = obtenerRandomBase64Token(32);
        $this->sessionHandler->setToken([
            "AUTH_CSRF_TOKEN" => $csrfToken,
            "AUTH_CSRF_EXPIRATION_TIME" => time() + 3600
        ]);

        return $this->render($response, "layout/login", [
            "loginUrl" => LOGIN_URL,
            "sinInitJS" => true,
            "csrftoken" => $csrfToken
        ]);
    }

    // Login desde la app
    public function login(Request $request, Response $response, $args) {
        $csrftoken = $request->getParam('csrftoken');
        $token = $this->sessionHandler->getToken();

        if (empty($csrftoken) || empty($token) || empty($token['AUTH_CSRF_TOKEN']) || $csrftoken !== $token["AUTH_CSRF_TOKEN"]) {
            return $response->withError(403,['csrftoken'=>'invalido']);
        }
        if (empty($token["AUTH_CSRF_EXPIRATION_TIME"]) || time() > $token["AUTH_CSRF_EXPIRATION_TIME"]) {
            return $response->withError(403,['csrftoken'=>'vencido']);
        }

        $args['app_login'] = true;
        $login = $this->authHandler->login($request, $response, $args);

        if($login['success'] !== true){
            return $response->withError(401, $login['error']);
        }

        $this->sessionHandler->cleanToken();

        $cuentasM = new CuentasModel();
        $usuario = $login['usuario'];
        $usuario['roles'] = $cuentasM->obtenerCuentaRoles($usuario['cuenta_id']);
        $usuario['permisos'] = $cuentasM->obtenerCuentaPermisos($usuario['cuenta_id']);
        $usuario['permisos'] = array_flip(array_column($usuario['permisos'], 'permiso_key'));
        $this->sessionHandler->setSesion($usuario);

        return $response->withJson(['redirectTo'=>BASE_URL]);
    }

    // Login desde un cliente
    public function getToken(Request $request, Response $response, $args) {
        $clientID = $request->getParam("client_id", null);
        $clientSecret = $request->getParam("client_secret", null);

        if (empty($clientID) || empty($clientSecret)) {
            return $response->withError(401,"Se requiere un 'client_id' y un 'client_secret'");
        }

        if (strlen($clientSecret) != 36) {
            return $response->withError(401,"Credenciales incorrectas");
        }

        if ($clientID != PORTAL_CLIENT_ID || $clientSecret != PORTAL_CLIENT_SECRET){
            return $response->withError(401,"Credenciales invalidas");
        }

        $login = $this->authHandler->login($request, $response, $args);

        if($login['success'] !== true){
            return $response->withError(401, $login['error']);
        }

        $cuentasM = new CuentasModel();
        $usuario = $login['usuario'];
        $usuario['roles'] = $cuentasM->obtenerCuentaRoles($usuario['cuenta_id']);
        $usuario['permisos'] = $cuentasM->obtenerCuentaPermisos($usuario['cuenta_id']);

        $secret_key = $clientSecret;
        $issuer_claim = "GPA"; // this can be the servername
        $audience_claim = "PORTAL";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim-360; //not before in seconds
        $expire_claim = $issuedat_claim + 360; // expire time in seconds
        $usuarioToken = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "usuario" => $usuario,
        ));

        $token = \Firebase\JWT\JWT::encode($usuarioToken, $secret_key, 'HS512');

        $expire_claim = $issuedat_claim + 86400; // expire time in seconds
        $refreshToken = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "cuenta_id" => $usuario['cuenta_id'],
                "cuenta_key" => $usuario['cuenta_key']
        ));
        $refresh = \Firebase\JWT\JWT::encode($refreshToken, $secret_key, 'HS512');

        return $response->withJson(["token"=>$token, "refresh"=>$refresh]);
    }

    // Login desde un cliente
    public function refreshToken(Request $request, Response $response, $args) {
        if (!$request->hasHeader('Authorization')) {
            // no enviaron Authorization
            return $response->withError(403, "No se recibió un Authorization Header");
        }

        $jwt = $request->getHeaderLine("Authorization");

        if (!is_string($jwt) || strlen($jwt) <= 0) {
            return $response->withError(403, "No se recibió un Authorization Header");
        }

        $jwt = explode(" ", $jwt)[1];

        try{
            $jwtObj = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key(PORTAL_CLIENT_SECRET, 'HS512'));
        }catch(Exception $e){
            return $response->withError(403,$e->getMessage());
        }

        $cuentaID = $jwtObj->data->cuenta_id;

        $cuentasM = new CuentasModel();
        $usuario = $cuentasM->detalleCuenta($cuentaID);
        $usuario['roles'] = $cuentasM->obtenerCuentaRoles($usuario['cuenta_id']);
        $usuario['permisos'] = $cuentasM->obtenerCuentaPermisos($usuario['cuenta_id']);

        $secret_key = PORTAL_CLIENT_SECRET;
        $issuer_claim = "GPA"; // this can be the servername
        $audience_claim = "PORTAL";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim-360; //not before in seconds
        $expire_claim = $issuedat_claim + 360; // expire time in seconds
        $usuarioToken = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "usuario" => $usuario,
        ));

        $token = \Firebase\JWT\JWT::encode($usuarioToken, $secret_key, 'HS512');

        return $response->withJson(["token"=>$token]);
    }

    // Endpoint logout
    public function logout(Request $request, Response $response, $args) {
        $this->sessionHandler->deleteSesion();

        return $response->withRedirect(BASE_URL);
    }

    public function actualizarSesion(Request $request, Response $response, $args) {
        $usuario = $this->sessionHandler->getSesion();
        $cuentasM = new CuentasModel();
        $usuario = $cuentasM->detalleCuenta($usuario['cuenta_id']);
        $usuario['roles'] = $cuentasM->obtenerCuentaRoles($usuario['cuenta_id']);
        $usuario['permisos'] = $cuentasM->obtenerCuentaPermisos($usuario['cuenta_id']);
        $usuario['permisos'] = array_flip(array_unique(array_column($usuario['permisos'], 'permiso_key')));
        $this->sessionHandler->setSesion($usuario);
        return $response->withRedirect(BASE_URL);
    }

    public function sesionEsValida() {
        return $this->sessionHandler->existeSesion();
    }

    public function getUsuarioLogeado() {
        return $this->sessionHandler->getSesion();
    }

    public function debugSesion(Request $request, Response $response, $args) {
        return $response->withJson([$this->sessionHandler->getSesion()]);
    }

    public function debugToken(Request $request, Response $response, $args) {
        $token = $request->getParam('token');
        if(empty($token)) return $response->withError(404);
        try{
            $jwtObj = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(PORTAL_CLIENT_SECRET, 'HS512'));
        }catch(Exception $e){
            return $response->withError(403,$e->getMessage());
        }
        return $response->withJson([$jwtObj]);
    }
     // Endpoint del SWAGGER
    public function swagger(Request $request, Response $response, $args) {

        $data = array(
            'urlJson' => BASE_URL.'/api/docs/json'
        );
        $this->render($response, 'layout/swagger-ui', $data);
    }

    public function swaggerJson(Request $request, Response $response, $args) {
        return $response->withJson(json_decode(\OpenApi\scan([CONTROLLERS_DIR, MIDDLEWARE_DIR, CONFIG_DIR], ['validate'=>false])->toJson(),true));
    }
}
