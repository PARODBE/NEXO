# Comprobacion del enunciado

## Funcionalidades y unidades

| Requisito | Evidencia |
|---|---|
| Idea, objetivo y tecnologias | documentacion_proyecto.md, apartados 1-4 |
| HTML semantico | includes/layout.php y paginas PHP con header, nav, main y footer |
| CSS, Grid/Flexbox y responsive | assets/css/styles.css, media queries |
| JavaScript, DOM y eventos | assets/js/app.js y builder.js |
| Validacion en cliente | required, email, pattern, checkValidity y validacion de seleccion |
| PHP y formularios | register.php, login.php, actions.php |
| MySQL y persistencia | config/database.php, clases de repositorio y cinco tablas |
| SQL preparado, XSS y claves seguras | PDO, escape HTML, password_hash y password_verify |
| POO PHP | UserRepository y BusinessRepository; PDO encapsulado y constructores |
| Enviar y recibir JSON | api/catalogue.php admite GET y POST JSON; docs/api_json.md |
| Fetch asincrono y errores | filtro de catalogo en app.js con abort, carga y errores |
| Node.js | Opcional en el enunciado; no necesario para ejecutar NEXO |
| Demostracion y decisiones tecnicas | docs/guion_presentacion.md |

## Entrega

| Requisito | Archivo o accion |
|---|---|
| ZIP con todos los recursos | PabloRodriguezBelenguer_ProyectoFinal_WebApp.zip |
| Descripcion, tecnologias, funcionalidades e instrucciones | documentacion_proyecto.md |
| Exportacion SQL | database/nexo.sql; mysqldump e importacion comprobados |
| Clonado y ejecucion local | README.md |
| Enlace del repositorio en documentacion | https://github.com/PARODBE/NEXO |
| Enviar enlace al profesor | Accion pendiente del estudiante antes de la fecha limite |
| Presentacion final | Preparar y realizar la demostracion con el guion incluido |

Los avances semanales quedan fuera de esta revision por indicacion del estudiante. No se reconstruye ni se inventa un historial retrospectivo. Los nombres de archivos de ejemplo del material docente se adaptan a la estructura de NEXO; la entrada principal es index.php, con HTML generado por PHP.

La revision visual realizada no certifica todos los navegadores ni dispositivos. Se ha revisado manualmente Chrome en escritorio y emulacion movil de 400 x 540; el CSS usa Grid, Flexbox y media queries. Los resultados funcionales figuran en docs/verificacion.md.
