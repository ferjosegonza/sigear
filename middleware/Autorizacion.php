<?php

/**
 * requierePermiso
 * En caso de que no tenga permiso lo redirigue a BASE_URL con un mensaje
 */
function requierePermiso($permiso) {

    return function(Request $request, Response $response, callable $next) use ($permiso) {
        $usuario = $request->getAttribute('usuario_cliente');
        if($usuario) {
            if (is_null($permiso) || !array_key_exists($permiso, $usuario['permisos'])) {
                return $response->withError(403);
            }else{
                return $next($request, $response);
            }
        }

        /* @var $usuario services\usuario\UsuarioService */
        $usuario = $this->get("service.usuario.Usuario");
        if ($usuario->tienePermiso($permiso)) {
            return $next($request, $response);
        }

        return $response->withError(403);
    };
}

/**
    * @OA\SecurityScheme(
    *     type="apiKey",
    *     name="X-API-KEY",
    *     in="header",
    *     securityScheme="API_KEY"
    * )
    */
function requiereApiKey($apiKey) {
    return function(Request $request, Response $response, callable $next) use ($apiKey) {

        if (!$request->hasHeader('X-API-KEY')) {
            // no enviaron la api key
            return $response->withError(403);
        }

        $headerApiKey = $request->getHeader('X-API-KEY')[0];
        if ($headerApiKey === $apiKey) {
            return $next($request, $response);
        }

        // la api key no es valida
        return $response->withError(403);
    };
}

function requiereToken() {
    return function(Request $request, Response $response, callable $next) {
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

        return $next($request->withAttribute('recurso_token', $jwtObj->data), $response);

    };
}