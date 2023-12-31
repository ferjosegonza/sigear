<?php

class Request extends Slim\Http\Request {

    /**
     * Create new HTTP request with data extracted from the application
     * Environment object
     *
     * @param  Slim\Http\Environment $environment The Slim application Environment
     *
     * @return static
     */
    public static function createFromEnvironment(Slim\Http\Environment $environment) {
        $method = $environment['REQUEST_METHOD'];
        $uri = Slim\Http\Uri::createFromEnvironment($environment);

        // CUSTOM CODE
        $basePath = $uri->getbasePath();
        if (!$basePath) {
            // modificamos basepath para que pueda estar en la subcarpeta 'app'

            $requestScriptName = parse_url(str_replace("/app", "", $_SERVER['SCRIPT_NAME']), PHP_URL_PATH);
            $requestScriptDir = dirname($requestScriptName);
            $requestUri = parse_url('http://example.com' . $_SERVER['REQUEST_URI'], PHP_URL_PATH);

            if (stripos($requestUri, $requestScriptName) === 0) {
                $basePath = $requestScriptName;
            } elseif ($requestScriptDir !== '/' && stripos($requestUri, $requestScriptDir) === 0) {
                $basePath = $requestScriptDir;
            }

            if ($basePath) {
                $path = ltrim(substr($requestUri, strlen($basePath)), '/');
                if ($path === "") {
                    $path = "/";
                }
                $uri = $uri->withPath($path)->withBasePath($basePath);
            }
        }
        // =====================

        $headers = Slim\Http\Headers::createFromEnvironment($environment);
        $cookies = Slim\Http\Cookies::parseHeader($headers->get('Cookie', []));
        $serverParams = $environment->all();
        $body = new Slim\Http\RequestBody();
        $uploadedFiles = Slim\Http\UploadedFile::createFromEnvironment($environment);
        $request = new static($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);
        if ($method === 'POST' &&
            in_array($request->getMediaType(), ['application/x-www-form-urlencoded', 'multipart/form-data'])
        ) {
            // parsed body must be $_POST
            $request = $request->withParsedBody($_POST);
        }

        return $request;
    }

    /**
     * Is this an XHR request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isXhr() {
        $uri = $this->getUri();
        $path = $uri->getPath();
        $isApi = (substr( $path, 0, 5 ) === "/api/" || $path === '/api');

        return $isApi || parent::isXhr();
    }

    public function getArguments() {
        return $this->getAttribute('routeInfo')[2];
    }

    public function getArgument($arg, $default = null) {
        $args = $this->getArguments();
        if (empty($args) || !isset($args[$arg])) {
            return $default;
        }
        return $args[$arg];
    }
}