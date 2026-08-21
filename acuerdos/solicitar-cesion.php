<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
require_once __DIR__.'/pdf-generator.php';
$u=require_login();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método no permitido');}
verify_csrf();
$id=(int)($_POST['documento_id']??0);
$q=$db->prepare("SELECT d.id,d.estado,c.numero_documento FROM documentos d JOIN operaciones o ON o.id=d.operacion_id JOIN clientes c ON c.id=d.cliente_id WHERE d.id=? AND d.tipo='CESION_DERECHOS' AND d.acuerdo_id IS NULL AND o.cedente='SMA_INVERSIONES' AND o.activa_en_asignacion=1");
$q->execute([$id]);$doc=$q->fetch();
if(!$doc){http_response_code(404);exit('Cesión no encontrada');}
if(in_array($doc['estado'],['PENDIENTE','ERROR'],true)){$db->prepare("UPDATE documentos SET estado='PENDIENTE',solicitado_por=?,updated_at=NOW() WHERE id=?")->execute([$u['id'],$id]);audit($db,$u['id'],$u['username'],'DOCUMENT_REQUESTED','DOCUMENTO',$id,null,['tipo'=>'CESION_DERECHOS']);try{generate_document_now($db,$config,$id);}catch(Throwable $e){error_log('Immediate cession PDF failed: '.$e->getMessage());}}
header('Location: '.url('clientes.php?documento='.rawurlencode($doc['numero_documento'])));exit;
