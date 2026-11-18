# NEXO

Proyecto final de Ingeniería Web I — Pablo Rodríguez Belenguer.

NEXO es una web donde una empresa puede consultar un catálogo de agentes de IA y combinarlos en un equipo según su objetivo. Después de registrarse, puede guardar y editar sus equipos y enviar una solicitud de propuesta.

La web permite configurar los equipos y guardar las solicitudes. Los agentes no se ejecutan y los precios de los planes son ejemplos para el proyecto.

## Tecnologías

HTML, CSS, JavaScript, PHP 8.2 y MySQL/MariaDB. La conexión con la base de datos utiliza PDO y el buscador del catálogo utiliza Fetch con JSON.

## Cómo ejecutarlo

Necesitas XAMPP con Apache y MySQL. PHP debe tener activadas las extensiones `pdo_mysql` y `mbstring`.

### 1. Descargar el proyecto

Puedes extraer el ZIP y copiar la carpeta `Nexo` dentro de `C:\xampp\htdocs`, o clonar el repositorio:

```powershell
cd C:\xampp\htdocs
git clone https://github.com/PARODBE/NEXO.git Nexo
```

Comprueba que el archivo principal está en `C:\xampp\htdocs\Nexo\index.php`, sin otra carpeta `Nexo` intermedia.

### 2. Preparar la base de datos

Inicia Apache y MySQL desde XAMPP. Abre `http://localhost/phpmyadmin/` y, en **Importar**, selecciona `database/nexo.sql`.

El archivo crea la base `nexo_business`, sus cinco tablas y los seis agentes del catálogo. No incluye cuentas de usuario.

**Este paso solo se hace la primera vez.** Si ya tienes las tablas de NEXO, no vuelvas a importar el SQL: aparecerá el error «la tabla ya existe». Para usar la web de nuevo basta con iniciar los servicios.

### 3. Revisar la conexión

En `config/config.php` están los valores usados en este proyecto:

| Dato | Valor |
|---|---|
| Servidor | `127.0.0.1` |
| Puerto | `3307` |
| Base de datos | `nexo_business` |
| Usuario | `root` |
| Contraseña | Vacía |

Si tu MySQL usa el puerto `3306` u otras credenciales, cambia esos valores. También puedes copiar `config/local.example.php` como `config/local.php` y poner allí tu configuración; ese archivo no se sube al repositorio.

### 4. Abrir la web

Entra en **http://localhost/Nexo/**. Si has cambiado el nombre de la carpeta, cambia también esa parte de la dirección. No se ejecuta con Live Server: necesita PHP y MySQL.

Para probarla, crea una cuenta, elige un plan, selecciona y ordena los agentes y guarda el equipo. Desde tu cuenta puedes editarlo, eliminarlo y consultar las solicitudes que hayas enviado.

## Qué incluye

- Catálogo con búsqueda, filtros y ficha de cada agente.
- Planes y configurador de equipos.
- Registro e inicio de sesión.
- Equipos guardados con opciones para consultar, editar y eliminar.
- Formulario de solicitud y consulta de las solicitudes de la cuenta.
- Diseño adaptable a móvil.

Cada usuario solo puede acceder a sus equipos y solicitudes. Las contraseñas se guardan como hash y las consultas SQL utilizan parámetros preparados.

## Documentación y pruebas

La explicación del proyecto está en [documentacion_proyecto.md](documentacion_proyecto.md). En `docs/` están el guion de presentación, la explicación de la API y los resultados de las comprobaciones.

Con XAMPP iniciado, puedes ejecutar estas pruebas desde la carpeta del proyecto:

```powershell
py scripts/test_app.py
py scripts/test_json.py
node scripts/check_interactions.cjs
```

Las pruebas HTTP crean cuentas ficticias y eliminan sus datos al terminar. Python y Node solo se usan para estas comprobaciones; no hacen falta para abrir la web.

Para generar el ZIP de entrega:

```powershell
py scripts/package_project.py
```

Se guarda en `output/PabloRodriguezBelenguer_ProyectoFinal_WebApp.zip`.

Repositorio: https://github.com/PARODBE/NEXO
