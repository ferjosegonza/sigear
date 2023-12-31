<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Ramsey\Uuid\Type\Integer;

//use Imagick;



class FondosController extends Controller {

    
    private FondosModel $fm;
    
    public function __construct($container) {
        parent::__construct($container);
        $this->fm = new FondosModel();
     
    }


    public function vistaFondos(Request $request, Response $response, $args) {
        $this->render($response,'fondos/fondos', [
            'solapaNombre' => 'FONDOS',
            'appBackTo' =>'/administracion-botonera',
        ]);
    }

    public function vistaNuevoFondos(Request $request, Response $response, $args) {
        $this->render($response,'fondos/fondos_detalle', [
            'modalTitle' => 'AGREGAR FONDO',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
                ]
        ]);
    }

    public function vistaDetalleFondosColumna(Request $request, Response $response, $args) {
        $recurso = $request->getAttribute('recurso_fondo');
        $recurso['columnas'] = $this->fm->listarFondosColumnas(null,null,['fondo_id'=>$recurso['fondo_id']],[['orden'=>'asc']]);
               
       $this->render($response,'fondos/columnas', [
        'modalTitle' => "EDITANDO COLUMNAS DEL FONDO: '".$recurso['fondo_nombre']."'",
        "modal" => [
            "class" => "modal--rounded modal--full-width",
        ],
        "fondo" => $recurso,
        "columnas" =>$this->fm->listarColumnas(null,null,[],[['key'=>'asc']]),
       
    ]); 

 
    }


    public function vistaDetalleFondo(Request $request, Response $response, $args) {
        $recurso = $request->getAttribute('recurso_fondo');

        $this->render($response,'fondos/fondos_detalle', [
            'modalTitle' => 'EDITANDO FONDO',
            "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],
            "fondo" => $recurso
        ]);
    }

   public function importarExcel(Request $request, Response $response, $args) {
        
        $this->render($response,'fondos/fondoImportarExcel', [
            'modalTitle' => 'IMPORTAR DATOS DE UN EXCEL',
         /*   "modal" => [
                "class" => "modal--rounded modal-max-width-500",
            ],*/
            "fondos" => $this->fm->listarFondos(null,null,[],[['fondo_nombre']]),
            //'appBackTo' =>'/fondos-botonera',
            'arbol' => $this->fm->fnArbol('descendente', '1')
        ]);

    }

    public function procesarExcel(Request $request, Response $response, $args) {
        $json = $request->getParam('excel');
        $fondo = $request->getParam('fondo');
      //  $nodo = $request->getParam('estructuraFondoId');
        $nombreArchivo = $request->getParam('nombreArtchivo');
       // debug($nodo);
        $usuario = $this->getUsuario()->getIdCuenta();
     //   debug($request->getParam('data'));
        $json = json_decode($json, true);
        $json = $json[array_keys($json)[0]];
        //recorro columnas
       //valor a insertar
    //   debug($json);
        if (empty($json)) {
            return $response->withAppError('El archivo esta vacio');
        }

        $cantColumn = $this->fm->cantidadColumnasFondo($fondo);
       // debug(count($json[0]), $cantColumn);
        if (count($json[0]) != $cantColumn) {
            return $response->withAppError('El archivo no coincide con la cantidad de columnas declaradas');
        }

        $archivoTifTipo='';

        $pos = strpos($nombreArchivo, ".");
        //$pos--;
        $archivoTifTipo = substr($nombreArchivo,0,$pos);
        $descripcionFondo = $this->fm->dameNombreFondo($fondo); 
       // debug($descripcionFondo['fondo_nombre']);

    /*    switch ($fondo){
        case 1 : //DIPBA
        //    $archivoTifTipo='dipba';
            break;
        case 2: //PENITENCIARIO    unidades penitenciarias0001.tif
        //    $archivoTifTipo= 'unidades penitenciarias';
            $nodo=0;
            break;
        }*/

        if ($descripcionFondo['fondo_nombre'] == 'DIPBA'){
            $nodo = $request->getParam('estructuraFondoId');
        }
        else{
            $nodo =0;
        }
       
      // debug($nodo);
        $nro = 1;
        
        $ultimoIdExcel = $this->fm->idExcel();

        try {
            $this->fm->beginTransaction();
            $usuario = $this->getUsuario()->getIdCuenta();
            foreach ( $json as $renglon => $fila ){ 
                // debug($fila);
                //var_dump($fila);
                         
            $archivoTif= $archivoTifTipo . str_pad($nro, 4, "0", STR_PAD_LEFT).'.tif';
            
            
            $this->fm->insertarRepositorio(array_values($fila), $fondo, $usuario, $nodo, $archivoTif, $ultimoIdExcel);
            $archivoTif='';
            $nro++;    
            }
            $this->fm->commit();

        } catch (Exception $e) { 
            if ($this->fm->inTransaction()) $this->fm->rollBack();
            throw $e;
        }

        return $response->withJson(['cantidad'=>count($json), 'idExcel'=>$ultimoIdExcel]);

    }

   /* public function vistaRepositorioPlana(Request $request, Response $response, $args) {
        $this->render($response,'fondos/repositorio', [
            'solapaNombre' => 'REPOSITORIO',
            'appBackTo' =>'/fondos-botonera',
        ]);
    }*/


    public function vistaRepositorio(Request $request, Response $response, $args) {
        $this->render($response,'fondos/repositorio', [
            'solapaNombre' => 'REPOSITORIO',
            "fondosRepo" => $this->fm->listarFondos(null,null,[],[['fondo_nombre']]),
            'arbol' => $this->fm->fnArbol('descendente', '1')
        ]);
    }



