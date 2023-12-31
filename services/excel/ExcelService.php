<?php

use Slim\Http\Response;

require VENDOR_DIR . '/phpexcel/PHPExcel.php'; 
require VENDOR_DIR . '/phpexcel/PHPExcel/IOFactory.php';

class ExcelService extends Service {

    public function __construct($container) {
        parent::__construct($container);
    }

    public function generarExcel($titulo, $campos, $lista) {
        $letras = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $excel = new PHPExcel();
        $sheet = $excel->setActiveSheetIndex(0);
        $sheet->setTitle("Hoja1");
        
        // TITULO DOCUMENTO
        $sheet->getCell("A1")->setValue($titulo);
        $sheet->mergeCells('A1:'.$letras[count($campos)-1]."1");
        
        $sheet->getCell("A1")->getStyle()->applyFromArray([
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'e5e5e5'),
            ],
        ]);
        
        $sheet->getCell("A1")->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getCell("A1")->getStyle()->getFont()->setBold(true);
        $sheet->getCell("A1")->getStyle()->getFont()->setSize(12);  

        // HEADERS
        $i=0;
        foreach ($campos as $campo => $etiqueta) {
            $sheet->getCell($letras[$i]."2")->setValue($etiqueta);
            $i++;
        }
        
        $sheet->getStyle("A2:".$letras[count($campos)-1]."2")->applyFromArray(['font'=>['bold'=>true]]);
        
        // BODY
        foreach ($lista as $i=>$registro) {
            $j=0;
            foreach ($campos as $campo=>$etiqueta) {
                $valor = $registro[$campo];
                $sheet->getCell($letras[$j].($i+3))->setValue($valor);
                $j++; 
            }
        }

        $sheet->setSelectedCells('A1');
        $excel->setActiveSheetIndex(0);

        foreach(range('A', $letras[count($campos)-1]) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $excelWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $dirName = RECURSOS_DIR . "/excel/ ";
        $dir = dirname($dirName);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $tempFile = $dir . '/' . $titulo . '.xlsx';
        $excelWriter->save($tempFile);
        
        $response = new Response();
        $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->withHeader('Content-Disposition', 'attachment; filename="reporte-visitas.xlsx"');
        $stream = fopen($tempFile, 'r+');
        return $response->withBody(new \Slim\Http\Stream($stream));
    }

}