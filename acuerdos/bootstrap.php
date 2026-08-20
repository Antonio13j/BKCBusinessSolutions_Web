<?php
declare(strict_types=1);

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    http_response_code(503);
    exit('Portal pendiente de configuración.');
}
$config = require $configFile;
foreach (['DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD','APP_URL'] as $key) {
    if (!isset($config[$key]) || $config[$key] === '') exit('Configuración incompleta.');
}
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_name('BKC_ACUERDOS');
session_set_cookie_params(['lifetime'=>0,'path'=>'/acuerdos/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
session_start();
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Referrer-Policy: same-origin");
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https://raw.githubusercontent.com; script-src 'self'; base-uri 'self'; frame-ancestors 'none'; form-action 'self'");

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $config['DB_HOST'], $config['DB_PORT'], $config['DB_DATABASE']);
    $db = new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
} catch (Throwable $e) {
    error_log('BKC database connection failed');
    http_response_code(503); exit('Servicio temporalmente no disponible.');
}

function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path=''): string { global $config; return rtrim($config['APP_URL'],'/') . '/' . ltrim($path,'/'); }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Solicitud expirada.'); } }
function user(): ?array { return !empty($_SESSION['authenticated']) ? ['id'=>(int)$_SESSION['user_id'],'username'=>$_SESSION['username'],'role'=>$_SESSION['role']] : null; }
function require_login(): array { $u=user(); if (!$u) { header('Location: '.url()); exit; } return $u; }
function require_admin(): array { $u=require_login(); if ($u['role']!=='ADMIN') { http_response_code(403); exit('Acceso no autorizado'); } return $u; }
function audit(PDO $db, ?int $uid, string $username, string $action, string $entity='SESSION', ?int $entityId=null, ?array $old=null, ?array $new=null): void {
    $q=$db->prepare('INSERT INTO auditoria(usuario_id,username,accion,entidad,entidad_id,valor_anterior,valor_nuevo,ip,user_agent) VALUES(?,?,?,?,?,?,?,?,?)');
    $q->execute([$uid,$username,$action,$entity,$entityId,$old?json_encode($old):null,$new?json_encode($new):null,substr($_SERVER['REMOTE_ADDR']??'',0,45),substr($_SERVER['HTTP_USER_AGENT']??'',0,255)]);
}
function money($n): string { return 'S/ '.number_format((float)$n,2,'.',','); }

