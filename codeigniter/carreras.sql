# Host: localhost  (Version 5.5.5-10.4.17-MariaDB)
# Date: 2024-08-30 11:01:54
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "carreras"
#

CREATE TABLE `carreras` (
  `idCarrera` int(11) NOT NULL AUTO_INCREMENT,
  `carrera` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idCarrera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "carreras"
#

INSERT INTO `carreras` VALUES (1,'SISTEMAS'),(2,'DERECHO'),(3,'ADMINISTRACION');
