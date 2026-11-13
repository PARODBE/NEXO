# Documentación del proyecto: NEXO

## 1. Objetivo

NEXO es una aplicación de catálogo comercial para explorar servicios propuestos de agentes, componer un equipo para una empresa y solicitar una propuesta. Su finalidad es conectar una necesidad de negocio con una configuración concreta de especialidades.

El recorrido principal es: explorar el catálogo, consultar una ficha, elegir plan, seleccionar y ordenar agentes, guardar la configuración y enviar una solicitud. La cuenta permite retomar el trabajo y consultar las solicitudes presentadas.

Es un proyecto académico de Ingeniería Web I. El catálogo, los precios y los flujos describen una propuesta de producto. No representan contratos, clientes o resultados reales. El formulario registra una solicitud de interés; no cobra ni envía comunicaciones comerciales a terceros.

## 2. Usuarios y alcance

- **Visitante:** consulta catálogo, fichas y planes; puede registrarse para preparar un equipo.
- **Cuenta de empresa:** guarda, edita y elimina sus configuraciones y envía solicitudes asociadas a su cuenta.
- **Titular de la configuración:** decide qué especialidades incluye y su orden; no puede modificar configuraciones de otra cuenta.

El diseño prioriza catálogo, configuración y conversión a solicitud. Las identidades gráficas son formas geométricas HTML/CSS. No se atribuyen testimonios, marcas o métricas de éxito a empresas reales.

## 3. Funcionalidades

1. Registro, inicio y cierre de sesión.
2. Catálogo almacenado en MySQL con búsqueda y categorías.
3. Fichas con propósito, necesidades de entrada, entregables y casos de uso propuestos.
4. Presentación de planes Básico, Business y Enterprise con precios orientativos de 49, 149 y propuesta personalizada respectivamente, según el catálogo de la interfaz.
5. Configurador con selección y orden de agentes; límites por plan: Básico hasta 3, Business hasta 10 y Enterprise hasta 20, sujetos a los agentes disponibles.
6. CRUD de configuraciones de cada cuenta: crear, consultar, editar y eliminar.
7. Solicitud de propuesta con empresa, contacto, sector, objetivo, plan y mensaje; puede asociarse a una configuración propia.
8. Consulta de solicitudes persistidas desde el espacio de la cuenta.
9. Flujo ilustrativo de trabajo para explicar la colaboración propuesta entre especialidades.
10. Diseño responsive, validación y estados de carga, error y resultado vacío.

El catálogo inicial tiene seis especialidades. Los máximos de los planes no significan que se entreguen agentes adicionales inexistentes. El precio es ilustrativo y el formulario no genera un pedido ni una transacción.

## 4. Tecnologías y arquitectura

| Capa | Tecnología | Responsabilidad |
|---|---|---|
| Presentación | HTML5 y CSS3 | Catálogo, planes, fichas y formularios responsive |
| Interacción | JavaScript y DOM | Filtros, selección, orden y flujo ilustrativo |
| Servidor | PHP 8.2 | Sesiones, validación, permisos, formularios y API |
| Persistencia | MySQL/MariaDB y PDO | Usuarios, agentes, configuraciones y solicitudes |

JavaScript solicita el catálogo mediante Fetch. PHP valida los filtros, consulta MySQL y devuelve JSON. Las fichas iniciales y los formularios se generan con PHP. Las acciones de guardado son POST con sesión y token CSRF; el servidor vuelve a validar el plan, la selección y la propiedad de los registros.

`UserRepository` encapsula las cuentas. `BusinessRepository` organiza el catálogo, las configuraciones y las solicitudes. Las clases reciben PDO en el constructor y separan las consultas de las páginas. El código evita frameworks para que su funcionamiento pueda explicarse con los conceptos del curso.

## 5. Modelo de datos

| Tabla | Contenido | Relación |
|---|---|---|
| users | Nombre, correo único, hash y fecha | Titular de configuraciones y solicitudes |
| agents | Catálogo, especialidad y descripción del servicio propuesto | Participa en configuraciones |
| configurations | Nombre, objetivo, sector, plan y titular | Agrupa agentes ordenados |
| configuration_agents | Configuración, agente y posición | Relación muchos a muchos con orden |
| requests | Empresa, contacto, sector, objetivo, plan, mensaje y configuración opcional | Solicitud asociada a la cuenta |

