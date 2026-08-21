<?php
require __DIR__.'/bootstrap.php';
$message=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf(); $action=$_POST['action']??'';
    if ($action==='login') {
        $name=strtoupper(trim((string)($_POST['username']??'')));
        $q=$db->prepare('SELECT * FROM usuarios WHERE username=? LIMIT 1'); $q->execute([$name]); $found=$q->fetch();
        $blocked=$found && $found['locked_until'] && strtotime($found['locked_until'])>time();
        if (!$found || !$found['activo'] || $blocked || !password_verify((string)($_POST['password']??''),$found['password_hash'])) {
            if ($found) { $db->prepare('UPDATE usuarios SET failed_attempts=failed_attempts+1, locked_until=IF(failed_attempts>=4,DATE_ADD(NOW(),INTERVAL 15 MINUTE),locked_until) WHERE id=?')->execute([$found['id']]); }
            audit($db,$found?(int)$found['id']:null,$name,'LOGIN_FAILED'); $error='Usuario o contraseña incorrectos.';
        } else {
            session_regenerate_id(true); $_SESSION=['authenticated'=>true,'user_id'=>(int)$found['id'],'username'=>$found['username'],'role'=>$found['rol'],'csrf'=>bin2hex(random_bytes(32))];
            $db->prepare('UPDATE usuarios SET last_login_at=NOW(), failed_attempts=0, locked_until=NULL WHERE id=?')->execute([$found['id']]); audit($db,(int)$found['id'],$found['username'],'LOGIN_OK'); header('Location: '.url('dashboard.php')); exit;
        }
    }
}
if (user()) { header('Location: '.url('dashboard.php')); exit; }
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Portal de Acuerdos | BKC</title><link rel="stylesheet" href="assets/app.css"></head><body class="login-page"><main class="login-card"><div class="brand">BKC <span>BUSINESS SOLUTIONS</span></div><p class="eyebrow">Acceso privado</p><h1>Portal de Acuerdos</h1><p>Gestión de cancelaciones, convenios y documentación de acuerdos.</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif?><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="action" value="login"><label>Usuario<input name="username" autocomplete="username" required autofocus></label><label>Contraseña<span class="password"><input id="password" type="password" name="password" autocomplete="current-password" required><button type="button" data-toggle-password aria-label="Mostrar contraseña">Mostrar</button></span></label><button class="primary" type="submit">Ingresar</button></form></main><script src="assets/app.js"></script></body></html>

