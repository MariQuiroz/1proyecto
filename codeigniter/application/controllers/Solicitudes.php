<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitudes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Solicitud_model');
        $this->load->model('Prestamo_model');
        $this->load->model('Publicacion_model');
        $this->load->model('Usuario_model'); // Añade esta línea
        $this->load->model('Notificacion_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('estados');
    }
        private function _verificar_sesion() {
            if (!$this->session->userdata('login')) {
                redirect('usuarios/login');
            }
        }
    
        private function _verificar_rol($roles_permitidos) {
            $this->_verificar_sesion();
            $rol_actual = $this->session->userdata('rol');
            if (!in_array($rol_actual, $roles_permitidos)) {
                $this->session->set_flashdata('error', 'No tienes permiso para realizar esta acción.');
                redirect('usuarios/panel');
            }
        }
    
        public function pendientes() {
            $this->_verificar_rol(['administrador', 'encargado']);
            
            try {
                // Verificar y procesar expiraciones antes de mostrar
                $solicitudes_expiradas = $this->Solicitud_model->verificar_y_procesar_expiraciones();
                
                if ($solicitudes_expiradas > 0) {
                    log_message('info', "Se procesaron {$solicitudes_expiradas} solicitudes expiradas");
                }
                
                $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_pendientes();
                $data['tiempo_limite'] = TIEMPO_RESERVA;
                
                // Agregar datos adicionales para la vista
                foreach ($data['solicitudes'] as &$solicitud) {
                    $solicitud->tiempo_restante = max(0, 
                        strtotime($solicitud->fechaExpiracionReserva) - time()
                    );
                }
                
                $this->load->view('inc/header');
                $this->load->view('inc/nabvar');
                $this->load->view('inc/aside');
                $this->load->view('solicitudes/pendientes', $data);
                $this->load->view('inc/footer');
                
            } catch (Exception $e) {
                log_message('error', 'Error en pendientes(): ' . $e->getMessage());
                $this->session->set_flashdata('error', 'Error al cargar las solicitudes pendientes.');
                redirect('usuarios/panel');
            }
        }

        public function eliminar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
    
            $idUsuario = $this->session->userdata('idUsuario');
            $resultado = $this->Solicitud_model->eliminar_solicitud($idSolicitud, $idUsuario);
    
            if ($resultado) {
                $this->session->set_flashdata('mensaje', 'Solicitud eliminada con éxito.');
            } else {
                $this->session->set_flashdata('error', 'Error al eliminar la solicitud.');
            }
    
            redirect('solicitudes/pendientes');
        }

 public function mis_solicitudes() {
    $this->_verificar_rol(['lector']);
    
    try {
        $idUsuario = $this->session->userdata('idUsuario');
        
        // Obtener las solicitudes del usuario con toda la información procesada
        $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_usuario($idUsuario);
        
        // Obtener información adicional para la vista
        $data['puede_solicitar'] = $this->Solicitud_model->puede_crear_nueva_solicitud($idUsuario);
        $data['total_solicitudes'] = $this->Solicitud_model->contar_solicitudes_activas($idUsuario);
        
        // Cargar las vistas
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('solicitudes/mis_solicitudes', $data);
        $this->load->view('inc/footer');
        
    } catch (Exception $e) {
        log_message('error', 'Error en mis_solicitudes: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'Ocurrió un error al cargar las solicitudes.');
        redirect('usuarios/panel');
    }
}
    
        
        private function _enviar_email($to, $subject, $message) {
            $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
            $this->email->to($to);
            $this->email->subject($subject);
            $this->email->message($message);
            return $this->email->send();
        }
        public function detalle($idSolicitud = null) {
            $this->_verificar_sesion();
            
            // Validar que se proporcionó un ID de solicitud
            if ($idSolicitud === null) {
                $this->session->set_flashdata('error', 'No se especificó una solicitud para ver.');
                redirect('solicitudes/mis_solicitudes');
                return;
            }
            
            // Verificar y procesar expiraciones
            $this->Solicitud_model->verificar_y_procesar_expiraciones();
            
            // Obtener detalles de la solicitud con información completa
            $solicitud = $this->Solicitud_model->obtener_detalle_solicitud($idSolicitud);
            
            if (!$solicitud) {
                $this->session->set_flashdata('error', 'La solicitud no existe o ha sido eliminada.');
                redirect('solicitudes/mis_solicitudes');
                return;
            }
            
            // Verificar permisos de acceso
            $rol = $this->session->userdata('rol');
            $idUsuario = $this->session->userdata('idUsuario');
            
            if ($rol == 'lector' && $solicitud->idUsuario != $idUsuario) {
                $this->session->set_flashdata('error', 'No tienes permiso para ver esta solicitud.');
                redirect('solicitudes/mis_solicitudes');
                return;
            }
            
            // Preparar datos adicionales para la vista
            $data = array(
                'solicitud' => $solicitud,
                'es_lector' => ($rol == 'lector'),
                'puede_cancelar' => $this->_puede_cancelar_solicitud($solicitud),
                'estado_texto' => $this->_obtener_texto_estado($solicitud->estadoSolicitud)
            );
            
            // Cargar las vistas
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/detalle', $data);
            $this->load->view('inc/footer');
        }
        
        private function _puede_cancelar_solicitud($solicitud) {
            $rol = $this->session->userdata('rol');
            
            return (
                $rol == 'lector' && 
                $solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE &&
                !$this->Prestamo_model->tiene_prestamo_activo($solicitud->idSolicitud)
            );
        }
        
        private function _obtener_texto_estado($estado) {
            $estados = array(
                ESTADO_SOLICITUD_PENDIENTE => 'Pendiente',
                ESTADO_SOLICITUD_APROBADA => 'Aprobada',
                ESTADO_SOLICITUD_RECHAZADA => 'Rechazada',
                ESTADO_SOLICITUD_FINALIZADA => 'Finalizada',
                ESTADO_SOLICITUD_EXPIRADA => 'Expirada',
                ESTADO_SOLICITUD_CANCELADA => 'Cancelada'
            );
            
            return isset($estados[$estado]) ? $estados[$estado] : 'Desconocido';
        }

        public function aprobar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
            
            $this->db->trans_start();
            
            try {
                $idEncargado = $this->session->userdata('idUsuario');
                $fechaActual = date('Y-m-d H:i:s');
                
                // Obtener detalles de la solicitud con campos específicos
                $solicitud = $this->Solicitud_model->obtener_solicitud($idSolicitud);
                if (!$solicitud || $solicitud->estadoSolicitud != ESTADO_SOLICITUD_PENDIENTE) {
                    throw new Exception('La solicitud no es válida o ya fue procesada');
                }
        
                // Actualizar estado de la solicitud
                $datos_actualizacion = [
                    'estadoSolicitud' => ESTADO_SOLICITUD_APROBADA,
                    'fechaAprobacionRechazo' => $fechaActual,
                    'fechaActualizacion' => $fechaActual,
                    'idUsuarioCreador' => $idEncargado
                ];
        
                $this->db->where('idSolicitud', $idSolicitud);
                $this->db->update('SOLICITUD_PRESTAMO', $datos_actualizacion);
        
                // Iniciar el préstamo
                if (!$this->Prestamo_model->iniciar_prestamo($idSolicitud, $idEncargado)) {
                    throw new Exception('Error al iniciar el préstamo');
                }
        
                // Guardar información de la ficha en sesión para generación posterior
                $datos_solicitud = $this->Solicitud_model->obtener_detalle_solicitud_multiple($idSolicitud);
                $datos_ficha = [
                    'idSolicitud' => $idSolicitud,
                    'nombreCompletoLector' => $datos_solicitud[0]->nombres . ' ' . $datos_solicitud[0]->apellidoPaterno,
                    'carnet' => $datos_solicitud[0]->carnet,
                    'profesion' => $datos_solicitud[0]->profesion,
                    'fechaPrestamo' => $fechaActual
                ];
                
                $this->session->set_userdata('datos_ficha_pendiente', $datos_ficha);
        
                // Notificar al usuario
                $this->Notificacion_model->crear_notificacion(
                    $solicitud->idUsuario,
                    null,
                    NOTIFICACION_APROBACION_PRESTAMO,
                    'Su solicitud ha sido aprobada y el préstamo ha sido iniciado.'
                );
        
                $this->db->trans_complete();
        
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Error en la transacción');
                }
        
                $this->session->set_flashdata('mensaje', 'Solicitud aprobada y préstamo iniciado exitosamente. Puede generar la ficha de préstamo desde la lista de préstamos activos.');
                redirect('prestamos/activos');
        
            } catch (Exception $e) {
                $this->db->trans_rollback();
                log_message('error', 'Error en aprobación: ' . $e->getMessage());
                $this->session->set_flashdata('error', $e->getMessage());
                redirect('solicitudes/pendientes');
            }
        }
        
        // Nuevo método para generar ficha bajo demanda
        public function generar_ficha($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
            
            try {
                $datos_solicitud = $this->Solicitud_model->obtener_detalle_solicitud_multiple($idSolicitud);
                
                if (empty($datos_solicitud)) {
                    throw new Exception('No se encontraron detalles de la solicitud');
                }
        
                $datos_ficha = [
                    'nombreCompletoLector' => $datos_solicitud[0]->nombres . ' ' . $datos_solicitud[0]->apellidoPaterno,
                    'carnet' => $datos_solicitud[0]->carnet,
                    'profesion' => $datos_solicitud[0]->profesion,
                    'fechaPrestamo' => $datos_solicitud[0]->fechaPrestamo
                ];
        
                $pdfUrl = $this->generar_pdf_ficha_prestamo($datos_ficha, $idSolicitud);
                
                if (!$pdfUrl) {
                    throw new Exception('Error al generar la ficha PDF');
                }
        
                $this->session->set_flashdata('mensaje', 'Ficha de préstamo generada exitosamente');
                redirect('prestamos/activos?pdf=' . urlencode($pdfUrl));
        
            } catch (Exception $e) {
                log_message('error', 'Error al generar ficha: ' . $e->getMessage());
                $this->session->set_flashdata('error', $e->getMessage());
                redirect('prestamos/activos');
            }
        }
        public function rechazar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
            
            $this->db->trans_start();
            
            try {
                // Establecer la zona horaria correcta para Bolivia
                date_default_timezone_set('America/La_Paz');
                
                // Crear objeto DateTime para manejar las fechas correctamente
                $fechaHoraActual = new DateTime();
                
                $idEncargado = $this->session->userdata('idUsuario');
                
                // Obtener detalles de la solicitud con campos específicos
                $solicitud = $this->Solicitud_model->obtener_detalle_solicitud_multiple($idSolicitud);
                
                if (!$solicitud) {
                    throw new Exception('Solicitud no encontrada');
                }
        
                // Verificar que la solicitud esté en estado pendiente
                if ($solicitud[0]->estadoSolicitud != ESTADO_SOLICITUD_PENDIENTE) {
                    throw new Exception('La solicitud no está en estado pendiente');
                }
        
                // Actualizar el estado de la solicitud a rechazada
                $data_solicitud = array(
                    'estadoSolicitud' => ESTADO_SOLICITUD_RECHAZADA,
                    'fechaAprobacionRechazo' => $fechaHoraActual->format('Y-m-d H:i:s'),
                    'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
                    'idUsuarioCreador' => $idEncargado
                );
        
                $this->db->where('idSolicitud', $idSolicitud);
                $this->db->update('SOLICITUD_PRESTAMO', $data_solicitud);
        
                // Recolectar títulos para la notificación
                $titulos = array_map(function($pub) {
                    return $pub->titulo;
                }, $solicitud);
        
                // Procesar cada publicación de la solicitud
                foreach ($solicitud as $pub) {
                    // Actualizar estado de la publicación a disponible
                    $data_publicacion = array(
                        'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                        'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
                        'idUsuarioCreador' => $idEncargado
                    );
                    
                    $this->db->where('idPublicacion', $pub->idPublicacion);
                    $this->db->update('PUBLICACION', $data_publicacion);
        
                    // Actualizar detalle de solicitud
                    $data_detalle = array(
                        'observaciones' => 'Solicitud rechazada el ' . $fechaHoraActual->format('Y-m-d H:i:s'),
                        'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s')
                    );
                    
                    $this->db->where('idSolicitud', $idSolicitud);
                    $this->db->where('idPublicacion', $pub->idPublicacion);
                    $this->db->update('DETALLE_SOLICITUD', $data_detalle);
        
                    // Notificar a usuarios en lista de espera
                    $usuarios_interesados = $this->Notificacion_model->obtener_usuarios_interesados_excepto(
                        $pub->idPublicacion,
                        $solicitud[0]->idUsuario
                    );
        
                    foreach ($usuarios_interesados as $usuario) {
                        $this->Notificacion_model->crear_notificacion(
                            $usuario->idUsuario,
                            $pub->idPublicacion,
                            NOTIFICACION_DISPONIBILIDAD,
                            sprintf('La publicación "%s" está nuevamente disponible para préstamo.', $pub->titulo)
                        );
                    }
                }
        
                // Crear notificación de rechazo para el solicitante
                $mensaje = "Tu solicitud de préstamo ha sido rechazada para las siguientes publicaciones: " . 
                          implode(", ", $titulos);
        
                $this->Notificacion_model->crear_notificacion(
                    $solicitud[0]->idUsuario,
                    $solicitud[0]->idPublicacion,
                    NOTIFICACION_RECHAZO_PRESTAMO,
                    $mensaje
                );
        
                $this->db->trans_complete();
        
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Error en la transacción');
                }
        
                $this->session->set_flashdata('mensaje', 'Solicitud rechazada correctamente y publicaciones disponibles.');
        
            } catch (Exception $e) {
                $this->db->trans_rollback();
                log_message('error', 'Error al rechazar solicitud: ' . $e->getMessage());
                $this->session->set_flashdata('error', $e->getMessage());
            }
        
            redirect('solicitudes/pendientes');
        }
        
