-- Exportacion real con mysqldump: esquema y catalogo inicial, sin datos personales.
CREATE DATABASE IF NOT EXISTS nexo_business CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexo_business;
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: nexo_business
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
-- Table structure for table `agents`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(80) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('analisis','estrategia','contenido','comercial','operaciones','soporte') NOT NULL,
  `summary` varchar(300) NOT NULL,
  `purpose` text NOT NULL,
  `deliverable` text NOT NULL,
  `limitations` text NOT NULL,
  `icon` varchar(30) NOT NULL,
  `color` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_agent_category` (`is_active`,`category`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agents`
--

LOCK TABLES `agents` WRITE;
/*!40000 ALTER TABLE `agents` DISABLE KEYS */;
INSERT INTO `agents` VALUES (1,'analista-negocio','Analista de negocio','analisis','De datos dispersos a una lectura clara de tu negocio.','Propuesta para organizar los indicadores aportados por una empresa, identificar preguntas y preparar una lectura inicial de tendencias.','Borrador de informe con indicadores, supuestos, preguntas abiertas y prioridades para revisión.','Requiere datos facilitados por la empresa y revisión profesional. No accede a sistemas ni toma decisiones financieras.','analyst','violet',1),(2,'estratega-campanas','Estratega de campañas','estrategia','Una dirección común para objetivos, públicos y canales.','Propuesta para transformar un briefing comercial en un esquema de campaña con objetivos, públicos y acciones coordinadas.','Propuesta de campaña con mensajes clave, canales sugeridos y calendario inicial.','No publica campañas, compra publicidad ni garantiza resultados. Presupuesto y decisiones deben aprobarse por la empresa.','campaign','amber',1),(3,'redactor-marca','Redactor de marca','contenido','Textos consistentes con la voz de tu empresa.','Propuesta para desarrollar borradores de contenido a partir de una guía de marca, una audiencia y un objetivo definidos.','Borradores de piezas y variantes de tono, con puntos que requieren comprobación editorial.','No inventa testimonios ni sustituye la revisión de hechos, derechos o requisitos legales. No publica contenido.','writer','mint',1),(4,'asistente-comercial','Asistente comercial','comercial','Mejor contexto antes de cada conversación de venta.','Propuesta para ordenar información aportada sobre oportunidades comerciales y preparar seguimientos adaptados a cada contexto.','Fichas de oportunidad y borradores de seguimiento para aprobación del equipo.','No contacta a clientes, accede a un CRM ni negocia en nombre de la empresa. Las comunicaciones requieren revisión humana.','sales','violet',1),(5,'coordinador-proyectos','Coordinador de proyectos','operaciones','Objetivos, dependencias y próximos pasos en una misma vista.','Propuesta para estructurar un proyecto a partir de sus objetivos, alcance, equipo y restricciones declaradas.','Plan inicial con tareas, dependencias, responsables propuestos y riesgos por revisar.','No asigna tareas reales ni modifica herramientas externas. Fechas y recursos deben validarse con el equipo.','project','cyan',1),(6,'asistente-soporte','Asistente de soporte','soporte','Respuestas más claras a partir de tu conocimiento interno.','Propuesta para clasificar consultas y preparar respuestas basadas en una base de conocimiento facilitada y revisada por la empresa.','Clasificación de consultas y borradores de respuesta con casos que requieren escalado.','No atiende canales reales, resuelve incidencias técnicas ni accede a datos de clientes. Las respuestas deben revisarse antes de enviarse.','support','mint',1);
/*!40000 ALTER TABLE `agents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuration_agents`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuration_agents` (
  `config_id` int(10) unsigned NOT NULL,
  `agent_id` int(10) unsigned NOT NULL,
  `position` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`config_id`,`agent_id`),
  UNIQUE KEY `uq_configuration_position` (`config_id`,`position`),
  KEY `agent_id` (`agent_id`),
  CONSTRAINT `configuration_agents_ibfk_1` FOREIGN KEY (`config_id`) REFERENCES `configurations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `configuration_agents_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuration_agents`
--

LOCK TABLES `configuration_agents` WRITE;
/*!40000 ALTER TABLE `configuration_agents` DISABLE KEYS */;
/*!40000 ALTER TABLE `configuration_agents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configurations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `objective` text NOT NULL,
  `sector` varchar(80) NOT NULL,
  `plan` enum('basico','business','enterprise') NOT NULL DEFAULT 'basico',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `configurations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configurations`
--

LOCK TABLES `configurations` WRITE;
/*!40000 ALTER TABLE `configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requests`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `configuration_id` int(10) unsigned DEFAULT NULL,
  `company` varchar(160) NOT NULL,
  `contact_email` varchar(190) NOT NULL,
  `sector` varchar(80) NOT NULL,
  `objective` text NOT NULL,
  `plan` enum('basico','business','enterprise') NOT NULL,
  `message` text NOT NULL,
  `status` enum('nueva','revisada') NOT NULL DEFAULT 'nueva',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `configuration_id` (`configuration_id`),
  KEY `idx_request_owner` (`user_id`,`created_at`),
  CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`configuration_id`) REFERENCES `configurations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requests`
--

LOCK TABLES `requests` WRITE;
/*!40000 ALTER TABLE `requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-18 19:47:46
