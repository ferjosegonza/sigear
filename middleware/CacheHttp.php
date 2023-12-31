<?php


/**
 * Activa la cache del navegador y le setea un tiempo de vida
 *
 * @param int|null $tiempo tiempo en segundos
 * @return Response
 */
function cacheTiempo(int $tiempo = null){
    return function(Request $request, Response $response, callable $next) use ($tiempo){
        $res = $next($request, $response);
        if(is_null($tiempo) || !is_integer($tiempo)) return $res;
        header_remove('Cache-control');
        header_remove('Pragma');
        return $res->withHeader('Cache-control','public, max-age='.$tiempo);
    };
}