La selección se conserva en una tabla intermedia con posición. Las claves foráneas mantienen la integridad. Las configuraciones y solicitudes se consultan por el usuario autenticado; conocer un id no concede acceso. El SQL de entrega contiene estructura y catálogo inicial, no los datos de usuarios o empresas de la instalación de desarrollo.

## 6. Relación con el enunciado

| Unidad | Peso indicado | Evidencia |
|---|---|---|
| UD1 · Idea y contexto | Planificación | Objetivo, perfiles, recorrido y arquitectura de esta documentación |
| UD2 · HTML y CSS | 20 % | Páginas semánticas, formularios y CSS responsive |
| UD3 · JavaScript y DOM | 20 % | Eventos, validación, selección, orden y demostración del flujo |
| UD4 · PHP y MySQL | 25 % | Registro seguro, sesiones, PDO, persistencia y SQL |
| UD5 · POO | 15 % | UserRepository y BusinessRepository con responsabilidades concretas |
| UD6 · Responsive y AJAX | 20 % | Media queries, Fetch/JSON y estados de carga/error |

Node.js es opcional según el enunciado y no se requiere para ejecutar el servidor. El proyecto usa PHP y MySQL de forma real: configuraciones y solicitudes sobreviven a recargas y cierres de sesión.

## 7. Seguridad y validación

- `password_hash()` y `password_verify()` para las credenciales de las cuentas.
- Consultas preparadas PDO y valores admitidos para planes y categorías.
- Validación de campos, longitud, correo, selección y límites del plan en PHP.
- Sesiones con regeneración de id al autenticar, HttpOnly y SameSite.
- Token CSRF en formularios que modifican estado, incluido el cierre de sesión.
- Escape HTML en PHP y contenido dinámico insertado como texto en JavaScript.
- Control de propiedad de configuraciones y solicitudes en el servidor.
- Confirmación explícita para eliminar configuraciones.

La instalación está pensada para entrega local. Un despliegue público exigiría HTTPS, usuario de base de datos de privilegios mínimos, control de abuso, copias de seguridad y medidas organizativas para gestionar datos de contacto. No se presentan esas ampliaciones como implementadas.

## 8. Instalación y ejecución

1. Copiar el proyecto dentro de htdocs de XAMPP.
2. Iniciar Apache y MySQL.
3. Importar `database/nexo.sql` en phpMyAdmin. La base se llama `nexo_business`.
4. Adaptar `config/config.php` o el archivo local a las credenciales de tu equipo. Predeterminado de entrega: 127.0.0.1, puerto 3307 y root sin contraseña.
5. Abrir la URL localhost correspondiente al nombre real de la carpeta.
6. Registrar una cuenta, configurar un equipo y enviar una solicitud con datos ficticios de demostración.

PHP necesita pdo_mysql y mbstring. No ejecutar con Live Server. Las variables admitidas son NEXO_DSN, NEXO_DB_USER, NEXO_DB_PASS y NEXO_BASE_URL. El archivo `config/local.php` y las variables de entorno no forman parte del ZIP. No se necesita un servicio externo para completar el recorrido.

## 9. Verificación

Las pruebas HTTP incluidas crean dos cuentas temporales para verificar catálogo, CRUD, permisos, orden, límites del plan, solicitudes y persistencia. Las pruebas JavaScript comprueban las interacciones relevantes del configurador y del flujo ilustrativo. Sus resultados concretos se guardan en `docs/verificacion.md`.

Las comprobaciones de sintaxis y comportamiento no sustituyen una revisión visual en un navegador real. Los datos ficticios de pruebas se eliminan sin tocar registros ajenos.

## 10. Entrega

- **Autor indicado en el material recibido:** Pablo Rodríguez Belenguer. Verificar antes de entregar.
- **Asignatura:** Ingeniería Web I.
- **Grupo:** pendiente de completar por el estudiante.
- **Repositorio de entrega:** https://github.com/PARODBE/NEXO.
- **Archivo:** `PabloRodriguezBelenguer_ProyectoFinal_WebApp.zip`.

El ZIP incluye carpeta raiz Nexo, codigo, SQL, README, documentacion, fuentes, guion y pruebas. Excluye sesiones, credenciales, datos de empresas de prueba y directorios de salida. El estudiante debe enviar el enlace del repositorio al profesor antes de la fecha limite. Los avances semanales deben reflejar trabajo real.
