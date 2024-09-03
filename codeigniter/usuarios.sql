# Host: localhost  (Version 5.5.5-10.4.17-MariaDB)
# Date: 2024-08-30 11:02:31
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "usuarios"
#

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Data for table "usuarios"
#

INSERT INTO `usuarios` VALUES (1,'amendez','c0ecb37749cbe64e5c8ac4d35eec3e54','admin'),(2,'pcanedo','c0ecb37749cbe64e5c8ac4d35eec3e54','guest');
