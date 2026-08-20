<?php require __DIR__.'/bootstrap.php'; if($u=user()) audit($db,$u['id'],$u['username'],'LOGOUT'); $_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$p['path'],'',$p['secure'],$p['httponly']);}session_destroy();header('Location: '.url());exit;

