<?php
declare(strict_types=1);
require dirname(__DIR__).'/pdf-generator.php';

$output=$argv[1]??dirname(__DIR__,2).'/docs/pruebas_pdf_php';
if(!is_dir($output)&&!mkdir($output,0777,true))throw new RuntimeException('No se pudo crear la carpeta de salida');
$data=[
 'fecha_documento'=>'2026-08-21','nombre_completo'=>'VICTOR HUANACO TONCCOCCHI','dni'=>'23949155',
 'telefono'=>'984540533','correo'=>'-','direccion'=>'AV. DIRECCIÓN DE PRUEBA 123','distrito'=>'LIMA',
 'departamento'=>'LIMA','numero_operacion'=>'4040710013988553','producto'=>'TARJETA',
 'deuda_total'=>1644.84,'tipo_acuerdo'=>'CANCELACION','monto_acordado'=>1000.00,'monto_inicial'=>300.00,
 'cuotas'=>[
  ['fecha'=>'2026-08-21','monto'=>300.00],['fecha'=>'2026-09-21','monto'=>350.00],['fecha'=>'2026-10-21','monto'=>350.00],
 ],
];
$agreement=$output.'/Acuerdo de pago - DNI_23949155.pdf';$cession=$output.'/Cesión de Derechos - DNI_23949155.pdf';
file_put_contents($agreement,render_document_pdf('ACUERDO_PAGO',$data));
file_put_contents($cession,render_document_pdf('CESION_DERECHOS',$data));
$fivePayments=$data;
$fivePayments['monto_inicial']=200.00;
$fivePayments['cuotas']=[
 ['fecha'=>'2026-08-21','monto'=>200.00],['fecha'=>'2026-09-21','monto'=>200.00],
 ['fecha'=>'2026-10-21','monto'=>200.00],['fecha'=>'2026-11-21','monto'=>200.00],
 ['fecha'=>'2026-12-21','monto'=>200.00],
];
$agreementFive=$output.'/Acuerdo de pago 5 cuotas - DNI_23949155.pdf';
file_put_contents($agreementFive,render_document_pdf('ACUERDO_PAGO',$fivePayments));
foreach([$agreement,$agreementFive,$cession]as $file){if(!is_file($file)||filesize($file)<1000||file_get_contents($file,false,null,0,5)!=='%PDF-')throw new RuntimeException('PDF inválido: '.$file);echo basename($file).' '.filesize($file)." bytes\n";}
