-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sis_asistencia
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `amonestacion`
--

DROP TABLE IF EXISTS `amonestacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amonestacion` (
  `id_amonestacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `gravedad` enum('Leve','Moderada','Grave') NOT NULL,
  `observacion` text DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_amonestacion`),
  KEY `fk_amonestacion_empleado` (`id_empleado`),
  CONSTRAINT `fk_amonestacion_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `amonestacion`
--

LOCK TABLES `amonestacion` WRITE;
/*!40000 ALTER TABLE `amonestacion` DISABLE KEYS */;
INSERT INTO `amonestacion` VALUES (1,17,'3 dias sin asistir injustificadamente','Moderada','empleado debe de avisar y/o justificar su ausencia al trabajo','2026-04-28 03:09:41'),(3,21,'3 dias sin asistir injustificadamente','Grave','','2026-05-17 12:36:32');
/*!40000 ALTER TABLE `amonestacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistencia`
--

DROP TABLE IF EXISTS `asistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `entrada` datetime DEFAULT NULL,
  `salida` datetime DEFAULT NULL,
  `biometrico_id` varchar(255) DEFAULT NULL,
  `estado_biometrico` enum('exito','fallo') DEFAULT NULL,
  PRIMARY KEY (`id_asistencia`),
  KEY `fk2` (`id_empleado`),
  CONSTRAINT `fk2` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia`
--

LOCK TABLES `asistencia` WRITE;
/*!40000 ALTER TABLE `asistencia` DISABLE KEYS */;
INSERT INTO `asistencia` VALUES (25,2,'2026-03-04 08:00:00','2026-03-04 17:00:00','biometrico_test_1','exito'),(26,4,'2026-03-04 08:30:00','2026-03-04 17:30:00','biometrico_test_2','exito'),(28,11,'2026-03-22 08:30:00','2026-03-22 17:30:00','biometrico_test_2','exito'),(29,15,'2026-03-22 09:00:00','2026-03-22 18:00:00','biometrico_test_4','exito'),(30,14,'2026-03-22 09:30:00','2026-03-22 18:30:00','biometrico_test_6','exito'),(31,15,'2026-04-07 01:46:56','2026-04-07 01:47:35','sim_69d49a50821fc','exito'),(32,14,'2026-04-07 01:48:26','2026-04-07 01:57:16','sim_69d49aaa71e58','exito'),(33,16,'2026-04-07 02:00:06','2026-04-07 02:00:34','sim_69d49d6658617','exito'),(34,17,'2026-04-07 02:06:28','2026-04-07 02:08:58','sim_69d49ee4c0802','exito'),(35,2,'2026-04-07 02:14:30','2026-04-07 02:15:45','sim_69d4a0c658edc','exito'),(36,4,'2026-04-07 02:15:17','2026-04-07 02:19:04','sim_69d4a0f55f1e7','exito'),(37,11,'2026-04-07 02:17:33','2026-04-07 02:18:24','sim_69d4a17dcc7c2','exito'),(38,15,'2026-04-07 02:33:04','2026-04-07 03:06:46','sim_69d4a52026991','exito'),(39,16,'2026-04-07 10:19:54','2026-04-07 10:20:10','sim_69d5128a5381a','exito'),(40,17,'2026-04-12 07:38:38','2026-04-12 09:55:04','sim_69db843ea4375','exito'),(41,15,'2026-04-12 11:48:07',NULL,'sim_69dbbeb7ab5e6','exito'),(42,4,'2026-04-12 11:48:28','2026-04-12 11:48:53','sim_69dbbecc0c91d','exito'),(43,19,'2026-04-12 12:04:33','2026-04-12 12:22:20','sim_69dbc291f0a2b','exito'),(44,2,'2026-04-16 10:52:34','2026-04-16 10:56:06','sim_69e0f7b2b529a','exito'),(45,16,'2026-04-18 23:04:50',NULL,'sim_69e44652ab8e8','exito'),(46,16,'2026-04-18 23:05:46',NULL,'sim_69e4468af0800','exito'),(47,16,'2026-04-18 23:06:09',NULL,'sim_69e446a10908f','exito'),(48,17,'2026-04-19 11:59:38','2026-04-19 12:00:39','sim_69e4fbeac68ad','exito'),(49,15,'2026-04-23 03:21:27','2026-04-23 03:46:56','15','exito'),(50,11,'2026-04-23 03:28:49','2026-04-23 03:35:22','11','exito'),(51,19,'2026-04-23 03:41:22','2026-04-23 03:41:49','19','exito'),(52,4,'2026-04-23 03:42:38','2026-04-23 03:42:59','4','exito'),(53,16,'2026-04-23 03:45:09','2026-04-23 03:45:18','16','exito'),(54,18,'2026-04-23 03:51:01','2026-04-23 03:51:57','18','exito'),(55,2,'2026-04-23 03:53:20','2026-04-23 03:53:27','2','exito'),(56,19,'2026-04-26 00:46:50','2026-04-26 01:33:37','19','exito'),(57,15,'2026-04-26 01:31:47','2026-04-26 01:33:03','15','exito'),(58,4,'2026-04-26 01:32:19','2026-04-26 01:33:50','4','exito'),(59,16,'2026-04-26 02:28:18','2026-04-26 02:28:49','16','exito'),(60,19,'2026-04-30 09:05:54','2026-04-30 09:06:24','19','exito'),(61,4,'2026-04-30 10:14:06','2026-04-30 10:16:09','4','exito'),(62,15,'2026-04-30 10:15:20','2026-04-30 10:30:08','15','exito'),(63,19,'2026-05-08 00:29:13','2026-05-08 02:41:54','19','exito'),(64,4,'2026-05-08 00:29:31','2026-05-08 00:54:13','4','exito'),(65,15,'2026-05-08 00:32:01','2026-05-08 02:43:01','15','exito'),(66,16,'2026-05-08 01:02:39','2026-05-08 01:35:17','16','exito'),(67,18,'2026-05-08 01:10:30','2026-05-08 01:35:22','18','exito'),(68,2,'2026-05-08 01:11:11','2026-05-08 02:44:06','2','exito'),(69,14,'2026-05-08 01:31:43','2026-05-08 01:35:32','14','exito'),(70,17,'2026-05-08 01:36:11','2026-05-08 02:43:21','17','exito'),(71,20,'2026-05-08 02:42:51','2026-05-08 02:43:29','20','exito'),(72,20,'2026-05-09 01:28:49','2026-05-09 01:30:47','20','exito'),(73,4,'2026-05-09 01:29:37','2026-05-09 01:35:19','4','exito'),(74,15,'2026-05-09 01:38:02','2026-05-09 01:39:51','15','exito'),(75,17,'2026-05-09 02:24:09','2026-05-09 02:26:12','17','exito'),(76,18,'2026-05-09 02:31:45','2026-05-09 02:31:48','18','exito'),(77,14,'2026-05-09 02:35:27','2026-05-09 02:35:52','14','exito'),(78,20,'2026-05-13 02:08:57','2026-05-13 02:12:07','20','exito'),(79,15,'2026-05-13 02:12:34','2026-05-13 02:13:13','15','exito'),(80,16,'2026-05-13 02:16:55','2026-05-13 02:21:32','16','exito'),(81,18,'2026-05-13 04:23:14','2026-05-13 04:23:29','18','exito'),(82,20,'2026-05-17 11:17:20','2026-05-17 11:18:00','20','exito'),(83,4,'2026-05-17 11:17:48',NULL,'4','exito'),(84,21,'2026-05-17 11:21:47','2026-05-17 11:22:51','21','exito'),(85,15,'2026-05-17 11:25:15',NULL,'15','exito'),(86,17,'2026-05-28 09:24:33','2026-05-28 09:25:34','17','exito'),(87,15,'2026-05-28 09:25:55','2026-05-28 09:26:08','15','exito');
/*!40000 ALTER TABLE `asistencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoria` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `detalle` text NOT NULL,
  `ip` varchar(45) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_auditoria`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria`
--

LOCK TABLES `auditoria` WRITE;
/*!40000 ALTER TABLE `auditoria` DISABLE KEYS */;
INSERT INTO `auditoria` VALUES (1,'lui','Actualizar','Nómina','Actualizó tasa de cambio a Bs. 490.48','::1','2026-05-07 00:20:55'),(2,'lui','Actualizar','Nómina','Actualizó configuración salarial (Base: 220, Cesta Ticket: 59.95)','::1','2026-05-07 00:44:35'),(3,'lui','Registrar','Permisos','Registró Vacaciones para: Luis  Laya del 2026-06-29 al 2026-07-08','::1','2026-05-07 02:39:10'),(4,'lui','REGISTRO','Cajas','Registró cuadre #1. Dif: 9001834.94 (Sobrante).','::1','2026-05-15 06:44:35'),(5,'lui','REGISTRO','Cajas','Registró cuadre #2. Dif: -64.43 (Faltante).','::1','2026-05-15 07:00:44'),(6,'lui','REGISTRO','Cajas','Registró cuadre #3. Dif: -173 (Faltante).','::1','2026-05-17 13:13:33');
/*!40000 ALTER TABLE `auditoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bonificacion`
--

DROP TABLE IF EXISTS `bonificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bonificacion` (
  `id_bono` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `anio` int(11) NOT NULL,
  `quincena` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `monto_usd` decimal(10,2) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_bono`),
  KEY `id_empleado` (`id_empleado`),
  CONSTRAINT `bonificacion_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bonificacion`
