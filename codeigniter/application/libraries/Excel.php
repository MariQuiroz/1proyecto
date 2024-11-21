<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel {
    protected $ci;

    public function __construct() {
        $this->ci =& get_instance();
    }

    public function export_to_excel($data, $filename = 'export') {
        // Establecer headers para descarga
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename.xls\"");
        header("Cache-Control: max-age=0");
        
        // Inicio del archivo Excel
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\">\n";
        echo "<Worksheet ss:Name=\"Sheet1\">\n";
        echo "<Table>\n";

        // Si hay datos, obtener los encabezados del primer registro
        if (!empty($data)) {
            echo "<Row>\n";
            foreach (array_keys((array)$data[0]) as $header) {
                echo "<Cell><Data ss:Type=\"String\">" . htmlspecialchars(ucfirst($header)) . "</Data></Cell>\n";
            }
            echo "</Row>\n";
        }

        // Agregar los datos
        foreach ($data as $row) {
            echo "<Row>\n";
            foreach ((array)$row as $cell) {
                $type = is_numeric($cell) ? "Number" : "String";
                echo "<Cell><Data ss:Type=\"$type\">" . htmlspecialchars($cell) . "</Data></Cell>\n";
            }
            echo "</Row>\n";
        }

        // Cerrar el archivo Excel
        echo "</Table>\n";
        echo "</Worksheet>\n";
        echo "</Workbook>";
        exit;
    }

}