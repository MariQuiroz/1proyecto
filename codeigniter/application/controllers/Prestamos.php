<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Prestamo_model');
        $this->load->model('Solicitud_model');
        $this->load->model('Publicacion_model');
        $this->load->model('Notificacion_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('pdf');
        $this->load->library('email');
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
    public function iniciar($idSolicitud) {
        $this->_verificar_rol(['administrador', 'encargado']);
    
        $this->db->trans_start();
    
        $solicitud = $this->Solicitud_model->obtener_solicitud($idSolicitud);
        if (!$solicitud || $solicitud->estadoSolicitud != ESTADO_SOLICITUD_APROBADA) {
            $this->session->set_flashdata('error', 'La solicitud no es válida para iniciar un préstamo.');
            redirect('solicitudes/pendientes');
        }
    
        $idEncargado = $this->session->userdata('idUsuario');
        $resultado = $this->Prestamo_model->iniciar_prestamo($idSolicitud, $idEncargado);
    
        if ($resultado) {
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Hubo un error al iniciar el préstamo. Por favor, intente de nuevo.');
            } else {
                $this->session->set_flashdata('mensaje', 'Préstamo iniciado con éxito.');
                
                // Redirigir a la página de préstamos activos o donde sea apropiado
                redirect('prestamos/activos');
            }
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'No se pudo iniciar el préstamo. Verifique la disponibilidad de la publicación.');
        }
        
        redirect('prestamos/activos');
    }
    
   
/*public function finalizar($idPrestamo) {
    $this->_verificar_rol(['administrador', 'encargado']);
    
    $prestamo = $this->Prestamo_model->obtener_prestamo($idPrestamo);
    if (!$prestamo || $prestamo->estadoPrestamo != ESTADO_PRESTAMO_ACTIVO) {
        $this->session->set_flashdata('error', 'El préstamo no es válido para ser finalizado.');
        redirect('prestamos/activos');
        return;
    }
    
    $this->db->trans_start();
    
    $idEncargado = $this->session->userdata('idUsuario');
    $resultado = $this->Prestamo_model->finalizar_prestamo($idPrestamo, $idEncargado);
    
    if ($resultado) {
        // Generar la ficha de devolución y enviar por correo
        $pdf_content = $this->generar_ficha_devolucion($idPrestamo);
        $envio_exitoso = $this->enviar_ficha_por_correo($idPrestamo, $pdf_content);
        
        // Crear notificación de devolución
        $mensaje = "El préstamo de la publicación '{$prestamo->titulo}' ha sido finalizado.";
        $this->Notificacion_model->crear_notificacion($prestamo->idUsuario, $prestamo->idPublicacion, NOTIFICACION_DEVOLUCION, $mensaje);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Hubo un error al finalizar el préstamo. Por favor, intente de nuevo.');
        } else {
            $mensaje = 'Préstamo finalizado con éxito.';
            if ($envio_exitoso) {
                $mensaje .= ' La ficha de devolución ha sido enviada por correo electrónico al lector.';
            } else {
                $mensaje .= ' Sin embargo, hubo un problema al enviar el correo electrónico.';
            }
            $this->session->set_flashdata('mensaje', $mensaje);
        }
    } else {
        $this->db->trans_rollback();
        $this->session->set_flashdata('error', 'No se pudo finalizar el préstamo. Por favor, intente de nuevo.');
    }
    
    redirect('prestamos/activos');
}*/
public function finalizar($idPrestamo) {
    $this->_verificar_rol(['administrador', 'encargado']);
    
    $prestamo = $this->Prestamo_model->obtener_prestamo($idPrestamo);
    if (!$prestamo || $prestamo->estadoPrestamo != ESTADO_PRESTAMO_ACTIVO) {
        $this->session->set_flashdata('error', 'El préstamo no es válido para ser finalizado.');
        redirect('prestamos/activos');
        return;
    }
    
    $this->db->trans_start();
    
    $idEncargado = $this->session->userdata('idUsuario');
    $resultado = $this->Prestamo_model->finalizar_prestamo($idPrestamo, $idEncargado);
    
    if ($resultado) {
        // Generar la ficha de devolución y enviar por correo
        $pdf_content = $this->generar_ficha_devolucion($idPrestamo);
        $envio_exitoso = $this->enviar_ficha_por_correo($idPrestamo, $pdf_content);
        
        // Crear notificación de devolución
        $mensaje = "El préstamo de la publicación '{$prestamo->titulo}' ha sido finalizado.";
        $this->Notificacion_model->crear_notificacion($prestamo->idUsuario, $prestamo->idPublicacion, NOTIFICACION_DEVOLUCION, $mensaje);
        
        // Actualizar el estado de la publicación a disponible
        $this->Publicacion_model->cambiar_estado_publicacion($prestamo->idPublicacion, ESTADO_PUBLICACION_DISPONIBLE);
        
        // Notificar a los usuarios interesados
        $this->_notificar_disponibilidad($prestamo->idPublicacion);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Hubo un error al finalizar el préstamo. Por favor, intente de nuevo.');
        } else {
            $mensaje = 'Préstamo finalizado con éxito.';
            if ($envio_exitoso) {
                $mensaje .= ' La ficha de devolución ha sido enviada por correo electrónico al lector.';
            } else {
                $mensaje .= ' Sin embargo, hubo un problema al enviar el correo electrónico.';
            }
            $this->session->set_flashdata('mensaje', $mensaje);
        }
    } else {
        $this->db->trans_rollback();
        $this->session->set_flashdata('error', 'No se pudo finalizar el préstamo. Por favor, intente de nuevo.');
    }
    
    redirect('prestamos/activos');
}

