<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    require_once APPPATH."/third_party/fpdf/fpdf.php";
    class Pdf extends FPDF {		
		
        public function Header(){
            //si se requiere agregar una imagen
            //$this->Image('ruta de la imagen',x,y,ancho,alto);
            $this->SetFont('Arial','B',10);
            $this->Cell(30);
            $this->Cell(120,10,'TITULO CABECERA',0,0,'C');
            $this->Ln('5');
       }

	   public function Footer(){
           $this->SetY(-15);
           $this->SetFont('Arial','I',7);
           $this->Cell(0,10,'Pag. '.$this->PageNo().'/{nb}',0,0,'R');
      }
}
?>