//-----------------API------------------------
public function listarFondos(Request $request, Response $response, $args ){
    $draw = $request->getParam("draw", null);
    $start = $request->getParam("start", null);
    $length = $request->getParam("length", null);
    $filtros = $request->getParam("filtros", array());
    $ordenes = $request->getParam("ordenes", array());

    $lista = $this->fm->listarFondos($start, $length, $filtros, $ordenes);
    $total = $this->fm->totalFondos();
    $totalWithFilter = is_null($filtros) ? $total : $this->fm->totalFondos($filtros);

    
    return $response->withJson([
        "draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $totalWithFilter,
        "data" => $lista
    ]);
}

public function crearFondo(Request $request, Response $response, $args ){
    $data = [
        "key" => $request->getParam("nombre"),
        "usuario" => $this->getUsuario()->getIdCuenta()
    ];

    if(!empty($this->fm->listarFondos(null,null,['key'=>'[EQ:'.$data['key'].']'])))
        return $response->withAppError('Ya existe un fondo con ese nombre');

    $data['id'] = $this->fm->crearFondo($data);
    return $response->withJson($data);
}


public function actualizarFondo(Request $request, Response $response, $args ){
    $recurso = $request->getAttribute('recurso_fondo');
    $data = [
        "fondo_id" => $recurso['fondo_id'],
        "key" => $request->getParam("nombre"),
        /*"descripcion" => $request->getParam("descripcion"),*/
        "usuario" => $this->getUsuario()->getIdCuenta()
    ];

    if(!empty($this->fm->listarFondos(null,null,['fondo_id'=>'[NEQ:'.$data['fondo_id'].']','key'=>'[EQ:'.$data['key'].']'])))
        return $response->withAppError('Ya existe un permiso con ese nombre');

    $this->fm->actualizarFondo($data);
    return $response->withJson($data);
}


public function eliminarFondo(Request $request, Response $response, $args ){
    $recurso = $request->getAttribute('recurso_fondo');
    $recurso['usuario'] = $this->getUsuario()->getIdCuenta();
    return $this->fm->eliminarFondo($recurso);
}

