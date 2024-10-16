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
    
        public function confirmar($idPublicacion) {
            $this->_verificar_rol(['lector']);
            
            $idUsuario = $this->session->userdata('idUsuario');
            $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);
            
            if (!$publicacion || $publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
                $this->session->set_flashdata('error', 'La publicación no está disponible para préstamo.');
                redirect('publicaciones/index');
            }
            
            $this->db->trans_start();
            
            $resultado = $this->Solicitud_model->crear_solicitud($idUsuario, $idPublicacion);
            
            if ($resultado) {
                // Crear notificación para el lector
                $mensaje_lector = "Se ha recibido tu solicitud de préstamo para la publicación '{$publicacion->titulo}'.";
                $this->Notificacion_model->crear_notificacion($idUsuario, $idPublicacion, NOTIFICACION_SOLICITUD_PRESTAMO, $mensaje_lector);
        
                // Crear notificación para los administradores y encargados
                $admins_encargados = $this->Usuario_model->obtener_admins_encargados();
                foreach ($admins_encargados as $usuario) {
                    $mensaje_admin = "Nueva solicitud de préstamo para la publicación '{$publicacion->titulo}' del usuario '{$this->session->userdata('nombres')} {$this->session->userdata('apellidoPaterno')}'.";
                    $this->Notificacion_model->crear_notificacion($usuario->idUsuario, $idPublicacion, NOTIFICACION_NUEVA_SOLICITUD, $mensaje_admin);
                    log_message('info', 'Notificación creada para admin/encargado: ID=' . $usuario->idUsuario);
                }
        
                // Enviar email si el usuario lo prefiere
                $preferencias = $this->Notificacion_model->obtener_preferencias($idUsuario);
                if ($preferencias && $preferencias->notificarEmail) {
                    $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
                    $this->_enviar_email($usuario->email, 'Confirmación de solicitud de préstamo', $mensaje_lector);
                }
        
                $this->db->trans_complete();
                
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Error al crear la solicitud. Por favor, intente de nuevo.');
                } else {
                    $this->session->set_flashdata('mensaje', 'Solicitud creada con éxito.');
                }
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error al crear la solicitud.');
            }
            
            redirect('solicitudes/mis_solicitudes');
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
        
            $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);
        
            if (!$publicacion || $publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
                $this->session->set_flashdata('error', 'La publicación no está disponible para préstamo.');
                redirect('publicaciones/index');
            }
        
            if ($this->input->post()) {
                $this->db->trans_start();
        
                $idUsuario = $this->session->userdata('idUsuario');
                $resultado = $this->Solicitud_model->crear_solicitud($idUsuario, $idPublicacion);
        
                if ($resultado) {
                    // Crear notificación de confirmación de solicitud
                    $this->load->model('Notificacion_model');
                    $mensaje = "Se ha recibido tu solicitud de préstamo para la publicación '{$publicacion->titulo}'.";
                    $this->Notificacion_model->crear_notificacion($idUsuario, $idPublicacion, 'solicitud_prestamo', $mensaje);
        
                    // Enviar email si el usuario lo prefiere
                    $preferencias = $this->Notificacion_model->obtener_preferencias($idUsuario);
                    if ($preferencias && $preferencias->notificarEmail) {
                        $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
                        $this->_enviar_email($usuario->email, 'Confirmación de solicitud de préstamo', $mensaje);
                    }
                    // Crear notificación para administradores y encargados
                    $this->load->model('Usuario_model');
                    $admins_encargados = $this->Usuario_model->obtener_admins_encargados();
                    foreach ($admins_encargados as $usuario) {
                        $mensaje = "Nueva solicitud de préstamo para la publicación '{$publicacion->titulo}'.";
                        $this->Notificacion_model->crear_notificacion_admin($usuario->idUsuario, $idPublicacion, 'nueva_solicitud', $mensaje);
                    }
        
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->session->set_flashdata('error', 'Error al crear la solicitud. Por favor, intente de nuevo.');
                    } else {
                        $this->session->set_flashdata('mensaje', 'Solicitud creada con éxito.');
                        redirect('solicitudes/mis_solicitudes');
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('error', 'Error al crear la solicitud.');
                }
            }
        
            $data['publicacion'] = $publicacion;
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/crear', $data);
            $this->load->view('inc/footer');
        }
    
        public function aprobar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
        
            $this->db->trans_start();
        
            $idEncargado = $this->session->userdata('idUsuario');
            $resultado = $this->Solicitud_model->aprobar_solicitud($idSolicitud, $idEncargado);
        
            if ($resultado) {
                // Crear notificación de aprobación
                $this->load->model('Notificacion_model');
                $solicitud = $this->Solicitud_model->obtener_solicitud($idSolicitud);
                $mensaje = "Tu solicitud de préstamo para la publicación '{$solicitud->titulo}' ha sido aprobada.";
                $this->Notificacion_model->crear_notificacion($solicitud->idUsuario, $solicitud->idPublicacion, 'aprobacion_rechazo', $mensaje);
        
                // Enviar email si el usuario lo prefiere
                $preferencias = $this->Notificacion_model->obtener_preferencias($solicitud->idUsuario);
                if ($preferencias && $preferencias->notificarEmail) {
                    $usuario = $this->Usuario_model->obtener_usuario($solicitud->idUsuario);
                    $this->_enviar_email($usuario->email, 'Solicitud de préstamo aprobada', $mensaje);
                }
        
                $this->db->trans_complete();
        
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Error al aprobar la solicitud. Por favor, intente de nuevo.');
                } else {
                    $this->session->set_flashdata('mensaje', 'Solicitud aprobada y préstamo registrado con éxito.');
                    
                    // Generar el PDF y obtener la URL
                    $pdfUrl = $this->generar_pdf_ficha_prestamo($resultado, $idSolicitud);
                    
                    // Redirigir a la página de solicitudes pendientes con la URL del PDF
                    redirect('solicitudes/pendientes?pdf=' . urlencode($pdfUrl));
                }
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error al aprobar la solicitud.');
            }
        
            redirect('solicitudes/pendientes');
        }
        
        
        private function generar_pdf_ficha_prestamo($datos, $idSolicitud) {
            // Asegurarse de que la librería TCPDF esté cargada
            $this->load->library('pdf');
        
            // Crear nueva instancia de TCPDF
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
            // Configurar el documento
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Hemeroteca UMSS');
            $pdf->SetTitle('Ficha de Préstamo');
            $pdf->SetSubject('Ficha de Préstamo');
            $pdf->SetKeywords('UMSS, Biblioteca, Préstamo');
        
            // Configurar fuentes
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
            // Configurar márgenes
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
            // Configurar saltos de página automáticos
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
            // Configurar factor de escala de imagen
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
            // Agregar una página
            $pdf->AddPage();
        
            // Preparar el contenido HTML
            $html = '
            <h1 style="text-align: center;">U.M.S.S. BIBLIOTECAS - EN SALA</h1>
            <table cellpadding="5">
                <tr><td width="30%"><strong>Editorial:</strong></td><td>' . $this->sanitize_for_pdf($datos['nombreEditorial']) . '</td></tr>
                <tr><td><strong>Fecha de Publicación:</strong></td><td>' . $this->sanitize_for_pdf($datos['fechaPublicacion']) . '</td></tr>
                <tr><td><strong>Ubicación:</strong></td><td>' . $this->sanitize_for_pdf($datos['ubicacionFisica']) . '</td></tr>
                <tr><td><strong>Título:</strong></td><td>' . $this->sanitize_for_pdf($datos['titulo']) . '</td></tr>
                <tr><td><strong>Nombre del Lector:</strong></td><td>' . $this->sanitize_for_pdf($datos['nombreCompletoLector']) . '</td></tr>
                <tr><td><strong>Carnet del Lector:</strong></td><td>' . $this->sanitize_for_pdf($datos['carnet']) . '</td></tr>
                <tr><td><strong>Profesión:</strong></td><td>' . $this->sanitize_for_pdf($datos['profesion']) . '</td></tr>
                <tr><td><strong>Fecha de Préstamo:</strong></td><td>' . $this->sanitize_for_pdf($datos['fechaPrestamo']) . '</td></tr>
                <tr><td><strong>Prestado por:</strong></td><td>' . $this->sanitize_for_pdf($datos['nombreCompletoEncargado']) . '</td></tr>
            </table>
            <br><br><br>
            <div style="text-align: center;">
                <p>_________________________</p>
                <p>Firma del Lector</p>
            </div>
            ';
        
            // Escribir el HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');
        
            // Generar un nombre único para el archivo PDF
            $pdfFileName = 'ficha_prestamo_' . $idSolicitud . '_' . time() . '.pdf';
            $pdfPath = FCPATH . 'uploads/' . $pdfFileName;
        
            // Guardar el PDF en el servidor
            $pdf->Output($pdfPath, 'F');
        
            // Devolver la URL del PDF
            return base_url('uploads/' . $pdfFileName);
        }
        
        // Función auxiliar para sanitizar texto para PDF
        private function sanitize_for_pdf($text) {
            // Convertir caracteres especiales a entidades HTML
            $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            // Convertir entidades HTML a sus equivalentes Unicode
            return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        }
        
        public function rechazar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
            
            $this->db->trans_start();
            
            $idEncargado = $this->session->userdata('idUsuario');
            $resultado = $this->Solicitud_model->rechazar_solicitud($idSolicitud, $idEncargado);
            
            if ($resultado) {
                // Crear notificación de rechazo
                $solicitud = $this->Solicitud_model->obtener_solicitud($idSolicitud);
                $mensaje = "Tu solicitud de préstamo para la publicación '{$solicitud->titulo}' ha sido rechazada.";
                $this->Notificacion_model->crear_notificacion($solicitud->idUsuario, $solicitud->idPublicacion, NOTIFICACION_RECHAZO_PRESTAMO, $mensaje);
                
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Error al rechazar la solicitud. Por favor, intente de nuevo.');
                } else {
                    $this->session->set_flashdata('mensaje', 'Solicitud rechazada con éxito.');
                }
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error al rechazar la solicitud.');
            }
            
            redirect('solicitudes/pendientes');
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
}