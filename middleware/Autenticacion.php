<?php

/**
 * RequiereUsuario
 * En caso de que no haya una sesion valida redirige al LOGIN_URL
 *
 *
 * Depende de:
 * - LOGIN_URL
 */
function requiereUsuario() {
    return function(Request $request, Response $response, callable $next) {
        //evaluar de donde viene el request
        //si es por api de cliente
        if ($request->hasHeader('Authorization')) {

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

            $usuario = json_decode(json_encode($jwtObj->data->usuario),true);
            $usuario['permisos'] = array_flip(array_unique(array_column($usuario['permisos'], 'permiso_key')));
            return $next($request->withAttribute('usuario_cliente', $usuario), $response);
        }

        //si es por app
        $authenticator = $this->get("service.authenticator.SessionHandler");
        if ($authenticator->existeSesion()) {
            return $next($request, $response);
        }

        if (!$request->isXhr()) {
            return $response->withRedirect(LOGIN_URL);
        }

        return $response->withError(401);
    };
}


/**
 * RequiereAnonimo
 * En caso de que haya un usuario logueado lo redirigue a BASE_URL
 *
 *
 * Depende de:
 * - BASE_URL
 */
function requiereAnonimo() {
    return function(Request $request, Response $response, callable $next) {
        $authenticator = $this->get("service.authenticator.SessionHandler");
        if (!$authenticator->existeSesion()) {
            return $next($request, $response);
        }

        if (!$request->isXhr()) {
            return $response->withRedirect(BASE_URL);
        }

        return $response->withError(403);
    };
}

