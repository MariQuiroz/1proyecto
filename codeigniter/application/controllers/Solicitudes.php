<?php defined('BASEPATH') OR exit('No direct script access allowed');

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
    
       /* public function confirmar($idPublicacion) {
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
*/
        
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
        
        
        /*public function crear($idPublicacion) {
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
    */
    /*public function crear($idPublicacion) {
        log_message('debug', '=== INICIO MÉTODO CREAR ===');
        $this->_verificar_rol(['lector']);
    
        $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);
        log_message('debug', 'Publicación obtenida: ' . $idPublicacion);
    
        if (!$publicacion || $publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
            log_message('debug', 'Publicación no disponible');
            $this->session->set_flashdata('error', 'La publicación no está disponible para préstamo.');
            redirect('publicaciones/index');
        }
    
        if ($this->input->post()) {
            log_message('debug', 'Procesando POST request');
            $this->db->trans_start();
    
            $idUsuario = $this->session->userdata('idUsuario');
            log_message('debug', 'ID Usuario solicitante: ' . $idUsuario);
            
            $resultado = $this->Solicitud_model->crear_solicitud($idUsuario, $idPublicacion);
            log_message('debug', 'Resultado crear_solicitud: ' . ($resultado ? 'true' : 'false'));
    
            if ($resultado) {
                log_message('debug', 'Solicitud creada exitosamente, procediendo a crear notificaciones');
                
                // Crear notificación para el lector
                $mensaje = "Se ha recibido tu solicitud de préstamo para la publicación '{$publicacion->titulo}'.";
                $this->Notificacion_model->crear_notificacion($idUsuario, $idPublicacion, 'solicitud_prestamo', $mensaje);
                
                // Obtener admins y encargados
                $admins_encargados = $this->Usuario_model->obtener_admins_encargados();
                log_message('debug', 'Número de admins/encargados encontrados: ' . count($admins_encargados));
    
                foreach ($admins_encargados as $usuario) {
                    log_message('debug', 'Creando notificación para admin/encargado ID: ' . $usuario->idUsuario);
                    $mensaje = "Nueva solicitud de préstamo para la publicación '{$publicacion->titulo}'.";
                    $this->Notificacion_model->crear_notificacion($usuario->idUsuario, $idPublicacion, 'nueva_solicitud', $mensaje);
                }
    
                $this->db->trans_complete();
                
                if ($this->db->trans_status() === FALSE) {
                    log_message('error', 'Error en la transacción');
                    $this->session->set_flashdata('error', 'Error al crear la solicitud. Por favor, intente de nuevo.');
                } else {
                    log_message('debug', 'Transacción completada exitosamente');
                    $this->session->set_flashdata('mensaje', 'Solicitud creada con éxito.');
                    redirect('solicitudes/mis_solicitudes');
                }
            }
        }
    
        log_message('debug', 'Cargando vista de creación');
        $data['publicacion'] = $publicacion;
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('solicitudes/crear', $data);
        $this->load->view('inc/footer');
        log_message('debug', '=== FIN MÉTODO CREAR ===');
    }*/
    
    /*public function confirmar($idPublicacion) {
        log_message('debug', 'INICIO Solicitudes::confirmar()');
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
            $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
            
            // Notificación para el lector
            $mensaje_lector = "Se ha recibido tu solicitud de préstamo para la publicación '{$publicacion->titulo}'.";
            $this->Notificacion_model->crear_notificacion(
                $idUsuario, 
                $idPublicacion, 
                NOTIFICACION_SOLICITUD_PRESTAMO, 
                $mensaje_lector
            );
    
            // Obtener solo encargados activos
            $encargados = $this->Usuario_model->obtener_encargados_activos();
            foreach ($encargados as $encargado) {
                $mensaje = "Nueva solicitud de préstamo para la publicación '{$publicacion->titulo}' del usuario '{$usuario->nombres} {$usuario->apellidoPaterno}'.";
                $this->Notificacion_model->crear_notificacion(
                    $encargado->idUsuario,
                    $idPublicacion,
                    NOTIFICACION_NUEVA_SOLICITUD,
                    $mensaje
                );
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
    }*/
    /**
 * Confirma una solicitud de préstamo de publicación
 * @param int $idPublicacion ID de la publicación solicitada
 * @return void
 */
