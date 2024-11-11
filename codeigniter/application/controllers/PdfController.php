<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PdfController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // Carga cualquier biblioteca o modelo necesario
    }

    public function index() {
        // Crea una instancia de Dompdf
        $dompdf = new \Dompdf\Dompdf();

        // Lógica para generar el PDF
        $dompdf->loadHtml('<h1>Hola, este es un PDF generado con Dompdf en CodeIgniter 3</h1>');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Salida del PDF al navegador
        $dompdf->stream("mi_archivo.pdf", array("Attachment" => 0));
    }
}

