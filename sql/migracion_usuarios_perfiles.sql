-- ============================================================================
-- SCRIPT DE MIGRACIÓN: RELACIÓN USUARIOS - PERFILES (MUCHOS A MUCHOS)
-- Sistema Premoldeado - Fecha: 2025-10-12
-- ============================================================================

-- Descripción:
-- Este script convierte la relación directa usuarios -> perfiles a una relación
-- muchos a muchos mediante una tabla intermedia usuarios_perfiles.
-- Esto permite que un usuario tenga múltiples perfiles asignados.

-- ============================================================================
-- PASO 1: CREAR TABLA INTERMEDIA usuarios_perfiles
-- ============================================================================

CREATE TABLE `usuarios_perfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `perfil_id` int(11) NOT NULL COMMENT 'ID del perfil asignado',
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha cuando se asignó el perfil',
  `asignado_por` int(11) DEFAULT NULL COMMENT 'ID del usuario que realizó la asignación',
  `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_perfil` (`usuario_id`, `perfil_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_perfil_id` (`perfil_id`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Relación muchos a muchos entre usuarios y perfiles';

-- ============================================================================
-- PASO 2: MIGRAR DATOS EXISTENTES
-- ============================================================================

-- Insertar las relaciones actuales en la nueva tabla
INSERT INTO `usuarios_perfiles` (`usuario_id`, `perfil_id`, `fecha_asignacion`, `activo`)
SELECT 
    `id` as usuario_id,
    `perfil_id`,
    `fecha_creacion`,
    `activo`
FROM `usuarios` 
WHERE `perfil_id` IS NOT NULL 
AND `activo` = 1;

-- Verificar la migración (este SELECT debe mostrar los mismos registros)
SELECT 
    u.id as usuario_id,
    u.nombre_usuario,
    u.perfil_id as perfil_original,
    up.perfil_id as perfil_migrado,
    p.nombre as nombre_perfil
FROM usuarios u
LEFT JOIN usuarios_perfiles up ON u.id = up.usuario_id
LEFT JOIN perfiles p ON up.perfil_id = p.id
WHERE u.activo = 1
ORDER BY u.id;

-- ============================================================================
-- PASO 3: CREAR ÍNDICES Y RESTRICCIONES
-- ============================================================================

-- Agregar foreign keys a la tabla intermedia
ALTER TABLE `usuarios_perfiles`
  ADD CONSTRAINT `fk_usuarios_perfiles_usuario` 
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuarios_perfiles_perfil` 
    FOREIGN KEY (`perfil_id`) REFERENCES `perfiles` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuarios_perfiles_asignador`
    FOREIGN KEY (`asignado_por`) REFERENCES `usuarios` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================================================
-- PASO 4: ELIMINAR RELACIÓN DIRECTA ANTERIOR (¡HACER BACKUP ANTES!)
-- ============================================================================

-- IMPORTANTE: Ejecutar solo después de verificar que la migración fue exitosa

-- 4.1. Eliminar foreign key constraint existente
ALTER TABLE `usuarios` DROP FOREIGN KEY `usuarios_ibfk_2`;

-- 4.2. Eliminar la columna perfil_id de la tabla usuarios
ALTER TABLE `usuarios` DROP COLUMN `perfil_id`;

-- ============================================================================
-- PASO 5: ACTUALIZAR VISTA usuarios_completa
-- ============================================================================

-- Eliminar la vista existente
DROP VIEW IF EXISTS `vista_usuarios_completa`;

-- Crear nueva vista que maneja múltiples perfiles
CREATE VIEW `vista_usuarios_completa` AS
SELECT 
    u.id AS usuario_id,
    u.nombre_usuario,
    u.email,
    u.activo,
    per.nombres,
    per.apellidos,
    -- Concatenar todos los perfiles del usuario
    GROUP_CONCAT(
        DISTINCT p.nombre 
        ORDER BY p.nombre 
        SEPARATOR ', '
    ) AS perfiles_nombres,
    -- Obtener IDs de perfiles para uso en PHP
    GROUP_CONCAT(
        DISTINCT CONCAT(p.id, ':', p.nombre)
        ORDER BY p.nombre 
        SEPARATOR '|'
    ) AS perfiles_data,
    -- Contar cantidad de perfiles
    COUNT(DISTINCT up.perfil_id) AS cantidad_perfiles
FROM usuarios u 
LEFT JOIN personas per ON u.persona_id = per.id
LEFT JOIN usuarios_perfiles up ON u.id = up.usuario_id AND up.activo = 1
LEFT JOIN perfiles p ON up.perfil_id = p.id AND p.estado = 1
GROUP BY u.id, u.nombre_usuario, u.email, u.activo, per.nombres, per.apellidos;

-- ============================================================================
-- PASO 6: CREAR FUNCIONES Y PROCEDIMIENTOS AUXILIARES
-- ============================================================================

-- Función para verificar si un usuario tiene un perfil específico
DELIMITER $$

CREATE FUNCTION `usuario_tiene_perfil`(
    p_usuario_id INT, 
    p_perfil_id INT
) RETURNS BOOLEAN
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE tiene_perfil BOOLEAN DEFAULT FALSE;
    
    SELECT COUNT(*) > 0 INTO tiene_perfil
    FROM usuarios_perfiles 
    WHERE usuario_id = p_usuario_id 
    AND perfil_id = p_perfil_id 
    AND activo = 1;
    
    RETURN tiene_perfil;
END$$

-- Procedimiento para asignar perfil a usuario
CREATE PROCEDURE `asignar_perfil_usuario`(
    IN p_usuario_id INT,
    IN p_perfil_id INT,
    IN p_asignado_por INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Verificar si ya existe la asignación
    IF NOT usuario_tiene_perfil(p_usuario_id, p_perfil_id) THEN
        INSERT INTO usuarios_perfiles (usuario_id, perfil_id, asignado_por, activo)
        VALUES (p_usuario_id, p_perfil_id, p_asignado_por, 1);
    END IF;
    
    COMMIT;
END$$

-- Procedimiento para remover perfil de usuario
CREATE PROCEDURE `remover_perfil_usuario`(
    IN p_usuario_id INT,
    IN p_perfil_id INT
)
BEGIN
    UPDATE usuarios_perfiles 
    SET activo = 0 
    WHERE usuario_id = p_usuario_id 
    AND perfil_id = p_perfil_id;
END$$

DELIMITER ;

-- ============================================================================
-- VERIFICACIONES FINALES
-- ============================================================================

-- Mostrar estadísticas de la migración
SELECT 
    'Usuarios totales' as concepto,
    COUNT(*) as cantidad
FROM usuarios
UNION ALL
SELECT 
    'Relaciones migradas' as concepto,
    COUNT(*) as cantidad
FROM usuarios_perfiles
UNION ALL
SELECT 
    'Perfiles activos' as concepto,
    COUNT(*) as cantidad
FROM perfiles WHERE estado = 1;

-- Mostrar usuarios con sus perfiles
SELECT 
    usuario_id,
    nombre_usuario,
    email,
    perfiles_nombres,
    cantidad_perfiles
FROM vista_usuarios_completa
WHERE activo = 1
ORDER BY nombre_usuario;

-- ============================================================================
-- INSTRUCCIONES DE EJECUCIÓN
-- ============================================================================
/*
PASOS RECOMENDADOS PARA EJECUTAR:

1. **HACER BACKUP COMPLETO** de la base de datos antes de ejecutar
   
2. Ejecutar paso a paso en este orden:
   - PASO 1: Crear tabla usuarios_perfiles
   - PASO 2: Migrar datos (verificar resultados)
   - PASO 3: Crear constraints
   - Verificar que todo funciona correctamente
   - PASO 4: Eliminar relación anterior (¡IRREVERSIBLE!)
   - PASO 5: Actualizar vista
   - PASO 6: Crear funciones auxiliares

3. **Probar funcionamiento** antes de continuar con modificaciones PHP

4. **Actualizar código PHP** para usar la nueva estructura

ROLLBACK EN CASO DE PROBLEMAS:
Si algo sale mal ANTES del PASO 4, puedes eliminar la tabla usuarios_perfiles
y restaurar el backup. Una vez ejecutado el PASO 4, necesitarás el backup completo.
*/

-- ============================================================================
-- FIN DEL SCRIPT DE MIGRACIÓN
-- ============================================================================