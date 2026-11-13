<?php
require_once __DIR__.'/../includes/bootstrap.php';
require_once __DIR__.'/../classes/BusinessRepository.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_response(405,['error'=>'Método no permitido.']);
$query=$_GET['q'] ?? ''; $category=$_GET['category'] ?? '';
$categories=['','analisis','estrategia','contenido','comercial','operaciones','soporte'];
if (!is_string($query) || mb_strlen($query)>80 || !is_string($category) || !in_array($category,$categories,true)) json_response(422,['error'=>'Filtros de catálogo no válidos.']);
try {
    $agents=(new BusinessRepository(db()))->catalogue(trim($query),$category);
    json_response(200,['agents'=>$agents]);
} catch (PDOException $error) { error_log('Nexo catalogue database error: '.$error->getCode()); json_response(503,['error'=>'No se puede consultar el catálogo. Comprueba la conexión a MySQL.']); }
