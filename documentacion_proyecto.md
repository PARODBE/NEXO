# NEXO: documentación del proyecto

**Autor:** Pablo Rodríguez Belenguer  
**Asignatura:** Ingeniería Web I  
**Repositorio:** https://github.com/PARODBE/NEXO

## Descripción

NEXO es una aplicación web dirigida a empresas que quieren organizar un equipo de agentes de IA según sus necesidades. La idea es que puedan conocer las especialidades disponibles, elegir un plan y combinar varios agentes para un objetivo común, como preparar una campaña o coordinar un proyecto.

El usuario puede consultar el catálogo sin registrarse. Para guardar equipos y enviar solicitudes necesita una cuenta. Desde su espacio privado puede recuperar sus configuraciones y modificarlas más adelante.

El proyecto representa la parte comercial y de configuración del servicio. No ejecuta modelos de IA ni procesa pagos. Los planes y sus precios sirven para mostrar cómo se ofrecería el producto.

## Funcionalidades

### Catálogo de agentes

El catálogo contiene seis especialidades: análisis de negocio, estrategia de campañas, redacción, asistencia comercial, coordinación de proyectos y soporte. Cada ficha explica para qué serviría el agente, qué información necesita y qué entregaría.

Se puede buscar por texto y filtrar por especialidad. Los resultados cambian sin recargar la página. La portada también incluye tres ejemplos de colaboración entre agentes y una sección de planes.

### Cuenta de usuario

El registro pide nombre, correo y contraseña. Una vez iniciada la sesión, el usuario puede acceder a sus equipos y solicitudes. Las contraseñas se almacenan mediante un hash.

### Configuración de equipos

El configurador permite indicar un nombre, un objetivo, un sector y un plan, además de seleccionar y ordenar los agentes. Una configuración guardada se puede consultar, editar o eliminar.

El plan Básico permite hasta tres agentes, Business hasta diez y Enterprise hasta veinte. El catálogo actual tiene seis agentes distintos, por lo que la selección está limitada también por los disponibles.

### Solicitudes de propuesta

El formulario recoge la empresa, el correo de contacto, el sector, el objetivo, el plan y un mensaje. Puede asociarse a un equipo guardado. Las solicitudes quedan almacenadas y se consultan desde la cuenta; el formulario no envía correos ni realiza una contratación.

## Tecnologías utilizadas

| Tecnología | Uso |
|---|---|
| HTML5 | Estructura de las páginas, navegación y formularios |
| CSS3 | Estilos, Grid, Flexbox y adaptación a distintos anchos de pantalla |
| JavaScript | Eventos, validación, selección y orden de agentes |
| Fetch y JSON | Comunicación del buscador con el servidor |
| PHP 8.2 | Sesiones, procesamiento de formularios y consultas a la base de datos |
| MySQL/MariaDB | Almacenamiento de usuarios, agentes, equipos y solicitudes |
| PDO | Conexión y consultas preparadas desde PHP |

La aplicación funciona con Apache y MySQL en XAMPP. No utiliza un framework.

## Organización del código

Las páginas principales están en la raíz del proyecto. `index.php` muestra la portada y el catálogo; `agent.php`, la ficha de un agente; `builder.php`, el configurador; y `dashboard.php`, el espacio de la cuenta. El registro y el acceso se encuentran en `register.php` y `login.php`.

Los estilos y JavaScript están en `assets/`. Los archivos compartidos de sesión, funciones y estructura de página están en `includes/`, y la conexión a la base de datos, en `config/`.

La lógica de acceso a datos está separada en dos clases:

- `UserRepository`: busca y crea usuarios.
- `BusinessRepository`: consulta agentes y gestiona configuraciones y solicitudes.

Ambas reciben la conexión PDO en el constructor. Así las páginas pueden utilizar sus métodos sin repetir las consultas SQL.

El buscador envía los filtros como JSON mediante POST a `api/catalogue.php`. El servidor los valida, consulta la base de datos y devuelve los agentes en JSON. El mismo endpoint admite consultas GET. Los formularios que guardan datos utilizan POST y un token CSRF.

## Base de datos

La base se llama `nexo_business` y contiene cinco tablas:

| Tabla | Datos que guarda |
|---|---|
| `users` | Nombre, correo, hash de contraseña y fecha de registro |
| `agents` | Datos de los agentes del catálogo |
| `configurations` | Nombre, objetivo, sector, plan y usuario propietario del equipo |
| `configuration_agents` | Agentes de cada equipo y su posición |
| `requests` | Datos de la solicitud y su relación con la cuenta y el equipo |

Un usuario puede tener varias configuraciones y cada configuración puede incluir varios agentes. La tabla intermedia `configuration_agents` permite guardar esa relación y mantener el orden elegido.

El archivo `database/nexo.sql` contiene la estructura y los seis agentes iniciales. No contiene cuentas ni solicitudes personales.

## Validación y seguridad

Los formularios comprueban los campos obligatorios, el formato del correo y los requisitos de la contraseña. PHP vuelve a validar los datos recibidos y los límites del plan antes de guardarlos.

Las consultas utilizan parámetros preparados. El texto se escapa antes de mostrarlo en HTML y las contraseñas se gestionan con `password_hash()` y `password_verify()`.

El servidor comprueba que cada configuración pertenezca a la cuenta conectada. Cambiar un identificador en la dirección no permite acceder a equipos de otra cuenta. Los formularios que modifican datos incluyen protección CSRF y se pide confirmación antes de eliminar un equipo.

## Instalación y ejecución

1. Instala XAMPP y coloca la carpeta del proyecto en `C:\xampp\htdocs\Nexo`. El archivo `index.php` debe quedar directamente dentro de esa carpeta.
2. Inicia Apache y MySQL desde el panel de XAMPP.
3. Abre `http://localhost/phpmyadmin/` e importa `database/nexo.sql`. Este paso solo es necesario en la primera instalación. Si ya existen las tablas de NEXO, no vuelvas a importarlo.
4. Revisa la conexión en `config/config.php`. Los valores incluidos son servidor `127.0.0.1`, puerto `3307`, base `nexo_business`, usuario `root` y contraseña vacía. Si tu instalación utiliza otro puerto o credenciales, cámbialos.
5. Abre `http://localhost/Nexo/`. Si cambias el nombre de la carpeta, utiliza ese nombre en la dirección.
6. Crea una cuenta para guardar equipos y consultar las solicitudes enviadas.

PHP necesita las extensiones `pdo_mysql` y `mbstring`. La web debe abrirse a través de Apache; Live Server no ejecuta el código PHP.

También se puede descargar con Git:

```powershell
cd C:\xampp\htdocs
git clone https://github.com/PARODBE/NEXO.git Nexo
```

Para mantener una configuración local separada, copia `config/local.example.php` como `config/local.php` y completa los datos de conexión. Ese archivo está excluido del repositorio.

Al volver a utilizar la aplicación no hay que repetir la instalación: basta con iniciar Apache y MySQL y abrir la dirección local. Antes de reinstalar sobre una base con datos, conviene exportarla para conservar las cuentas, equipos y solicitudes.
