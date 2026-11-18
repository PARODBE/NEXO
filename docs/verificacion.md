# Verificación de NEXO

- Catálogo, búsqueda, categorías, fichas, vacíos y errores HTTP/CSRF: correctos.
- Dos cuentas, plan elegido conservado tras registro, guardado real, orden persistente y permisos de configuración: correctos.
- Edición, escape XSS, orden nuevo, límite Básico y selección duplicada rechazada: correctos.
- Solicitud persistente y privada, sesión, eliminación y conservación de solicitud sin configuración: correctos.

Las pruebas HTTP utilizan dos cuentas aleatorias y limpian únicamente sus configuraciones y solicitudes. No se envían correos, no se procesan pagos y el flujo de producto es ilustrativo.

Las pruebas de JavaScript se ejecutan con `node scripts/check_interactions.cjs`: comprueban orden y movimiento de agentes, selección sin duplicados, cambio de límite de plan, bloqueo de guardado y cambio del flujo ilustrativo sin peticiones de red. La sintaxis se comprueba con `php -l` y `node --check`. La revision visual manual en Chrome cubre la portada y las fichas del catalogo en escritorio y emulacion responsive de 400 x 540. No se ha comprobado una matriz completa de navegadores o telefonos reales.

## Comprobacion adicional de entrega

- POST JSON valido: 200 y filtro correcto. JSON roto y array: 400. Filtro desconocido o tipo incorrecto: 422. Cuerpo excesivo: 413.
- SQL generado con mysqldump e importado en una base temporal independiente: cinco tablas, seis agentes y cero cuentas.
- Revision visual manual en Chrome: portada, flujo y fichas del catalogo a 400 x 540, ademas de la vista de escritorio. Los servicios locales deben estar iniciados para repetir las pruebas.
