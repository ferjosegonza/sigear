<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of excelMapp
 *
 * @author fgallo
 */
require_once LIBS_DIR  . '/Excel/php-excel.class.php';    

class excelMapp extends Excel_XML {
    
    private $autowrap = true;
    private $headAutoFitHeight = true;
    private $bodyFontFamily = "Calibri";
    private $headerFontFamily = "Calibri";
    private $bodyFontBold = false;
    private $headerFontBold = true;
    private $bodyFontSize = '10';
    private $headerFontSize = '10';
    private $headerFontColor = '#FFFFFF';
    private $bodyFontColor = '#FFFFFF';
    private $headerBackgroundColor = '#6babe5';
    private $bodyBackgroundColor = '#f2f2f2';
    private $headerAlineamientoVertical ='Center';
    private $bodyAlineamientoVertical = 'Center';
    private $headerAlineamientoHorizontal ='Center';
    private $bodyAlineamientoHorizontal = 'Center';
    private $border =true;
    private $borderColor = '#FFFFFF';
    private $borderWidth = "1";
    private $rowHeadHeight = "30";
    private $bodyAutoFitHeight = true;
    private $rowBodyHeight = "30";
    private $nombreHoja = "Table1";
    
    
    function __construct() {
        parent::__construct();
    }
    public function generarXML($filename = 'excel-export',$contenido,$encabezados,$anchos=null,$alineamientoHorizontal=null) {
        
        /*foreach ($contenido as $key => $value) {
            foreach ($value as $j => $v) {
                $contenidoArr[$k+2][] = $v;
            }
            
        }*/
        //array_unshift($contenido, array(), array());
        foreach ($alineamientoHorizontal as $key => $value) {
            $arrayAlineamiento[$key] = $this->getAlineacionColumnHorizontal($value); 
        }
        $this->addArray($contenido,$arrayAlineamiento,$this->getBodyAutoFitHeight(),$this->getRowBodyHeight());

        $estilosXml = "<Styles>
                            <Style ss:ID='sHeader'>
                                   <Font ss:FontName='".$this->getHeaderFontFamily()."' x:Family='Swiss' ss:Bold='".$this->getHeaderFontBold()."'  ss:Size='".$this->getHeaderFontSize()."' ss:Color='".$this->getHeaderFontColor()."'/>
                                   <Interior ss:Color='".$this->getHeaderBackgroundColor()."' ss:Pattern='Solid'/> 
                                   ";
        if($this->getBorder())
            {
            $estilosXml .= "<Borders>
                                      <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                      <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                      <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                      <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                   </Borders>";
            }
            $estilosXml .= "</Style>";
            $estilosXml .= "<Style ss:ID='sBody'>";
            $estiloBody = "<Font ss:FontName='".$this->getBodyFontFamily()."' x:Family='Swiss' ss:Bold='".$this->getBodyFontBold()."'  ss:Size='".$this->getBodyFontSize()."'/>";                           
            $estiloBody .= "<Interior ss:Color='".$this->getBodyBackgroundColor()."' ss:Pattern='Solid'/> ";
             if($this->getBorder())
                {
                $estiloBody .=" <Borders>
                                      <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                      <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                      <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                      <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                   </Borders> ";
                }
            $estilosXml .= $estiloBody;
            $estilosXml .= "</Style> 
                            <Style ss:ID='sLeft' ss:Parent='sHeader'>
                                <Alignment ss:Vertical='".$this->getHeaderAlineamientoVertical()."' ss:Horizontal='Left' ss:WrapText='1'/>
                            </Style>
                            <Style ss:ID='sCenter' ss:Parent='sHeader'>
                                <Alignment ss:Vertical='".$this->getHeaderAlineamientoVertical()."' ss:Horizontal='Center' ss:WrapText='1'/>
                            </Style>
                            <Style ss:ID='sRight' ss:Parent='sHeader'>
                                <Alignment ss:Vertical='".$this->getHeaderAlineamientoVertical()."' ss:Horizontal='Right' ss:WrapText='1'/>
                            </Style>
                            <Style ss:ID='sColumnLeft'  >
                                <Alignment ss:Vertical='".$this->getBodyAlineamientoVertical()."' ss:Horizontal='Left' ss:WrapText='1'/>
                                    $estiloBody
                            </Style>
                            <Style ss:ID='sColumnCenter'  >
                                <Alignment ss:Vertical='".$this->getBodyAlineamientoVertical()."' ss:Horizontal='Center' ss:WrapText='1'/>
                                    $estiloBody
                            </Style>
                            <Style ss:ID='sColumnRight'  >
                                <Alignment ss:Vertical='".$this->getBodyAlineamientoVertical()."' ss:Horizontal='Right' ss:WrapText='1'/>
                                    $estiloBody
                            </Style>    
                            <Style ss:ID='WrapText'>
                                <Alignment ss:Vertical='Center' ss:WrapText='1'/>
                            </Style>
                        </Styles>";
        
        /*if ($estilos != '') {
            foreach ($estilos as $key => $s) {
                echo "<Column ss:AutoFitWidth='1' ss:Width='" . $s . "' ss:Position='Left' />\n";
            }
        }*/
            $celdas = "";
            $header = "";
                    
            $columnas = "";
            foreach($encabezados as $k => $cabecera)
                {
                    $columnas .= "<Column ss:AutoFitWidth='1' ss:Width='" . $anchos[$k] . "' ss:StyleID='".$this->getAlineacionColumnHorizontal($alineamientoHorizontal[$k])."'  />\n";
                    $celdas .= "<Cell ss:StyleID='".$this->getAlineacionHorizontal($alineamientoHorizontal[$k])."'><Data ss:Type='String'>".$cabecera."</Data></Cell>";
                }
        $header .= $columnas;         
        $header .= "<Row ss:AutoFitHeight='".$this->getHeadAutoFitHeight()."' ss:Height='".$this->getRowHeadHeight()."' >";        
        $header .= $celdas;                
        $header .= "</Row>";
        
        parent::generateXML($filename, $estilosXml, $header);
    }
    
