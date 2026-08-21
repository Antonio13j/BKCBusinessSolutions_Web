<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') exit("Solo CLI.\n");
$configFile=dirname(__DIR__).'/acuerdos/config.php'; if(!is_file($configFile)) exit("Falta acuerdos/config.php\n");$c=require $configFile;
$db=new PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',$c['DB_HOST'],$c['DB_PORT'],$c['DB_DATABASE']),$c['DB_USERNAME'],$c['DB_PASSWORD'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$users=['ANASAN'=>'AGENTE','ANARIV'=>'AGENTE','OSCGOM'=>'AGENTE','GELCAL'=>'AGENTE','DANFAR'=>'ADMIN'];$find=$db->prepare('SELECT id FROM usuarios WHERE username=?');$insert=$db->prepare("INSERT INTO usuarios(username,nombre,password_hash,rol,password_status) VALUES(?,?,?,?, 'INITIAL')");$created=0;
foreach($users as $name=>$role){$find->execute([$name]);if(!$find->fetchColumn()){$insert->execute([$name,$name,password_hash('bkc#2026',PASSWORD_DEFAULT),$role]);$created++;}}
echo "Setup completado. Usuarios creados: $created. Elimine o renombre este script después de usarlo.\n";

