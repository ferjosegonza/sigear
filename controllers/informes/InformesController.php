<?php

use Dompdf\Dompdf;

class InformesController extends Controller {

    
    private InformesModel $im;
    private ArchivosModel $am;

    public function __construct($container) {
        parent::__construct($container);
        $this->im = new InformesModel();
        $this->am = new ArchivosModel();
     
    }


    public function vistaNuevoInforme(Request $request, Response $response, $args) {
        $solicitud = $request->getParam('solicitudID');
        $this->render($response,'informes/nuevo_informe', [
            'modalTitle' => 'NUEVO INFORME',
            'solicitudID' => $solicitud,
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ]
        ]);
    }

    public function vistaDetalleInforme(Request $request, Response $response, $args) {
        $idInforme = $args['informeID'];
        $informe = $this->im->detalleInforme($idInforme);
               
        $this->render($response,'informes/edit_informe', [
            'modalTitle' => "EDITANDO INFORME: '".$informe['descripcion']."'",
            "modal" => [
                    "class" => "modal--rounded modal-max-width-500",
            ],
            "estados" => $this->im->estadosInforme(),
            "informe" => $informe
        
        ]); 
    }

    public function vistaConfeccionarInforme(Request $request, Response $response, $args) {
        $informeID = $args['informeID'];
        $informe = $this->im->detalleInforme($informeID);
        $archivos = $this->im->listarImagenes(null, null, ['informeID' => $informeID], [['orden' => 'asc']]);
        
        $this->render($response,'informes/informe', [
            'solapaNombre' => "ARCHIVOS DEL INFORME: ".strtoupper($informe['descripcion']),           
            "informe" => $informe,
            "archivos" => $archivos
        
        ]); 
    }

    //-----------------API------------------------
    public function listarInformes(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());

        $lista = $this->im->listarInformes($start, $length, $filtros, $ordenes);
        $total = $this->im->totalInformes();
        $totalWithFilter = is_null($filtros) ? $total : $this->im->totalInformes($filtros);

        
        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function listarImagenes(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());

        $lista = $this->im->listarImagenes($start, $length, ['informeID' => $args['informeID']],  [['orden' => 'asc']]);
        $total = $this->im->totalImagenes();
        $totalWithFilter = is_null($filtros) ? $total : $this->im->totalImagenes($filtros);

        
        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function addInforme(Request $request, Response $response, $args ){
        $solicitud = $request->getParam("solicitud");
        $titulo = $request->getParam("titulo");
        
        $informeID = $this->im->addInforme($solicitud, $titulo, $this->getUsuario()->getIdCuenta());

        //crear directorio de informe
        if (!file_exists(INFORMES_DIR.'/'.$informeID)) {
            mkdir(INFORMES_DIR.'/'.$informeID, 0777, true);
        }

        return $response->withJson(['informeID' => $informeID]);
    }


    public function updateInforme(Request $request, Response $response, $args ){
        $informeID = $request->getParam("informeID");
        $estado = $request->getParam("estado");
        $titulo = $request->getParam("titulo");

        $this->im->updateInforme($informeID, $titulo, $estado, $this->getUsuario()->getIdCuenta());
        
        //crear directorio de informe en update provisorio
        if (!file_exists(INFORMES_DIR.'/'.$informeID)) {
            mkdir(INFORMES_DIR.'/'.$informeID, 0777, true);
        }
        return $response->withJson($informeID);
    }


    public function deleteInforme(Request $request, Response $response, $args ){
        $informeID = $request->getParam("informeID");
        $usuario = $this->getUsuario()->getIdCuenta();

        // eliminar el directorio de informe
        $this->removeDirectory(INFORMES_DIR.'/'.$informeID);
        
        return $this->im->deleteInforme($informeID, $usuario);
    }

    private function removeDirectory($path) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);
    
        return;
    }


    public function crearPdfInforme(Request $request, Response $response, $args ){
        $informeID = $args["informeID"];
        
        /*
        // DELETE ERROR FILES
        $files = [
            INFORMES_DIR.'/'.$informeID.'/informe.pdf',
            INFORMES_DIR.'/'.$informeID.'/informe-0.pdf',
            INFORMES_DIR.'/'.$informeID.'/informe-1.pdf',
            INFORMES_DIR.'/'.$informeID.'/informe-2.pdf',
            INFORMES_DIR.'/'.$informeID.'/informe-3.pdf',
        ];
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        die;*/

        $lista = $this->im->listarImagenes(null, null, ['informeID' => $informeID],  [['orden' => 'asc']]);
        
        $images = array();
        foreach($lista as $image){
            $path = INFORMES_DIR.'/'.$informeID.'/'.$image['informe_archivo_id'].'.jpg';
            $images[] = $path;
        }
       
        $pathInforme = INFORMES_DIR.'/'.$informeID.'/informe.pdf';
        $fp = fopen($pathInforme, 'w');
        $pdf = new Imagick($images);
        //$pdf->cropImage(595,842, 0, 0);
        $pdf->setImagePage(842, 1190, 0, 0);
        $pdf->resetiterator();
        $pdf->setimageformat('pdf');
        $pdf->writeimagesfile($fp, true);
        fclose($fp);
        

        $pdf_content = file_get_contents($pathInforme, 'w');
        $file = base64_encode($pdf_content);

        header('Content-Type: application/pdf');
        return $response->withJson($file);
        

    }

}