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
    
            $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_pendientes();
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/pendientes', $data);
            $this->load->view('inc/footer');
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
            
            $idUsuario = $this->session->userdata('idUsuario');
            $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_usuario($idUsuario);
            
            // No es necesario pasar las constantes a la vista si están definidas globalmente
            
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/mis_solicitudes', $data);
            $this->load->view('inc/footer');
        }
    
        
        private function _enviar_email($to, $subject, $message) {
            $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
            $this->email->to($to);
            $this->email->subject($subject);
            $this->email->message($message);
            return $this->email->send();
        }
    
        public function detalle($idSolicitud) {
            $this->_verificar_sesion();
            
            $solicitud = $this->Solicitud_model->obtener_detalle_solicitud($idSolicitud);
            
            if (!$solicitud) {
                $this->session->set_flashdata('error', 'La solicitud no existe.');
                redirect('solicitudes/mis_solicitudes');
            }
            
            // Verificar si el usuario actual tiene permiso para ver esta solicitud
            if ($this->session->userdata('rol') == 'lector' && $solicitud->idUsuario != $this->session->userdata('idUsuario')) {
                $this->session->set_flashdata('error', 'No tienes permiso para ver esta solicitud.');
                redirect('solicitudes/mis_solicitudes');
            }
            
            $data['solicitud'] = $solicitud;
            
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/detalle', $data);
            $this->load->view('inc/footer');
        }
        
  
public function crear($idPublicacion) {
    $this->_verificar_rol(['lector']);

    // Inicializar array de publicaciones seleccionadas en sesión si no existe
    if (!$this->session->userdata('publicaciones_seleccionadas')) {
        $this->session->set_userdata('publicaciones_seleccionadas', array());
    }

    // Obtener publicaciones seleccionadas actuales
    $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas');

    // Añadir la nueva publicación si no está ya seleccionada
    if (!in_array($idPublicacion, $publicaciones_seleccionadas)) {
        // Verificar límite de 5 publicaciones
        if (count($publicaciones_seleccionadas) >= 5) {
            $this->session->set_flashdata('error', 'Solo puede solicitar hasta 5 publicaciones a la vez.');
            redirect('publicaciones');
            return;
        }

        $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);
        if (!$publicacion || $publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
            $this->session->set_flashdata('error', 'La publicación no está disponible para préstamo.');
            redirect('publicaciones');
            return;
        }

        $publicaciones_seleccionadas[] = $idPublicacion;
        $this->session->set_userdata('publicaciones_seleccionadas', $publicaciones_seleccionadas);
    }

    // Obtener todas las publicaciones seleccionadas
    $data['publicaciones'] = array();
    foreach ($publicaciones_seleccionadas as $id) {
        $data['publicaciones'][] = $this->Publicacion_model->obtener_publicacion($id);
    }

    $this->load->view('inc/header');
    $this->load->view('inc/nabvar');
    $this->load->view('inc/aside');
    $this->load->view('solicitudes/crear', $data);
    $this->load->view('inc/footer');
}

