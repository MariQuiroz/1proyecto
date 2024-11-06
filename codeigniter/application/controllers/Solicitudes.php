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
            
            if (!$this->session->userdata('idUsuario')) {
                redirect('usuarios/login');
                return;
            }
        
            // Inicializar array de publicaciones seleccionadas si no existe
            if (!$this->session->userdata('publicaciones_seleccionadas')) {
                $this->session->set_userdata('publicaciones_seleccionadas', array());
            }
        
            $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas');
        
            // Si se está intentando añadir una nueva publicación
            if ($idPublicacion !== null) {
                // Validar límite de publicaciones
                if (!in_array($idPublicacion, $publicaciones_seleccionadas)) {
                    if (count($publicaciones_seleccionadas) >= 5) {
                        $this->session->set_flashdata('error', 'Solo puede solicitar hasta 5 publicaciones a la vez.');
                    } else {
                        // Verificar disponibilidad de la publicación
                        $publicacion = $this->Publicacion_model->obtener_publicacion_detallada($idPublicacion);
                        if ($publicacion && $publicacion->estado == ESTADO_PUBLICACION_DISPONIBLE) {
                            $publicaciones_seleccionadas[] = $idPublicacion;
                            $this->session->set_userdata('publicaciones_seleccionadas', $publicaciones_seleccionadas);
                            $this->session->set_flashdata('mensaje', 'Publicación añadida a la solicitud.');
                        } else {
                            $this->session->set_flashdata('error', 'La publicación no está disponible para préstamo.');
                        }
                    }
                }
            }
        
            // Obtener detalles de las publicaciones seleccionadas
            $data['publicaciones'] = array();
            if (!empty($publicaciones_seleccionadas)) {
                $data['publicaciones'] = $this->Publicacion_model->obtener_publicaciones_seleccionadas($publicaciones_seleccionadas);
            }
        
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/crear', $data);
            $this->load->view('inc/footer');
        }
        
        public function cancelar() {
            $this->_verificar_rol(['lector']);
            $this->session->unset_userdata('publicaciones_seleccionadas');
            $this->session->set_flashdata('mensaje', 'Solicitud cancelada.');
            redirect('publicaciones');
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
        $fechaActual = date('Y-m-d H:i:s');
        
        // Verificar si la solicitud ya fue procesada
        $solicitud_actual = $this->Solicitud_model->obtener_solicitud($idSolicitud);
        if (!$solicitud_actual || $solicitud_actual->estadoSolicitud != ESTADO_SOLICITUD_PENDIENTE) {
            throw new Exception('La solicitud ya fue procesada o no existe');
        }

        // Obtener detalles de la solicitud
        $solicitud = $this->Solicitud_model->obtener_detalle_solicitud_multiple($idSolicitud);
        if (empty($solicitud)) {
            throw new Exception('No se encontraron detalles de la solicitud');
        }

        // Clasificar publicaciones según disponibilidad
        $publicaciones_disponibles = [];
        $publicaciones_no_disponibles = [];
        
        foreach ($solicitud as $pub) {
            $publicacion = $this->Publicacion_model->obtener_publicacion($pub->idPublicacion);
            if ($publicacion && $publicacion->estado == ESTADO_PUBLICACION_DISPONIBLE) {
                $publicaciones_disponibles[] = $pub;
            } else {
                $publicaciones_no_disponibles[] = $pub;
            }
        }

        if (empty($publicaciones_disponibles)) {
            throw new Exception('Ninguna publicación está disponible actualmente');
        }

        // Procesar publicaciones disponibles
        foreach ($publicaciones_disponibles as $pub) {
            // Crear registro de préstamo
            $data_prestamo = [
                'idSolicitud' => $idSolicitud,
                'idEncargadoPrestamo' => $idEncargado,
                'fechaPrestamo' => $fechaActual,
                'horaInicio' => date('H:i:s'),
                'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
                'estado' => 1,
                'fechaCreacion' => $fechaActual,
                'idUsuarioCreador' => $idEncargado
            ];
            
            $this->db->insert('PRESTAMO', $data_prestamo);

            // Actualizar estado de la publicación
            $this->db->where('idPublicacion', $pub->idPublicacion)
                    ->update('PUBLICACION', [
                        'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
                        'fechaActualizacion' => $fechaActual,
                        'idUsuarioCreador' => $idEncargado
                    ]);

            // Actualizar detalle de solicitud
            $this->db->where([
                'idSolicitud' => $idSolicitud,
                'idPublicacion' => $pub->idPublicacion
            ])->update('DETALLE_SOLICITUD', [
                'observaciones' => 'Préstamo aprobado y procesado'
            ]);
        }

        // Determinar estado general de la solicitud
        $estadoGeneral = empty($publicaciones_no_disponibles) ? 
                        ESTADO_SOLICITUD_APROBADA : 
                        ESTADO_SOLICITUD_APROBADA_PARCIAL;

        // Actualizar estado de la solicitud
        $this->db->where('idSolicitud', $idSolicitud)
                ->update('SOLICITUD_PRESTAMO', [
                    'estadoSolicitud' => $estadoGeneral,
                    'fechaAprobacionRechazo' => $fechaActual,
                    'fechaActualizacion' => $fechaActual,
                    'idUsuarioCreador' => $idEncargado
                ]);

        // Crear notificaciones según disponibilidad
        if (!empty($publicaciones_disponibles)) {
            $mensaje_aprobadas = "Se han aprobado las siguientes publicaciones:\n" . 
                               implode("\n", array_map(function($pub) {
                                   return $pub->titulo;
                               }, $publicaciones_disponibles)) . 
                               "\nPuede pasar a recogerlas a la hemeroteca.";

            $this->Notificacion_model->crear_notificacion(
                $solicitud[0]->idUsuario,
                null,
                NOTIFICACION_APROBACION_PRESTAMO,
                $mensaje_aprobadas
            );
        }

        if (!empty($publicaciones_no_disponibles)) {
            $mensaje_pendientes = "Las siguientes publicaciones están pendientes de disponibilidad:\n" . 
                                implode("\n", array_map(function($pub) {
                                    return $pub->titulo;
                                }, $publicaciones_no_disponibles)) . 
                                "\nSerá notificado cuando estén disponibles.";

            $this->Notificacion_model->crear_notificacion(
                $solicitud[0]->idUsuario,
                null,
                NOTIFICACION_SOLICITUD_PRESTAMO,
                $mensaje_pendientes
            );

            // Actualizar observaciones para publicaciones pendientes
            foreach ($publicaciones_no_disponibles as $pub) {
                $this->db->where([
                    'idSolicitud' => $idSolicitud,
                    'idPublicacion' => $pub->idPublicacion
                ])->update('DETALLE_SOLICITUD', [
                    'observaciones' => 'Pendiente por disponibilidad'
                ]);
            }
        }

        // Preparar datos para la ficha de préstamo
        $datos_ficha = [
            'nombreCompletoLector' => $solicitud[0]->nombres . ' ' . $solicitud[0]->apellidoPaterno,
            'carnet' => $solicitud[0]->carnet,
            'profesion' => $solicitud[0]->profesion,
            'fechaPrestamo' => $fechaActual
        ];

        // Generar PDF usando el método privado existente
        $pdfUrl = $this->generar_pdf_ficha_prestamo($datos_ficha, $idSolicitud);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Error en la transacción. Los cambios han sido revertidos.');
        }

        // Registrar la acción exitosa
        log_message('info', 'Solicitud ' . $idSolicitud . ' procesada con ' . 
                   count($publicaciones_disponibles) . ' publicaciones aprobadas y ' . 
                   count($publicaciones_no_disponibles) . ' pendientes');
        
        $mensaje = "Solicitud procesada: " . count($publicaciones_disponibles) . " publicaciones aprobadas";
        if (!empty($publicaciones_no_disponibles)) {
            $mensaje .= " y " . count($publicaciones_no_disponibles) . " pendientes de disponibilidad";
        }
        $this->session->set_flashdata('mensaje', $mensaje);
        
        // Redirigir con el PDF si está disponible
        if ($pdfUrl) {
            redirect('solicitudes/pendientes?pdf=' . urlencode($pdfUrl));
        } else {
            redirect('solicitudes/pendientes');
        }
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error al aprobar solicitud ' . $idSolicitud . ': ' . $e->getMessage());
        $this->session->set_flashdata('error', $e->getMessage());
        redirect('solicitudes/pendientes');
    }
}