public function guardarFondoColumnas(Request $request, Response $response, $args ){
    $recurso = $request->getAttribute('recurso_fondo');    
    $columnas_asignadas = $request->getParam("columnas", []);
    $columnas_actuales = $this->fm->listarFondosColumnas(null,null,['fondo_id'=>$recurso['fondo_id']]);
    
    $agregar = [];
    $borrar = [];

    $borrar = array_column($columnas_actuales,'columna_id');
    $agregar = array_column($columnas_asignadas,'columna_id');

    foreach ($borrar as $key => $value) {
       // debug($borrar);
        $columna = current(array_filter($columnas_actuales, function($actual)use($value){return ($actual['columna_id'] == $value);}));
       
        $this->fm->eliminarFondoColumna($columna);
    }

    try {
        $this->fm->beginTransaction();
        $usuario = $this->getUsuario()->getIdCuenta();
        $orden = 0;
        foreach ($agregar as $key => $value) {
            $orden++;
            $columna = [
                'fondo_id' => $recurso['fondo_id'],
                'columna_id' => $value,
                'orden' => $orden
            ];

            $this->fm->crearFondoColumnas($columna);
        }

        $this->fm->commit();
    } catch (Exception $e) {
        if ($this->fm->inTransaction()) $this->fm->rollBack();
        throw $e;
    }
}

public function acDescripcion(Request $request, Response $response, $args ){
    $descripcion = $request->getParam('descripcion', null);
    $filtros = $request->getParam('filtros', []);
    $lista = $this->fm->acDescripcion("key", $descripcion, "fondo_nombre", "fondo_nombre", $filtros, true);
    return $response->withJson($lista);
}

public function listarRepositorioPlana(Request $request, Response $response, $args ){
   // var_dump('entrooo');
    $draw = $request->getParam("draw", null);
    $start = $request->getParam("start", null);
    $length = $request->getParam("length", null);
    $filtros = $request->getParam("filtros", array());
    $ordenes = $request->getParam("ordenes", array());

    $lista = $this->fm->listarRepositorioPlana($start, $length, $filtros, $ordenes);
    $total = $this->fm->totalRepositorioPlana();
    $totalWithFilter = is_null($filtros) ? $total : $this->fm->totalRepositorioPlana($filtros);

    
    return $response->withJson([
        "draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $totalWithFilter,
        "data" => $lista
    ]); 
}


public function listarRepositorio(Request $request, Response $response, $args ){
     $draw = $request->getParam("draw", null);
     $start = $request->getParam("start", null);
     $length = $request->getParam("length", null);
     $filtros = $request->getParam("filtros", array());
     $ordenes = $request->getParam("ordenes", array());
 
     $lista = $this->fm->listarRepositorio($start, $length, $filtros, $ordenes);
     $total = $this->fm->totalRepositorio();
     $totalWithFilter = is_null($filtros) ? $total : $this->fm->totalRepositorio($filtros);
 
     
     return $response->withJson([
         "draw" => $draw,
         "recordsTotal" => $total,
         "recordsFiltered" => $totalWithFilter,
         "data" => $lista
     ]); 
 }

public function buscarRepo(Request $request, Response $response, $args ){
    $draw = $request->getParam("draw", null);
    $start = $request->getParam("start", null);
    $length = $request->getParam("length", null);
    //$filtros = $request->getParam("filtros", array());
    $ordenes = $request->getParam("ordenes", array());

    $fondo = $request->getParam("fondo", null);
    $seccionInput = $request->getParam("seccionInput", null);
    $subFondo = $request->getParam("subFondo", null);
    $asuntoInput = $request->getParam("asuntoInput", null);
    $apellidoNombreInput = $request->getParam("apellidoNombreInput", null);

    //$filtros = ['fondo_nombre' => $fondo];
    $filtros = array('fondo_id' => $fondo);

    //echo "fondo:".$fondo." seccionInput:".$seccionInput." subFondo:".$subFondo." asuntoInput:".$asuntoInput." apellidoNombreInput:".$apellidoNombreInput;

    //$lista = $this->fm->listarRepoFiltrado($fondo, $seccionInput, $subFondo, $asuntoInput, $apellidoNombreInput);
    $lista = $this->fm->listarRepositorio($start, $length, $filtros, $ordenes);
    $total = $this->fm->totalRepositorio();
    $totalWithFilter = is_null($filtros) ? $total : $this->fm->totalRepositorio($filtros);

    return $response->withJson([
        "draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $totalWithFilter,
        "data" => $lista
    ]); 
}