public function confirmar() {
    $this->_verificar_rol(['lector']);
    $idUsuario = $this->session->userdata('idUsuario');
    
    // Obtener publicaciones seleccionadas de la sesión
    $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas');
    
    if (empty($publicaciones_seleccionadas)) {
        $this->session->set_flashdata('error', 'No hay publicaciones seleccionadas.');
        redirect('publicaciones');
        return;
    }

    $this->db->trans_start();

    try {
        $usuario = $this->Usuario_model->obtener_usuario($idUsuario);

        // Crear una única solicitud con todas las publicaciones
        $resultado = $this->Solicitud_model->crear_solicitud_multiple($idUsuario, $publicaciones_seleccionadas);
        
        if ($resultado) {
            // Notificar al lector
            $titulos = array();
            foreach ($publicaciones_seleccionadas as $idPub) {
                $pub = $this->Publicacion_model->obtener_publicacion($idPub);
                $titulos[] = $pub->titulo;
            }

            $mensaje_lector = "Se ha recibido tu solicitud de préstamo para las siguientes publicaciones: " . implode(", ", $titulos);
            $this->Notificacion_model->crear_notificacion(
                $idUsuario,
                null,
                NOTIFICACION_SOLICITUD_PRESTAMO,
                $mensaje_lector
            );

            // Notificar a encargados
            $encargados = $this->Usuario_model->obtener_encargados_activos();
            foreach ($encargados as $encargado) {
                $mensaje = "Nueva solicitud de préstamo del usuario '{$usuario->nombres} {$usuario->apellidoPaterno}'";
                $this->Notificacion_model->crear_notificacion(
                    $encargado->idUsuario,
                    null,
                    NOTIFICACION_NUEVA_SOLICITUD,
                    $mensaje
                );
            }

            // Limpiar las publicaciones seleccionadas de la sesión
            $this->session->unset_userdata('publicaciones_seleccionadas');

            $this->session->set_flashdata('mensaje', 'Solicitud creada con éxito.');
        }

        $this->db->trans_complete();
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('error', 'Error al crear la solicitud.');
    }

    redirect('solicitudes/mis_solicitudes');
}
public function aprobar($idSolicitud) {
    $this->_verificar_rol(['administrador', 'encargado']);
    
    $this->db->trans_start();
    
    try {
        $idEncargado = $this->session->userdata('idUsuario');
        // Obtener detalles de la solicitud
        $solicitud = $this->Solicitud_model->obtener_detalle_solicitud_multiple($idSolicitud);
        
        if (!$solicitud) {
            throw new Exception('Solicitud no encontrada');
        }

        // Verificar disponibilidad de publicaciones
        foreach ($solicitud as $pub) {
            $publicacion = $this->Publicacion_model->obtener_publicacion($pub->idPublicacion);
            if (!$publicacion || $publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
                throw new Exception('Una o más publicaciones no están disponibles');
            }
        }

        // Aprobar solicitud
        $resultado = $this->Solicitud_model->aprobar_solicitud($idSolicitud, $idEncargado);
        
        if ($resultado) {
            // Crear notificación
            $titulos = array_map(function($pub) {
                return $pub->titulo;
            }, $solicitud);
            
            $mensaje = "Tu solicitud de préstamo para las siguientes publicaciones ha sido aprobada: " . 
                      implode(", ", $titulos);

            $this->Notificacion_model->crear_notificacion(
                $solicitud[0]->idUsuario,
                $solicitud[0]->idPublicacion,
                NOTIFICACION_APROBACION_PRESTAMO,
                $mensaje
            );

            // Generar PDF
            $datos_ficha = [
                'nombreCompletoLector' => $solicitud[0]->nombres . ' ' . $solicitud[0]->apellidoPaterno,
                'carnet' => $solicitud[0]->carnet,
                'profesion' => $solicitud[0]->profesion,
                'fechaPrestamo' => date('Y-m-d H:i:s'),
                'nombreCompletoEncargado' => $this->session->userdata('nombres') . ' ' . 
                                           $this->session->userdata('apellidoPaterno'),
                'publicaciones' => $solicitud
            ];

            $pdfUrl = $this->generar_pdf_ficha_prestamo($datos_ficha, $idSolicitud);
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Error en la transacción');
            }

            $this->session->set_flashdata('mensaje', 'Solicitud aprobada y préstamos registrados con éxito.');
            redirect('solicitudes/pendientes' . ($pdfUrl ? '?pdf=' . urlencode($pdfUrl) : ''));
            return;
        }
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error al aprobar solicitud: ' . $e->getMessage());
        $this->session->set_flashdata('error', $e->getMessage());
    }

    redirect('solicitudes/pendientes');
}