private function _notificar_disponibilidad($idPublicacion) {
    // Obtener la publicación
    $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);
    if (!$publicacion) {
        log_message('error', 'Publicación no encontrada para notificación de disponibilidad. ID: ' . $idPublicacion);
        return;
    }

    // Obtener usuarios interesados
    $usuarios_interesados = $this->Notificacion_model->obtener_usuarios_interesados($idPublicacion);

    foreach ($usuarios_interesados as $usuario) {
        $preferencias = $this->Notificacion_model->obtener_preferencias($usuario->idUsuario);
        
        if ($preferencias) {
            if ($preferencias->notificarSistema) {
                $this->Notificacion_model->crear_notificacion(
                    $usuario->idUsuario,
                    $idPublicacion,
                    NOTIFICACION_DISPONIBILIDAD,
                    'La publicación "' . $publicacion->titulo . '" ya está disponible.'
                );
                log_message('info', 'Notificación del sistema enviada al usuario ID: ' . $usuario->idUsuario);
            }

            if ($preferencias->notificarEmail) {
                $envio_exitoso = $this->_enviar_email_disponibilidad($usuario->idUsuario, $publicacion);
                if ($envio_exitoso) {
                    log_message('info', 'Email de notificación enviado al usuario ID: ' . $usuario->idUsuario);
                } else {
                    log_message('error', 'Fallo al enviar notificación por email al usuario ID: ' . $usuario->idUsuario);
                }
            }
        }
    }
}

private function _enviar_email_disponibilidad($idUsuario, $publicacion) {
    $usuario = $this->Usuario_model->obtener_usuario($idUsuario);

    if (!$usuario) {
        log_message('error', 'Usuario no encontrado para notificación de disponibilidad. ID: ' . $idUsuario);
        return false;
    }

    $this->load->library('email');

    $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'smtp.gmail.com';
    $config['smtp_port'] = 587;
    $config['smtp_user'] = 'quirozmolinamaritza@gmail.com';
    $config['smtp_pass'] = 'zdmk qkfw wgdf lshq';
    $config['smtp_crypto'] = 'tls';
    $config['mailtype'] = 'html';
    $config['charset'] = 'utf-8';
    $config['newline'] = "\r\n";

    $this->email->initialize($config);

    $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
    $this->email->to($usuario->email);
    $this->email->subject('Publicación Disponible - Hemeroteca UMSS');

    $mensaje = "
    <html>
    <head>
        <title>Publicación Disponible</title>
    </head>
    <body>
        <h2>Publicación Disponible en la Hemeroteca UMSS</h2>
        <p>Estimado/a {$usuario->nombres} {$usuario->apellidoPaterno},</p>
        <p>La publicación '{$publicacion->titulo}' que solicitaste está ahora disponible en la hemeroteca.</p>
        <p>Puedes pasar a solicitarla cuando lo desees.</p>
        <p>Gracias por utilizar nuestros servicios.</p>
        <p>Atentamente,<br>Hemeroteca UMSS</p>
    </body>
    </html>
    ";

    $this->email->message($mensaje);

    if ($this->email->send()) {
        return true;
    } else {
        log_message('error', 'Error al enviar correo de notificación de disponibilidad: ' . $this->email->print_debugger());
        return false;
    }
}

