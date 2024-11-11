<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$url = $base_url . '/solicitudes/verificar_expiraciones';
ejecutar_tarea($url);


class Cron extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Publicacion_model');
        $this->load->model('Notificacion_model');
        $this->load->library('session');
    }

    private function _verificar_acceso() {
        // En desarrollo local, permitir acceso desde localhost
        if ($this->input->ip_address() === '127.0.0.1' || 
            $this->input->ip_address() === '::1') {
            return true;
        }
        
        // Verificar token
        $token = $this->input->get('token');
        if ($token !== 'tu_token_seguro') {
            log_message('error', 'Acceso no autorizado a cron - IP: ' . $this->input->ip_address());
            show_error('No autorizado', 403);
            return false;
        }
        return true;
    }

    public function verificar_reservas() {
        if (!$this->_verificar_acceso()) {
            return;
        }

        log_message('info', 'Iniciando verificación de reservas expiradas - ' . date('Y-m-d H:i:s'));
        
        $resultado = $this->Publicacion_model->verificar_y_actualizar_reservas_expiradas();
        
        if ($this->input->is_cli_request()) {
            echo date('Y-m-d H:i:s') . " - ";
            echo $resultado ? "Verificación de reservas completada exitosamente\n" : "Error en la verificación de reservas\n";
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => $resultado,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'message' => $resultado ? 'Verificación completada' : 'Error en la verificación'
                ]));
        }
        
        log_message('info', 'Finalización de verificación de reservas - Resultado: ' . ($resultado ? 'exitoso' : 'fallido'));
    }

    public function ejecutar_tareas_programadas() {
        if (!$this->_verificar_acceso()) {
            return;
        }

        log_message('info', 'Iniciando tareas programadas - ' . date('Y-m-d H:i:s'));

        // Ejecutar todas las tareas programadas
        $resultados = [];
        
        // Verificar reservas expiradas
        $resultados['reservas'] = $this->Publicacion_model->verificar_y_actualizar_reservas_expiradas();
        
        // Aquí puedes agregar más tareas programadas
        
        if ($this->input->is_cli_request()) {
            foreach ($resultados as $tarea => $resultado) {
                echo date('Y-m-d H:i:s') . " - Tarea '$tarea': " . 
                     ($resultado ? "Completada\n" : "Error\n");
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'timestamp' => date('Y-m-d H:i:s'),
                    'resultados' => $resultados
                ]));
        }
        
        log_message('info', 'Finalización de tareas programadas - ' . json_encode($resultados));
    }
}