<?php
require_once __DIR__.'/../includes/bootstrap.php';
require_once __DIR__.'/../classes/BusinessRepository.php';
// GET conserva enlaces compartibles; POST permite intercambio JSON con Fetch.
$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    json_response(405, ['error'=>'Método no permitido.']);
}
$filters = $_GET;
if ($method === 'POST') {
    $contentType = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]));
    if ($contentType !== 'application/json') json_response(415, ['error'=>'Utiliza Content-Type: application/json.']);
    $body = file_get_contents('php://input', false, null, 0, 4097);
    if (strlen($body) > 4096) json_response(413, ['error'=>'La consulta es demasiado grande.']);
    try {
        $decoded = json_decode($body, false, 16, JSON_THROW_ON_ERROR);
    } catch (JsonException $error) {
        json_response(400, ['error'=>'El cuerpo JSON no es válido.']);
    }
    if (!($decoded instanceof stdClass)) json_response(400, ['error'=>'Se espera un objeto JSON.']);
    $filters = (array) $decoded;
    if (array_diff(array_keys($filters), ['q','category'])) json_response(422, ['error'=>'Filtros desconocidos.']);
}
$query=$filters['q'] ?? ''; $category=$filters['category'] ?? '';
$categories=['','analisis','estrategia','contenido','comercial','operaciones','soporte'];
if (!is_string($query) || mb_strlen($query)>80 || !is_string($category) || !in_array($category,$categories,true)) json_response(422,['error'=>'Filtros de catálogo no válidos.']);
try {
    $agents=(new BusinessRepository(db()))->catalogue(trim($query),$category);
    json_response(200,['agents'=>$agents]);
} catch (PDOException $error) { error_log('Nexo catalogue database error: '.$error->getCode()); json_response(503,['error'=>'No se puede consultar el catálogo. Comprueba la conexión a MySQL.']); }