/* public function confirmar($idPublicacion) {
    log_message('debug', "\n==== INICIO Solicitudes::confirmar() ====");
    $this->_verificar_rol(['lector']);
    
    $idUsuario = $this->session->userdata('idUsuario');
    log_message('debug', 'Usuario solicitante ID: ' . $idUsuario);
    
    // Obtener todas las publicaciones seleccionadas
    $publicaciones = $this->input->post('publicaciones') ?: array($idPublicacion);
    
    // Verificar disponibilidad de todas las publicaciones
    foreach ($publicaciones as $idPub) {
        $publicacion = $this->Publicacion_model->obtener_publicacion($idPub);
        if (!$publicacion || $publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
            log_message('debug', 'Publicación no disponible: ' . $idPub);
            $this->session->set_flashdata('error', 'Una o más publicaciones no están disponibles para préstamo.');
            redirect('publicaciones/index');
            return;
        }
    }
    
    $this->db->trans_start();
    
    try {
        // Crear la solicitud múltiple
        $resultado = $this->Solicitud_model->crear_solicitud_multiple($idUsuario, $publicaciones);
        
        if ($resultado) {
            $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
            log_message('debug', 'Creando solicitud múltiple para usuario: ' . $usuario->nombres);
            
            // Obtener detalles de las publicaciones
            $titulos_publicaciones = array();
            foreach ($publicaciones as $idPub) {
                $pub = $this->Publicacion_model->obtener_publicacion($idPub);
                $titulos_publicaciones[] = $pub->titulo;
            }
            
            // Notificación para el lector
            $mensaje_lector = "Se ha recibido tu solicitud de préstamo para las siguientes publicaciones: " . 
                            implode(", ", $titulos_publicaciones);
            $this->Notificacion_model->crear_notificacion(
                $idUsuario, 
                null, 
                NOTIFICACION_SOLICITUD_PRESTAMO, 
                $mensaje_lector
            );
    
            // Notificaciones para encargados
            $encargados = $this->Usuario_model->obtener_encargados_activos();
            log_message('debug', 'Encargados encontrados: ' . count($encargados));
            
            foreach ($encargados as $encargado) {
                log_message('debug', 'Procesando notificación para encargado ID: ' . $encargado->idUsuario);
                $mensaje = "Nueva solicitud múltiple de préstamo del usuario '{$usuario->nombres} {$usuario->apellidoPaterno}' " .
                          "para las publicaciones: " . implode(", ", $titulos_publicaciones);
                $this->Notificacion_model->crear_notificacion(
                    $encargado->idUsuario,
                    null,
                    NOTIFICACION_NUEVA_SOLICITUD,
                    $mensaje
                );
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Error en la transacción de base de datos');
            }
            
            $this->session->set_flashdata('mensaje', 'Solicitud de préstamo creada con éxito.');
        } else {
            throw new Exception('Error al crear la solicitud múltiple');
        }
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error al crear solicitud: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'Error al crear la solicitud.');
    }
    
    log_message('debug', "==== FIN Solicitudes::confirmar() ====\n");
    redirect('solicitudes/mis_solicitudes');
}*/
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
        
            $idEncargado = $this->session->userdata('idUsuario');
            $solicitud = $this->Solicitud_model->obtener_solicitud($idSolicitud);
            
            if (!$solicitud || $solicitud->estadoSolicitud != ESTADO_SOLICITUD_PENDIENTE) {
                $this->session->set_flashdata('error', 'La solicitud no es válida para ser aprobada.');
                redirect('solicitudes/pendientes');
                return;
            }
        
            $resultado = $this->Solicitud_model->aprobar_solicitud($idSolicitud, $idEncargado);
        
            if ($resultado) {
                // Crear una única notificación de aprobación para el lector
                $mensaje = "Tu solicitud de préstamo para la publicación '{$solicitud->titulo}' ha sido aprobada.";
                $this->Notificacion_model->crear_notificacion(
                    $solicitud->idUsuario,
                    $solicitud->idPublicacion,
                    NOTIFICACION_APROBACION_PRESTAMO,
                    $mensaje
                );
        
                // Generar el PDF y obtener la URL
                $pdfUrl = $this->generar_pdf_ficha_prestamo($resultado, $idSolicitud);
        
                $this->db->trans_complete();
        
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Error al aprobar la solicitud. Por favor, intente de nuevo.');
                } else {
                    $this->session->set_flashdata('mensaje', 'Solicitud aprobada y préstamo registrado con éxito.');
                    redirect('solicitudes/pendientes?pdf=' . urlencode($pdfUrl));
                }
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error al aprobar la solicitud.');
            }
        
            redirect('solicitudes/pendientes');
        }
        
        private function generar_pdf_ficha_prestamo($datos, $idSolicitud) {
            $this->load->library('pdf');
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
            // Configuración del documento
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Hemeroteca UMSS');
            $pdf->SetTitle('Ficha de Préstamo');
        
            // Configuración de márgenes
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
            $pdf->AddPage();
        
            // Datos del usuario
            $html = '
            <h1 style="text-align: center;">U.M.S.S. BIBLIOTECAS - EN SALA</h1>
            <h4 style="text-align: center;">FICHA DE PRÉSTAMO</h4>
            <br>
            <table cellpadding="5" style="width: 100%;">
                <tr><td width="30%"><strong>Nombre del Lector:</strong></td><td>' . $this->sanitize_for_pdf($datos['nombreCompletoLector']) . '</td></tr>
                <tr><td><strong>Carnet del Lector:</strong></td><td>' . $this->sanitize_for_pdf($datos['carnet']) . '</td></tr>
                <tr><td><strong>Profesión:</strong></td><td>' . $this->sanitize_for_pdf($datos['profesion']) . '</td></tr>
                <tr><td><strong>Fecha de Préstamo:</strong></td><td>' . $this->sanitize_for_pdf($datos['fechaPrestamo']) . '</td></tr>
                <tr><td><strong>Prestado por:</strong></td><td>' . $this->sanitize_for_pdf($datos['nombreCompletoEncargado']) . '</td></tr>
            </table>
            <br>
            <h4>Publicaciones Prestadas:</h4>
            <table cellpadding="5" style="width: 100%; border: 1px solid #000;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #000;">N°</th>
                        <th style="border: 1px solid #000;">Editorial</th>
                        <th style="border: 1px solid #000;">Fecha de Publicación</th>
                        <th style="border: 1px solid #000;">Ubicación</th>
                        <th style="border: 1px solid #000;">Título</th>
                    </tr>
                </thead>
                <tbody>';
        
            foreach ($datos['publicaciones'] as $index => $pub) {
                $html .= '
                    <tr>
                        <td style="border: 1px solid #000;">' . ($index + 1) . '</td>
                        <td style="border: 1px solid #000;">' . $this->sanitize_for_pdf($pub->nombreEditorial) . '</td>
                        <td style="border: 1px solid #000;">' . date('d/m/Y', strtotime($pub->fechaPublicacion)) . '</td>
                        <td style="border: 1px solid #000;">' . $this->sanitize_for_pdf($pub->ubicacionFisica) . '</td>
                        <td style="border: 1px solid #000;">' . $this->sanitize_for_pdf($pub->titulo) . '</td>
                    </tr>';
            }
        
            $html .= '</tbody></table>
            <br><br><br>
            <div style="text-align: center;">
                <p>_________________________</p>
                <p>Firma del Lector</p>
            </div>';
        
            $pdf->writeHTML($html, true, false, true, false, '');
            
            $pdfFileName = 'ficha_prestamo_' . $idSolicitud . '_' . time() . '.pdf';
            $pdfPath = FCPATH . 'uploads/' . $pdfFileName;
            
            $pdf->Output($pdfPath, 'F');
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