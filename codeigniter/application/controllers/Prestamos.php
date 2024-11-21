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
    
    public function finalizar() {
        $this->_verificar_rol(['administrador', 'encargado']);
        
        $idPrestamo = $this->input->post('idPrestamo');
        $estadoDevolucion = $this->input->post('estadoDevolucion');
        
        $this->db->trans_start();
        
        try {
            // Obtener información completa del préstamo
            $prestamo = $this->Prestamo_model->obtener_prestamo_detallado($idPrestamo);
            if (!$prestamo || $prestamo->estadoPrestamo != ESTADO_PRESTAMO_ACTIVO) {
                throw new Exception('El préstamo no es válido para ser finalizado.');
            }
    
            $idEncargado = $this->session->userdata('idUsuario');
            
            // Finalizar el préstamo
            $resultado = $this->Prestamo_model->finalizar_prestamo($idPrestamo, $idEncargado, $estadoDevolucion);
            
            if ($resultado) {
                // Generar ficha de devolución y enviar por correo
            $pdf_content = $this->generar_ficha_devolucion($idPrestamo);
            $envio_exitoso = false;
            $pdf_path = '';
            
            if ($pdf_content) {
                // Guardar PDF temporalmente
                $filename = 'ficha_devolucion_' . $idPrestamo . '_' . date('YmdHis') . '.pdf';
                $filepath = FCPATH . 'uploads/temp/' . $filename;
                
                // Asegurar que el directorio existe
                if (!file_exists(FCPATH . 'uploads/temp/')) {
                    mkdir(FCPATH . 'uploads/temp/', 0777, true);
                }
                
                file_put_contents($filepath, $pdf_content);
                $pdf_path = base_url('uploads/temp/' . $filename);
                
                // Enviar por correo electrónico
                $envio_exitoso = $this->enviar_ficha_por_correo($idPrestamo, $pdf_content);
            }

                // Cambiar estado de la publicación a disponible
                $this->Publicacion_model->cambiar_estado_publicacion(
                    $prestamo->idPublicacion, 
                    ESTADO_PUBLICACION_DISPONIBLE
                );
                
                // Notificar a usuario que devolvió
                $this->Notificacion_model->crear_notificacion(
                    $prestamo->idUsuario,
                    $prestamo->idPublicacion,
                    NOTIFICACION_DEVOLUCION,
                    sprintf(
                        'Has devuelto la publicación "%s". Estado: %s',
                        $prestamo->titulo,
                        $this->_obtener_texto_estado_devolucion($estadoDevolucion)
                    )
                );
    
                 // Obtener usuarios interesados EXCLUYENDO al usuario que devolvió
            $usuarios_interesados = $this->Notificacion_model->obtener_usuarios_interesados_excepto(
                $prestamo->idPublicacion,
                $prestamo->idUsuario
            );

            foreach ($usuarios_interesados as $usuario) {
                // Crear notificación de disponibilidad solo para otros usuarios interesados
                $this->Notificacion_model->crear_notificacion(
                    $usuario->idUsuario,
                    $prestamo->idPublicacion,
                    NOTIFICACION_DISPONIBILIDAD,
                    sprintf(
                        'La publicación "%s" ya está disponible para préstamo.',
                        $prestamo->titulo
                    )
                );

    
                    // Actualizar estado del interés a notificado
                    $this->Notificacion_model->actualizar_estado_interes(
                        $usuario->idUsuario,
                        $prestamo->idPublicacion,
                        ESTADO_INTERES_NOTIFICADO
                    );
                }
                
                
                 // Preparar mensaje de respuesta
            $mensaje = 'Préstamo finalizado con éxito. ';
            if (!empty($usuarios_interesados)) {
                $mensaje .= sprintf('Se ha notificado a %d usuario(s) interesado(s). ', count($usuarios_interesados));
            }
            if ($pdf_content) {
                $mensaje .= 'Se ha generado la ficha de devolución. ';
                if ($envio_exitoso) {
                    $mensaje .= 'La ficha ha sido enviada por correo electrónico al lector.';
                } else {
                    $mensaje .= 'Hubo un problema al enviar el correo electrónico.';
                }
            }
                
                $this->session->set_flashdata('mensaje', $mensaje);
                
                // Si se generó PDF, guardarlo en sesión para descarga
                // Guardar ruta del PDF en sesión para descarga
            if ($pdf_path) {
                $this->session->set_flashdata('pdf_path', $pdf_path);
            }
                
            } else {
                throw new Exception('Error al finalizar el préstamo.');
            }
    
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Error en la transacción de la base de datos.');
            }
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al finalizar préstamo: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al procesar la devolución: ' . $e->getMessage());
        }
    
        redirect('prestamos/activos');
    }
    
    private function _obtener_texto_estado_devolucion($estado) {
        switch ($estado) {
            case ESTADO_DEVOLUCION_BUENO:
                return 'Buen estado';
            case ESTADO_DEVOLUCION_DAÑADO:
                return 'Dañado';
            case ESTADO_DEVOLUCION_PERDIDO:
                return 'Perdido';
            default:
                return 'Estado desconocido';
        }
    }
   /* private function _obtener_texto_estado_devolucion($estado) {
        $estados = [
            ESTADO_DEVOLUCION_BUENO => 'Buen estado',
            ESTADO_DEVOLUCION_DAÑADO => 'Con daños',
            ESTADO_DEVOLUCION_PERDIDO => 'Perdido'
        ];
        return isset($estados[$estado]) ? $estados[$estado] : 'Estado desconocido';
    }*/
    private function _procesar_documentacion_devolucion($idPrestamo) {
        $resultado = [
            'pdf_generado' => false,
            'email_enviado' => false,
            'pdf_path' => null
        ];
    
        // Generar PDF de ficha de devolución
        $pdf_content = $this->generar_ficha_devolucion($idPrestamo);
        
        if ($pdf_content) {
            $resultado['pdf_generado'] = true;
            
            // Guardar PDF temporalmente
            $filename = 'ficha_devolucion_' . $idPrestamo . '_' . date('YmdHis') . '.pdf';
            $filepath = FCPATH . 'uploads/temp/' . $filename;
            
            if (file_put_contents($filepath, $pdf_content)) {
                $resultado['pdf_path'] = base_url('uploads/temp/' . $filename);
                
                // Enviar por correo si está configurado
                $resultado['email_enviado'] = $this->enviar_ficha_por_correo($idPrestamo, $pdf_content);
            }
        }
    
        return $resultado;
    }
    
    private function _actualizar_estado_y_notificar($prestamo) {
        // Cambiar estado de la publicación a disponible
        $this->Publicacion_model->cambiar_estado_publicacion(
            $prestamo->idPublicacion, 
            ESTADO_PUBLICACION_DISPONIBLE
        );
    
        // Obtener usuarios interesados con sus preferencias
        $usuarios_interesados = $this->Notificacion_model->obtener_usuarios_interesados_con_preferencias($prestamo->idPublicacion);
        
        foreach ($usuarios_interesados as $usuario) {
            if ($usuario->notificarSistema) {
                // Notificación en el sistema
                $this->Notificacion_model->crear_notificacion(
                    $usuario->idUsuario,
                    $prestamo->idPublicacion,
                    NOTIFICACION_DISPONIBILIDAD,
                    sprintf(
                        'La publicación "%s" está nuevamente disponible para préstamo.',
                        $prestamo->titulo
                    )
                );
            }
    
            if ($usuario->notificarEmail) {
                // Notificación por correo
                $this->_enviar_email_disponibilidad(
                    $usuario->idUsuario,
                    $prestamo->idPublicacion
                );
            }
        }
    
        // Notificar al usuario que devolvió la publicación
        $this->Notificacion_model->crear_notificacion(
            $prestamo->idUsuario,
            $prestamo->idPublicacion,
            NOTIFICACION_DEVOLUCION,
            sprintf(
                'Has devuelto la publicación "%s". Estado: %s',
                $prestamo->titulo,
                $this->_obtener_texto_estado_devolucion($prestamo->estadoDevolucion)
            )
        );
    }
    
    private function _registrar_historial_devolucion($prestamo, $estadoDevolucion) {

        // Establecer la zona horaria correcta para Bolivia
        date_default_timezone_set('America/La_Paz');
        
        // Crear objeto DateTime para manejar las fechas correctamente
        $fechaHoraActual = new DateTime();

        $datos_historial = [
            'idPrestamo' => $prestamo->idPrestamo,
            'idPublicacion' => $prestamo->idPublicacion,
            'idUsuario' => $prestamo->idUsuario,
            'fechaDevolucion' => $fechaHoraActual->format('Y-m-d H:i:s'),
            'estadoDevolucion' => $estadoDevolucion,
            'idEncargado' => $this->session->userdata('idUsuario'),
            'observaciones' => sprintf(
                'Devolución procesada. Estado: %s',
                $this->_obtener_texto_estado_devolucion($estadoDevolucion)
            )
        ];
    
        $this->Prestamo_model->registrar_historial_devolucion($datos_historial);
    }
    
    private function _preparar_mensaje_respuesta($documentacion) {
        $mensaje = 'Préstamo finalizado con éxito.';
        
        if ($documentacion['pdf_generado']) {
            $mensaje .= ' Se ha generado la ficha de devolución.';
            
            if ($documentacion['email_enviado']) {
                $mensaje .= ' La ficha ha sido enviada por correo electrónico al lector.';
            } else {
                $mensaje .= ' No se pudo enviar el correo electrónico con la ficha.';
            }
        } else {
            $mensaje .= ' No se pudo generar la ficha de devolución.';
        }
        
        return $mensaje;
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
    
    
    
   private function generar_ficha_devolucion($idPrestamo) {
    try {
        // Obtener datos necesarios para la ficha
        $datos_prestamo = $this->Prestamo_model->obtener_datos_ficha_devolucion($idPrestamo);
        
        if (!$datos_prestamo) {
            log_message('error', 'No se encontraron datos para la ficha de devolución - ID Préstamo: ' . $idPrestamo);
            return false;
        }

        // Cargar Dompdf
        require_once APPPATH . '../vendor/autoload.php';
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);

        // Construir el HTML de la ficha
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Ficha de Devolución - Hemeroteca UMSS</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    margin: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .header img {
                    max-width: 150px;
                    margin-bottom: 10px;
                }
                .titulo {
                    font-size: 20px;
                    font-weight: bold;
                    margin-bottom: 20px;
                    text-align: center;
                    color: #003366;
                }
                .seccion {
                    margin-bottom: 20px;
                }
                .seccion h3 {
                    color: #003366;
                    border-bottom: 1px solid #003366;
                    padding-bottom: 5px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f5f5f5;
                    font-weight: bold;
                }
                .firmas {
                    margin-top: 50px;
                    text-align: center;
                }
                .firma {
                    display: inline-block;
                    width: 45%;
                    margin: 0 2%;
                }
                .linea-firma {
                    border-top: 1px solid #000;
                    margin-top: 50px;
                    padding-top: 10px;
                }
                .footer {
                    position: fixed;
                    bottom: 0;
                    width: 100%;
                    text-align: center;
                    font-size: 10px;
                    color: #666;
                    padding: 10px 0;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="' . base_url('assets/img/logo-umss.png') . '" alt="Logo UMSS">
                <h1>HEMEROTECA UMSS</h1>
                <h2>COMPROBANTE DE DEVOLUCIÓN</h2>
            </div>

            <div class="seccion">
                <h3>Datos del Lector</h3>
                <table>
                    <tr>
                        <th width="30%">Nombre Completo:</th>
                        <td>' . htmlspecialchars($datos_prestamo['nombreLector'] . ' ' . $datos_prestamo['apellidoLector']) . '</td>
                    </tr>
                    <tr>
                        <th>Carnet:</th>
                        <td>' . htmlspecialchars($datos_prestamo['carnet']) . '</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>' . htmlspecialchars($datos_prestamo['email']) . '</td>
                    </tr>
                </table>
            </div>

            <div class="seccion">
                <h3>Datos de la Publicación</h3>
                <table>
                    <tr>
                        <th width="30%">Título:</th>
                        <td>' . htmlspecialchars($datos_prestamo['titulo']) . '</td>
                    </tr>
                    <tr>
                        <th>Editorial:</th>
                        <td>' . htmlspecialchars($datos_prestamo['nombreEditorial']) . '</td>
                    </tr>
                    <tr>
                        <th>Ubicación Física:</th>
                        <td>' . htmlspecialchars($datos_prestamo['ubicacionFisica']) . '</td>
                    </tr>
                </table>
            </div>

            <div class="seccion">
                <h3>Datos de la Devolución</h3>
                <table>
                    <tr>
                        <th width="30%">Fecha de Préstamo:</th>
                        <td>' . date('d/m/Y H:i:s', strtotime($datos_prestamo['fechaPrestamo'])) . '</td>
                    </tr>
                    <tr>
                        <th>Fecha de Devolución:</th>
                        <td>' . date('d/m/Y H:i:s') . '</td>
                    </tr>
                    <tr>
                        <th>Estado de Devolución:</th>
                        <td>' . htmlspecialchars($datos_prestamo['estadoDevolucion']) . '</td>
                    </tr>
                    <tr>
                        <th>Encargado:</th>
                        <td>' . htmlspecialchars($datos_prestamo['nombreEncargado'] . ' ' . $datos_prestamo['apellidoEncargado']) . '</td>
                    </tr>
                </table>
            </div>

            <div class="firmas">
                <div class="firma">
                    <div class="linea-firma">Firma del Lector</div>
                </div>
                <div class="firma">
                    <div class="linea-firma">Firma del Encargado</div>
                </div>
            </div>

            <div class="footer">
                <p>Fecha y hora de impresión: ' . date('d/m/Y H:i:s') . '</p>
                <p>ID Préstamo: ' . $idPrestamo . '</p>
                <p>Este documento es un comprobante oficial de devolución de la Hemeroteca UMSS</p>
            </div>
        </body>
        </html>';

        // Cargar HTML en Dompdf
        $dompdf->loadHtml($html);

        // Configurar tamaño de papel y orientación
        $dompdf->setPaper('letter', 'portrait');

        // Renderizar el PDF
        $dompdf->render();

        // Obtener el contenido del PDF
        $output = $dompdf->output();

        // Registrar la generación exitosa
        log_message('info', 'Ficha de devolución generada exitosamente - ID Préstamo: ' . $idPrestamo);

        return $output;

    } catch (Exception $e) {
        log_message('error', 'Error al generar ficha de devolución: ' . $e->getMessage());
        return false;
    }
}
    private function enviar_ficha_por_correo($idPrestamo, $pdf_content) {
        try {
            // Obtener datos del préstamo y el usuario
            $datos_prestamo = $this->Prestamo_model->obtener_datos_ficha_devolucion($idPrestamo);
            if (!$datos_prestamo) {
                log_message('error', 'No se encontraron datos para generar la ficha de devolución del préstamo ID: ' . $idPrestamo);
                return false;
            }
    
            // Configuración del email
            $config = [
                'protocol' => 'smtp',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_user' => 'quirozmolinamaritza@gmail.com',
                'smtp_pass' => 'zdmk qkfw wgdf lshq',
                'smtp_crypto' => 'tls',
                'mailtype' => 'html',
                'charset' => 'utf-8',
                'newline' => "\r\n"
            ];
    
            $this->email->initialize($config);
            $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
            $this->email->to($datos_prestamo['email']);
            $this->email->subject('Comprobante de Devolución - Hemeroteca UMSS');
    
            // Construir el cuerpo del mensaje con estilo mejorado
            $mensaje = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { padding: 20px; }
                    .header { background-color: #003366; color: white; padding: 10px; text-align: center; }
                    .content { margin: 20px 0; }
                    .details { background-color: #f5f5f5; padding: 15px; border-radius: 5px; }
                    .footer { font-size: 12px; color: #666; margin-top: 20px; }
                    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Comprobante de Devolución - Hemeroteca UMSS</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Estimado/a {$datos_prestamo['nombreLector']} {$datos_prestamo['apellidoLector']},</p>
                        <p>Se confirma la devolución de la siguiente publicación:</p>
                        
                        <div class='details'>
                            <table>
                                <tr>
                                    <th>Título</th>
                                    <td>{$datos_prestamo['titulo']}</td>
                                </tr>
                                <tr>
                                    <th>Editorial</th>
                                    <td>{$datos_prestamo['nombreEditorial']}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de Devolución</th>
                                    <td>" . date('d/m/Y H:i:s') . "</td>
                                </tr>
                                <tr>
                                    <th>Estado de Devolución</th>
                                    <td>{$datos_prestamo['estadoDevolucion']}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <p>Se adjunta el comprobante detallado de la devolución en formato PDF.</p>
                    </div>
                    
                    <div class='footer'>
                        <p>Este es un correo automático, por favor no responder.</p>
                        <p>Hemeroteca UMSS - " . date('Y') . "</p>
                    </div>
                </div>
            </body>
            </html>";
    
            $this->email->message($mensaje);
    
            // Adjuntar el PDF
            $this->email->attach($pdf_content, 'attachment', 'comprobante_devolucion.pdf', 'application/pdf');
    
            if (!$this->email->send()) {
                log_message('error', 'Error al enviar correo de devolución: ' . $this->email->print_debugger());
                return false;
            }
    
            // Registrar el envío exitoso en el log
            log_message('info', "Ficha de devolución enviada por correo - Préstamo ID: {$idPrestamo}, Email: {$datos_prestamo['email']}");
            return true;
    
        } catch (Exception $e) {
            log_message('error', 'Error en enviar_ficha_por_correo: ' . $e->getMessage());
            return false;
        }
    }

    public function devueltos() {
        $this->_verificar_rol(['administrador', 'encargado']);
        
        try {
            // Obtener lista de préstamos devueltos
            $data['prestamos'] = $this->Prestamo_model->obtener_prestamos_devueltos();
            $data['estados_devolucion'] = [
                'bueno' => 'BUENO',
                'dañado' => 'DAÑADO',
                'perdido' => 'PERDIDO'
            ];
            
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('prestamos/devueltos', $data);
            $this->load->view('inc/footer');
            
        } catch (Exception $e) {
            log_message('error', 'Error al cargar préstamos devueltos: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al cargar la lista de préstamos devueltos.');
            redirect('prestamos/activos');
        }
    }
    public function generar_ficha_manual($idPrestamo) {
        $this->_verificar_rol(['administrador', 'encargado']);
        
        try {
            // Validar que el préstamo exista y esté finalizado
            $prestamo = $this->Prestamo_model->obtener_prestamo_detallado($idPrestamo);
            if (!$prestamo) {
                throw new Exception('Préstamo no encontrado.');
            }
    
            // Generar el PDF
            $pdf_content = $this->generar_ficha_devolucion($idPrestamo);
            
            if (!$pdf_content) {
                throw new Exception('Error al generar la ficha de devolución.');
            }
    
            // Configurar headers para descarga directa
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="ficha_devolucion_' . $idPrestamo . '.pdf"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
    
            echo $pdf_content;
            exit();
    
        } catch (Exception $e) {
            log_message('error', 'Error al generar ficha manual: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al generar la ficha: ' . $e->getMessage());
            redirect('prestamos/devueltos');
        }
    }
    
    public function reenviar_ficha_email($idPrestamo) {
        $this->_verificar_rol(['administrador', 'encargado']);
        
        try {
            // Validar que el préstamo exista y esté finalizado
            $prestamo = $this->Prestamo_model->obtener_prestamo_detallado($idPrestamo);
            if (!$prestamo) {
                throw new Exception('Préstamo no encontrado.');
            }
    
            // Generar el PDF primero
            $pdf_content = $this->generar_ficha_devolucion($idPrestamo);
            if (!$pdf_content) {
                throw new Exception('Error al generar el PDF de la ficha.');
            }
    
            // Intentar enviar el correo
            $envio_exitoso = $this->enviar_ficha_por_correo($idPrestamo, $pdf_content);
            
            if ($envio_exitoso) {
                $this->session->set_flashdata('mensaje', 'Ficha de devolución enviada por correo exitosamente.');
            } else {
                throw new Exception('Error al enviar el correo electrónico.');
            }
    
        } catch (Exception $e) {
            log_message('error', 'Error al reenviar ficha por email: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al enviar la ficha: ' . $e->getMessage());
        }
    
        redirect('prestamos/devueltos');
    }
    
}