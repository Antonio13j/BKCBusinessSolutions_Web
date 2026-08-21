"""Sincroniza sólo el último periodo por asignación desde SQL Server al portal."""
from __future__ import annotations
import argparse, hashlib, json, os, urllib.request
from datetime import date, datetime
import pyodbc

SQL="""SELECT documento,nombre_titular,operacion,deuda_total,monto_capital,moneda,
 asignacion,periodo_archivo,fecha_carga,origen_entidad,producto,direccion,distrito,
 provincia,departamento FROM rpt.consolidado c
 WHERE asignacion=? AND periodo_archivo=(SELECT MAX(periodo_archivo) FROM dbo.consolidado WHERE asignacion=?)
 AND (? IS NULL OR documento=?) ORDER BY documento,operacion"""

def scalar(value):
    if isinstance(value,(date,datetime)): return value.isoformat()[:10]
    return value

def main():
    p=argparse.ArgumentParser();p.add_argument('--endpoint',default='https://bkcsolution.com/acuerdos/api/sync.php');p.add_argument('--asignacion',choices=['SMA_INVERSIONES','KPINVEST'],default='SMA_INVERSIONES');p.add_argument('--documento');p.add_argument('--dry-run',action='store_true');a=p.parse_args()
    conn=pyodbc.connect(r'DRIVER={ODBC Driver 17 for SQL Server};SERVER=localhost\MSSQLSERVER01;DATABASE=bkc_datawarehouse;Trusted_Connection=yes;Encrypt=no;TrustServerCertificate=yes;',timeout=15)
    cur=conn.cursor();cur.execute(SQL,a.asignacion,a.asignacion,a.documento,a.documento);columns=[x[0] for x in cur.description];rows=[{k:scalar(v) for k,v in zip(columns,row)} for row in cur.fetchall()];conn.close()
    if not rows: raise SystemExit('No se encontraron filas para el periodo vigente')
    period=rows[0]['periodo_archivo'];batch=hashlib.sha256(f"{a.asignacion}:{period}:{len(rows)}".encode()).hexdigest()[:40]
    print(json.dumps({'asignacion':a.asignacion,'periodo':period,'filas':len(rows),'batch_id':batch},ensure_ascii=False))
    if a.dry_run:return
    token=os.environ.get('BKC_PORTAL_SYNC_TOKEN','');
    if len(token)<32:raise SystemExit('Falta BKC_PORTAL_SYNC_TOKEN (32+ caracteres)')
    for start in range(0,len(rows),200):
        chunk=rows[start:start+200];payload=json.dumps({'asignacion':a.asignacion,'periodo_archivo':period,'batch_id':batch,'rows':chunk,'final':start+200>=len(rows)},ensure_ascii=False,default=str).encode();req=urllib.request.Request(a.endpoint,data=payload,headers={'Content-Type':'application/json','Authorization':f'Bearer {token}'},method='POST')
        with urllib.request.urlopen(req,timeout=60) as response:
            result=json.load(response)
        if not result.get('ok'):raise RuntimeError(result)
        print(f"Sincronizadas {min(start+len(chunk),len(rows))}/{len(rows)}")

if __name__=='__main__':main()
