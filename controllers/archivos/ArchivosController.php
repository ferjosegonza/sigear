<?php

class ArchivosController extends Controller {

    
    private ArchivosModel $am;
    
    public function __construct($container) {
        parent::__construct($container);
        $this->am = new ArchivosModel();
     
    }

    public function vistaBuscarArchivos(Request $request, Response $response, $args) {
        $informeID = $request->getParam("informeID");
            
        $this->render($response,'archivos/buscador', [
            'modalTitle' => "BUSCAR REPOSITORIOS",           
            "informeID" => $informeID
        ]); 
    }

    public function vistaEditorImagen(Request $request, Response $response, $args) {
        $informeID = $request->getParam("informeID");
        $imagenID = $request->getParam("imagenID");    
        $nombre = $request->getParam("nombre"); 
        $orden = $request->getParam("orden");
        $forceCache = $request->getParam("forceCache");

        $this->render($response,'archivos/editor_imagen', [
            'modalTitle' => "BUSCAR REPOSITORIOS",           
            "informeID" => $informeID,
            "imagenID" => $imagenID,
            "nombre" => $nombre,
            "orden" => $orden,
            "forceCache" => $forceCache
        ]); 
    }

    //-----------------API------------------------
    public function listarArchivos(Request $request, Response $response, $args ){
        $draw = $request->getParam("draw", null);
        $start = $request->getParam("start", null);
        $length = $request->getParam("length", null);
        $filtros = $request->getParam("filtros", array());
        $ordenes = $request->getParam("ordenes", array());

        $lista = $this->am->listarArchivos($start, $length, $filtros, $ordenes);
        $total = $this->am->totalArchivos();
        $totalWithFilter = is_null($filtros) ? $total : $this->am->totalArchivos($filtros);

        
        return $response->withJson([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalWithFilter,
            "data" => $lista
        ]);
    }

    public function getTiffFromRepositorio(Request $request, Response $response, $args ){
        $repositorioID = $args['repositorioID'];
        $total_paginas = $request->getParam("total_paginas", null);
        $pagina = $request->getParam("pagina", null);

        $repo = $this->am->listarArchivos(null, null, ['repositorio' => $repositorioID], []);
        
        $path = ROOT_DIR.'/public/paginas_tif'.'/tif'.$repositorioID.'_pagina'.$pagina.'.tif';
        
        $mime = mime_content_type($path);
        
        $file= new \GuzzleHttp\Psr7\LazyOpenStream($path, 'r');
        
        header_remove("Cache-Control");
        header_remove("Pragma");
        header_remove("Expires");

        return $response->withHeader('Content-Disposition', 'inline')
                        ->withHeader('Cache-Control', "public, max-age=604800, immutable")
                        //->withHeader('Cache-Control', "no-cache, no-store, must-revalidate")
                        ->withHeader('Content-Type', $mime)
                        ->withBody($file);
    }

    public function getImagenArchivo(Request $request, Response $response, $args ){
        $archivoID = $args['archivoID'];
        $informeID = $args['informeID'];
        
        $path = INFORMES_DIR.'/'.$informeID.'/'.$archivoID.'.jpg';

        $mime = mime_content_type($path);
        $file= new \GuzzleHttp\Psr7\LazyOpenStream($path, 'r');
        
        header_remove("Cache-Control");
        header_remove("Pragma");
        header_remove("Expires");

        return $response->withHeader('Content-Disposition', 'inline')
                        ->withHeader('Cache-Control', "public, max-age=604800, immutable")
                        //->withHeader('Cache-Control', "no-cache, no-store, must-revalidate")
                        ->withHeader('Content-Type', $mime)
                        ->withBody($file);
    }

    public function addAImagenInforme(Request $request, Response $response, $args ){
        
        $imagen = $request->getParam("imagen");
        
        $informeID = $request->getParam("informeID");
        $repoID = $request->getParam("repositorioID");
        $nombre = $request->getParam("nombre");
        $orden = $request->getParam("orden", null);
        $orden = $orden == "null" ? null : $orden;

        if (!file_exists(INFORMES_DIR.'/'.$informeID)) {
            throw new Exception("El directorio de imagenes del informe no existe, actualice el informe."); 
        }

        $archivoID = $this->am->addArchivo(
            $informeID, 
            $repoID, 
            $nombre,
            $orden,
            $this->getUsuario()->getIdCuenta()
        );

        //guardar imagen directorio de archivo
        $imagen = $request->getUploadedFiles('imagen')['imagen'];
        $imagen = file_get_contents($imagen->file);
       
        $path = INFORMES_DIR.'/'.$informeID.'/'.$archivoID.'.jpg';
        $ifp = fopen( $path, "wb" ); 
        fwrite( $ifp, $imagen); 
        fclose( $ifp ); 

        return $response->withJson(['archivoID' => $archivoID]);
    }

    public function updateImagenInforme(Request $request, Response $response, $args ){
        
        $imagen = $request->getParam("imagen");
        
        $archivoID = $request->getParam("imagenID");
        $informeID = $request->getParam("informeID");
        $nombre = $request->getParam("nombre");
        $orden = $request->getParam("orden", null);
        $orden = $orden == "null" ? null : $orden;
        
        if (!file_exists(INFORMES_DIR.'/'.$informeID)) {
            throw new Exception("El directorio de imagenes del informe no existe, actualice el informe."); 
        }

        $update = $this->am->updateArchivo(
            $archivoID,
            $nombre,
            $orden,
            $this->getUsuario()->getIdCuenta()
        );

        //guardar imagen directorio de archivo
        $imagen = $request->getUploadedFiles('imagen')['imagen'];
        $imagen = file_get_contents($imagen->file);
       
        $path = INFORMES_DIR.'/'.$informeID.'/'.$archivoID.'.jpg';
        $ifp = fopen( $path, "wb" ); 
        fwrite( $ifp, $imagen); 
        fclose( $ifp ); 

        return $response->withJson(['archivoID' => $archivoID]);
    }

    public function updateOrden(Request $request, Response $response, $args ){
        $positions = $request->getParam("positions", []);
        if($positions){
            foreach($positions as $item){
                $update = $this->am->updateArchivo(
                    $item['imagenID'],
                    $item['nombre'],
                    $item['orden'],
                    $this->getUsuario()->getIdCuenta()
                );
            } 
        }

        return $response->withJson(['response' => true]);
    }

    public function deleteArchivo(Request $request, Response $response, $args ){
        $archivoID = $args["archivoID"];
        $informeID = $request->getParam('informeID');
        $usuario = $this->getUsuario()->getIdCuenta();

        // eliminar el directorio de archivo
        if(file_exists(INFORMES_DIR.'/'.$informeID.'/'.$archivoID.'.jpg')){
            // borrado fisico
            unlink(INFORMES_DIR.'/'.$informeID.'/'.$archivoID.'.jpg');
            // borrado virtual
            $this->am->deleteArchivo($archivoID, $usuario);
        } else {
            return $response->withStatus(404, 'NO SE ENCONTRO EL ARCHIVO A ELIMINAR');
        }

        return $response->withStatus(200, 'ARCHIVO ELIMINADO');
    }


}