<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

   
    /**
     * Obtiene un usuario por su nombre de usuario
     * @param string $username
     * @return object|null
     */
    public function obtener_por_username($username) {
        $this->db->select('
            idUsuario, 
            username, 
            password, 
            nombres, 
            apellidoPaterno, 
            rol, 
            estado, 
            verificado, 
            cambioPasswordRequerido
        ');
        return $this->db->get_where('USUARIO', ['username' => $username])->row();
    }

    public function registrarUsuario($data) {
        $this->db->trans_start();
        
        try {
            // Validar campos requeridos
            $campos_requeridos = ['nombres', 'apellidoPaterno', 'carnet', 'email', 'username', 'password', 'rol'];
            foreach ($campos_requeridos as $campo) {
                if (!isset($data[$campo]) || empty($data[$campo])) {
                    log_message('error', 'Campo requerido faltante en registrarUsuario: ' . $campo);
                    return false;
                }
            }
    
            // Verificar si el usuario ya existe
            if ($this->_usuario_existe($data['email'], $data['carnet'])) {
                log_message('error', 'Intento de registro con email o carnet duplicado');
                return false;
            }
    
            // Asegurarse de que la contraseña esté hasheada
            if (isset($data['password']) && !password_get_info($data['password'])['algo']) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
    
            // Establecer valores por defecto
            $defaults = [
                'verificado' => 0,
                'estado' => 1,
                'fechaCreacion' => date('Y-m-d H:i:s'),
                'cambioPasswordRequerido' => 1,
                'intentosVerificacion' => 0
            ];
    
            foreach ($defaults as $key => $value) {
                if (!isset($data[$key])) {
                    $data[$key] = $value;
                }
            }
    
            // Formatear datos
            $data['nombres'] = strtoupper(trim($data['nombres']));
            $data['apellidoPaterno'] = strtoupper(trim($data['apellidoPaterno']));
            if (isset($data['apellidoMaterno'])) {
                $data['apellidoMaterno'] = strtoupper(trim($data['apellidoMaterno']));
            }
            $data['email'] = strtolower(trim($data['email']));
            $data['username'] = trim($data['username']);
    
            // Insertar el usuario
            $resultado = $this->db->insert('USUARIO', $data);
            
            if (!$resultado) {
                log_message('error', 'Error al insertar usuario: ' . $this->db->error()['message']);
                throw new Exception('Error al insertar el usuario en la base de datos');
            }
    
            $idUsuario = $this->db->insert_id();
    
            // Si se especificó un rol de lector, crear registro en tabla de preferencias
            if ($data['rol'] === 'lector') {
                $preferencias = [
                    'idUsuario' => $idUsuario,
                    'notificarDisponibilidad' => 1,
                    'notificarEmail' => 1,
                    'notificarSistema' => 1,
                    'fechaCreacion' => date('Y-m-d H:i:s')
                ];
                
                $this->db->insert('PREFERENCIAS_NOTIFICACION', $preferencias);
            }
    
            $this->db->trans_complete();
    
            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Error en transacción al registrar usuario');
                return false;
            }
    
            // Registrar en el log del sistema
            log_message('info', 'Usuario registrado exitosamente - ID: ' . $idUsuario . 
                              ' - Username: ' . $data['username'] . 
                              ' - Rol: ' . $data['rol']);
    
            return $idUsuario;
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Exception en registrarUsuario: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si ya existe un usuario con el email o carnet proporcionado
     */
    private function _usuario_existe($email, $carnet) {
        $this->db->where('email', $email);
        $this->db->or_where('carnet', $carnet);
        return $this->db->get('USUARIO')->num_rows() > 0;
    }
  
    /**
     * Obtiene un usuario por su ID
     */
    public function obtener_usuario($idUsuario) {
        $this->db->select([
            'idUsuario',
            'nombres',
            'apellidoPaterno',
            'apellidoMaterno',
            'carnet',
            'profesion',
            'fechaNacimiento',
            'sexo',
            'telefono',
            'email',
            'username',
            'rol',
            'verificado',
            'estado'
        ]);
        
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->get('USUARIO')->row();
    }
    
    public function username_existe($username) {
        $this->db->where('username', $username);
        return $this->db->get('USUARIO')->num_rows() > 0;
    }

    public function listaUsuarios() {
        $this->db->select('
            u.idUsuario,
            u.nombres,
            u.carnet,
            u.apellidoPaterno,
            u.apellidoMaterno,
            u.email,
            u.rol,
            u.profesion,
            u.estado,
            COUNT(DISTINCT sp.idSolicitud) as solicitudes_activas
        ');
        $this->db->from('USUARIO u');
        $this->db->join('SOLICITUD_PRESTAMO sp', 
            'u.idUsuario = sp.idUsuario AND sp.estado = 1', 
            'left');
        $this->db->where('u.estado', 1);
        $this->db->group_by('u.idUsuario');
        $this->db->order_by('u.nombres');
        return $this->db->get()->result();
    }

    public function modificarUsuario($idUsuario, $data)
    {
        $data['fechaActualizacion'] = date('Y-m-d H:i:s');
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function listaUsuariosDeshabilitados()
{
    $this->db->where('estado', 0);
    return $this->db->get('USUARIO')->result();
}
    public function contar_usuarios()
    {
        return $this->db->count_all('USUARIO');
    }

    public function verificar_cuenta($token)
    {
        $this->db->where('tokenVerificacion', $token);
        $this->db->where('fechaToken >', date('Y-m-d H:i:s', strtotime('-24 hours')));
        $usuario = $this->db->get('USUARIO')->row();
    
        if ($usuario) {
            $this->db->where('idUsuario', $usuario->idUsuario);
            $this->db->update('USUARIO', [
                'verificado' => 1,
                'tokenVerificacion' => null,
                'fechaToken' => null,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);
            return true;
        }
        return false;
    }

    public function actualizar_password($idUsuario, $nueva_password, $requerir_cambio = false)
    {
        $data = [
            'password' => password_hash($nueva_password, PASSWORD_DEFAULT),
            'cambioPasswordRequerido' => $requerir_cambio ? 1 : 0,
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function cambiar_estado_usuario($idUsuario, $estado)
    {
        $data = [
            'estado' => $estado,
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function obtener_por_email($email)
    {
        return $this->db->get_where('USUARIO', ['email' => $email])->row();
    }

    public function actualizar_token_verificacion($idUsuario, $token)
    {
        $data = [
            'tokenVerificacion' => $token,
            'fechaToken' => date('Y-m-d H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('idUsuario', $idUsuario);
        $this->db->update('USUARIO', $data);
        
        return $this->db->affected_rows() > 0;
    }
    public function actualizar_token_verificacion1($idUsuario, $nuevoToken)
    {
        $data = [
            'tokenVerificacion' => $nuevoToken,
            'fechaToken' => date('Y-m-d H:i:s')
        ];
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function actualizar_preferencias_notificacion($idUsuario, $preferencias)
    {
        $data = [
            'preferenciasNotificacion' => json_encode($preferencias),
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function obtener_preferencias_notificacion($idUsuario)
    {
        $this->db->select('preferenciasNotificacion');
        $this->db->where('idUsuario', $idUsuario);
        $result = $this->db->get('USUARIO')->row();
        
        if ($result && $result->preferenciasNotificacion) {
            return json_decode($result->preferenciasNotificacion, true);
        }
        
        return []; // Retorna un array vacío si no hay preferencias
    }
    public function recuperarUsuario($idUsuario)
{
    $this->db->where('idUsuario', $idUsuario);
    $query = $this->db->get('USUARIO');
    return $query->row();
}


    // Nuevas funciones basadas en el controlador...

    public function contar_usuarios_activos()
    {
        $this->db->where('estado', 1);
        return $this->db->count_all_results('USUARIO');
    }

    public function obtener_usuario_por_id_cambio_password($id_cambio_password)
    {
        return $this->db->get_where('USUARIO', ['idUsuario' => $id_cambio_password])->row();
    }

    public function obtener_prestamos_activos_usuario($idUsuario) {
        $this->db->select('COUNT(DISTINCT p.idPrestamo) as total');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PRESTAMO p', 'p.idSolicitud = sp.idSolicitud');
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'p.estadoPrestamo' => 1,
            'sp.estado' => 1
        ]);
        return $this->db->get()->row()->total;
    }


    public function obtener_proximas_devoluciones_usuario($idUsuario) {
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            pub.titulo,
            pub.idPublicacion,
            ds.observaciones
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PRESTAMO p', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'p.estadoPrestamo' => 1,
            'p.horaDevolucion IS NULL'
        ]);
        $this->db->order_by('p.fechaPrestamo', 'ASC');
        return $this->db->get()->result();
    }


    public function obtener_historial_prestamos($idUsuario) {
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.horaDevolucion,
            p.estadoPrestamo,
            p.estadoDevolucion,
            pub.titulo,
            pub.idPublicacion,
            e.nombreEditorial,
            t.nombreTipo
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PRESTAMO p', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'pub.idTipo = t.idTipo');
        $this->db->where('sp.idUsuario', $idUsuario);
        $this->db->order_by('p.fechaPrestamo', 'DESC');
        return $this->db->get()->result();
    }

    public function actualizar_usuario($idUsuario, $datos)
    {
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $datos);
    }
    public function obtener_usuario_no_verificado($email)
    {
        return $this->db->get_where('USUARIO', ['email' => $email, 'verificado' => 0])->row();
    }

    public function incrementar_intentos_verificacion($idUsuario)
    {
        $this->db->set('intentosVerificacion', 'intentosVerificacion + 1', FALSE);
        $this->db->where('idUsuario', $idUsuario);
        $this->db->update('USUARIO');
    }
    public function guardar_token_recuperacion($idUsuario, $token) {
        $data = array(
            'tokenRecuperacion' => $token,
            'fechaTokenRecuperacion' => date('Y-m-d H:i:s')
        );
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function obtener_por_token_recuperacion($token) {
        $this->db->where('tokenRecuperacion', $token);
        $this->db->where('fechaTokenRecuperacion >', date('Y-m-d H:i:s', strtotime('-24 hours')));
        return $this->db->get('USUARIO')->row();
    }

    public function eliminar_token_recuperacion($idUsuario) {
        $data = array(
            'tokenRecuperacion' => NULL,
            'fechaTokenRecuperacion' => NULL
        );
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    /*public function obtener_admins_encargados() {
        $this->db->select('idUsuario, nombres, apellidoPaterno, email');
        $this->db->from('USUARIO');
        $this->db->where_in('rol', ['administrador', 'encargado']);
        $this->db->where('estado', 1); // Asumiendo que 1 significa activo
        return $this->db->get()->result();
    }*/
    public function obtener_admins_encargados() {
        log_message('debug', '=== INICIO OBTENER_ADMINS_ENCARGADOS ===');
        
        $this->db->select('idUsuario, nombres, apellidoPaterno, email, rol');
        $this->db->from('USUARIO');
        $this->db->where_in('rol', ['administrador', 'encargado']);
        $this->db->where('estado', 1);
        
        $query = $this->db->get();
        $resultado = $query->result();
        
        log_message('debug', 'Query ejecutado: ' . $this->db->last_query());
        log_message('debug', 'Número de admins/encargados encontrados: ' . count($resultado));
        foreach ($resultado as $admin) {
            log_message('debug', 'Admin/Encargado encontrado - ID: ' . $admin->idUsuario . ', Rol: ' . $admin->rol);
        }
        
        log_message('debug', '=== FIN OBTENER_ADMINS_ENCARGADOS ===');
        return $resultado;
    }

 
    public function actualizar_configuracion($idUsuario, $nuevo_username, $nueva_password) {
        $data = [
            'username' => $nuevo_username,
            'password' => password_hash($nueva_password, PASSWORD_DEFAULT),
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        ];
        
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }
    public function obtener_encargados_activos() {
        log_message('debug', "\n==== INICIO obtener_encargados_activos() ====");
        
        $this->db->select('idUsuario, nombres, apellidoPaterno, rol');
        $this->db->from('USUARIO');
        $this->db->where('rol', 'encargado');
        $this->db->where('estado', 1);
        
        $encargados = $this->db->get()->result();
        
        log_message('debug', 'Query ejecutado: ' . $this->db->last_query());
        log_message('debug', 'Número de encargados encontrados: ' . count($encargados));
        foreach ($encargados as $encargado) {
            log_message('debug', 'Encargado encontrado - ID: ' . $encargado->idUsuario . ', Rol: ' . $encargado->rol);
        }
        
        log_message('debug', "==== FIN obtener_encargados_activos() ====\n");
        return $encargados;
    }
    
}