/*public function buscarRepoFiltro(){
    echo "llegó";
}*/

/*public function acDescripcion(Request $request, Response $response, $args ){
    $fondo_id = $request->getParam('fondo_id', null);
    $filtros = $request->getParam('filtros', []);
    $lista = $this->fm->acDescripcion("key", $fondo_id, "fondo_nombre", "fondo_nombre", $filtros, true);
    return $response->withJson($lista);
}*/


public function vistaArbol(Request $request, Response $response, $args) {
   
    $fnArbol = $this->fm->fnArbol('descendente', '1');
    
    $data = array(
        'solapaNombre' => 'ORGANIGRAMA',
        'arbol' => $fnArbol,
    );
    $this->render($response, 'fondos/arbol', $data);
}

/*
public function createPdf() 
{
    $document = \n($path."/".$fileName.tiff);
    $data = $document->getImageBlob();
    $document->setImageFormat("pdf");
    $document->writeImages($path."/".$fileName.pdf, true);
}*/

  /*  public function mostrarProbandoOCR(Request $request, Response $response, $args) {

        // $ruta_tiff = '{{baseUrl}}/images/unidadespenitenciarias0050.tif';
        $ruta_tiff = BASE_URL.'/images/unidadespenitenciarias0050.tif';
        $tipo_mime = 'application/pdf';
        $contenido = base64_encode(file_get_contents($ruta_tiff));
        // $archivo = base64_encode(file_get_contents(ROOT_DIR.'/log/upload/'.$archivoId.'.pdf'));
        // $response->write($contenido);
        // $response->withHeader('Content-Type', $tipo_mime);

        $this->render($response,'fondos/probando_ocr', [
            'urlIframe' => $contenido,
            'tipo_mime' => $tipo_mime,
            'solapaNombre' => 'PRUEBA',
            'appBackTo' =>'/fondos-botonera',
        ]);
    }*/

/*public function mostrarProbandoOCR(Request $request, Response $response, $args) {

    

    $ruta_tiff = PUBLIC_DIR.'/images/unidadespenitenciarias0050.tif';
    $tipo_mime = 'image/tiff';

    // Convert TIFF to PDF
    $imagick = new Imagick();
    $imagick->readImage($ruta_tiff);
    $pdf_content = $imagick->getImageBlob();
    $contenido = base64_encode($pdf_content);

    $this->render($response,'fondos/probando_ocr', [
        'contenido' => $contenido,
        'tipo_mime' => $tipo_mime,
        'solapaNombre' => 'PRUEBA',
        'appBackTo' =>'/fondos-botonera',
    ]);
} */

private function convertirTIFFaPNG($ruta_tiff) {
    // Cargar imagen TIFF utilizando la biblioteca GD
    $imagen_tiff = imagecreatefromtiff($ruta_tiff);

    // Crear una imagen PNG vacía con las mismas dimensiones que la imagen TIFF
    $ancho = imagesx($imagen_tiff);
    $alto = imagesy($imagen_tiff);
    $imagen_png = imagecreatetruecolor($ancho, $alto);

    // Copiar los datos de la imagen TIFF a la imagen PNG
    imagecopy($imagen_png, $imagen_tiff, 0, 0, 0, 0, $ancho, $alto);

    // Guardar la imagen PNG en un archivo temporal
    $archivo_temporal = tempnam(sys_get_temp_dir(), 'tiff_to_png');
    imagepng($imagen_png, $archivo_temporal);

    // Leer el contenido del archivo temporal
    $contenido_png = file_get_contents($archivo_temporal);

    // Eliminar el archivo temporal
    unlink($archivo_temporal);

    return $contenido_png;
}

