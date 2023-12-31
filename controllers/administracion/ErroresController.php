<?php

class ErroresController extends Controller {

    /*=========================================Vistas de Error es=========================================*/

    public function vistaErrores(Request $request, Response $response, $args) {
        $this->render($response, 'administracion/auditoria/errores', [
            'solapaNombre' => 'ERRORES',           
            'appBackTo' =>'/administracion-botonera',
        ]);
    }

    public function vistaError(Request $request, Response $response, $args) {
        require LIBS_DIR . '/logParser/MonologViewer.php';
        $search = $request->getParam('q');
        $filter = $request->getParam('filter');
        $sc = $request->getParam('sc');

        $viewer->render($lines, $filter, $sc, $search);
    }

    /*=========================================Xhr de Error es=========================================*/

    public function listarErrores(Request $request, Response $response, $args) {
        $data = [];

        foreach (glob(DEVLOG_DIR."/*.*") as $filename) {
            $shortFileName = end(explode("/",$filename));
            $data[$shortFileName] = [
                "TEXTO" => ""
            ];

            $fp = fopen($filename, "r");
            while (!feof($fp)){
                $data[$shortFileName]["TEXTO"] .= fgets($fp) . "\n";
            }
            fclose($fp);
        }

        return $response->withJson([
            "data" => $data,
        ]);
    }

    public function guardarError(Request $request, Response $response, $args) {
        $texto = $request->getParam("texto", null);
        throw new ServerException($texto);
    }

    public function lecturaesJson($logPath){
        $lines = array_reverse(explode("\n", file_get_contents(DEVLOG_DIR.'/'.$logPath)));
        $logs = [];
        foreach ($lines as $line) {
            if (!$line) {
                continue;
            }
            $json = json_decode($line, true);
            if ($json === null) {
                die('Could not read log line: ' . $line);
            }

            $logs[] = $json;

        }
        return $logs;
    }
}
