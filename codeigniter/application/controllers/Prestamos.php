<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Prestamo_model');
        $this->load->model('Solicitud_model');
        $this->load->model('Publicacion_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('pdf');
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
    
    /*public function iniciar($idSolicitud) {
        $this->_verificar_rol(['administrador', 'encargado']);
    
        $solicitud = $this->Solicitud_model->obtener_solicitud($idSolicitud);
        if (!$solicitud || $solicitud->estadoSolicitud != ESTADO_SOLICITUD_APROBADA) {
            $this->session->set_flashdata('error', 'La solicitud no es válida para iniciar un préstamo.');
            redirect('solicitudes/pendientes');
        }
    
        $this->db->trans_start();
    
        $idEncargado = $this->session->userdata('idUsuario');
        $resultado = $this->Prestamo_model->iniciar_prestamo($idSolicitud, $idEncargado);
    
        if ($resultado) {
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Hubo un error al iniciar el préstamo. Por favor, intente de nuevo.');
            } else {
                $this->session->set_flashdata('mensaje', 'Préstamo iniciado con éxito.');
                
                // Obtener los datos para la ficha de préstamo
                $ficha_prestamo = $this->Prestamo_model->obtener_datos_ficha_prestamo($resultado);
                
                // Cargar la vista de la ficha de préstamo
                $this->load->view('prestamos/ficha_prestamo', $ficha_prestamo);
                return; // Detenemos la ejecución aquí para mostrar la ficha
            }
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'No se pudo iniciar el préstamo. Verifique la disponibilidad de la publicación.');
        }
    
        redirect('prestamos/activos');
    }
    
    public function generar_ficha($idPrestamo) {
        $this->_verificar_rol(['administrador', 'encargado']);

        $ficha_prestamo = $this->generar_ficha_prestamo($idPrestamo);
        $this->load->view('prestamos/ficha_prestamo', $ficha_prestamo);
    }

    private function generar_ficha_prestamo($idPrestamo) {
        $prestamo = $this->Prestamo_model->obtener_prestamo_detallado($idPrestamo);
        
        $data = [
            'titulo' => $prestamo->titulo,
            'fecha_publicacion' => $prestamo->fechaPublicacion,
            'ubicacion' => $prestamo->ubicacionFisica,
            'carnet_lector' => $prestamo->carnet,
            'profesion' => $prestamo->profesion,
            'fecha_prestamo' => $prestamo->fechaPrestamo,
            'prestado_por' => $prestamo->nombres_encargado . ' ' . $prestamo->apellidoPaterno_encargado,
          
        ];
        
        return $data;
    }*/
/*public function finalizar($idPrestamo) {
    $this->_verificar_rol(['administrador', 'encargado']);

    $prestamo = $this->Prestamo_model->obtener_prestamo($idPrestamo);
    if (!$prestamo || $prestamo->estadoPrestamo != ESTADO_PRESTAMO_ACTIVO) {
        $this->session->set_flashdata('error', 'El préstamo no es válido para ser finalizado.');
        redirect('prestamos/activos');
    }

    $idEncargado = $this->session->userdata('idUsuario');
    $resultado = $this->Prestamo_model->finalizar_prestamo($idPrestamo, $idEncargado);

    if ($resultado) {
        $this->session->set_flashdata('mensaje', 'Préstamo finalizado con éxito.');
    } else {
        $this->session->set_flashdata('error', 'No se pudo finalizar el préstamo. Por favor, intente de nuevo.');
    }

    redirect('prestamos/activos');
}
*/
/*public function finalizar($idPrestamo) {
    $this->_verificar_rol(['administrador', 'encargado']);

    $prestamo = $this->Prestamo_model->obtener_prestamo($idPrestamo);
    if (!$prestamo || $prestamo->estadoPrestamo != ESTADO_PRESTAMO_ACTIVO) {
        $this->session->set_flashdata('error', 'El préstamo no es válido para ser finalizado.');
        redirect('prestamos/activos');
        return;
    }

    $idEncargado = $this->session->userdata('idUsuario');
    $resultado = $this->Prestamo_model->finalizar_prestamo($idPrestamo, $idEncargado);

    if ($resultado) {
        // Obtener datos para la ficha de devolución
        $ficha_devolucion = $this->Prestamo_model->obtener_datos_ficha_devolucion($idPrestamo);
        
        // Generar la ficha de devolución
        $html_ficha = $this->load->view('prestamos/ficha_devolucion', $ficha_devolucion, TRUE);
        
        // Enviar correo electrónico con la ficha de devolución
        $correo_enviado = $this->_enviar_correo_devolucion($ficha_devolucion['email'], $html_ficha);

        if ($correo_enviado) {
            $this->session->set_flashdata('mensaje', 'Préstamo finalizado con éxito y ficha de devolución enviada por correo.');

        } else {
            $this->session->set_flashdata('mensaje', 'Préstamo finalizado con éxito, pero hubo un problema al enviar el correo.');
        }
    } else {
        $this->session->set_flashdata('error', 'No se pudo finalizar el préstamo. Por favor, intente de nuevo.');
    }

    // Asegurarse de que la redirección ocurra después de establecer el mensaje flash
    redirect('prestamos/activos');
}
*/
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
/*private function _enviar_correo_devolucion($email, $html_ficha) {
    $this->load->library('email');

    $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'smtp.gmail.com';
    $config['smtp_port'] = 587;
    $config['smtp_user'] = 'quirozmolinamaritza@gmail.com'; // Reemplaza con tu correo
    $config['smtp_pass'] = 'zdmk qkfw wgdf lshq'; // Reemplaza con tu contraseña
    $config['smtp_crypto'] = 'tls';
    $config['mailtype'] = 'html';
    $config['charset'] = 'utf-8';
    $config['newline'] = "\r\n";

    $this->email->initialize($config);

    $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
    $this->email->to($email);
    $this->email->subject('Comprobante de Devolución - Hemeroteca UMSS');
    $this->email->message($html_ficha);

    if ($this->email->send()) {
        return true;
    } else {
        log_message('error', 'Error al enviar correo de devolución: ' . $this->email->print_debugger());
        return false;
    }
}
*/
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