private function generar_ficha_devolucion($idPrestamo) {
    $datos_prestamo = $this->Prestamo_model->obtener_datos_ficha_devolucion($idPrestamo);

    if (!$datos_prestamo) {
        return false;
    }

    $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Hemeroteca UMSS');
    $pdf->SetTitle('Ficha de Devolución');
    $pdf->SetSubject('Comprobante de Devolución');
    $pdf->SetKeywords('UMSS, Biblioteca, Devolución');

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Hemeroteca UMSS', 'Comprobante de Devolución', array(0,64,255), array(0,64,128));
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 12);

    $html = '
    <h1>U.M.S.S. BIBLIOTECAS - COMPROBANTE DE DEVOLUCIÓN</h1>
    <p><strong>Título de la publicación:</strong> '.$datos_prestamo['titulo'].'</p>
    <p><strong>Lector:</strong> '.$datos_prestamo['nombreLector'].' '.$datos_prestamo['apellidoLector'].'</p>
    <p><strong>Fecha de préstamo:</strong> '.$datos_prestamo['fechaPrestamo'].'</p>
    <p><strong>Fecha de devolución:</strong> '.date('Y-m-d H:i:s').'</p>
    <p><strong>Encargado que recibió:</strong> '.$datos_prestamo['nombreEncargado'].' '.$datos_prestamo['apellidoEncargado'].'</p>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');

    return $pdf->Output('ficha_devolucion.pdf', 'S');
}

private function enviar_ficha_por_correo($idPrestamo, $pdf_content) {
    $datos_prestamo = $this->Prestamo_model->obtener_datos_ficha_devolucion($idPrestamo);

    if (!$datos_prestamo) {
        return false;
    }

    $this->load->library('email');

    $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'smtp.gmail.com';
    $config['smtp_port'] = 587;
    $config['smtp_user'] = 'quirozmolinamaritza@gmail.com';
    $config['smtp_pass'] = 'zdmk qkfw wgdf lshq';
    $config['smtp_crypto'] = 'tls';
    $config['mailtype'] = 'html';
    $config['charset'] = 'utf-8';
    $config['newline'] = "\r\n";

    $this->email->initialize($config);

    $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
    $this->email->to($datos_prestamo['email']);
    $this->email->subject('Comprobante de Devolución - Hemeroteca UMSS');

    $mensaje = "
    <html>
    <head>
        <title>Comprobante de Devolución</title>
    </head>
    <body>
        <h2>Comprobante de Devolución - Hemeroteca UMSS</h2>
        <p>Estimado/a {$datos_prestamo['nombreLector']} {$datos_prestamo['apellidoLector']},</p>
        <p>Adjunto encontrará el comprobante de devolución de la publicación '{$datos_prestamo['titulo']}'.</p>
        <p>Gracias por utilizar nuestros servicios.</p>
        <p>Atentamente,<br>Hemeroteca UMSS</p>
    </body>
    </html>
    ";

    $this->email->message($mensaje);
    $this->email->attach($pdf_content, 'attachment', 'comprobante_devolucion.pdf', 'application/pdf');

    if ($this->email->send()) {
        return true;
    } else {
        log_message('error', 'Error al enviar correo de devolución: ' . $this->email->print_debugger());
        return false;
    }
}

    public function activos() {
        $this->_verificar_rol(['administrador', 'encargado']);
  
        $data['prestamos'] = $this->Prestamo_model->obtener_prestamos_activos();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('prestamos/activos', $data);
        $this->load->view('inc/footer');
    }

    public function historial() {
        $this->_verificar_rol(['administrador', 'encargado']);
        $data['prestamos'] = $this->Prestamo_model->obtener_historial_prestamos();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('prestamos/historial', $data);
        $this->load->view('inc/footer');
    }

    public function mis_prestamos() {
        $this->_verificar_rol(['lector']);
        $idUsuario = $this->session->userdata('idUsuario');
        $data['prestamos'] = $this->Prestamo_model->obtener_prestamos_usuario($idUsuario);
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('prestamos/mis_prestamos', $data);
        $this->load->view('inc/footer');
    }
}