public function generarOCR($archivo,$nombreNvo, $pathNvaCarpeta){
    $path = PUBLIC_DIR.'/images';
    $fileName = $archivo;
    $imagePath = $path.'/'.$fileName;

    $tifNuevoNombre = $nombreNvo.'.tif';
    // Copiar el archivo y renombrarlo
    /*if (copy($imagePath, $pathNvaCarpeta.'/'.$tifNuevoNombre)) {
        // echo 'Archivo copiado y renombrado correctamente.<br>';
    } else {
        // echo 'Error al copiar el archivo.<br>';
    } */

    // Guardar el comando en una variable
    $imagePath .= ".tif";
    $comando = "tesseract " . $imagePath . " stdout -l spa -c debug_file=/dev/null --oem 1 --psm 4 -c preserve_interword_spaces=1 ";

    // Retornar el comando
    return $comando;

    // Ejecutar el comando
    //exec($comando, $output, $returnCode);

    // guardar el resultado del ocr en una variable
    //$txt = implode("\n", $output);

    /*if ($returnCode === 0) {
        // El comando se ejecutó correctamente
        // echo "Se ejecutó correcamente el comando: " . $comando;
    } else {
        // Hubo un error en la ejecución del comando
        // echo "Error al ejecutar el comando: " . $comando;
    }

    return $txt;*/

}

public function deTifaJPG(){
    $path = PUBLIC_DIR.'/images';
    $imagePath = $path.'/unidades penitenciarias0001.tif';

    // Ruta del archivo JPEG de salida
    $outputFile =  DEVLOG_DIR.'/upload/unidades penitenciarias0001.jpg';

    // Crear una instancia de Imagick
    $imagick = new Imagick($imagePath);

    // Convertir la imagen TIFF a formato JPEG
    $imagick->setImageFormat('jpeg');

    // Ajustar la calidad del JPEG (opcional)
    $imagick->setImageCompressionQuality(80); // Cambia el valor según tus preferencias

    // Guardar la imagen convertida en formato JPEG
    $imagick->writeImage($outputFile);

    // Liberar recursos
    $imagick->destroy();

    echo "Conversión completada.";
    // return $outputFile;
    return json_encode(['imagenURL' => $outputFile]);
}

public function getPaginasOCR(Request $request, Response $response, $args) {
    //$recurso = $request->getAttribute('recurso_fondo');
    $repositorioID = $request->getArgument("repositorioID");
    //$URL_DE_LA_IMAGEN = PUBLIC_DIR.'/paginas_jpg/jpg9_pagina9.jpg';
    //$URL_DE_LA_IMAGEN = PUBLIC_DIR.'/images/load.gif';
    //$URL_DE_LA_IMAGEN = ROOT_DIR.'/log/upload/83.jpg';

    $URL_DE_LA_IMAGEN = ROOT_DIR.'/public/paginas_jpg';

    $this->render($response, 'fondos/paginasOCR', [
        'repositorioID' => $repositorioID,
        'URL_DE_LA_IMAGEN' => $URL_DE_LA_IMAGEN,
        'listaJPG' => $this->fm->listaJPG($repositorioID),
        //'columnas' =>$this->fm->listarPaginasOCR(),
        'modalTitle' => 'OCR DEL TIFF DIVIDIDO EN PÁGINAS',
        "modal" => [
            "class" => "modal--rounded modal-max-width-500",
        ],
        //"fondo" => $recurso,
        //"columnas" =>$this->fm->listarColumnas(null,null,[],[['key'=>'asc']]),
    ]);
}

public function mostrarJPG(Request $request, Response $response, $args ){

    $image_url = $request->getAttribute("image_url");
    echo $image_url;
    $image_nombre = $request->getAttribute("image_nombre");
    echo $image_nombre;

    $image_url = $request->getParam('image_url');
    $image_nombre = $request->getParam('image_nombre');

      $archivo = base64_encode(file_get_contents($image_url));
    //debug($archivo);

    $this->render($response,'fondos/mostrarJPG', [
        'urlIframe' => $archivo,
        'image_url' => $image_url,
        'image_nombre' => $image_nombre,
        //'documento' => $documento,
        //'id_solicitud' => $id_solicitud,
       // 'contenido' => $contenido,
    ]);
}