// Método auxiliar para notificar disponibilidad
private function _notificar_disponibilidad($idPublicacion) {
    $usuarios_interesados = $this->Notificacion_model->obtener_usuarios_interesados($idPublicacion);
    $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);

    foreach ($usuarios_interesados as $usuario) {
        $mensaje = "La publicación '{$publicacion->titulo}' está nuevamente disponible.";
        $this->Notificacion_model->crear_notificacion(
            $usuario->idUsuario,
            $idPublicacion,
            NOTIFICACION_DISPONIBILIDAD,
            $mensaje
        );
    }
}
private function generar_pdf_ficha_prestamo($datos, $idSolicitud) {
    // Validación de datos y carga de modelos
    if (!isset($this->solicitud_model)) {
        $this->load->model('Solicitud_model');
    }
    
    $detalles_solicitud = $this->solicitud_model->obtener_detalle_solicitud_multiple($idSolicitud);
    if (empty($detalles_solicitud)) {
        log_message('error', 'No se encontraron detalles para la solicitud ID: ' . $idSolicitud);
        return false;
    }

    $idEncargado = $this->session->userdata('idUsuario');
    $this->load->model('Usuario_model');
    $encargado = $this->Usuario_model->obtener_usuario($idEncargado);

    // Cargar Dompdf
    require_once APPPATH . '../vendor/autoload.php';
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('isRemoteEnabled', true);
    
    $dompdf = new \Dompdf\Dompdf($options);
    
    // Construcción del HTML
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }
            .header {
                text-align: center;
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .subheader {
                text-align: center;
                font-size: 14px;
                margin-bottom: 15px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }
            table.info td, table.info th {
                border: 1px solid #000;
                padding: 8px;
            }
            table.info th {
                background-color: #f2f2f2;
                font-weight: bold;
                text-align: center;
            }
            .signature-line {
                border-top: 1px solid #000;
                width: 200px;
                text-align: center;
                margin: 0 auto;
                padding-top: 5px;
            }
            .footer {
                position: absolute;
                bottom: 0;
                right: 0;
                font-size: 8px;
                text-align: right;
            }
        </style>
    </head>
    <body>
        <div class="header">U.M.S.S. BIBLIOTECAS - EN SALA</div>
        <div class="subheader">FICHA DE PRÉSTAMO</div>

        <table class="info">
            <tr>
                <td width="30%"><strong>Nombre del Lector:</strong></td>
                <td>' . $this->sanitize_for_pdf($datos['nombreCompletoLector']) . '</td>
            </tr>
            <tr>
                <td><strong>Carnet del Lector:</strong></td>
                <td>' . $this->sanitize_for_pdf($datos['carnet']) . '</td>
            </tr>
            <tr>
                <td><strong>Profesión:</strong></td>
                <td>' . $this->sanitize_for_pdf($datos['profesion']) . '</td>
            </tr>
            <tr>
                <td><strong>Fecha de Préstamo:</strong></td>
                <td>'  . date('d/m/Y H:i:s') . '</td>
            </tr>
            <tr>
                <td><strong>Encargado:</strong></td>
                <td>' . $this->sanitize_for_pdf($encargado->nombres . ' ' . $encargado->apellidoPaterno) . '</td>
            </tr>
        </table>

        <h4>Publicaciones Prestadas:</h4>
        <table class="info">
            <thead>
                <tr>
                    <th width="8%">N°</th>
                    <th width="40%">Título</th>
                    <th width="22%">Editorial</th>
                    <th width="15%">Fecha Public.</th>
                    <th width="15%">Ubicación</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($detalles_solicitud as $index => $pub) {
        $html .= '
            <tr>
                <td style="text-align: center;">' . ($index + 1) . '</td>
                <td>' . $this->sanitize_for_pdf($pub->titulo) . '</td>
                <td style="text-align: center;">' . $this->sanitize_for_pdf($pub->nombreEditorial) . '</td>
                <td style="text-align: center;">' . date('d/m/Y', strtotime($pub->fechaPublicacion)) . '</td>
                <td style="text-align: center;">' . $this->sanitize_for_pdf($pub->ubicacionFisica) . '</td>
            </tr>';
    }

    $html .= '</tbody></table>
        
        <table style="width: 100%; margin-top: 50px;">
            <tr>
                <td width="50%" style="text-align: center;">
                    <div class="signature-line">Firma del Lector</div>
                </td>
                <td width="50%" style="text-align: center;">
                    <div class="signature-line">Firma del Encargado</div>
                </td>
            </tr>
        </table>
        
        <div class="footer">
            <p>Fecha y hora de impresión: ' . date('d/m/Y H:i:s') . '</p>
            <p>ID Solicitud: ' . $idSolicitud . '</p>
        </div>
    </body>
    </html>';

    try {
        // Configurar Dompdf
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        // Generar nombre único y guardar el archivo
        $pdfFileName = 'ficha_prestamo_' . $idSolicitud . '_' . time() . '.pdf';
        $pdfPath = FCPATH . 'uploads/' . $pdfFileName;
        
        // Asegurar que el directorio existe
        if (!file_exists(FCPATH . 'uploads/')) {
            mkdir(FCPATH . 'uploads/', 0777, true);
        }

        // Guardar el PDF
        file_put_contents($pdfPath, $dompdf->output());
        
        return base_url('uploads/' . $pdfFileName);
    } catch (Exception $e) {
        log_message('error', 'Error al generar PDF con Dompdf: ' . $e->getMessage());
        return false;
    }
}
        private function sanitize_for_pdf($text) {
            if (empty($text)) {
                return '';
            }
            // Convertir caracteres especiales a entidades HTML
            $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            // Convertir entidades HTML a sus equivalentes Unicode
            return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        }
        
        public function aprobadas() {
            $this->_verificar_rol(['administrador', 'encargado']);
            $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_por_estado(ESTADO_SOLICITUD_APROBADA);
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/aprobadas', $data);
            $this->load->view('inc/footer');
        }
        
        public function rechazadas() {
            $this->_verificar_rol(['administrador', 'encargado']);
            $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_por_estado(ESTADO_SOLICITUD_RECHAZADA);
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/rechazadas', $data);
            $this->load->view('inc/footer');
        }
        
        public function historial() {
            $this->_verificar_rol(['administrador', 'encargado']);
            $data['solicitudes'] = $this->Solicitud_model->obtener_historial_solicitudes();
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/historial', $data);
            $this->load->view('inc/footer');
        }

        public function crear_solicitud_multiple() {
            $this->_verificar_rol(['lector']);
            
            if ($this->input->post()) {
                $publicaciones = $this->input->post('publicaciones');
                $idUsuario = $this->session->userdata('idUsuario');
                
                // Validar que se hayan seleccionado publicaciones
                if (empty($publicaciones)) {
                    $this->session->set_flashdata('error', 'Debe seleccionar al menos una publicación');
                    redirect('publicaciones');
                }
                
                // Verificar disponibilidad
                $no_disponibles = $this->Solicitud_model->verificar_disponibilidad_multiple($publicaciones);
                if (!empty($no_disponibles)) {
                    $this->session->set_flashdata('error', 'Algunas publicaciones ya no están disponibles');
                    redirect('publicaciones');
                }
                
                // Crear la solicitud
                $resultado = $this->Solicitud_model->crear_solicitud_multiple($idUsuario, $publicaciones);
                
                if ($resultado['success']) {
                    // Crear notificaciones para los encargados
                    $encargados = $this->Usuario_model->obtener_encargados_activos();
                    foreach ($encargados as $encargado) {
                        $mensaje = "Nueva solicitud múltiple de préstamo recibida";
                        $this->Notificacion_model->crear_notificacion(
                            $encargado->idUsuario,
                            null,
                            NOTIFICACION_NUEVA_SOLICITUD,
                            $mensaje
                        );
                    }
                    
                    $this->session->set_flashdata('mensaje', 'Solicitud creada exitosamente');
                } else {
                    $this->session->set_flashdata('error', $resultado['message']);
                }
                
                redirect('solicitudes/mis_solicitudes');
            }
            
            // Cargar vista para crear solicitud múltiple
            $data['publicaciones_disponibles'] = $this->Publicacion_model->obtener_publicaciones_disponibles();
            
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/crear_multiple', $data);
            $this->load->view('inc/footer');
        }
        public function obtener_disponibles_ajax() {
            $this->_verificar_rol(['lector']);
            
            $publicaciones = $this->publicacion_model->obtener_publicaciones_disponibles();
            header('Content-Type: application/json');
            echo json_encode($publicaciones);
        }


