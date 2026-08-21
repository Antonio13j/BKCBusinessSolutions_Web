SET NAMES utf8mb4;

ALTER TABLE clientes
  ADD COLUMN edad SMALLINT UNSIGNED NULL AFTER departamento,
  ADD COLUMN situacion_laboral VARCHAR(100) NULL AFTER edad,
  ADD COLUMN ultimo_sueldo DECIMAL(14,2) NULL AFTER situacion_laboral,
  ADD COLUMN ruc_trabajo VARCHAR(20) NULL AFTER ultimo_sueldo,
  ADD COLUMN empresa_trabajo VARCHAR(255) NULL AFTER ruc_trabajo,
  ADD COLUMN deuda_sbs DECIMAL(14,2) NULL AFTER empresa_trabajo,
  ADD COLUMN perfil_sbs VARCHAR(100) NULL AFTER deuda_sbs,
  ADD COLUMN bienes VARCHAR(100) NULL AFTER perfil_sbs,
  ADD COLUMN departamento_perfil VARCHAR(100) NULL AFTER bienes;

ALTER TABLE operaciones
  ADD COLUMN fecha_castigo DATE NULL AFTER fecha_carga_origen;
