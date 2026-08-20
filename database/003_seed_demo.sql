-- Datos ficticios opcionales. No ejecutar en producción si se sincronizarán datos reales.
INSERT INTO clientes(tipo_documento,numero_documento,nombre_completo,origen) VALUES ('DNI','00000000','CLIENTE DEMOSTRACIÓN','DEMO');
INSERT INTO operaciones(cliente_id,cedente,cartera,numero_operacion,saldo,capital) SELECT id,'SMA','DEMO','OP-DEMO-001',10000,8000 FROM clientes WHERE numero_documento='00000000';
INSERT INTO campanas(nombre,cedente,cartera,fecha_inicio,fecha_fin) VALUES ('Campaña demostración','SMA','DEMO','2026-01-01','2026-12-31');
INSERT INTO reglas_negociacion(campana_id,tipo_acuerdo,descuento_maximo,monto_minimo,cuotas_maximas) SELECT id,'CANCELACION',40,6000,3 FROM campanas WHERE nombre='Campaña demostración';
INSERT INTO reglas_negociacion(campana_id,tipo_acuerdo,descuento_maximo,monto_minimo,inicial_minima_porcentaje,cuotas_maximas) SELECT id,'CONVENIO',20,8000,10,12 FROM campanas WHERE nombre='Campaña demostración';

