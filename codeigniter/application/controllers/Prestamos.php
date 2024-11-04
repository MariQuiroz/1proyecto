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
    
   
    public function finalizar($idPrestamo) {
        $this->_verificar_rol(['administrador', 'encargado']);
        
        // Verificar token CSRF
        if ($this->input->post()) {
            if (!$this->security->get_csrf_hash() || 
                $this->input->post('csrf_test_name') != $this->security->get_csrf_hash()) {
                $this->session->set_flashdata('error', 'Error de validación del formulario.');
                redirect('prestamos/activos');
                return;
            }
        }
    
        // Validar estado del préstamo
        $prestamo = $this->Prestamo_model->obtener_prestamo($idPrestamo);
        if (!$prestamo || $prestamo->estadoPrestamo != ESTADO_PRESTAMO_ACTIVO) {
            $this->session->set_flashdata('error', 'El préstamo no es válido para ser finalizado.');
            redirect('prestamos/activos');
            return;
        }
    
        $this->db->trans_start();
        
        try {
            $idEncargado = $this->session->userdata('idUsuario');
            $estadoDevolucion = $this->input->post('estadoDevolucion');
            
            // Validar estado de devolución
            if (!in_array($estadoDevolucion, [
                ESTADO_DEVOLUCION_BUENO, 
                ESTADO_DEVOLUCION_DAÑADO, 
                ESTADO_DEVOLUCION_PERDIDO
            ])) {
                $estadoDevolucion = ESTADO_DEVOLUCION_BUENO; // Valor por defecto si no es válido
            }
    
            // 1. Finalizar el préstamo y registrar estado de devolución
            $resultado = $this->Prestamo_model->finalizar_prestamo($idPrestamo, $idEncargado, $estadoDevolucion);
            
            if (!$resultado) {
                throw new Exception('Error al finalizar el préstamo en la base de datos');
            }
    
            // 2. Generar ficha de devolución
            $pdf_content = $this->generar_ficha_devolucion($idPrestamo);
            
            // 3. Enviar ficha por correo solo si se generó correctamente
            $envio_exitoso = false;
            if ($pdf_content) {
                $envio_exitoso = $this->enviar_ficha_por_correo($idPrestamo, $pdf_content);
            }
    
            // 4. Actualizar estado de la publicación siempre a disponible
            $actualizacion_publicacion = $this->Publicacion_model->cambiar_estado_publicacion(
                $prestamo->idPublicacion, 
                ESTADO_PUBLICACION_DISPONIBLE
            );
            
            if (!$actualizacion_publicacion) {
                throw new Exception('Error al actualizar el estado de la publicación');
            }
    
            // 5. Crear notificación de devolución
            $texto_estado = 'en estado ' . strtolower($estadoDevolucion);
            
            $mensaje = "El préstamo de la publicación '{$prestamo->titulo}' ha sido finalizado. " . 
                      "La publicación fue registrada {$texto_estado}.";
            
            $this->Notificacion_model->crear_notificacion(
                $prestamo->idUsuario,
                $prestamo->idPublicacion,
                NOTIFICACION_DEVOLUCION,
                $mensaje
            );
    
            // 6. Notificar a usuarios interesados que la publicación está disponible
            $this->_notificar_disponibilidad($prestamo->idPublicacion);
    
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Error en la transacción de la base de datos');
            }
    
            // Mensaje de éxito
            $mensaje = 'Préstamo finalizado con éxito.';
            if ($envio_exitoso) {
                $mensaje .= ' La ficha de devolución ha sido enviada por correo electrónico al lector.';
            } else {
                $mensaje .= ' Sin embargo, hubo un problema al enviar el correo electrónico.';
                log_message('error', 'Error al enviar correo de ficha de devolución para préstamo ID: ' . $idPrestamo);
            }
            
            $this->session->set_flashdata('mensaje', $mensaje);
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error en finalizar préstamo ID ' . $idPrestamo . ': ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Hubo un error al procesar la devolución: ' . $e->getMessage());
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
    public function devolver_multiple() {
        $this->_verificar_rol(['administrador', 'encargado']);
        
        $idUsuario = $this->input->get('idUsuario');
        $data['prestamos'] = $this->Prestamo_model->obtener_prestamos_activos_usuario($idUsuario);
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('prestamos/devolucion_multiple', $data);
        $this->load->view('inc/footer');
    }
    public function procesar_devolucion_multiple() {
        $this->_verificar_rol(['administrador', 'encargado']);

        $prestamos = $this->input->post('prestamos');
        $estados = $this->input->post('estado_devolucion');
        $observaciones = $this->input->post('observaciones');

        if (empty($prestamos)) {
            $this->session->set_flashdata('error', 'Debe seleccionar al menos un préstamo para devolver');
            redirect('prestamos/devolver_multiple');
            return;
        }

        $devoluciones = [];
        foreach ($prestamos as $idPrestamo) {
            if (!isset($estados[$idPrestamo]) || empty($estados[$idPrestamo])) {
                $this->session->set_flashdata('error', 'Debe especificar el estado de devolución para todas las publicaciones');
                redirect('prestamos/devolver_multiple');
                return;
            }

            $devoluciones[$idPrestamo] = [
                'estado' => $estados[$idPrestamo],
                'observaciones' => $observaciones[$idPrestamo] ?? ''
            ];
        }

        $resultado = $this->Prestamo_model->finalizar_prestamo_multiple(
            $devoluciones,
            $this->session->userdata('idUsuario')
        );

        if ($resultado['success']) {
            // Generar comprobantes de devolución
            $comprobantes = [];
            foreach ($prestamos as $idPrestamo) {
                $pdf_content = $this->generar_ficha_devolucion($idPrestamo);
                if ($pdf_content) {
                    $comprobantes[] = $pdf_content;
                }
                // Enviar correo con comprobante
                $this->enviar_ficha_por_correo($idPrestamo, $pdf_content);
            }

            $this->session->set_flashdata('mensaje', 'Devoluciones procesadas correctamente');
            
            // Si hay comprobantes, redirigir con parámetros para descargarlos
            if (!empty($comprobantes)) {
                $ids = implode(',', array_keys($devoluciones));
                redirect('prestamos/descargar_comprobantes/' . $ids);
            } else {
                redirect('prestamos/activos');
            }
        } else {
            $this->session->set_flashdata('error', $resultado['message']);
            redirect('prestamos/devolver_multiple');
        }
    }

    public function descargar_comprobantes($ids) {
        $this->_verificar_rol(['administrador', 'encargado']);
        
        // Crear PDF consolidado con todos los comprobantes
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Hemeroteca UMSS');
        $pdf->SetTitle('Comprobantes de Devolución');
        
        // Configuración básica del PDF
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Hemeroteca UMSS', 'Comprobantes de Devolución');
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        $idPrestamos = explode(',', $ids);
        foreach ($idPrestamos as $idPrestamo) {
            $pdf->AddPage();
            
            $datos_prestamo = $this->Prestamo_model->obtener_datos_ficha_devolucion($idPrestamo);
            if ($datos_prestamo) {
                // Agregar contenido al PDF
                $html = $this->load->view('prestamos/plantilla_comprobante', $datos_prestamo, true);
                $pdf->writeHTML($html, true, false, true, false, '');
            }
        }
        
        // Generar y descargar el PDF
        $pdf->Output('comprobantes_devolucion.pdf', 'D');
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
        <h1 style="text-align: center;">U.M.S.S. BIBLIOTECAS - COMPROBANTE DE DEVOLUCIÓN</h1>
        
        <h3>Datos del Lector</h3>
        <table border="1" cellpadding="5">
            <tr>
                <td width="30%"><strong>Nombre:</strong></td>
                <td width="70%">'.$datos_prestamo['nombreLector'].' '.$datos_prestamo['apellidoLector'].'</td>
            </tr>
            <tr>
                <td><strong>Carnet:</strong></td>
                <td>'.$datos_prestamo['carnet'].'</td>
            </tr>
        </table>
    
        <h3>Datos del Préstamo</h3>
        <table border="1" cellpadding="5">
            <tr>
                <td width="30%"><strong>Fecha de préstamo:</strong></td>
                <td width="70%">'.$datos_prestamo['fechaPrestamo'].'</td>
            </tr>
            <tr>
                <td><strong>Fecha de devolución:</strong></td>
                <td>'.date('Y-m-d H:i:s').'</td>
            </tr>
            <tr>
                <td><strong>Estado de devolución:</strong></td>
                <td>'.$datos_prestamo['estadoDevolucion'].'</td>
            </tr>
        </table>
    
        <h3>Datos de la Publicación</h3>
        <table border="1" cellpadding="5">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th>Título</th>
                    <th>Editorial</th>
                    <th>Tipo</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$datos_prestamo['titulo'].'</td>
                    <td>'.$datos_prestamo['nombreEditorial'].'</td>
                    <td>'.$datos_prestamo['nombreTipo'].'</td>
                    <td>'.$datos_prestamo['ubicacionFisica'].'</td>
                </tr>
            </tbody>
        </table>
    
        <h3>Encargado de la Devolución</h3>
        <table border="1" cellpadding="5">
            <tr>
                <td width="30%"><strong>Nombre:</strong></td>
                <td width="70%">'.$datos_prestamo['nombreEncargado'].' '.$datos_prestamo['apellidoEncargado'].'</td>
            </tr>
        </table>
    
        <div style="text-align: center; margin-top: 50px;">
            <table width="100%">
                <tr>
                    <td width="50%" style="text-align: center;">
                        ________________________<br>
                        Firma del Lector
                    </td>
                    <td width="50%" style="text-align: center;">
                        ________________________<br>
                        Firma del Encargado
                    </td>
                </tr>
            </table>
        </div>';
    
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
            <style>
                table { 
                    border-collapse: collapse; 
                    width: 100%; 
                    margin: 10px 0; 
                }
                th, td { 
                    border: 1px solid #ddd; 
                    padding: 8px; 
                    text-align: left; 
                }
                th { 
                    background-color: #f2f2f2; 
                }
            </style>
        </head>
        <body>
            <h2>Comprobante de Devolución - Hemeroteca UMSS</h2>
            <p>Estimado/a {$datos_prestamo['nombreLector']} {$datos_prestamo['apellidoLector']},</p>
            <p>Se confirma la devolución de la siguiente publicación:</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Editorial</th>
                        <th>Tipo</th>
                        <th>Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{$datos_prestamo['titulo']}</td>
                        <td>{$datos_prestamo['nombreEditorial']}</td>
                        <td>{$datos_prestamo['nombreTipo']}</td>
                        <td>{$datos_prestamo['ubicacionFisica']}</td>
                    </tr>
                </tbody>
            </table>
    
            <p><strong>Fecha de devolución:</strong> " . date('Y-m-d H:i:s') . "</p>
            <p><strong>Estado de devolución:</strong> {$datos_prestamo['estadoDevolucion']}</p>
            
            <p>Se adjunta el comprobante de devolución en formato PDF.</p>
            <p>Gracias por utilizar nuestros servicios.</p>
            <p>Atentamente,<br>Hemeroteca UMSS</p>
        </body>
        </html>";
    
        $this->email->message($mensaje);
        $this->email->attach($pdf_content, 'attachment', 'comprobante_devolucion.pdf', 'application/pdf');
    
        if ($this->email->send()) {
            return true;
        } else {
            log_message('error', 'Error al enviar correo de devolución: ' . $this->email->print_debugger());
            return false;
        }
    }
}