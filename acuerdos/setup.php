<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$lockFile = __DIR__ . '/storage/setup.lock';
if (is_file($lockFile)) {
    http_response_code(410);
    exit('La instalación inicial ya fue completada.');
}

$error = '';
$complete = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $expected = (string)($config['SETUP_TOKEN'] ?? '');
    $provided = (string)($_POST['setup_token'] ?? '');
    if (strlen($expected) < 32 || !hash_equals($expected, $provided)) {
        $error = 'Token de instalación incorrecto.';
    } else {
        $users = ['ANASAN'=>'AGENTE','ANARIV'=>'AGENTE','OSCGOM'=>'AGENTE','GELCAL'=>'AGENTE','DANFAR'=>'ADMIN'];
        $find = $db->prepare('SELECT id FROM usuarios WHERE username=?');
        $insert = $db->prepare("INSERT INTO usuarios(username,nombre,password_hash,rol,password_status) VALUES(?,?,?,?, 'INITIAL')");
        $created = 0;
        $db->beginTransaction();
        try {
            foreach ($users as $username => $role) {
                $find->execute([$username]);
                if (!$find->fetchColumn()) {
                    $insert->execute([$username,$username,password_hash('bkc#2026',PASSWORD_DEFAULT),$role]);
                    $created++;
                }
            }
            $db->commit();
            if (file_put_contents($lockFile, date(DATE_ATOM), LOCK_EX) === false) {
                throw new RuntimeException('No se pudo bloquear el instalador.');
            }
            $complete = true;
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('BKC initial setup failed');
            $error = 'No se pudo completar la instalación.';
        }
    }
}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Instalación | BKC</title><link rel="stylesheet" href="assets/app.css"></head><body class="login-page"><main class="login-card"><div class="brand">BKC <span>INSTALACIÓN SEGURA</span></div><h1>Usuarios iniciales</h1><?php if($complete):?><div class="alert success">Usuarios iniciales creados. Elimina setup.php y SETUP_TOKEN antes de usar el portal.</div><a class="primary" href="<?=url()?>">Ir al portal</a><?php else:?><p>Introduce el token privado configurado en config.php.</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif?><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><label>Token de instalación<input type="password" name="setup_token" autocomplete="off" required minlength="32"></label><button class="primary">Crear usuarios</button></form><?php endif?></main></body></html>