private function programar_verificacion_tiempo_limite($idSolicitud) {
    // Esta función podría implementarse con un cron job o un sistema de tareas programadas
    // Por ahora, registramos la tarea en una tabla de verificaciones pendientes
    $data = array(
        'idSolicitud' => $idSolicitud,
        'fechaVerificacion' => date('Y-m-d H:i:s', strtotime('+2 hours')),
        'estado' => 'pendiente'
    );
    $this->db->insert('verificaciones_tiempo_limite', $data);
}


public function crear($idPublicacion) {
    $this->_verificar_rol(['lector']);
    
    log_message('debug', "=== INICIO crear() - idPublicacion: {$idPublicacion} ===");
    
    if (!$this->session->userdata('idUsuario')) {
        log_message('error', 'Usuario no autenticado intentando crear solicitud');
        redirect('usuarios/login');
        return;
    }

    $idUsuario = $this->session->userdata('idUsuario');
    
    // Inicializar array de publicaciones seleccionadas si no existe
    if (!$this->session->userdata('publicaciones_seleccionadas')) {
        $this->session->set_userdata('publicaciones_seleccionadas', array());
        log_message('debug', 'Inicializando array de publicaciones seleccionadas');
    }

    $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas');
    log_message('debug', 'Publicaciones seleccionadas actuales: ' . json_encode($publicaciones_seleccionadas));

    // Si se está intentando añadir una nueva publicación
    if ($idPublicacion !== null && $idPublicacion !== '0') {
        log_message('debug', "Intentando añadir publicación ID: {$idPublicacion}");
        
        $publicacion = $this->Publicacion_model->obtener_publicacion_detallada($idPublicacion);
        log_message('debug', "Estado de la publicación: " . ($publicacion ? $publicacion->estado : 'No encontrada'));

        if (!$publicacion) {
            log_message('error', "Publicación {$idPublicacion} no encontrada");
            $this->session->set_flashdata('error', 'La publicación no existe.');
            redirect('publicaciones/lista');
            return;
        }

        // Validar si ya está en la lista
        if (in_array($idPublicacion, $publicaciones_seleccionadas)) {
            $this->session->set_flashdata('error', 'La publicación ya está en la lista de solicitudes.');
        } else {
            // Validar límite de publicaciones
            if (count($publicaciones_seleccionadas) >= 5) {
                $this->session->set_flashdata('error', 'Solo puede solicitar hasta 5 publicaciones a la vez.');
            } 
            // Validar disponibilidad
            else if ($publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
                $this->session->set_flashdata('error', 'La publicación no está disponible para préstamo.');
            }
            // Agregar a la lista temporal
            else {
                $publicaciones_seleccionadas[] = $idPublicacion;
                $this->session->set_userdata('publicaciones_seleccionadas', $publicaciones_seleccionadas);
                $this->session->set_flashdata('mensaje', 'Publicación añadida a la solicitud.');
                log_message('debug', "Publicación {$idPublicacion} añadida exitosamente");
            }
        }
    }

    // Obtener detalles de las publicaciones seleccionadas
    $data['publicaciones'] = array();
    if (!empty($publicaciones_seleccionadas)) {
        $data['publicaciones'] = $this->Publicacion_model->obtener_publicaciones_seleccionadas($publicaciones_seleccionadas);
        log_message('debug', 'Total publicaciones en la lista: ' . count($data['publicaciones']));
    }

    // Cargar vista
    $this->load->view('inc/header');
    $this->load->view('inc/nabvar');
    $this->load->view('inc/aside');
    $this->load->view('solicitudes/crear', $data);
    $this->load->view('inc/footer');
}

