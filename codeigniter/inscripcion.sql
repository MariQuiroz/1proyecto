# Host: localhost  (Version 5.5.5-10.4.17-MariaDB)
# Date: 2024-08-30 11:02:19
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "inscripcion"
#

CREATE TABLE `inscripcion` (
  `idInscripcion` int(11) NOT NULL AUTO_INCREMENT,
  `idCarrera` int(11) NOT NULL DEFAULT 0,
  `idEstudiante` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idInscripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "inscripcion"
#

INSERT INTO `inscripcion` VALUES (1,2,24),(2,1,26),(3,2,28),(4,1,29);
