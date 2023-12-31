<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

//==============================================================================
// CONSTANTES
//==============================================================================

// Directorios del proyecto
define('PROJECT_DIR', dirname(__DIR__));
define('ROOT_DIR', PROJECT_DIR);

define('APP_DIR', PROJECT_DIR . '/app');
define('CONFIG_DIR', PROJECT_DIR . '/config');

define('ROUTES_DIR', PROJECT_DIR . '/routes');
define('CONTROLLERS_DIR', PROJECT_DIR . '/controllers');
define('MIDDLEWARE_DIR', PROJECT_DIR . '/middleware');
define('SERVICES_DIR', PROJECT_DIR . '/services');
define('MODELS_DIR', PROJECT_DIR . '/models');
define('TEMPLATES_DIR', PROJECT_DIR . '/views');

define('PUBLIC_PATH', '/public');
define('PUBLIC_DIR', PROJECT_DIR . PUBLIC_PATH);

define('LIBS_DIR', PROJECT_DIR . '/libs');
define('VENDOR_DIR', PROJECT_DIR . '/vendor');
define('DEVLOG_DIR', PROJECT_DIR. '/log');
define('UPLOAD_DIR', PROJECT_DIR. '/upload');

// Tipos de conexion a bases
define('ORACLE', 'oracle');
define('MYSQL', 'mysql');
define('SQLSERVER', 'sqlserver');
define('POSTGRES','postgres');

// Entorno
if (strstr($_SERVER["SERVER_NAME"], "desa-")) {
    define('ENTORNO', 'desarrollo');
} else if (strstr($_SERVER["SERVER_NAME"], "test-")) {
    define('ENTORNO', 'test');
} else if (strstr($_SERVER["SERVER_NAME"], "-local")) {
    define('ENTORNO', 'local');
} else {
    define('ENTORNO', 'produccion');
}

//==============================================================================
// APLICACION
//==============================================================================
require VENDOR_DIR . '/autoload.php';
require LIBS_DIR . '/utils.php';
require LIBS_DIR . '/date.php';
require LIBS_DIR . '/flash.php';
require LIBS_DIR . '/string.php';
require LIBS_DIR . '/http.php';
require LIBS_DIR . '/security.php';

// Cargar configuracion
require CONFIG_DIR . "/config.".ENTORNO.".php";
require CONFIG_DIR . '/config.php';

// iniciar session php
session_name('SID_' . APP_NAME);
session_set_cookie_params(0, '/', $_SERVER["SERVER_NAME"], false);
session_start();

// Cargar clases maestras
foreach (rglob(APP_DIR . "/*.php") as $componente) {
    require_once $componente;
}

// Cargar middlewares
foreach (rglob(MIDDLEWARE_DIR . "/*.php") as $componente) {
    require_once $componente;
}

// Cargar modelos
foreach (rglob(MODELS_DIR . "/*.php") as $componente) {
    require_once $componente;
}

//==============================================================================
// ERROR HANDLER
//==============================================================================
set_error_handler(function ($severidad, $mensaje, $fichero, $línea) {
    if (!(error_reporting() & $severidad)) {
        // Este código de error no está incluido en error_reporting
        return;
    }
    throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
});

//==============================================================================
// INSTANCIAR SLIM APP
//==============================================================================
$app = new App(array(
    'settings' => array(
        'addContentLengthHeader' => false,
        'displayErrorDetails' => DEBUG,
        "determineRouteBeforeAppMiddleware" => true,
    ),
    'request' => function ($container) {
        return Request::createFromEnvironment($container->get('environment'));
    },
    'response' => function ($container) {
        $headers = new Slim\Http\Headers(['Content-Type' => 'text/html; charset=UTF-8']);
        $response = new Response(200, $headers);
        return $response->withProtocolVersion($container->get('settings')['httpVersion']);
    }
));

//==============================================================================
// DEPENDENCIAS
//==============================================================================
$container = $app->getContainer();

// Logger: Monolog
$container['logger'] = function ($c) {
    // filename
    $logfile = DEVLOG_DIR . '/app.log';
    // channel
    $logger = new Monolog\Logger(APP_NAME);
    // config handler
    $handler = new Monolog\Handler\RotatingFileHandler($logfile, 7, \Monolog\Logger::DEBUG);
    // formatter
    $formatter = new Monolog\Formatter\JsonFormatter();
    $formatter->includeStacktraces(true);
    $handler->setFormatter($formatter);
    $logger->pushHandler($handler);
    return $logger;
};

