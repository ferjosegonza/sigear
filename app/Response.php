<?php

class Response extends Slim\Http\Response {
    public function withError($code, $metadata = array(), $reasonPhrase = null) {
        $code = intval($code);
        if ($code < 400) {
            throw new Exception('withError solo soporta codigos de error');
        }

        $response = $this;

        $data = array(
            'error' => array(
                'code' => $code,
                'metadata' => $metadata
            )
        );

        switch($code) {
            case 400:
                $data['error']['message'] = "SOLICITUD INVÁLIDA";
                break;
            case 401:
                $data['error']['message'] = "AUTENTICACIÓN INVÁLIDA O VENCIDA";
                break;
            case 403:
                $data['error']['message'] = "NO TIENE PERMISOS PARA REALIZAR ESTA ACCIÓN";
                break;
            case 404:
                $data['error']['message'] = "PÁGINA NO ENCONTRADA";
                break;
            case 405:
                $data['error']['message'] = "MÉTODO INVÁLIDO";
                break;
            default:
                $data['error']['message'] =  strtoupper($response->getReasonPhrase());
                break;
        }

        if ($reasonPhrase) {
            $data['error']['message'] = strtoupper($reasonPhrase);
        }

        $responseWithError = $response->withStatus($code, $data['error']['message'])
                                      ->withNoCache();

        global $container;
        if ($container->request->isXhr()) {
            return $responseWithError->withJson($data);
        } else {
            return $container['view']->render($responseWithError, 'layout/error.twig', $data);
        }
    }

    public function withAppError($reasonPhrase, $metadata = array()) {
        return $this->withError(417, $metadata, $reasonPhrase);
    }


    /*
     * The authorization server MUST include the HTTP response headers:
     * - Cache-Control: no-store
     * - Pragma: no-cache
     *
     * In any response containing tokens, credentials, or other sensitive info.
     */
    public function withNoCache() {
        return $this->withHeader('Cache-Control', 'no-store')
                    ->withHeader('Pragma', 'no-cache');
    }

    public function withFile(Request $request, string $filePath, $opts = []) {
        if (!file_exists($filePath)) {
            return $this->withStatus(404);
        }

        // obtenemos stream del archivo
        $stream = new \GuzzleHttp\Psr7\LazyOpenStream($filePath, 'r');

        // generamos respuesta base
        $response = $this->withHeader('Content-disposition', 'inline')
            ->withHeader('Content-Length', $stream->getSize())
            ->withBody($stream);

        // si esta definido el mimetype, agregamos Content-Type
        $mimetype = isset($opts['mimetype']) ? $opts['mimetype'] : mime_content_type($filePath);
        if ($mimetype) $response = $response->withHeader('Content-Type', $mimetype);

        return $response;
    }

}