private function _verificar_expiracion_solicitud($idSolicitud) {
    $solicitud = $this->Solicitud_model->obtener_solicitud($idSolicitud);
    
    if (!$solicitud) {
        return false;
    }

    $tiempo_limite = strtotime('-2 minutes');
    $fecha_creacion = strtotime($solicitud->fechaCreacion);

    if ($fecha_creacion <= $tiempo_limite) {
        $this->Solicitud_model->verificar_y_procesar_expiraciones();
        return true;
    }

    return false;
}

public function confirmar() {
    $this->_verificar_rol(['lector']);
    $idUsuario = $this->session->userdata('idUsuario');
    
    $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas');
    
    if (empty($publicaciones_seleccionadas)) {
        $this->session->set_flashdata('error', 'No hay publicaciones seleccionadas.');
        redirect('publicaciones');
        return;
    }

    $this->db->trans_start();

    try {
        // Verificar expiración antes de procesar
        $idSolicitud = $this->input->post('idSolicitud');
        if ($this->_verificar_expiracion_solicitud($idSolicitud)) {
            $this->session->set_flashdata('error', 'La solicitud ha expirado por tiempo límite.');
            redirect('solicitudes/mis_solicitudes');
            return;
        }

        $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
        $hora_actual = date('Y-m-d H:i:s');
        $hora_expiracion = date('Y-m-d H:i:s', strtotime('+2 minutes'));

        // Validación inicial de disponibilidad
        foreach ($publicaciones_seleccionadas as $idPublicacion) {
            // Verificar disponibilidad actual y posibles conflictos
            $disponibilidad = $this->Publicacion_model->verificar_disponibilidad($idPublicacion, $idUsuario);
            if (!$disponibilidad) {
                throw new Exception('Una o más publicaciones ya no están disponibles.');
            }
            
            // Verificar si no hay otras solicitudes pendientes
            $solicitud_existente = $this->Solicitud_model->tiene_solicitud_pendiente($idPublicacion);
            if ($solicitud_existente) {
                throw new Exception('Existe una solicitud pendiente para una de las publicaciones.');
            }
        }

        // Crear solicitud principal con estado pendiente y tiempo de expiración
        $datos_solicitud = array(
            'idUsuario' => $idUsuario,
            'fechaSolicitud' => $hora_actual,
            'estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'estado' => 1,
            'fechaCreacion' => $hora_actual,
            'observaciones' => "Reserva temporal hasta: " . $hora_expiracion,
            'idUsuarioCreador' => $idUsuario
        );

        $this->db->insert('SOLICITUD_PRESTAMO', $datos_solicitud);
        $idSolicitud = $this->db->insert_id();

        if (!$idSolicitud) {
            throw new Exception('Error al crear la solicitud');
        }

        // Procesar cada publicación
        foreach ($publicaciones_seleccionadas as $idPublicacion) {
            $datos_detalle = array(
                'idSolicitud' => $idSolicitud,
                'idPublicacion' => $idPublicacion,
                'fechaReserva' => $hora_actual,
                'fechaExpiracionReserva' => $hora_expiracion,
                'estadoReserva' => 1,
                'observaciones' => "Reserva temporal hasta: " . $hora_expiracion
            );
            
            $this->db->insert('DETALLE_SOLICITUD', $datos_detalle);

            // Actualizar estado de publicación a RESERVADA
            $this->Publicacion_model->reservar_publicacion($idPublicacion, $idUsuario);
        }

        // Preparar mensaje para el lector
        $titulos = array();
        foreach ($publicaciones_seleccionadas as $idPub) {
            $pub = $this->Publicacion_model->obtener_publicacion($idPub);
            if ($pub) {
                $titulos[] = $pub->titulo;
            }
        }

        $mensaje_lector = sprintf(
            "Publicaciones reservadas temporalmente:\n%s\nTu reserva expira a las: %s\nPor favor, acércate al encargado antes de ese horario.",
            implode(", ", $titulos),
            date('H:i', strtotime($hora_expiracion))
        );

        // Crear notificación para el lector
        $this->Notificacion_model->crear_notificacion(
            $idUsuario,
            null,
            NOTIFICACION_SOLICITUD_PRESTAMO,
            $mensaje_lector
        );

        // Notificar a encargados
        $this->_notificar_encargados_nueva_reserva($usuario, $titulos, $hora_expiracion);

        // Limpiar sesión y mostrar mensaje de éxito
        $this->session->unset_userdata('publicaciones_seleccionadas');
        $this->session->set_flashdata('mensaje', 
            'Publicaciones reservadas temporalmente. Por favor acércate al encargado antes de las ' . 
            date('H:i', strtotime($hora_expiracion))
        );

        $this->db->trans_complete();

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error en confirmación de reserva: ' . $e->getMessage());
        $this->session->set_flashdata('error', $e->getMessage());
    }

    redirect('solicitudes/mis_solicitudes');
}