// View Renderer
$container['view'] = function ($c) use ($viewCustomGlobals) {
    $view = new \Slim\Views\Twig(TEMPLATES_DIR, [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    // Los parametros de URL los incluimos desde aqui en vez del Controller para que sean cargados
    // aunque haya un error 500
    $view->offsetSet("appName", APP_NAME);
    $view->offsetSet("appVersion", defined("APP_VERSION") ? APP_VERSION : rand());
    $view->offsetSet("baseUrl", BASE_URL);
    $view->offsetSet("publicUrl", BASE_URL . PUBLIC_PATH);
    $view->offsetSet("debug", DEBUG);

    foreach ($viewCustomGlobals as $clave => $valor) {
        $view->offsetSet($clave, $valor);
    }
    return $view;
};

function suscribir($entidad, $path, $container) {
    $len = strlen($path) + 1;
    $len2 = strlen($entidad.".php");
    foreach (rglob($path . "/*".$entidad.".php") as $componente) {
        $aux = explode("/", substr($componente, $len, strlen($componente)-$len-$len2));
        $key = implode(".", $aux);
        $class = end($aux) . $entidad;

        $container[mb_strtolower($entidad).'.' . $key] = function($container) use($componente, $class) {
            require_once $componente;
            return new $class($container);
        };
    }
}

suscribir('Controller',CONTROLLERS_DIR, $container);

suscribir('Service',SERVICES_DIR, $container);

//debug($container);
// Configuramos la gestion de errores
$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $isDBException = get_class($exception) === 'DBException';

        $metadata = array(
            'archivo' => $exception->getFile(),
            'linea' => $exception->getLine(),
            'codigo' => $exception->getCode()
        );
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        if ($isDBException) {
            $metadata = array_merge($metadata, $exception->getMetadata());
            $lastFn = array_shift($trace);
            $metadata['archivo'] = $lastFn['file'];
            $metadata['linea'] = $lastFn['line'];
        }

        // log error
        $logger = $container['logger'];
        $logger->error($exception->getMessage(), $metadata);

        if (!DEBUG) {
            // respuesta personalizada
            $response->getBody()->rewind();
        }
        $response = $response->withStatus(500);

        if (DEBUG) {
            $metadata['trace'] = $trace;
            $payload = array(
                'error' => array(
                    'code' => 500,
                    'message' => $exception->getMessage(),
                    'metadata' => $metadata
                )
            );
        } else {
            $payload = array(
                'error' => array(
                    'code' => 500,
                    'message' => "ERROR INTERNO DEL SERVIDOR"
                )
            );
        }

        if ($request->isXhr()) {
            return $response->withJson($payload);
        } else {
            return $container->view->render($response, 'layout/error.twig', $payload);
        }
    };
};

// PHP 7 error handler
$container['phpErrorHandler'] = function ($container) {
    return $container['errorHandler'];
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $metadata = array(
            'ruta' => array(
                'base' => $request->getUri()->getBasePath(),
                'path' => $request->getUri()->getPath()
            )
        );

        return $response->withError(404, $metadata, "ENDPOINT NO EXISTE");
    };
};

$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $response->withHeader('Allow', implode(', ', $methods))->withError(405, array(
            'allowed' => $methods
        ));
    };
};

//==============================================================================
// REGISTRAR RUTAS Y MIDDLEWARES
//==============================================================================

$app->add(removerUltimaBarra());

if (file_exists(ROUTES_DIR . '/App.php')) {
    require ROUTES_DIR . '/App.php';
}

foreach (rglob(ROUTES_DIR . "/*.php") as $enrutador) {
    require_once $enrutador;
}

//------------------- CORS -------------------------------
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function (Request $req, Response $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin',"{$_SERVER['HTTP_ORIGIN']}" )
            ->withHeader('Access-Control-Allow-Headers',"{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}" )
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->add(function($request, $response, $next) {
    $route = $request->getAttribute("route");

    $methods = [];

    if (!empty($route)) {
        $pattern = $route->getPattern();

        foreach ($this->router->getRoutes() as $route) {
            if ($pattern === $route->getPattern()) {
                $methods = array_merge_recursive($methods, $route->getMethods());
            }
        }
        //Methods holds all of the HTTP Verbs that a particular route handles.
    } else {
        $methods[] = $request->getMethod();
    }

    $response = $next($request, $response);

    return $response->withHeader("Access-Control-Allow-Methods", implode(",", $methods));
});

//==============================================================================
// RUN!
//==============================================================================

//$_SERVER['REQUEST_URI'] = str_replace("//","/",$_SERVER['REQUEST_URI']);

$app->run();
?>