public function rechazar($idSolicitud) {
    $this->_verificar_rol(['administrador', 'encargado']);

    $this->db->trans_start();

    try {
        $idEncargado = $this->session->userdata('idUsuario');
        $solicitud = $this->Solicitud_model->obtener_detalle_solicitud_multiple($idSolicitud);

        if (empty($solicitud)) {
            throw new Exception('Solicitud no encontrada');
        }

        if ($this->Solicitud_model->rechazar_solicitud($idSolicitud, $idEncargado)) {
            // Obtener datos del usuario
            $usuario_lector = $this->Usuario_model->obtener_usuario($solicitud[0]->idUsuario);

            // Preparar mensaje con los títulos de las publicaciones
            $titulos = array_map(function($pub) {
                return $pub->titulo;
            }, $solicitud);

            $mensaje = "Tu solicitud de préstamo ha sido rechazada para las siguientes publicaciones: " . 
                      implode(", ", $titulos);

            // Crear notificación para el lector
            $this->Notificacion_model->crear_notificacion(
                $usuario_lector->idUsuario,
                null,
                NOTIFICACION_RECHAZO_PRESTAMO,
                $mensaje
            );

            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Error en la transacción');
            }

            $this->session->set_flashdata('mensaje', 'Solicitud rechazada correctamente');
        } else {
            throw new Exception('Error al rechazar la solicitud');
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error al rechazar solicitud: ' . $e->getMessage());
        $this->session->set_flashdata('error', $e->getMessage());
    }

    redirect('solicitudes/pendientes');
}
        
        private function generar_pdf_ficha_prestamo($datos, $idSolicitud) {
            // Cargar el modelo de solicitudes si no está cargado
            if (!isset($this->solicitud_model)) {
                $this->load->model('Solicitud_model');
            }
            
            // Obtener detalles completos de la solicitud y sus publicaciones
            $detalles_solicitud = $this->solicitud_model->obtener_detalle_solicitud_multiple($idSolicitud);
            if (empty($detalles_solicitud)) {
                log_message('error', 'No se encontraron detalles para la solicitud ID: ' . $idSolicitud);
                return false;
            }
        
            $this->load->library('pdf');
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
            // Configuración del documento
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Hemeroteca UMSS');
            $pdf->SetTitle('Ficha de Préstamo');
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Hemeroteca UMSS', 'Ficha de Préstamo');
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
            // Configuración de márgenes
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
            $pdf->AddPage();
        
            // Datos del usuario y encabezado
            $html = '
            <h1 style="text-align: center;">U.M.S.S. BIBLIOTECAS - EN SALA</h1>
            <h4 style="text-align: center;">FICHA DE PRÉSTAMO</h4>
            <br>
            <table cellpadding="5" style="width: 100%;">
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
                    <td>' . date('d/m/Y H:i:s', strtotime($datos['fechaPrestamo'])) . '</td>
                </tr>
                <tr>
                    <td><strong>Prestado por:</strong></td>
                    <td>' . $this->sanitize_for_pdf($datos['nombreCompletoEncargado']) . '</td>
                </tr>
            </table>
            <br>
            <h4>Publicaciones Prestadas:</h4>
            <table cellpadding="5" style="width: 100%; border: 1px solid #000;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #000; width: 5%;">N°</th>
                        <th style="border: 1px solid #000; width: 20%;">Editorial</th>
                        <th style="border: 1px solid #000; width: 15%;">Fecha de Public.</th>
                        <th style="border: 1px solid #000; width: 15%;">Ubicación</th>
                        <th style="border: 1px solid #000; width: 45%;">Título</th>
                    </tr>
                </thead>
                <tbody>';
        
            foreach ($detalles_solicitud as $index => $pub) {
                $html .= '
                    <tr>
                        <td style="border: 1px solid #000; text-align: center;">' . ($index + 1) . '</td>
                        <td style="border: 1px solid #000;">' . $this->sanitize_for_pdf($pub->nombreEditorial) . '</td>
                        <td style="border: 1px solid #000; text-align: center;">' . date('d/m/Y', strtotime($pub->fechaPublicacion)) . '</td>
                        <td style="border: 1px solid #000;">' . $this->sanitize_for_pdf($pub->ubicacionFisica) . '</td>
                        <td style="border: 1px solid #000;">' . $this->sanitize_for_pdf($pub->titulo) . '</td>
                    </tr>';
            }
        
            $html .= '</tbody></table>
            <br><br>
            <div style="text-align: center;">
                <p style="border-top: 1px solid #000; width: 200px; margin: 0 auto; padding-top: 5px;">Firma del Lector</p>
            </div>
            <br>
            <div style="text-align: right; font-size: 8pt;">
                <p>Fecha y hora de impresión: ' . date('d/m/Y H:i:s') . '</p>
                <p>ID Solicitud: ' . $idSolicitud . '</p>
            </div>';
        
            $pdf->writeHTML($html, true, false, true, false, '');
            
            // Generar nombre único para el archivo
            $pdfFileName = 'ficha_prestamo_' . $idSolicitud . '_' . time() . '.pdf';
            $pdfPath = FCPATH . 'uploads/' . $pdfFileName;
            
            // Verificar y crear el directorio si no existe
            $uploadDir = FCPATH . 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        
            // Guardar el PDF y retornar la URL
            try {
                $pdf->Output($pdfPath, 'F');
                return base_url('uploads/' . $pdfFileName);
            } catch (Exception $e) {
                log_message('error', 'Error al generar PDF: ' . $e->getMessage());
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
        
       
}