CREATE INDEX idx_clientes_documento ON clientes(numero_documento);
CREATE INDEX idx_operaciones_cliente ON operaciones(cliente_id,estado);
CREATE INDEX idx_acuerdos_creador_fecha ON acuerdos(creado_por_id,created_at);
CREATE INDEX idx_acuerdos_estado_fecha ON acuerdos(estado,created_at);
CREATE INDEX idx_cuotas_vencimiento ON cuotas(estado,fecha_vencimiento);
CREATE INDEX idx_auditoria_fecha ON auditoria(created_at);

