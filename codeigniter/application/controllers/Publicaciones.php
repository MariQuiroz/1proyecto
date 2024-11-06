<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Publicaciones extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('publicacion_model');
        $this->load->model('tipo_model');
        $this->load->model('editorial_model');
        $this->load->model('Notificacion_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('upload');
        
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/index/3', 'refresh');
        }
    }

    private function _verificar_permisos_admin_encargado() {
        $this->_verificar_sesion();
        $rol = $this->session->userdata('rol');
        if ($rol != 'administrador' && $rol != 'encargado') {
            $this->session->set_flashdata('error', 'No tienes permisos para realizar esta acción.');
            redirect('publicaciones/index', 'refresh');
        }
    }

    private function _es_lector() {
        return $this->session->userdata('rol') == 'lector';
    }

    public function index() {
        $this->_verificar_sesion();
        $idUsuario = $this->session->userdata('idUsuario');
        $publicaciones = $this->publicacion_model->listar_todas_publicaciones();

        foreach ($publicaciones as &$publicacion) {
            $publicacion->estado_personalizado = $this->publicacion_model->obtener_estado_personalizado($publicacion->idPublicacion, $idUsuario);
        }

        $data['publicaciones'] = $publicaciones;
        $data['es_lector'] = $this->_es_lector();
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/lista', $data);
        $this->load->view('inc/footer');
    }

    public function solicitar($idPublicacion) {
        $this->_verificar_sesion();
        if (!$this->_es_lector()) {
            $this->session->set_flashdata('error', 'Solo los lectores pueden solicitar publicaciones.');
            redirect('publicaciones');
        }
        
        $publicacion = $this->publicacion_model->obtener_publicacion($idPublicacion);
        if ($publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
            $this->session->set_flashdata('error', 'Esta publicación no está disponible para préstamo.');
            redirect('publicaciones');
        }
    
        $data['publicacion'] = $publicacion;
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/solicitar', $data);
        $this->load->view('inc/footer');
    }
    
    public function agregar() {
        $this->_verificar_permisos_admin_encargado();
        $data['tipos'] = $this->tipo_model->obtener_tipos();
        $data['editoriales'] = $this->editorial_model->obtener_editoriales();
        $data['ubicaciones'] = array(
            'Estante A' => 'Estante A',
            'Estante B' => 'Estante B',
            'Archivo' => 'Archivo',
            'Hemeroteca' => 'Hemeroteca'
        );
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/agregar', $data);
        $this->load->view('inc/footer');
    }
    
    public function agregarbd()
    {
        $this->_verificar_permisos_admin_encargado();
        $this->form_validation->set_rules('titulo', 'Título', 'required');
        $this->form_validation->set_rules('idEditorial', 'Editorial', 'required|numeric');
        $this->form_validation->set_rules('idTipo', 'Tipo', 'required|numeric');
        $this->form_validation->set_rules('fechaPublicacion', 'Fecha de Publicación', 'required');
        $this->form_validation->set_rules('numeroPaginas', 'Número de Páginas', 'numeric');
        $this->form_validation->set_rules('ubicacionFisica', 'Ubicación Física', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->agregar();
        } else {
            $data = array(
                'idTipo' => $this->input->post('idTipo'),
                'idEditorial' => $this->input->post('idEditorial'),
                'titulo' => $this->input->post('titulo'),
                'fechaPublicacion' => $this->input->post('fechaPublicacion'),
                'numeroPaginas' => $this->input->post('numeroPaginas'),
                'descripcion' => $this->input->post('descripcion'),
                'ubicacionFisica' => $this->input->post('ubicacionFisica'),
                'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                'fechaCreacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );

            $idPublicacion = $this->publicacion_model->agregar_publicacion($data);

            if ($idPublicacion) {
                $this->_manejar_carga_portada($idPublicacion);
                $this->session->set_flashdata('mensaje', 'Publicación agregada correctamente.');
                redirect('publicaciones/index', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error al agregar la publicación.');
                redirect('publicaciones/agregar', 'refresh');
            }
            if (!empty($_FILES['portada']['name'])) {
                $config['upload_path'] = './uploads/portadas/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = 2048; // 2MB
                $config['file_name'] = 'portada_' . time();
        
                $this->upload->initialize($config);
        
                if (!$this->upload->do_upload('portada')) {
                    $error = $this->upload->display_errors();
                    log_message('error', 'Error al subir imagen: ' . $error);
                    $this->session->set_flashdata('error', 'Error al subir la imagen: ' . $error);
                    redirect('publicaciones/agregar');
                    return;
                } else {
                    $upload_data = $this->upload->data();
                    $data['portada'] = $upload_data['file_name'];
                }
            }
        
        
            if ($this->publicacion_model->agregar_publicacion($data)) {
                $this->session->set_flashdata('mensaje', 'Publicación agregada correctamente.');
                redirect('publicaciones/index');
            } else {
                $this->session->set_flashdata('error', 'Error al agregar la publicación.');
                redirect('publicaciones/agregar');
            }
        }
    }

    public function modificar($idPublicacion)
    {
        $this->_verificar_permisos_admin_encargado();
        $data['publicacion'] = $this->publicacion_model->obtener_publicacion($idPublicacion);
        $data['tipos'] = $this->tipo_model->obtener_tipos();
        $data['editoriales'] = $this->editorial_model->obtener_editoriales();
        $data['ubicaciones'] = array(
            'Estante A' => 'Estante A',
            'Estante B' => 'Estante B',
            'Archivo' => 'Archivo',
            'Hemeroteca' => 'Hemeroteca'
        );
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/editar', $data);
        $this->load->view('inc/footer');
    }

    public function modificarbd()
    {
        $this->_verificar_permisos_admin_encargado();
        $idPublicacion = $this->input->post('idPublicacion');
        $this->form_validation->set_rules('titulo', 'Título', 'required');
        $this->form_validation->set_rules('idEditorial', 'Editorial', 'required|numeric');
        $this->form_validation->set_rules('idTipo', 'Tipo', 'required|numeric');
        $this->form_validation->set_rules('fechaPublicacion', 'Fecha de Publicación', 'required');
        $this->form_validation->set_rules('numeroPaginas', 'Número de Páginas', 'numeric');
        $this->form_validation->set_rules('ubicacionFisica', 'Ubicación Física', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->modificar($idPublicacion);
        } else {
            $data = array(
                'idTipo' => $this->input->post('idTipo'),
                'idEditorial' => $this->input->post('idEditorial'),
                'titulo' => $this->input->post('titulo'),
                'fechaPublicacion' => $this->input->post('fechaPublicacion'),
                'numeroPaginas' => $this->input->post('numeroPaginas'),
                'descripcion' => $this->input->post('descripcion'),
                'ubicacionFisica' => $this->input->post('ubicacionFisica'),
                'fechaActualizacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );

            $this->_manejar_carga_portada($idPublicacion);
            if (!empty($_FILES['portada']['name'])) {
                $config['upload_path'] = './uploads/portadas/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = 2048; // 2MB
                $config['file_name'] = 'portada_' . time();
        
                $this->upload->initialize($config);
        
                if (!$this->upload->do_upload('portada')) {
                    $error = $this->upload->display_errors();
                    log_message('error', 'Error al subir imagen: ' . $error);
                    $this->session->set_flashdata('error', $error);
                } else {
                    $upload_data = $this->upload->data();
                    $data['portada'] = $upload_data['file_name'];
                }
            }

            if ($this->publicacion_model->actualizar_publicacion($idPublicacion, $data)) {
                $this->session->set_flashdata('mensaje', 'Publicación actualizada correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar la publicación.');
            }
            redirect('publicaciones/index', 'refresh');
        }
    }
    
    private function _manejar_carga_portada($idPublicacion) {
        // Verificar si el directorio existe, si no, crearlo
        $upload_path = './uploads/portadas/';
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
    
        $nombrearchivo = $idPublicacion . ".jpg";
        
        $config = array(
            'upload_path' => $upload_path,
            'file_name' => $nombrearchivo,
            'allowed_types' => 'jpg|jpeg|png|gif',
            'max_size' => 2048, // 2MB max
            'overwrite' => TRUE
        );
    
        $this->load->library('upload');
        $this->upload->initialize($config);
    
        // Eliminar archivo anterior si existe
        $direccion = $config['upload_path'] . $nombrearchivo;
        if (file_exists($direccion)) {
            unlink($direccion);
        }
    
        if ($this->upload->do_upload('portada')) {
            $data_update = array('portada' => $nombrearchivo);
            $this->publicacion_model->actualizar_publicacion($idPublicacion, $data_update);
            return true;
        } else {
            $upload_error = $this->upload->display_errors();
            if (!empty($_FILES['portada']['name'])) {
                log_message('error', 'Error al subir portada: ' . $upload_error);
                $this->session->set_flashdata('error', 'Error al subir la portada: ' . $upload_error);
            }
            return false;
        }
    }
    public function eliminar($idPublicacion) {
        $this->_verificar_permisos_admin_encargado();
    
        // Verificar que la publicación exista y no esté en préstamo
        $publicacion = $this->publicacion_model->obtener_publicacion($idPublicacion);
        
        if (!$publicacion) {
            $this->session->set_flashdata('error', 'La publicación no existe.');
            redirect('publicaciones/index');
            return;
        }
    
        // Iniciar transacción
        $this->db->trans_start();
    
        try {
            // Datos para la actualización
            $data = array(
                'estado' => ESTADO_PUBLICACION_ELIMINADO,
                'fechaActualizacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );
    
            // Actualizar el estado de la publicación
            $this->db->where('idPublicacion', $idPublicacion);
            $resultado = $this->db->update('PUBLICACION', $data);
    
            if ($resultado) {
                // Registrar la eliminación en el historial
                $this->db->trans_complete();
                
                // Notificar a administradores y encargados
                $usuarios_notificar = $this->usuario_model->obtener_admins_encargados();
                foreach ($usuarios_notificar as $usuario) {
                    $this->notificacion_model->crear_notificacion(
                        $usuario->idUsuario,
                        $idPublicacion,
                        NOTIFICACION_ELIMINACION,
                        "La publicación '{$publicacion->titulo}' ha sido eliminada."
                    );
                }
    
                $this->session->set_flashdata('mensaje', 'Publicación eliminada correctamente.');
            } else {
                throw new Exception('No se pudo eliminar la publicación.');
            }
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Error al eliminar la publicación: ' . $e->getMessage());
            log_message('error', 'Error al eliminar publicación: ' . $e->getMessage());
        }
    
        redirect('publicaciones/index');
    }
    
    // Método auxiliar para registrar la eliminación lógica
    private function _registrar_eliminacion_logica($idPublicacion) {
        $datos = array(
            'idPublicacion' => $idPublicacion,
            'fechaEliminacion' => date('Y-m-d H:i:s'),
            'idUsuarioElimina' => $this->session->userdata('idUsuario'),
            'motivo' => 'Eliminación lógica por usuario'
        );
        
        // Registro en el historial si existe el modelo
        if (isset($this->historial_model)) {
            $this->historial_model->registrar_eliminacion($datos);
        }
        
        // Registro en log del sistema
        log_message('info', 'Publicación ID: ' . $idPublicacion . ' eliminada lógicamente por usuario ID: ' . $this->session->userdata('idUsuario'));
    }
    
    // Método auxiliar para registrar cambios de estado
    private function _registrar_cambio_estado($idPublicacion, $estadoAnterior, $nuevoEstado) {
        $datos = array(
            'idPublicacion' => $idPublicacion,
            'estadoAnterior' => $estadoAnterior,
            'estadoNuevo' => $nuevoEstado,
            'fechaCambio' => date('Y-m-d H:i:s'),
            'idUsuario' => $this->session->userdata('idUsuario')
        );
        
        // Aquí podrías tener un modelo para registrar estos cambios
        // $this->historial_model->registrar_cambio_estado($datos);
    }
    // Método auxiliar para enviar notificaciones
private function _enviar_notificaciones($idPublicacion, $tipoNotificacion, $titulo) {
    // Cargar el modelo de notificaciones si no está cargado
    if (!isset($this->notificacion_model)) {
        $this->load->model('notificacion_model');
    }
    
    try {
        // Obtener usuarios interesados
        $usuarios_interesados = $this->notificacion_model->obtener_usuarios_interesados($idPublicacion);
        
        if (!empty($usuarios_interesados)) {
            foreach ($usuarios_interesados as $usuario) {
                switch ($tipoNotificacion) {
                    case NOTIFICACION_DISPONIBILIDAD:
                        $mensaje = "La publicación '$titulo' está disponible.";
                        break;
                    case NOTIFICACION_ELIMINACION:
                        $mensaje = "La publicación '$titulo' ha sido retirada del catálogo.";
                        break;
                    case NOTIFICACION_VENCIMIENTO:
                        $mensaje = "La publicación '$titulo' está próxima a vencer.";
                        break;
                    default:
                        $mensaje = "Hay una actualización sobre la publicación '$titulo'.";
                }
                
                $this->notificacion_model->crear_notificacion(
                    $usuario->idUsuario,
                    $idPublicacion,
                    $tipoNotificacion,
                    $mensaje
                );
            }
            
            // Registrar en el log del sistema
            log_message('info', "Notificaciones enviadas - Tipo: $tipoNotificacion, Publicación: $idPublicacion, Usuarios notificados: " . count($usuarios_interesados));
            return true;
        }
        
        return false;
        
    } catch (Exception $e) {
        log_message('error', "Error al enviar notificaciones: " . $e->getMessage());
        return false;
    }
}
    // Método auxiliar para notificar a usuarios interesados
    private function _notificar_usuarios_interesados($idPublicacion) {
        // Verificar si el modelo de notificaciones está cargado
        if (!isset($this->notificacion_model)) {
            $this->load->model('notificacion_model');
        }
        
        // Obtener usuarios interesados y enviar notificaciones
        $usuarios_interesados = $this->notificacion_model->obtener_usuarios_interesados($idPublicacion);
        foreach ($usuarios_interesados as $usuario) {
            $this->notificacion_model->crear_notificacion(
                $usuario->idUsuario,
                $idPublicacion,
                NOTIFICACION_DISPONIBILIDAD,
                'La publicación que te interesa ahora está disponible.'
            );
        }
    }

    public function buscar() {
        $this->_verificar_sesion();
        $termino = $this->input->get('termino');
        $data['publicaciones'] = $this->publicacion_model->buscar_publicaciones($termino);
        $data['es_lector'] = $this->_es_lector();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/resultados_busqueda', $data);
        $this->load->view('inc/footer');
    }

    public function cambiar_estado($idPublicacion, $nuevoEstado) {
        $this->_verificar_permisos_admin_encargado();
        $data = array(
            'estado' => $nuevoEstado,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        );
        if ($this->publicacion_model->cambiar_estado_publicacion($idPublicacion, $data)) {
            $this->session->set_flashdata('mensaje', 'Estado de la publicación actualizado correctamente.');
        } else {
            $this->session->set_flashdata('error', 'Error al actualizar el estado de la publicación.');
        }
        redirect('publicaciones/index', 'refresh');
    }

    public function ver($idPublicacion) {
        $this->_verificar_sesion();
        $publicacion = $this->publicacion_model->obtener_publicacion_detallada($idPublicacion);
        if (!$publicacion) {
            $this->session->set_flashdata('error', 'La publicación no existe.');
            redirect('publicaciones/index');
        }
        $data['publicacion'] = $publicacion;
        $data['es_lector'] = $this->_es_lector();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/ver', $data);
        $this->load->view('inc/footer');
    }

    public function obtener_publicaciones_disponibles() {
        $this->_verificar_sesion();
        return $this->publicacion_model->obtener_publicaciones_disponibles();
    }
    public function obtener_publicacion_detallada($idPublicacion) {
        $this->db->select('PUBLICACION.*, TIPO.nombreTipo, EDITORIAL.nombreEditorial, USUARIO.nombres, USUARIO.apellidoPaterno');
        $this->db->from('PUBLICACION');
        $this->db->join('TIPO', 'TIPO.idTipo = PUBLICACION.idTipo');
        $this->db->join('EDITORIAL', 'EDITORIAL.idEditorial = PUBLICACION.idEditorial');
        $this->db->join('USUARIO', 'USUARIO.idUsuario = PUBLICACION.idUsuarioCreador', 'left');
        $this->db->where('PUBLICACION.idPublicacion', $idPublicacion);
        return $this->db->get()->row();
    }

}