public function rechazar($idSolicitud) {
    $this->_verificar_rol(['administrador', 'encargado']);
    
    $this->db->trans_start();
    
    try {
        $idEncargado = $this->session->userdata('idUsuario');
        
        // Obtener detalles de la solicitud
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
            'fechaAprobacionRechazo' => date('Y-m-d H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        );

        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', $data_solicitud);
        
        // Recolectar títulos y crear notificación
        $titulos = array_map(function($pub) {
            return $pub->titulo;
        }, $solicitud);
        
        $mensaje = "Tu solicitud de préstamo ha sido rechazada para las siguientes publicaciones: " . 
                  implode(", ", $titulos);

        // Crear notificación para el usuario
        $this->Notificacion_model->crear_notificacion(
            $solicitud[0]->idUsuario,
            $solicitud[0]->idPublicacion,
            NOTIFICACION_RECHAZO_PRESTAMO,
            $mensaje
        );

        // Asegurar que las publicaciones estén disponibles
        foreach ($solicitud as $pub) {
            $this->Publicacion_model->cambiar_estado_publicacion(
                $pub->idPublicacion,
                ESTADO_PUBLICACION_DISPONIBLE
            );
            
            // Notificar a usuarios en lista de espera
            $this->_notificar_disponibilidad($pub->idPublicacion);
        }

        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Error en la transacción');
        }

        $this->session->set_flashdata('mensaje', 'Solicitud rechazada correctamente.');

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
    // Validación de datos y carga de modelos necesarios
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

    // Configuración del PDF
    $this->load->library('pdf');
    $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Hemeroteca UMSS');
    $pdf->SetTitle('Ficha de Préstamo');
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Hemeroteca UMSS', 'Ficha de Préstamo');
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();

    // Construcción del HTML con estilos mejorados
    $html = '
    <style>
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.main-table td, table.main-table th {
            border: 1px solid #000;
            padding: 8px;
        }
        table.main-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .header {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .subheader {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 15px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            margin: 0 auto;
            padding-top: 5px;
        }
    </style>
    
    <div class="header">U.M.S.S. BIBLIOTECAS - EN SALA</div>
    <div class="subheader">FICHA DE PRÉSTAMO</div>

    <table class="main-table">
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
            <td><strong>Encargado:</strong></td>
            <td>' . $this->sanitize_for_pdf($encargado->nombres . ' ' . $encargado->apellidoPaterno) . '</td>
        </tr>
    </table>

    <h4>Publicaciones Prestadas:</h4>
    <table class="main-table">
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
    
    <div style="text-align: right; font-size: 8pt; margin-top: 30px;">
        <p>Fecha y hora de impresión: ' . date('d/m/Y H:i:s') . '</p>
        <p>ID Solicitud: ' . $idSolicitud . '</p>
    </div>';

    // Generar el PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Generar nombre único y guardar el archivo
    $pdfFileName = 'ficha_prestamo_' . $idSolicitud . '_' . time() . '.pdf';
    $pdfPath = FCPATH . 'uploads/' . $pdfFileName;
    
    if (!file_exists(FCPATH . 'uploads/')) {
        mkdir(FCPATH . 'uploads/', 0777, true);
    }

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
        public function remover($idPublicacion) {
            $this->_verificar_rol(['lector']);
            
            // Inicializar array de publicaciones seleccionadas si no existe
            if (!$this->session->userdata('publicaciones_seleccionadas')) {
                $this->session->set_userdata('publicaciones_seleccionadas', array());
            }
            
            $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas');
            
            // Verificar si la publicación está en la lista
            $key = array_search($idPublicacion, $publicaciones_seleccionadas);
            if ($key !== false) {
                // Remover la publicación del array
                unset($publicaciones_seleccionadas[$key]);
                // Reindexar el array
                $publicaciones_seleccionadas = array_values($publicaciones_seleccionadas);
                // Actualizar la sesión
                $this->session->set_userdata('publicaciones_seleccionadas', $publicaciones_seleccionadas);
                
                // Mensaje de éxito
                $this->session->set_flashdata('mensaje', 'Publicación removida de la solicitud exitosamente.');
            }
            
            // Redirigir de vuelta a la vista de crear
            redirect('solicitudes/crear/0');
        }
}