public function mostrarGaleria(Request $request, Response $response, $args) {

    $repositorioID = $request->getParam('repositorioID');
    $pagina = $request->getParam('pagina');
    //echo $pagina;
    //buscar el idExcel del repositorio
    $id_excel = $this->fm->dameIdExcel($repositorioID);

    //si hacemos ese cambio aca quedaria
    //$URL_DE_LA_IMAGEN = PUBLIC_DIR.'/paginas_jpg/'.$id_excel.'_pagina1.jpg';

    if (ENTORNO == 'desarrollo'){
        $URL_DE_LA_IMAGEN = PUBLIC_DIR.'/paginas_jpg/'.$repositorioID.'_pagina'.$pagina.'.jpg';
    }else{
        $URL_DE_LA_IMAGEN = PUBLIC_DIR.'/paginas/paginas_jpg/'.$repositorioID.'_pagina'.$pagina.'.jpg';
    }

    $archivo = base64_encode(file_get_contents($URL_DE_LA_IMAGEN));
    $cantPaginas = $this->fm->cantPaginas($repositorioID);
    $listaJPG = $this->fm->listaJPG($repositorioID);
    $agregarZoomListeners = true;
    $imageId = "image".$pagina;
    //echo "listaJPG:".$listaJPG;

    $this->render($response, 'fondos/galeria', [
        'repositorioID' => $repositorioID,
        'listaJPG' => $listaJPG,
        'URL_DE_LA_IMAGEN' => $URL_DE_LA_IMAGEN,
        'archivo' => $archivo,
        'pagina'=> $pagina,
        "imageId" => $imageId,
        'cantPaginas' => $cantPaginas,
        'modalTitle' => 'IMAGEN POR PÁGINAS',
        'agregarZoomListeners' => $agregarZoomListeners,
        "modal" => [
            //"class" => "modal--rounded modal-max-width-300",
            //"class" => "modal--rounded modal--full-width",
            "class" => "modal--rounded modal--full-screen", // Clase para modal de pantalla completa
        ],
    ]);
}

public function cargarImagen(Request $request, Response $response, $args) {
    $image_url = $request->getParam('image_url');
    $image_nombre = $request->getParam('image_nombre');
    $archivo = base64_encode(file_get_contents($image_url));

    $this->render($response,'fondos/galeria', [
        'urlIframe' => $archivo,
        'image_url' => $image_url,
        'image_nombre' => $image_nombre,
    ]);
}


public function cargarRepositorioCompleto($idExcel){
    try {
        $this->fm->beginTransaction();
        $usuario = $this->getUsuario()->getIdCuenta();

        $this->fm->insertarRepositorioCompleto($idExcel); 
        $this->fm->marcarPasadosEnPlana($idExcel);
        $this->fm->commit();

    } catch (Exception $e) { 
        if ($this->fm->inTransaction()) $this->fm->rollBack();
        throw $e;
    }
}

public function detalleRepositorio(Request $request, Response $response, $args){
    $repositorioID = $args['repositorioID'];
    $filtros = array('repositorio_id' =>$repositorioID);

    $lista = $this->fm->listarRepositorio(null, null, $filtros, []);

    $this->render($response, 'fondos/repositorio_detalle',[
        'solapaNombre' => 'EDITANDO REPOSITORIO',
        'appBackTo' => '/administracion-botonera',
        'registroRepo' => $lista[0],
    ]);
}

public function repositorioEditar(Request $request, Response $response, $args){
    $usuario = $this->getUsuario()->getIdCuenta();
    $this->fm->repositorioEditar();
}



public function cargarDatosEnBase(Request $request, Response $response, $args){
    $idExcel = $request->getArgument("idExcelValue");
    $this->cargarRepositorioCompleto($idExcel);
    //$this->insertOcrEnRepositorio($idExcel);
    $path = PUBLIC_DIR;

    //traer repositorios
    $repositorios = $this->fm->traerRepo($idExcel);
    //var_dump($repositorios);

    for ($i=0; $i < count($repositorios); $i++) {
        $repo_id = $repositorios[$i]['repositorio_id'];
        $imagePath = 'unidadesTODAS/'.$repositorios[$i]['archivo'];
       // shell_exec("$path/script.php");
        //$command = "docker exec -it desa-gpa bash -c 'cd public/ && php script.php'";
        $command = "docker exec -it desa-gpa bash -c 'cd public/ && php script.php'";
        
        $output = exec($command);
      //  var_dump($output);
       // exec("$path/script.php $repo_id $imagePath", $output, $return_var);
    }
       

 /*
    foreach ($repositorios as $repo=>$i) {
        $repo_id = $i['repositorio_id'];
        $imagePath = 'unidadesTODAS/'.$i['archivo'];
       // debug($repo_id, $imagePath);
        $prueba = shell_exec("$path/script.php $repo_id $imagePath");
       var_dump($prueba);
    }*/

    return $idExcel;
}

