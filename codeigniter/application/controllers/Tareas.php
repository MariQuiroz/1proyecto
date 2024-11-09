<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tareas extends CI_Controller {
    public function verificar_solicitudes_expiradas() {
        $this->load->model('Solicitud_model');
        $this->load->model('Notificacion_model');
        
        // Verificar solicitudes expiradas
        $solicitudes_expiradas = $this->Solicitud_model->verificar_solicitudes_expiradas();
        
        // Notificar sobre las solicitudes expiradas
        foreach ($solicitudes_expiradas as $solicitud) {
            $this->Notificacion_model->crear_notificacion(
                $solicitud->idUsuario,
                $solicitud->idPublicacion,
                NOTIFICACION_SOLICITUD_PRESTAMO,
                "Tu solicitud para '{$solicitud->titulo}' ha expirado. La publicación está nuevamente disponible."
            );
        }
        
        echo "Solicitudes procesadas: " . count($solicitudes_expiradas);
    }
}