private function _programar_verificacion_tiempo_limite($idSolicitud, $hora_expiracion) {
    // Registramos la verificación programada
    $datos_verificacion = array(
        'idSolicitud' => $idSolicitud,
        'fechaExpiracion' => $hora_expiracion,
        'estado' => 'pendiente'
    );
    
    $this->db->insert('VERIFICACIONES_EXPIRACION', $datos_verificacion);
    
    // Programar tarea de verificación
    $this->Solicitud_model->registrar_verificacion_expiracion($idSolicitud, $hora_expiracion);
}

private function _generar_notificaciones_reserva($idUsuario, $publicaciones_seleccionadas, $hora_expiracion) {
    $titulos = array();
    foreach ($publicaciones_seleccionadas as $idPub) {
        $pub = $this->Publicacion_model->obtener_publicacion($idPub);
        if ($pub) {
            $titulos[] = $pub->titulo;
        }
    }

    // Notificación para el lector
    $mensaje_lector = sprintf(
        "Publicaciones reservadas temporalmente:\n%s\nTu reserva expira a las: %s\nPor favor, acércate al encargado antes de ese horario.",
        implode(", ", $titulos),
        date('H:i', strtotime($hora_expiracion))
    );

    $this->Notificacion_model->crear_notificacion(
        $idUsuario,
        null,
        NOTIFICACION_SOLICITUD_PRESTAMO,
        $mensaje_lector
    );

    // Notificación para encargados
    $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
    $this->_notificar_encargados_nueva_reserva($usuario, $titulos, $hora_expiracion);
}

