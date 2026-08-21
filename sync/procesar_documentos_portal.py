"""Procesa solicitudes pendientes del portal usando las plantillas SMA locales."""
from __future__ import annotations
import argparse, base64, json, os, tempfile, urllib.request
from pathlib import Path
try:
    from sync.generar_documentos_sma import generar_acuerdo, generar_cesion, convert_pdf
except ModuleNotFoundError:
    from generar_documentos_sma import generar_acuerdo, generar_cesion, convert_pdf

def request(url,token,data=None):
    body=json.dumps(data).encode() if data is not None else None
    req=urllib.request.Request(url,data=body,headers={'Authorization':f'Bearer {token}','Content-Type':'application/json'},method='POST' if data is not None else 'GET')
    with urllib.request.urlopen(req,timeout=120) as response:return json.load(response)

def main():
    p=argparse.ArgumentParser();p.add_argument('--endpoint',default='https://bkcsolution.com/acuerdos/api/document-job.php');p.add_argument('--templates',type=Path,default=Path(r'C:\VS_Code\Cesion_Derechos\Plantillas'));p.add_argument('--soffice',default=r'C:\Program Files\LibreOffice\program\soffice.com');p.add_argument('--max-jobs',type=int,default=20);a=p.parse_args();token=os.environ.get('BKC_PORTAL_SYNC_TOKEN','')
    if len(token)<32:raise SystemExit('Falta BKC_PORTAL_SYNC_TOKEN')
    processed=0
    while processed<a.max_jobs:
        result=request(a.endpoint,token);job=result.get('job')
        if not job:break
        data={'fecha_documento':job['fecha_acuerdo'],'nombre_completo':job['nombre_completo'],'dni':job['dni'],'telefono':job.get('telefono'),'correo':job.get('correo'),'direccion':job.get('direccion') or '','distrito':job.get('distrito') or '','departamento':job.get('departamento') or '','numero_operacion':job['numero_operacion'],'producto':job.get('producto'),'deuda_total':job['deuda_total'],'tipo_acuerdo':job['tipo_acuerdo'],'monto_acordado':job['monto_acordado'],'monto_inicial':job['monto_inicial'],'cuotas':[{'fecha':x['fecha'],'monto':x['monto']} for x in job['cuotas']]}
        try:
            with tempfile.TemporaryDirectory() as folder:
                output=Path(folder)
                if job['tipo']=='ACUERDO_PAGO':docx=generar_acuerdo(a.templates/'Acuerdo_de_Pago.docx',output,data)
                elif job['tipo']=='CESION_DERECHOS':docx=generar_cesion(a.templates/'Cesion_Derechos.docx',output,data)
                else:raise RuntimeError('Tipo documental desconocido')
                pdf=convert_pdf(docx,output,a.soffice);payload={'documento_id':job['documento_id'],'pdf_base64':base64.b64encode(pdf.read_bytes()).decode()};request(a.endpoint,token,payload)
            print(f"Documento {job['documento_id']} generado")
        except Exception as exc:
            request(a.endpoint,token,{'documento_id':job['documento_id'],'generation_error':type(exc).__name__});print(f"Documento {job['documento_id']} marcado con error: {type(exc).__name__}: {exc}")
        processed+=1
    print(f"Proceso completo: {processed} documento(s)")

if __name__=='__main__':main()
