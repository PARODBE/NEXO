# Verificación de NEXO

- Catálogo, búsqueda, categorías, fichas, vacíos y errores HTTP/CSRF: correctos.
- Dos cuentas, plan elegido conservado tras registro, guardado real, orden persistente y permisos de configuración: correctos.
- Edición, escape XSS, orden nuevo, límite Básico y selección duplicada rechazada: correctos.
- Solicitud persistente y privada, sesión, eliminación y conservación de solicitud sin configuración: correctos.

Las pruebas HTTP utilizan dos cuentas aleatorias y limpian únicamente sus configuraciones y solicitudes. No se envían correos, no se procesan pagos y el flujo de producto es ilustrativo.

Las pruebas de JavaScript se ejecutan con `node scripts/check_interactions.cjs`: comprueban orden y movimiento de agentes, selección sin duplicados, cambio de límite de plan, bloqueo de guardado y cambio del flujo ilustrativo sin peticiones de red. La sintaxis se comprueba con `php -l` y `node --check`. No se ha completado una revisión visual automática en navegador; estas comprobaciones no la sustituyen.