    function getAutowrap() {
        return $this->autowrap ?'1':'0';
    }

    function setAutowrap($autowrap) {
        $this->autowrap = $autowrap;
    }

    function getAutoFitHeight() {
        return $this->autoFitHeight?'1':'0';
    }

    

    function setAutoFitHeight($autoFitHeight) {
        $this->autoFitHeight = $autoFitHeight;
    }

    function getBodyFontFamily() {
        return $this->bodyFontFamily;
    }

    function getHeaderFontFamily() {
        return $this->headerFontFamily;
    }

    function setBodyFontFamily($bodyFontFamily) {
        $this->bodyFontFamily = $bodyFontFamily;
    }

    function setHeaderFontFamily($headerFontFamily) {
        $this->headerFontFamily = $headerFontFamily;
    }
    function getBodyFontBold() {
        return $this->bodyFontBold?'1':'0';
    }

    function getHeaderFontBold() {
        
        return $this->headerFontBold?'1':'0';
    }

    function setBodyFontBold($bodyFontBold) {
        $this->bodyFontBold = $bodyFontBold;
    }

    function setHeaderFontBold($headerFontBold) {
        $this->headerFontBold = $headerFontBold;
    }

    function getBodyFontSize() {
        return $this->bodyFontSize;
    }

    function getHeaderFontSize() {
        return $this->headerFontSize;
    }

    function setBodyFontSize($bodyFontSize) {
        $this->bodyFontSize = $bodyFontSize;
    }

    function setHeaderFontSize($headerFontSize) {
        $this->headerFontSize = $headerFontSize;
    }

    function getHeaderFontColor() {
        return $this->headerFontColor;
    }

    function getBodyFontColor() {
        return $this->bodyFontColor;
    }

    function setHeaderFontColor($headerFontColor) {
        $this->headerFontColor = $headerFontColor;
    }

    function setBodyFontColor($bodyFontColor) {
        $this->bodyFontColor = $bodyFontColor;
    }
    
    function getHeaderBackgroundColor() {
        return $this->headerBackgroundColor;
    }

    function getBodyBackgroundColor() {
        return $this->bodyBackgroundColor;
    }

    function setHeaderBackgroundColor($headerBackgroundColor) {
        $this->headerBackgroundColor = $headerBackgroundColor;
    }

    function setBodyBackgroundColor($bodyBackgroundColor) {
        $this->bodyBackgroundColor = $bodyBackgroundColor;
    }
    function getHeaderAlineamientoVertical() {
        return $this->headerAlineamientoVertical;
    }