private function _notificar_encargados_nueva_reserva($usuario, $titulos, $hora_expiracion) {
    $encargados = $this->Usuario_model->obtener_encargados_activos();
    
    $mensaje_encargado = sprintf(
        "Nueva reserva temporal:\nUsuario: %s %s\nPublicaciones: %s\nExpira: %s",
        $usuario->nombres,
        $usuario->apellidoPaterno,
        implode(", ", $titulos),
        date('H:i', strtotime($hora_expiracion))
    );

    foreach ($encargados as $encargado) {
        $this->Notificacion_model->crear_notificacion(
            $encargado->idUsuario,
            null,
            NOTIFICACION_NUEVA_SOLICITUD,
            $mensaje_encargado
        );
    }
}

public function cancelar() {
    $this->_verificar_rol(['lector']);
    
    log_message('debug', '=== INICIO cancelar() ===');

    try {
        // Limpiar las publicaciones seleccionadas de la sesión
        $this->session->unset_userdata('publicaciones_seleccionadas');
        
        // Registrar en el log para seguimiento
        log_message('info', 'Usuario ID: ' . $this->session->userdata('idUsuario') . ' canceló su selección de publicaciones');
        
        $this->session->set_flashdata('mensaje', 'Se ha cancelado la selección de publicaciones.');
        
        // Redireccionar al catálogo de publicaciones
        redirect('publicaciones/index');
        
    } catch (Exception $e) {
        log_message('error', 'Error al cancelar selección: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'Ocurrió un error al cancelar la selección. Por favor, inténtelo nuevamente.');
        redirect('solicitudes/crear/0');
    }
}
public function remover($idPublicacion) {
    $this->_verificar_rol(['lector']);
    
    log_message('debug', "=== INICIO remover() - idPublicacion: {$idPublicacion} ===");
    
    // Validar que el ID de publicación es válido
    if (!$idPublicacion) {
        log_message('error', 'ID de publicación inválido');
        $this->session->set_flashdata('error', 'ID de publicación inválido.');
        redirect('solicitudes/crear/0');
        return;
    }

    $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas') ?: array();
    log_message('debug', 'Publicaciones antes de remover: ' . json_encode($publicaciones_seleccionadas));

    // Verificar si la publicación está en la lista
    $key = array_search($idPublicacion, $publicaciones_seleccionadas);
    if ($key !== false) {
        // Asegurarse de que el modelo está cargado
        if (!isset($this->Solicitud_model)) {
            $this->load->model('Solicitud_model');
        }

        // Intentar cancelar la reserva temporal
        $resultado = $this->Solicitud_model->cancelar_reserva_temporal(
            $idPublicacion, 
            $this->session->userdata('idUsuario')
        );

        if ($resultado) {
            // Remover de la sesión y actualizar la lista
            unset($publicaciones_seleccionadas[$key]);
            $publicaciones_seleccionadas = array_values($publicaciones_seleccionadas);
            $this->session->set_userdata('publicaciones_seleccionadas', $publicaciones_seleccionadas);
            
            log_message('debug', 'Publicaciones después de remover: ' . json_encode($publicaciones_seleccionadas));
            log_message('info', "Publicación {$idPublicacion} removida exitosamente");
            
            $this->session->set_flashdata('mensaje', 'Publicación removida de la solicitud exitosamente.');
            $this->session->unset_userdata('error');
        } else {
            log_message('error', "Error al cancelar reserva temporal para publicación {$idPublicacion}");
            $this->session->set_flashdata('error', 'No se pudo remover la publicación. Por favor, intente nuevamente.');
        }
    } else {
        log_message('warning', "Publicación {$idPublicacion} no encontrada en la lista");
        $this->session->set_flashdata('error', 'La publicación no está en la lista de solicitudes.');
    }
    
    log_message('debug', "=== FIN remover() ===");
    redirect('solicitudes/crear/0');
}

private function _enviar_notificaciones_solicitud($solicitudes, $idUsuario) {
    $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
    $encargados = $this->Usuario_model->obtener_encargados_activos();

    // Notificar al lector
    $this->Notificacion_model->crear_notificacion(
        $idUsuario,
        null,
        NOTIFICACION_SOLICITUD_CREADA,
        'Sus solicitudes de préstamo han sido registradas y están pendientes de aprobación.'
    );

    // Notificar a encargados
    foreach ($encargados as $encargado) {
        $this->Notificacion_model->crear_notificacion(
            $encargado->idUsuario,
            null,
            NOTIFICACION_NUEVA_SOLICITUD,
            "Nuevas solicitudes de préstamo del usuario: {$usuario->nombres} {$usuario->apellidoPaterno}"
        );
    }
}

private function cargar_vistas($vista, $data = array()) {
    $this->load->view('inc/header');
    $this->load->view('inc/nabvar');
    $this->load->view('inc/aside');
    $this->load->view($vista, $data);
    $this->load->view('inc/footer');
}

public function cancelar_solicitud($idSolicitud) {
    $this->_verificar_rol(['lector']);
    $idUsuario = $this->session->userdata('idUsuario');

    try {
        // Obtener información de la solicitud antes de cancelarla
        $solicitud = $this->Solicitud_model->obtener_solicitud($idSolicitud);
        
        if (!$solicitud) {
            throw new Exception('Solicitud no encontrada.');
        }

        // Verificar que la solicitud pertenezca al usuario actual
        if ($solicitud->idUsuario != $idUsuario) {
            throw new Exception('No tienes permiso para cancelar esta solicitud.');
        }

        $resultado = $this->Solicitud_model->cancelar_solicitud_enviada($idSolicitud, $idUsuario);
        
        if ($resultado['exito']) {
            // Notificar a los encargados si la solicitud estaba pendiente
            if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE) {
                $this->_notificar_cancelacion_encargados($solicitud);
            }
            
            $this->session->set_flashdata('mensaje', $resultado['mensaje']);
        } else {
            $this->session->set_flashdata('error', $resultado['mensaje']);
        }

    } catch (Exception $e) {
        log_message('error', 'Error en cancelación de solicitud: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'Error al cancelar la solicitud: ' . $e->getMessage());
    }

    redirect('solicitudes/mis_solicitudes');
}

private function _notificar_cancelacion_encargados($solicitud) {
    // Obtener información completa del usuario y la publicación
    $usuario = $this->Usuario_model->obtener_usuario($solicitud->idUsuario);
    $publicacion = $this->Publicacion_model->obtener_publicacion($solicitud->idPublicacion);
    
    // Obtener todos los encargados activos
    $encargados = $this->Usuario_model->obtener_encargados_activos();
    
    if (!empty($encargados)) {
        $fecha_cancelacion = date('d/m/Y H:i:s');
        
        foreach ($encargados as $encargado) {
            // Crear mensaje personalizado
            $mensaje = sprintf(
                "La solicitud pendiente #%d ha sido cancelada.\n" .
                "Usuario: %s %s\n" .
                "Publicación: %s\n" .
                "Fecha de cancelación: %s",
                $solicitud->idSolicitud,
                $usuario->nombres,
                $usuario->apellidoPaterno,
                $publicacion->titulo,
                $fecha_cancelacion
            );

            // Registrar la notificación para el encargado
            $this->Notificacion_model->crear_notificacion(
                $encargado->idUsuario,
                $solicitud->idPublicacion,
                NOTIFICACION_CANCELACION_SOLICITUD,
                $mensaje
            );

            // Registrar en el log del sistema
            log_message('info', sprintf(
                'Notificación de cancelación enviada - Solicitud: %d, Encargado: %d',
                $solicitud->idSolicitud,
                $encargado->idUsuario
            ));
        }
}
}
}