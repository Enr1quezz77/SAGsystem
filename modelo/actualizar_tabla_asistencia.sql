ALTER TABLE asistencia
ADD COLUMN biometrico_id VARCHAR(255) NULL,
ADD COLUMN estado_biometrico ENUM('exito', 'fallo') DEFAULT NULL;