    function getBodyAlineamientoVertical() {
        return $this->bodyAlineamientoVertical;
    }

    function getHeaderAlineamientoHorizontal() {
        return $this->headerAlineamientoHorizontal;
    }

    function getBodyAlineamientoHorizontal() {
        return $this->bodyAlineamientoHorizontal;
    }
    /**
     * Setea el alineamiento vertical de la celda de encabezado
     * @param type $headerAlineamientoVertical  valores permitidos 'Center', 'Right', 'Left', 'Bottom'
     */
     
    function setHeaderAlineamientoVertical($headerAlineamientoVertical) {
        $this->headerAlineamientoVertical = $headerAlineamientoVertical;
    }
    /**
     * Setea el alineamiento vertical de la celda de cuerpo
     * @param type $bodyAlineamientoVertical  valores permitidos 'Center', 'Right', 'Left', 'Bottom'
     */
    function setBodyAlineamientoVertical($bodyAlineamientoVertical) {
        $this->bodyAlineamientoVertical = $bodyAlineamientoVertical;
    }
    /**
     * Setea el alineamiento horizontal de la celda de encabezado
     * @param type $headerAlineamientoHorizontal  valores permitidos 'Center', 'Right', 'Left', 'Bottom'
     */
    function setHeaderAlineamientoHorizontal($headerAlineamientoHorizontal) {
        $this->headerAlineamientoHorizontal = $headerAlineamientoHorizontal;
    }
    /**
     * Setea el alineamiento horizontal de la celda de cuerpo
     * @param type $bodyAlineamientoHorizontal  valores permitidos 'Center', 'Right', 'Left', 'Bottom'
     */
    function setBodyAlineamientoHorizontal($bodyAlineamientoHorizontal) {
        $this->bodyAlineamientoHorizontal = $bodyAlineamientoHorizontal;
    }

    function getBorder() {
        return $this->border;
    }

    function setBorder($border) {
        $this->border = $border;
    }
    
    function getAlineacionHorizontal($valor){
        switch (strtolower($valor)) {
            case "left": 
                        $return = "sLeft";

                break;
            case "right": 
                        $return = "sRight";

                break;
            case "center": 
                        $return = "sCenter";

                break;
            default: $return = "sLeft";
                break;
        }
        return $return;
    }
    
    function getAlineacionColumnHorizontal($valor){
        switch (strtolower($valor)) {
            case "left": 
                        $return = "sColumnLeft";

                break;
            case "right": 
                        $return = "sColumnRight";

                break;
            case "center": 
                        $return = "sColumnCenter";

                break;
            default: $return = "sColumnLeft";
                break;
        }
        return $return;
    }
    
    function getRowHeight() {
        return $this->rowHeight;
    }

    function setRowHeight($rowHeight) {
        $this->rowHeight = $rowHeight;
    }

    function getBorderColor() {
        return $this->borderColor;
    }

    function setBorderColor($borderColor) {
        $this->borderColor = $borderColor;
    }


    function getNombreHoja() {
        return $this->nombreHoja;
    }

    function setNombreHoja($nombreHoja) {
        $this->nombreHoja = $nombreHoja;
    }

    function getBorderWidth() {
        return $this->borderWidth;
    }

    function setBorderWidth($borderWidth) {
        $this->borderWidth = $borderWidth;
    }

    
    function getHeadAutoFitHeight() {
        return $this->headAutoFitHeight?'1':'0';
    }

    function getRowHeadHeight() {
        return $this->rowHeadHeight;
    }

    function getBodyAutoFitHeight() {
        return $this->bodyAutoFitHeight?'1':'0';
    }

    function getRowBodyHeight() {
        return $this->rowBodyHeight;
    }

    function setHeadAutoFitHeight($headAutoFitHeight) {
        $this->headAutoFitHeight = $headAutoFitHeight;
    }

    function setRowHeadHeight($rowHeadHeight) {
        $this->rowHeadHeight = $rowHeadHeight;
    }

    function setBodyAutoFitHeight($bodyAutoFitHeight) {
        $this->bodyAutoFitHeight = $bodyAutoFitHeight;
    }

    function setRowBodyHeight($rowBodyHeight) {
        $this->rowBodyHeight = $rowBodyHeight;
    }



}
