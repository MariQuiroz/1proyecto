CREATE DATABASE IF NOT EXISTS bdhemero102;
USE bdhemero102;
 -- DROP database bdhemero102;
-- Tabla USUARIO
CREATE TABLE USUARIO (
    idUsuario INT PRIMARY KEY AUTO_INCREMENT,
    nombres VARCHAR(100) NOT NULL,
    apellidoPaterno VARCHAR(50) NOT NULL,
    apellidoMaterno VARCHAR(50),
    carnet VARCHAR(20) NOT NULL UNIQUE,
    profesion VARCHAR(100),
    fechaNacimiento DATE,
    sexo CHAR(1),
    telefono VARCHAR(15),
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'encargado', 'lector') NOT NULL,
    verificado TINYINT NOT NULL DEFAULT 0,
    tokenVerificacion VARCHAR(255),
    fechaToken DATETIME,
    intentosVerificacion INT,
    preferenciasNotificacion TEXT,
    estado TINYINT NOT NULL DEFAULT 1,
    fechaCreacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    idUsuarioCreador INT NOT NULL,
    cambioPasswordRequerido TINYINT DEFAULT 1,
    tokenRecuperacion VARCHAR(255),
    fechaTokenRecuperacion DATETIME
);

-- Tabla TIPO
CREATE TABLE TIPO (
    idTipo INT PRIMARY KEY AUTO_INCREMENT,
    nombreTipo VARCHAR(100) NOT NULL,
    estado TINYINT NOT NULL DEFAULT 1,
    fechaCreacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    idUsuarioCreador INT NOT NULL
);

-- Tabla EDITORIAL
CREATE TABLE EDITORIAL (
    idEditorial INT PRIMARY KEY AUTO_INCREMENT,
    nombreEditorial VARCHAR(100) NOT NULL,
    estado TINYINT NOT NULL DEFAULT 1,
    fechaCreacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    idUsuarioCreador INT NOT NULL
    
);

-- Tabla PUBLICACION
CREATE TABLE PUBLICACION (
    idPublicacion INT PRIMARY KEY AUTO_INCREMENT,
    idTipo INT NOT NULL,
    idEditorial INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    fechaPublicacion DATE NOT NULL,
    numeroPaginas INT,
    portada VARCHAR(255),
    descripcion TEXT,
    ubicacionFisica VARCHAR(100),
    estado TINYINT NOT NULL DEFAULT 1,
    fechaCreacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    idUsuarioCreador INT NOT NULL,
    FOREIGN KEY (idTipo) REFERENCES TIPO(idTipo),
    FOREIGN KEY (idEditorial) REFERENCES EDITORIAL(idEditorial)

);
-- Tabla SOLICITUD_PRESTAMO
CREATE TABLE SOLICITUD_PRESTAMO (
    idSolicitud INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    fechaSolicitud DATETIME NOT NULL,
    estadoSolicitud TINYINT NOT NULL DEFAULT 1,
    fechaAprobacionRechazo DATETIME,
    estado TINYINT NOT NULL DEFAULT 1,
    fechaCreacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    idUsuarioCreador INT NOT NULL,
    FOREIGN KEY (idUsuario) REFERENCES USUARIO(idUsuario)
  
);
ALTER TABLE SOLICITUD_PRESTAMO
ADD COLUMN observaciones TEXT NULL;
-- Nueva tabla intermedia DETALLE_SOLICITUD
CREATE TABLE DETALLE_SOLICITUD (
    idDetalleSolicitud INT PRIMARY KEY AUTO_INCREMENT,
    idSolicitud INT NOT NULL,
    idPublicacion INT NOT NULL,
    observaciones TEXT,
    FOREIGN KEY (idSolicitud) REFERENCES SOLICITUD_PRESTAMO(idSolicitud),
    FOREIGN KEY (idPublicacion) REFERENCES PUBLICACION(idPublicacion)
);
ALTER TABLE DETALLE_SOLICITUD
ADD COLUMN fechaReserva DATETIME DEFAULT NULL,
ADD COLUMN fechaExpiracionReserva DATETIME DEFAULT NULL,
ADD COLUMN estadoReserva TINYINT DEFAULT 0;
ALTER TABLE DETALLE_SOLICITUD
ADD COLUMN fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
-- Tabla PRESTAMO (modificada)
CREATE TABLE PRESTAMO (
    idPrestamo INT PRIMARY KEY AUTO_INCREMENT,
    idSolicitud INT NOT NULL, -- Cambiamos a idSolicitud para referirnos a la solicitud
    idEncargadoPrestamo INT,
    idEncargadoDevolucion INT,
    fechaPrestamo DATETIME NOT NULL,
    horaInicio TIME NOT NULL,
    horaDevolucion TIME,
    estadoPrestamo TINYINT NOT NULL, -- 1: Activo, 2: Devuelto
    estadoDevolucion TINYINT NOT NULL DEFAULT 1,
    estado TINYINT NOT NULL DEFAULT 1,
    fechaCreacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    idUsuarioCreador INT NOT NULL,
    FOREIGN KEY (idSolicitud) REFERENCES SOLICITUD_PRESTAMO(idSolicitud) -- Relación con solicitud

);
ALTER TABLE PRESTAMO ADD COLUMN fechaDevolucion DATETIME DEFAULT NULL;

UPDATE PRESTAMO SET fechaDevolucion = '2024-11-20 00:00:00'; -- O el valor adecuado.

ALTER TABLE PRESTAMO MODIFY fechaDevolucion DATETIME NOT NULL;
-- estadoDevolucion ENUM('bueno', 'dañado', 'perdido','no devuelto'),
-- Tabla INTERES_PUBLICACION
CREATE TABLE INTERES_PUBLICACION (
    idInteres INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    idPublicacion INT NOT NULL,
    fechaInteres DATETIME NOT NULL,
    estado TINYINT NOT NULL DEFAULT 1,
    FOREIGN KEY (idUsuario) REFERENCES USUARIO(idUsuario),
    FOREIGN KEY (idPublicacion) REFERENCES PUBLICACION(idPublicacion)
);
ALTER TABLE INTERES_PUBLICACION
ADD COLUMN fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;

-- Tabla NOTIFICACION
CREATE TABLE NOTIFICACION (
    idNotificacion INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    tipo TINYINT NOT NULL,
    mensaje TEXT NOT NULL,
    leida TINYINT DEFAULT 0,
    fechaEnvio DATETIME NOT NULL,
    fechaCreacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES USUARIO(idUsuario)
    
);
-- ALTER TABLE NOTIFICACION DROP COLUMN  idReferencia;
CREATE TABLE IF NOT EXISTS PREFERENCIAS_NOTIFICACION (
    idPreferencia INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    notificarDisponibilidad TINYINT DEFAULT 1,
    notificarEmail TINYINT DEFAULT 1,
    notificarSistema TINYINT DEFAULT 1,
    fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES USUARIO(idUsuario)
);
INSERT INTO USUARIO (
    nombres,
    apellidoPaterno,
    apellidoMaterno,
    carnet,
    profesion,
    fechaNacimiento,
    sexo,
    telefono,
    email,
    username,
    password,
    rol,
    verificado,
    estado,
    fechaCreacion,
    idUsuarioCreador,
    cambioPasswordRequerido
) VALUES (
    'Admin',
    'Sistema',
    'Hemeroteca',
    'ADMIN001',
    'Administrador de Sistemas',
    '1990-01-01',
    'F',
    '12345678',
    'quiroz.maritza.871@gmail.com',
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- "password"
    'administrador',
    1,
    1,
    NOW(),
    1,
    0
);

