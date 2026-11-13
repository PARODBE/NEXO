-- NEXO: catálogo B2B y configuraciones comerciales. Sin ejecución de IA ni claves API.
-- Base nueva para no borrar datos de instalaciones anteriores.
CREATE DATABASE IF NOT EXISTS nexo_business CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexo_business;
CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(80) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS agents (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 slug VARCHAR(80) NOT NULL UNIQUE,
 name VARCHAR(100) NOT NULL,
 category ENUM('analisis','estrategia','contenido','comercial','operaciones','soporte') NOT NULL,
 summary VARCHAR(300) NOT NULL,
 purpose TEXT NOT NULL,
 deliverable TEXT NOT NULL,
 limitations TEXT NOT NULL,
 icon VARCHAR(30) NOT NULL,
 color VARCHAR(20) NOT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 INDEX idx_agent_category (is_active,category)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS configurations (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 name VARCHAR(100) NOT NULL,
 objective TEXT NOT NULL,
 sector VARCHAR(80) NOT NULL,
 plan ENUM('basico','business','enterprise') NOT NULL DEFAULT 'basico',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS configuration_agents (
 config_id INT UNSIGNED NOT NULL,
 agent_id INT UNSIGNED NOT NULL,
 position SMALLINT UNSIGNED NOT NULL,
 PRIMARY KEY (config_id,agent_id),
 UNIQUE KEY uq_configuration_position (config_id,position),
 FOREIGN KEY (config_id) REFERENCES configurations(id) ON DELETE CASCADE,
 FOREIGN KEY (agent_id) REFERENCES agents(id)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS requests (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NULL,
 configuration_id INT UNSIGNED NULL,
 company VARCHAR(160) NOT NULL,
 contact_email VARCHAR(190) NOT NULL,
 sector VARCHAR(80) NOT NULL,
 objective TEXT NOT NULL,
 plan ENUM('basico','business','enterprise') NOT NULL,
 message TEXT NOT NULL,
 status ENUM('nueva','revisada') NOT NULL DEFAULT 'nueva',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
 FOREIGN KEY (configuration_id) REFERENCES configurations(id) ON DELETE SET NULL,
 INDEX idx_request_owner (user_id,created_at)
) ENGINE=InnoDB;
-- Servicios propuestos: el catálogo describe funciones y entregables, no automatizaciones activas.
INSERT IGNORE INTO agents (id,slug,name,category,summary,purpose,deliverable,limitations,icon,color,is_active) VALUES
(1,'analista-negocio','Analista de negocio','analisis','De datos dispersos a una lectura clara de tu negocio.','Propuesta para organizar los indicadores aportados por una empresa, identificar preguntas y preparar una lectura inicial de tendencias.','Borrador de informe con indicadores, supuestos, preguntas abiertas y prioridades para revisión.','Requiere datos facilitados por la empresa y revisión profesional. No accede a sistemas ni toma decisiones financieras.','analyst','violet',1),
(2,'estratega-campanas','Estratega de campañas','estrategia','Una dirección común para objetivos, públicos y canales.','Propuesta para transformar un briefing comercial en un esquema de campaña con objetivos, públicos y acciones coordinadas.','Propuesta de campaña con mensajes clave, canales sugeridos y calendario inicial.','No publica campañas, compra publicidad ni garantiza resultados. Presupuesto y decisiones deben aprobarse por la empresa.','campaign','amber',1),
(3,'redactor-marca','Redactor de marca','contenido','Textos consistentes con la voz de tu empresa.','Propuesta para desarrollar borradores de contenido a partir de una guía de marca, una audiencia y un objetivo definidos.','Borradores de piezas y variantes de tono, con puntos que requieren comprobación editorial.','No inventa testimonios ni sustituye la revisión de hechos, derechos o requisitos legales. No publica contenido.','writer','mint',1),
(4,'asistente-comercial','Asistente comercial','comercial','Mejor contexto antes de cada conversación de venta.','Propuesta para ordenar información aportada sobre oportunidades comerciales y preparar seguimientos adaptados a cada contexto.','Fichas de oportunidad y borradores de seguimiento para aprobación del equipo.','No contacta a clientes, accede a un CRM ni negocia en nombre de la empresa. Las comunicaciones requieren revisión humana.','sales','violet',1),
(5,'coordinador-proyectos','Coordinador de proyectos','operaciones','Objetivos, dependencias y próximos pasos en una misma vista.','Propuesta para estructurar un proyecto a partir de sus objetivos, alcance, equipo y restricciones declaradas.','Plan inicial con tareas, dependencias, responsables propuestos y riesgos por revisar.','No asigna tareas reales ni modifica herramientas externas. Fechas y recursos deben validarse con el equipo.','project','cyan',1),
(6,'asistente-soporte','Asistente de soporte','soporte','Respuestas más claras a partir de tu conocimiento interno.','Propuesta para clasificar consultas y preparar respuestas basadas en una base de conocimiento facilitada y revisada por la empresa.','Clasificación de consultas y borradores de respuesta con casos que requieren escalado.','No atiende canales reales, resuelve incidencias técnicas ni accede a datos de clientes. Las respuestas deben revisarse antes de enviarse.','support','mint',1);
