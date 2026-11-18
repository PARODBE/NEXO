# NEXO · Equipos de agentes para empresas

Aplicación web de catálogo comercial y configuración de equipos de agentes. Una empresa puede explorar especialidades, elegir un plan, preparar una configuración y enviar una solicitud de propuesta. La cuenta permite conservar configuraciones y consultar las solicitudes realizadas.

## Tecnologías

HTML5, CSS3, JavaScript nativo, PHP 8.2 y MySQL/MariaDB mediante PDO. No necesita frameworks ni Node.js para ejecutarse.

## Clonar el repositorio

Con Git instalado, ejecuta en PowerShell:

```powershell
cd C:\xampp\htdocs
git clone https://github.com/PARODBE/NEXO.git Nexo
cd Nexo
```

Continua con la instalacion desde el inicio de Apache y MySQL. Si ya existe `Nexo`, elige otra carpeta de destino y adapta la URL local.

## Instalación en XAMPP

1. Copia la carpeta en `C:\xampp\htdocs\Nexo`. Si mantienes otro nombre, utiliza ese nombre en la URL.
2. Inicia Apache y MySQL.
3. Importa `database/nexo.sql` desde phpMyAdmin. Se crea la base `nexo_business`, las tablas y el catálogo inicial sin borrar otras bases.
4. Revisa `config/config.php`. El entorno de entrega utiliza `127.0.0.1`, puerto `3307`, base `nexo_business`, usuario `root` y contraseña vacía. Adapta el puerto y las credenciales a tu instalación.
5. Abre `http://localhost/Nexo/`, usando el nombre real de tu carpeta.
6. Registra una cuenta, prepara un equipo y guarda la configuración o envía una solicitud.

PHP necesita `pdo_mysql` y `mbstring`. Live Server no ejecuta PHP. Las variables `NEXO_DSN`, `NEXO_DB_USER`, `NEXO_DB_PASS` y `NEXO_BASE_URL` permiten ajustar otro entorno. `config/local.example.php` puede copiarse a `config/local.php` para configurar la base localmente; el archivo local está excluido de Git y del ZIP.

## Funcionalidades

- Catálogo de especialidades con búsqueda y filtros mediante Fetch/JSON.
- Fichas de agentes con propósito, entradas, entregables y aplicaciones propuestas.
- Planes y configurador de equipos con selección y orden de agentes.
- Registro, inicio y cierre de sesión.
- Crear, consultar, editar y eliminar configuraciones de la cuenta.
- Solicitudes de propuesta almacenadas y consultables por su titular.
- Diseño responsive, validación en cliente y servidor y mensajes de estado.

Es un prototipo académico de una web comercial. Los precios y ejemplos de servicios son propuestas ilustrativas; no se procesan pagos ni se contratan servicios al enviar el formulario. La demostración de un flujo describe pasos y entregables previstos, no ejecuta trabajo para una empresa. Los nombres de producto y los ejemplos no representan clientes reales.

## Datos y permisos

La base contiene usuarios, catálogo, configuraciones, agentes ordenados de cada configuración y solicitudes. Las cuentas solo modifican sus propias configuraciones y consultan sus propias solicitudes. El SQL de entrega no incluye cuentas, datos de empresas o solicitudes personales.

## Comprobación y entrega

Con XAMPP activo en un entorno local de pruebas:

```powershell
py scripts/test_app.py
py scripts/test_json.py
node scripts/check_interactions.cjs
py scripts/package_project.py
```

Python y Node son herramientas opcionales de comprobación y empaquetado, no dependencias de la aplicación. Las pruebas HTTP crean dos cuentas aleatorias y eliminan únicamente sus datos al terminar. `NEXO_TEST_URL`, `NEXO_TEST_MYSQL`, `NEXO_TEST_DB_PORT` y `NEXO_TEST_DB_USER` permiten ajustar el entorno de pruebas. No se envían correos ni se procesan pagos.

El empaquetador genera `output/PabloRodriguezBelenguer_ProyectoFinal_WebApp.zip`, con carpeta raíz `Nexo`, código, SQL y documentación. Excluye sesiones, credenciales, archivos de entorno y el propio output.

Consulta `documentacion_proyecto.md`, `docs/guion_presentacion.md` y `docs/verificacion.md`. Repositorio: [PARODBE/NEXO](https://github.com/PARODBE/NEXO). El estudiante debe enviar el enlace al profesor antes de la fecha limite.

El SQL es una exportacion real con mysqldump de una base limpia, sin datos personales. Importalo para la primera instalacion; si ya existen tablas de NEXO, no lo importes sobre tus datos en uso. Haz una copia de seguridad antes de reinstalar. Consulta `docs/api_json.md` y `docs/cumplimiento_entrega.md`.
