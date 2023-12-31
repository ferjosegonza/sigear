<?php
//@author bgonzalez
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class excelGen{
    //BODY
    private $bodyFontFamily = "Calibri";
    private $bodyFontBold = false;
    private $bodyFontSize = '10';
    private $bodyFontColor = '#FFFFFF';
    private $bodyBackgroundColor = '#f2f2f2';
    private $bodyAlineamientoHorizontal = 'Center';
    private $bodyAlineamientoVertical = 'Center';
    private $bodyHeight = "30";
    //HEADER
    private $headerFontFamily = "Calibri";
    private $headerFontBold = true;
    private $headerFontSize = '10';
    private $headerFontColor = '#FFFFFF';
    private $headerBackgroundColor = '#6babe5';
    private $headerAlineamientoVertical ='Center';
    private $headerAlineamientoHorizontal ='Center';
    private $headerHeight = "30";
    //BORDER
    private $border = true;
    private $borderColor = '#FFFFFF';
    private $borderWidth = "1";
    //GENERAL
    private $header = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
    private $footer = "</Workbook>";
    private $content = "";
    private $styles = "";
    private $encoding = "utf-8";
    private $convertTypes = true;
    private $worksheetTitle = "Tabla";

    
    function __construct() {
        $this->generarEstilos();
    }
    
    private function generarEstilos(){
        //HEADER
        $estilosXml = "<Styles>
                        <Style ss:ID='sHeader'>
                            <Font ss:FontName='".$this->getHeaderFontFamily()."' x:Family='Swiss' ss:Bold='".$this->getHeaderFontBold()."'  ss:Size='".$this->getHeaderFontSize()."' ss:Color='".$this->getHeaderFontColor()."'/>
                            <Interior ss:Color='".$this->getHeaderBackgroundColor()."' ss:Pattern='Solid'/>";

        if($this->getBorder()){
            $estilosXml .= "<Borders>
                            <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                            <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                            <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                            <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                        </Borders>";
        }
        //BODY
        $estilosXml .= "</Style>
                        <Style ss:ID='sBody'>
                            <Font ss:FontName='".$this->getBodyFontFamily()."' x:Family='Swiss' ss:Bold='".$this->getBodyFontBold()."'  ss:Size='".$this->getBodyFontSize()."'/>
                            <Interior ss:Color='".$this->getBodyBackgroundColor()."' ss:Pattern='Solid'/>";

        if($this->getBorder()){
            $estilosXml .= " <Borders>
                                <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                                <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='".$this->getBorderWidth()."' ss:Color='".$this->getBorderColor()."'/>
                            </Borders> ";
        }
        //ALIGNS
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
                        </Style>
                        <Style ss:ID='sColumnCenter'  >
                            <Alignment ss:Vertical='".$this->getBodyAlineamientoVertical()."' ss:Horizontal='Center' ss:WrapText='1'/>
                        </Style>
                        <Style ss:ID='sColumnRight'  >
                            <Alignment ss:Vertical='".$this->getBodyAlineamientoVertical()."' ss:Horizontal='Right' ss:WrapText='1'/>
                        </Style>
                        <Style ss:ID='WrapText'>
                            <Alignment ss:Vertical='Center' ss:WrapText='1'/>
                        </Style>
                    </Styles>";

        $this->setStyles($estilosXml);
    }

    public function agregarFila($array) {        
        $this->content .= "<Row ss:Height='".$this->getBodyHeight()."' >\n";
        foreach ($array as $fila){
            $type = 'String';
            if ($this->convertTypes === true && is_numeric($fila)) $type = 'Number';
            $fila = htmlentities($fila, ENT_COMPAT, $this->encoding);
            $this->content .= "<Cell ss:StyleID='sBody'><Data ss:Type=\"$type\">" . ($fila) . "</Data></Cell>\n";
        }
        $this->content .= "</Row>\n";
    }

    public function agregarEncabezado($array) {
        $this->content .= "<Row ss:AutoFitHeight='1' ss:Height='".$this->getHeaderHeight()."' >\n";
        
        foreach($array as $cabecera){
            $this->content .= "<Column ss:AutoFitWidth='1' ss:Width='". $columna ."' ss:StyleID='".$this->getAlineacionColumnHorizontal($this->getHeaderAlineamientoHorizontal())."'  />\n";
        }

        foreach($array as $cabecera){
            $this->content .= "<Cell ss:StyleID='".$this->getAlineacionHorizontal($this->getHeaderAlineamientoHorizontal())."'><Data ss:Type='String'>".$cabecera."</Data></Cell>";
        }  

        $this->content .= "</Row>";
    }

    public function generar($filename) {
        $filename = preg_replace('/[^aA-zZ0-9\_\-]/', '', $filename);
        
        header("Content-Type: application/vnd.ms-excel; charset=" . $this->getEncoding());
        header("Content-Disposition: attachment; filename=\"" . $filename ."-".date("Y-m-d--H-i-s"). ".xls\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        
        // need to use stripslashes for the damn ">"                
        echo stripslashes(sprintf($this->getHeader(), $this->getEncoding()));
        echo $this->getStyles();
        echo "\n<Worksheet ss:Name=\"" . $this->getWorksheetTitle() . "\">\n<Table>\n" ;
        echo $this->getContent();
        echo "\n</Table>\n</Worksheet>\n";
        echo $this->getFooter();
    }

    function getContent(){
        return $this->content;
    }
    function setStyles($styles){
        $this->styles = $styles;
    }
    function getStyles(){
        return $this->styles;
    }
    function setEncoding($encoding){
        $this->encoding = $encoding;
    }
    function getEncoding(){
        return $this->encoding;
    }
    function setHeader($header){
        $this->header = $header;
    }
    function getHeader(){
        return $this->header;
    }
    function setFooter($footer){
        $this->footer = $footer;
    }
    function getFooter(){
        return $this->footer;
    }
    function setWorksheetTitle($title) {
        $title = preg_replace("/[\\\|:|\/|\?|\*|\[|\]]/", "", $title);
        $title = substr($title, 0, 31);
        $this->worksheetTitle = $title;
    }
    function getWorksheetTitle() {
        return $this->worksheetTitle;
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
     * @param type valores permitidos 'Center', 'Right', 'Left', 'Bottom'
     */
    function setHeaderAlineamientoVertical($headerAlineamientoVertical) {
        $this->headerAlineamientoVertical = $headerAlineamientoVertical;
    }
    function setBodyAlineamientoVertical($bodyAlineamientoVertical) {
        $this->bodyAlineamientoVertical = $bodyAlineamientoVertical;
    }
    function setHeaderAlineamientoHorizontal($headerAlineamientoHorizontal) {
        $this->headerAlineamientoHorizontal = $headerAlineamientoHorizontal;
    }
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
            default: 
                    $return = "sLeft";
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
            default: 
                    $return = "sColumnLeft";
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
    function getHeaderHeight() {
        return $this->headerHeight;
    }
    function getBodyHeight() {
        return $this->bodyHeight;
    }
    function setHeaderHeight($headerHeight) {
        $this->headerHeight = $headerHeight;
    }
    function setBodyHeight($bodyHeight) {
        $this->bodyHeight = $bodyHeight;
    }
}
