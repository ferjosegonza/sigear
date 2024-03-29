<?php

/**
 * RemoverUltimaBarra
 * Redirige de manera permanente todas las url terminadas en '/'
 * a la correspondiente sin '/'
 */
function removerUltimaBarra() {

    return function(Request $request, Response $response, callable $next) {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path != '/' && substr($path, -1) == '/') {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));
            if($request->getMethod() == 'GET') {
                return $response->withRedirect(/* (string)$uri */$uri->getPath(), 301);
            } else {
                return $next($request->withUri($uri), $response);
            }
        }

        return $next($request, $response);
    };
}


