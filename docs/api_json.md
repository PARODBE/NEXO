# Intercambio JSON y Fetch

El filtro del catalogo envia un POST con JSON y recibe una respuesta JSON sin recargar la pagina. GET permite consultar el mismo catalogo con parametros en la URL.

Ejemplo de peticion a `api/catalogue.php`:

```javascript
const response = await fetch('api/catalogue.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json', Accept: 'application/json'},
  body: JSON.stringify({q: 'Analista', category: 'analisis'})
});
const data = await response.json();
if (!response.ok) throw new Error(data.error);
console.log(data.agents);
```

La busqueda solo lee el catalogo; no cambia datos ni necesita una cuenta. Se mantienen los formularios tradicionales para registro, configuraciones y solicitudes, con sesion, CSRF y validacion en servidor.

Respuestas: 200 consulta correcta; 400 JSON mal formado o sin objeto; 405 metodo no permitido; 413 mas de 4096 bytes; 415 tipo de contenido incorrecto; 422 filtros invalidos; 503 base de datos no disponible.

Ejecutar `py scripts/test_json.py` con Apache y MySQL iniciados. Esta prueba no crea ni modifica datos.