--

LOCK TABLES `bonificacion` WRITE;
/*!40000 ALTER TABLE `bonificacion` DISABLE KEYS */;
INSERT INTO `bonificacion` VALUES (1,15,5,2026,1,'Bono de Producción/Desempeño',149.96,'2026-05-07 02:36:39'),(2,15,5,2026,2,'Horas Extras',40.00,'2026-05-21 13:34:27');
/*!40000 ALTER TABLE `bonificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caja_cuadres`
--

DROP TABLE IF EXISTS `caja_cuadres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja_cuadres` (
  `id_cuadre` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `id_empleado` int(11) NOT NULL,
  `turno` enum('Mañana','Noche') NOT NULL DEFAULT 'Mañana',
  `fondo_apertura_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fondo_apertura_bs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tasa_dia` decimal(10,2) NOT NULL,
  `ventas_sistema_bs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ventas_sistema_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gastos_caja_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `punto_venta_bs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pago_movil_bs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `zelle_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cashea_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `efectivo_fisico_bs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `efectivo_fisico_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `diferencia_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('Cuadrada','Faltante','Sobrante') NOT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_cuadre`),
  KEY `id_empleado` (`id_empleado`),
  CONSTRAINT `caja_cuadres_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja_cuadres`
--

LOCK TABLES `caja_cuadres` WRITE;
/*!40000 ALTER TABLE `caja_cuadres` DISABLE KEYS */;
INSERT INTO `caja_cuadres` VALUES (1,'2026-05-15 06:44:35',15,'Mañana',10000.00,0.00,490.48,100000.00,1000000.00,10000.00,0.00,0.00,0.00,0.00,1000000.00,10000000.00,9001834.94,'Sobrante',''),(2,'2026-05-15 07:00:44',18,'Mañana',40.00,20000.00,490.48,39238.00,20.00,0.00,0.00,0.00,0.00,0.00,14999.99,30.00,-64.43,'Faltante','la caja 1 le falto 64.43$ en ventas'),(3,'2026-05-17 13:13:33',19,'Noche',333.00,200000.00,490.48,0.00,0.00,0.00,200000.00,0.00,0.00,0.00,0.00,160.00,-173.00,'Faltante','falto');
/*!40000 ALTER TABLE `caja_cuadres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargo`
--

DROP TABLE IF EXISTS `cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cargo` (
  `id_cargo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_cargo`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargo`
--

LOCK TABLES `cargo` WRITE;
/*!40000 ALTER TABLE `cargo` DISABLE KEYS */;
INSERT INTO `cargo` VALUES (1,'gerente'),(2,'pollero'),(3,'cocinera'),(4,'mantenimiento'),(5,'supervisor/a'),(6,'despachador/a'),(8,'pizzero'),(10,'hamburguesero'),(12,'administrativo');
/*!40000 ALTER TABLE `cargo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deduccion`
--

DROP TABLE IF EXISTS `deduccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deduccion` (
  `id_deduccion` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `anio` int(11) NOT NULL,
  `quincena` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `monto_usd` decimal(10,2) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_deduccion`),
  KEY `id_empleado` (`id_empleado`),
  CONSTRAINT `deduccion_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deduccion`
--

LOCK TABLES `deduccion` WRITE;
/*!40000 ALTER TABLE `deduccion` DISABLE KEYS */;
INSERT INTO `deduccion` VALUES (1,15,5,2026,1,'Deuda Pendiente',10.00,'2026-05-07 02:09:34');
/*!40000 ALTER TABLE `deduccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `destacado`
--

DROP TABLE IF EXISTS `destacado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `destacado` (
  `id_destacado` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `nivel` enum('Oro','Plata','Bronce') NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_destacado`),
  KEY `k_destacado_empleado` (`id_empleado`),
  CONSTRAINT `k_destacado_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `destacado`
--

LOCK TABLES `destacado` WRITE;
/*!40000 ALTER TABLE `destacado` DISABLE KEYS */;
INSERT INTO `destacado` VALUES (1,16,'buena distincion, limpieza extrema y buen trabajo en general.','Plata','2026-04-28 02:52:33'),(2,11,'es lo ma duro del bloque','Bronce','2026-04-28 02:59:48');
/*!40000 ALTER TABLE `destacado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documento_empleado`
--

DROP TABLE IF EXISTS `documento_empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documento_empleado` (
  `id_documento` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `nombre_encriptado` varchar(255) NOT NULL,
  `tipo_documento` enum('Contrato','Identidad','Salud','Laboral','Otro') NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `fecha_vence` date DEFAULT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_documento`),
  KEY `id_empleado` (`id_empleado`),
  CONSTRAINT `documento_empleado_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documento_empleado`
--

LOCK TABLES `documento_empleado` WRITE;
/*!40000 ALTER TABLE `documento_empleado` DISABLE KEYS */;
/*!40000 ALTER TABLE `documento_empleado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleado`
--

DROP TABLE IF EXISTS `empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empleado` (
  `id_empleado` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `dni` varchar(255) NOT NULL,
  `cargo` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Suspendido') NOT NULL DEFAULT 'Activo',
  `salario_base` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_empleado`),
  KEY `fk1` (`cargo`),
  CONSTRAINT `fk1` FOREIGN KEY (`cargo`) REFERENCES `cargo` (`id_cargo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleado`
--

LOCK TABLES `empleado` WRITE;
/*!40000 ALTER TABLE `empleado` DISABLE KEYS */;
INSERT INTO `empleado` VALUES (2,'josep','vega chavez','E-77441122',4,'emp_1775289921_69d0c64136986.jpg','Activo',0.00),(4,'maria','molina gutierrez','V-00225566',4,'emp_1775289949_69d0c65d7458e.jpeg','Activo',0.00),(11,'prueba','prueba','V-00225588',4,'emp_1775289988_69d0c6841da66.jpeg','Activo',0.00),(14,'prueba25','prueba25','V-32419369',1,'emp_1775290004_69d0c694822f3.jpg','Activo',0.00),(15,'Luis ','Laya','V-31579209',2,'emp_1775289935_69d0c64f08f95.jpg','Activo',0.00),(16,'adrian','laya','V-10872499',2,'emp_1775289700_69d0c5641b052.jpg','Activo',0.00),(17,'Elimar ','Iruiz','V-14409686',5,'emp_1775292064_69d0cea03cb3d.jpeg','Activo',0.00),(18,'Emely ','perez','V-15637890',6,'emp_1775543927_69d4a677c3bd4.jpeg','Activo',0.00),(19,'david','zurita','V-24377686',2,'emp_1776009288_69dbc0488a217.jpeg','Activo',0.00),(20,'pedro','martinez','E-3456778900',4,'emp_1778222179_69fd8463c0f7a.jpg','Activo',0.00),(21,'javier','hernandez','V-20774705',5,'emp_1779035700_6a09ee340b3e0.jpg','Activo',0.00);
/*!40000 ALTER TABLE `empleado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institucion`
--

DROP TABLE IF EXISTS `institucion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institucion` (
  `id_institucion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `ruc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_institucion`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institucion`
--

LOCK TABLES `institucion` WRITE;
/*!40000 ALTER TABLE `institucion` DISABLE KEYS */;
INSERT INTO `institucion` VALUES (1,'Salon del pollo C.A','34567890123456789','','1234567890123');
/*!40000 ALTER TABLE `institucion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `justificacion_inasistencia`
--

DROP TABLE IF EXISTS `justificacion_inasistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justificacion_inasistencia` (
  `id_justificacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('falta','reposo','vacaciones','permiso') NOT NULL DEFAULT 'falta',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_justificacion`),
  UNIQUE KEY `id_empleado` (`id_empleado`,`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `justificacion_inasistencia`
--

LOCK TABLES `justificacion_inasistencia` WRITE;
/*!40000 ALTER TABLE `justificacion_inasistencia` DISABLE KEYS */;
INSERT INTO `justificacion_inasistencia` VALUES (1,18,'2026-04-07','falta','2026-04-07 07:13:13'),(17,15,'2026-04-12','permiso','2026-04-12 12:26:51'),(21,14,'2026-04-12','falta','2026-04-12 11:45:48'),(23,4,'2026-04-12','falta','2026-04-12 12:06:54'),(25,18,'2026-04-12','vacaciones','2026-04-12 12:25:57'),(29,2,'2026-04-12','permiso','2026-04-12 16:23:26'),(32,4,'2026-04-16','vacaciones','2026-04-16 14:57:00'),(33,2,'2026-04-19','vacaciones','2026-04-19 15:57:53'),(34,4,'2026-04-28','falta','2026-04-28 07:22:22'),(36,2,'2026-04-28','falta','2026-04-28 07:22:29'),(46,4,'2026-05-13','falta','2026-05-13 06:21:37');
/*!40000 ALTER TABLE `justificacion_inasistencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nomina`
--

DROP TABLE IF EXISTS `nomina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nomina` (
  `id_nomina` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `anio` int(11) NOT NULL,
  `quincena` int(11) NOT NULL COMMENT '1 o 2',
  `salario_base_bs` decimal(10,2) NOT NULL,
  `tasa_dolar_dia` decimal(10,2) NOT NULL,
  `dias_trabajados` int(11) NOT NULL,
  `dias_permiso` int(11) NOT NULL DEFAULT 0,
  `faltas_injustificadas` int(11) NOT NULL DEFAULT 0,
  `bonos_bs` decimal(10,2) DEFAULT 0.00,
  `deducciones_bs` decimal(10,2) DEFAULT 0.00,
  `total_pagar_bs` decimal(10,2) NOT NULL,
  `total_pagar_usd` decimal(10,2) NOT NULL,
  `estado` enum('Pendiente','Pagado') DEFAULT 'Pendiente',
  `fecha_generacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_nomina`),
  KEY `id_empleado` (`id_empleado`),
  CONSTRAINT `nomina_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nomina`
--

LOCK TABLES `nomina` WRITE;
/*!40000 ALTER TABLE `nomina` DISABLE KEYS */;
/*!40000 ALTER TABLE `nomina` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `tipo` enum('Vacaciones','Permiso Médico','Asunto Personal') NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `motivo` text DEFAULT NULL,
  `estado` enum('Pendiente','Aprobado','Rechazado') DEFAULT 'Pendiente',
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_permiso`),
  KEY `id_empleado` (`id_empleado`),
  CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (1,15,'Vacaciones','2026-06-29','2026-07-08','vacaciones administrativas','Aprobado','2026-05-07 02:39:10');
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasa_cambio`
--

DROP TABLE IF EXISTS `tasa_cambio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasa_cambio` (
  `id_tasa` int(11) NOT NULL AUTO_INCREMENT,
  `tasa` decimal(10,2) NOT NULL,
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sueldo_base_usd` decimal(10,2) NOT NULL DEFAULT 210.00,
  `cesta_ticket_usd` decimal(10,2) NOT NULL DEFAULT 30.00,
  PRIMARY KEY (`id_tasa`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasa_cambio`
--

LOCK TABLES `tasa_cambio` WRITE;
/*!40000 ALTER TABLE `tasa_cambio` DISABLE KEYS */;
INSERT INTO `tasa_cambio` VALUES (1,490.48,'2026-05-07 03:00:59',220.00,60.00);
/*!40000 ALTER TABLE `tasa_cambio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  `pregunta_seguridad` varchar(255) DEFAULT NULL,
  `respuesta_seguridad` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (16,'lui','layaluis013@gmail.com','$2y$10$0JwGYUhyyCEnyShu0kh5Z.dD/DSM7yfnf6XSYkBnuvsw/Rdihchf6','admin','color','$2y$10$Qnd5EMTSCFZScgcxtTRSJe7Uph7IF1.6xvQAXMSURBZkCGivKKrXm'),(20,'Vanessa','vanessa@gmail.com','$2y$10$bPuWyB2kWt.FPz8m0DowGe4PuQqZvZocIzDyv.6jGMfyQnGkjrXJa','user',NULL,NULL),(23,'valeria','Valeria@gmail.com','$2y$10$T7Sp/szluk5mnkwEKfv7fujovOmgcU4ouJSLFJpCbPppD9N1oEm9a','admin',NULL,NULL),(24,'user','nose@gmail.com','$2y$10$IKk9ty0lvqeqL4K9FhZkyu.2020DSbVlqVirRvETZrN9mjxjqWhb2','user',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pregunta_seguridad` varchar(255) DEFAULT NULL,
  `respuesta_seguridad` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios_roles`
--

DROP TABLE IF EXISTS `usuarios_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios_roles`
--

LOCK TABLES `usuarios_roles` WRITE;
/*!40000 ALTER TABLE `usuarios_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuarios_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-14 10:10:46