/*public function ocrIndividual(Request $request, Response $response, $args){
    //debug('llego');
    $idExcel = $request->getArgument("idExcelNuevo");
    //$i = $request->getArgument("i");
    $i = $request->getParam("i");
    $repo = $this->traerRepoSinOCR($idExcel);

    date_default_timezone_set('America/Argentina/Buenos_Aires'); // Establecer la zona horaria GMT-3
    $nombreNvo = date('Y-m-d H:i:s');
    $nombreNvo = str_replace(' ', '_', $nombreNvo);
    $outputPath = DEVLOG_DIR.'/upload/';
    $pathNvaCarpeta = $outputPath.$nombreNvo;

    if(mkdir($pathNvaCarpeta, 0777, true)){
        // echo "Carpeta creada exitosamente en: ".$pathNvaCarpeta . "<br>";
    } else {
        // echo "NO se pudo crear la carpeta en: ".$pathNvaCarpeta . "<br>";
    }


    try {
        $this->fm->beginTransaction();
        $ocr = $this->generarOCR($repo['archivo'], $nombreNvo.'_'.$i, $pathNvaCarpeta);
        $this->fm->insertOcrEnRepositorio($ocr, $repo["repositorio_id"]);

        $this->fm->commit();
    } catch (Exception $e) {
        if ($this->fm->inTransaction()) $this->fm->rollBack();
        throw $e;
    }
}*/

public function mostrarProgreso(Request $request, Response $response, $args)
{
    $idExcel = $request->getArgument("idExcelValue");
    $this->render($response, 'fondos/mostrar_progreso', [
        'idExcel' => $idExcel,
        'modalTitle' => 'AGUARDE UNOS INSTANTES...',
        "modal" => [
            "class" => "modal--rounded modal-max-width-500",
        ],
    ]);
}

public function cantidadParaOcerrear(Request $request, Response $response, $args){
    $idExcel = $request->getArgument("idExcel");
    $respuesta = $this->fm->cantidadParaOcerrear($idExcel);
    $array = [$respuesta['fn_cantidadparaocerrear'], $idExcel];
    return $response->withJson($array);
    //return json_encode($array);
}

/* *** ** ** * * para filtros  *** ** *** */

public function acFondo(Request $request, Response $response, $args ){
    $descripcion = $request->getParam('descripcion', null);
    $filtros = $request->getParam('filtros', []);
    $lista = $this->fm->acRepositorio("key", $descripcion, "fondo_id", "fondo_nombre", $filtros, true);
    return $response->withJson($lista);  
}

public function acApellido(Request $request, Response $response, $args ){
    $descripcion = $request->getParam('descripcion', null);
    $filtros = $request->getParam('filtros', []);
    $lista = $this->fm->acApellido("apellido", $descripcion, "apellido", "apellido", $filtros, true);
    return $response->withJson($lista);
}

public function acAsunto(Request $request, Response $response, $args ){
    $descripcion = $request->getParam('descripcion', null);
    $filtros = $request->getParam('filtros', []);
    $lista = $this->fm->acAsunto("asunto", $descripcion, "asunto", "asunto", $filtros, true);
    return $response->withJson($lista);
}

public function acnodoArbol(Request $request, Response $response, $args ){
    $descripcion = $request->getParam('descripcion', null);
    $filtros = $request->getParam('filtros', []);
    $lista = $this->fm->acNodo("nodo", $descripcion, "nodo_arbol_id", "nodo_detalle", $filtros, true);
    return $response->withJson($lista);
}



}
