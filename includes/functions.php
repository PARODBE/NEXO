<?php
require_once __DIR__ . '/../config/config.php';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . ($path === '' ? '' : '/' . ltrim($path, '/'));
}

// Cambia la URL cuando cambia el contenido, evitando CSS o JS antiguos en caché.
function asset_url(string $path): string
{
    $file = __DIR__ . '/../' . $path;
    $version = is_file($file) ? substr(hash_file('sha256', $file), 0, 12) : 'missing';
    return url($path) . '?v=' . $version;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        exit('La sesión del formulario ha caducado. Recarga la página e inténtalo de nuevo.');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        $page=basename($_SERVER['SCRIPT_NAME']);
        if(in_array($page,['builder.php','request.php','dashboard.php'],true)) {
            $params=[];
            foreach(['id','agent','configuration'] as $key) if(isset($_GET[$key]) && filter_var($_GET[$key],FILTER_VALIDATE_INT)>0) $params[$key]=(int)$_GET[$key];
            if(isset($_GET['plan']) && is_string($_GET['plan']) && isset(plan_definitions()[$_GET['plan']])) $params['plan']=$_GET['plan'];
            $_SESSION['after_login']=$page.($params?'?'.http_build_query($params):'');
        }
        flash('error', 'Accede a tu espacio de empresa para guardar equipos y solicitudes.');
        header('Location: ' . url('login.php'));
        exit;
    }
    return $user;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function normalize_text(string $value): string
{
    return trim(preg_replace('/\s+/', ' ', $value));
}

function json_response(int $status, array $payload): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function post_text(string $key): string
{
    $value = $_POST[$key] ?? '';
    if (!is_string($value)) throw new InvalidArgumentException('Formato no válido en el formulario.');
    return trim($value);
}

function plan_definitions(): array { return [
    'basico'=>['name'=>'Básico','price'=>'49 €/mes','limit'=>3],
    'business'=>['name'=>'Business','price'=>'149 €/mes','limit'=>10],
    'enterprise'=>['name'=>'Enterprise','price'=>'A medida','limit'=>20],
]; }
function sector_labels(): array { return ['ecommerce'=>'Comercio electrónico','consultoria'=>'Consultoría','marketing'=>'Marketing','otro'=>'Otro sector']; }
function category_labels(): array { return ['analisis'=>'Análisis','estrategia'=>'Estrategia','contenido'=>'Contenido','comercial'=>'Comercial','operaciones'=>'Operaciones','soporte'=>'Soporte']; }
function after_login(): never {
    $target=$_SESSION['after_login'] ?? 'dashboard.php'; unset($_SESSION['after_login']);
    if(!is_string($target) || !preg_match('/^(builder|request|dashboard)\.php(?:\?[a-z0-9_=&%+-]+)?$/',$target))$target='dashboard.php';
    redirect($target);
}

function plain_length(string $value, int $min, int $max, string $label): void
{
    if (mb_strlen($value) < $min || mb_strlen($value) > $max) throw new InvalidArgumentException($label.' debe tener entre '.$min.' y '.$max.' caracteres.');
}

