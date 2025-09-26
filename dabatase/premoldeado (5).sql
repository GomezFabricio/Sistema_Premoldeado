-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-09-2025 a las 18:19:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `premoldeado`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditorias`
--

CREATE TABLE `auditorias` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tabla_afectada` varchar(45) NOT NULL,
  `accion` varchar(45) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `usuarios_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `codigo_cliente` varchar(20) DEFAULT NULL,
  `tipo_cliente` enum('MINORISTA','MAYORISTA','EMPRESARIAL') NOT NULL DEFAULT 'MINORISTA',
  `condicion_iva` enum('RESPONSABLE_INSCRIPTO','MONOTRIBUTISTA','CONSUMIDOR_FINAL','EXENTO') NOT NULL DEFAULT 'CONSUMIDOR_FINAL',
  `limite_credito` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuento_general` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fecha_alta` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `persona_id`, `codigo_cliente`, `tipo_cliente`, `condicion_iva`, `limite_credito`, `descuento_general`, `fecha_alta`, `observaciones`, `activo`) VALUES
(1, 2, 'CLI001', 'MINORISTA', 'CONSUMIDOR_FINAL', 0.00, 0.00, '2025-06-28', 'nolosd', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes_backup`
--

CREATE TABLE `clientes_backup` (
  `id` int(11) NOT NULL,
  `fecha_alta` date NOT NULL,
  `observaciones` varchar(45) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes_backup`
--

INSERT INTO `clientes_backup` (`id`, `fecha_alta`, `observaciones`, `activo`) VALUES
(1, '2025-06-28', 'nolosd', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `proveedor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura`
--

CREATE TABLE `detalle_factura` (
  `id` int(11) NOT NULL,
  `cantidad` varchar(45) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `factura_id` int(11) NOT NULL,
  `productos_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `productos_id` int(11) NOT NULL,
  `pedidos_id` int(11) NOT NULL,
  `cantidad` varchar(45) DEFAULT NULL,
  `precio_unitario` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedidos`
--

INSERT INTO `detalle_pedidos` (`productos_id`, `pedidos_id`, `cantidad`, `precio_unitario`) VALUES
(6, 2, '1', '32.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucion`
--

CREATE TABLE `devolucion` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `clientes_id` int(11) NOT NULL,
  `productos_id` int(11) NOT NULL,
  `estado_devolucion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_devolucion`
--

CREATE TABLE `estado_devolucion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_devolucion`
--

INSERT INTO `estado_devolucion` (`id`, `nombre`) VALUES
(2, 'Aprobada'),
(3, 'Rechazada'),
(4, 'Cancelada por el cliente'),
(5, 'Reembolsado'),
(6, 'Pendiente de Evaluacion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_pedido`
--

CREATE TABLE `estado_pedido` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_pedido`
--

INSERT INTO `estado_pedido` (`id`, `nombre`) VALUES
(1, 'En proceso ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_produccion`
--

CREATE TABLE `estado_produccion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_produccion`
--

INSERT INTO `estado_produccion` (`id`, `nombre`) VALUES
(1, 'Pendiente '),
(2, 'FINALIZADO'),
(3, 'RETRASADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `clientes_id` int(11) NOT NULL,
  `metodo_pago_id` int(11) NOT NULL,
  `pedidos_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forma_entrega`
--

CREATE TABLE `forma_entrega` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `forma_entrega`
--

INSERT INTO `forma_entrega` (`id`, `nombre`) VALUES
(1, 'a domicilio '),
(2, 'retiro en fabrica'),
(3, 'retiro en fabrica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `codigo_material` varchar(20) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidad_medida_id` int(11) NOT NULL,
  `cantidad_stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `costo_unitario` decimal(12,2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(12,2) DEFAULT NULL,
  `stock_minimo` decimal(10,3) NOT NULL DEFAULT 0.000,
  `stock_maximo` decimal(10,3) DEFAULT NULL,
  `ubicacion_deposito` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `codigo_material`, `nombre`, `descripcion`, `unidad_medida_id`, `cantidad_stock`, `costo_unitario`, `precio_venta`, `stock_minimo`, `stock_maximo`, `ubicacion_deposito`, `activo`, `fecha_creacion`) VALUES
(6, NULL, 'Cemento', 'Material migrado desde materiales_new', 6, 10.000, 10000.00, NULL, 15.000, NULL, NULL, 1, '2025-09-18 23:54:54'),
(7, NULL, 'Piedra', 'Material migrado desde materiales_new', 1, 5.000, 30000.00, NULL, 10.000, NULL, NULL, 1, '2025-09-18 23:54:54'),
(8, NULL, 'Hierro', 'Material migrado desde materiales_new', 1, 5.000, 10000.00, NULL, 10.000, NULL, NULL, 1, '2025-09-18 23:54:54'),
(9, NULL, 'arena fina', 'Material migrado desde materiales_new', 3, 25.000, 100000.00, NULL, 15.000, NULL, NULL, 1, '2025-09-18 23:54:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales_compras`
--

CREATE TABLE `materiales_compras` (
  `materiales_id` int(11) NOT NULL,
  `compras_id` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales_new`
--

CREATE TABLE `materiales_new` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `unidad_medida_id` int(11) NOT NULL,
  `cantidad_stock` decimal(12,2) DEFAULT 0.00,
  `costo_unitario` decimal(12,2) DEFAULT 0.00,
  `stock_minimo` decimal(12,2) DEFAULT 0.00,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales_new`
--

INSERT INTO `materiales_new` (`id`, `nombre`, `unidad_medida_id`, `cantidad_stock`, `costo_unitario`, `stock_minimo`, `activo`) VALUES
(1, 'Cemento', 6, 10.00, 10000.00, 15.00, 1),
(2, 'Piedra', 1, 5.00, 30000.00, 10.00, 1),
(3, 'Hierro', 1, 5.00, 10000.00, 10.00, 1),
(4, 'arena fina', 3, 25.00, 100000.00, 15.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales_produccion`
--

CREATE TABLE `materiales_produccion` (
  `materiales_id` int(11) NOT NULL,
  `produccion_id` int(10) UNSIGNED NOT NULL,
  `cantidad_usada` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodo_pago`
--

INSERT INTO `metodo_pago` (`id`, `nombre`) VALUES
(1, 'efectivo'),
(3, 'transferencia bancaria'),
(4, 'Tarjeta de credito'),
(5, 'tarjeta de debito');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id`, `nombre`) VALUES
(1, 'Usuarios'),
(2, 'Clientes'),
(3, 'Productos'),
(4, 'Materiales'),
(5, 'Pedidos y reservas'),
(6, 'Producción'),
(7, 'Proveedores'),
(8, 'Compras'),
(9, 'Ventas'),
(10, 'Modulos'),
(20, 'Parámetros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `forma_entrega_id` int(11) NOT NULL,
  `estado_pedido_id` int(11) NOT NULL,
  `clientes_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `fecha`, `forma_entrega_id`, `estado_pedido_id`, `clientes_id`) VALUES
(2, '2025-06-17', 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_items_new`
--
-- Error leyendo la estructura de la tabla premoldeado.pedidos_items_new: #1932 - Table 'premoldeado.pedidos_items_new' doesn't exist in engine
-- Error leyendo datos de la tabla premoldeado.pedidos_items_new: #1064 - Algo está equivocado en su sintax cerca 'FROM `premoldeado`.`pedidos_items_new`' en la linea 1

--
-- Disparadores `pedidos_items_new`
--
DELIMITER $$
CREATE TRIGGER `trg_pedidos_items_calcular_subtotal` BEFORE INSERT ON `pedidos_items_new` FOR EACH ROW BEGIN
    IF NEW.descuento_porcentaje > 0 AND NEW.descuento_monto = 0 THEN
        SET NEW.descuento_monto = (NEW.cantidad * NEW.precio_unitario * NEW.descuento_porcentaje / 100);
    END IF;
    
    SET NEW.subtotal = (NEW.cantidad * NEW.precio_unitario) - NEW.descuento_monto;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_pedidos_items_update_subtotal` BEFORE UPDATE ON `pedidos_items_new` FOR EACH ROW BEGIN
    IF NEW.descuento_porcentaje > 0 AND NEW.descuento_monto = 0 THEN
        SET NEW.descuento_monto = (NEW.cantidad * NEW.precio_unitario * NEW.descuento_porcentaje / 100);
    END IF;
    
    SET NEW.subtotal = (NEW.cantidad * NEW.precio_unitario) - NEW.descuento_monto;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_new`
--

CREATE TABLE `pedidos_new` (
  `id` int(11) NOT NULL,
  `numero_pedido` varchar(20) DEFAULT NULL COMMENT 'C├│digo autom├ítico PED####',
  `cliente_id` int(11) NOT NULL,
  `fecha_pedido` date NOT NULL,
  `fecha_entrega_solicitada` date DEFAULT NULL,
  `fecha_entrega_real` date DEFAULT NULL,
  `estado_pedido_id` int(11) NOT NULL,
  `forma_entrega_id` int(11) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuentos` decimal(12,2) NOT NULL DEFAULT 0.00,
  `impuestos` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `usuario_creacion` int(11) DEFAULT NULL,
  `usuario_modificacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla normalizada para gesti├│n de pedidos - Fase 4';

--
-- Volcado de datos para la tabla `pedidos_new`
--

INSERT INTO `pedidos_new` (`id`, `numero_pedido`, `cliente_id`, `fecha_pedido`, `fecha_entrega_solicitada`, `fecha_entrega_real`, `estado_pedido_id`, `forma_entrega_id`, `subtotal`, `descuentos`, `impuestos`, `total`, `observaciones`, `usuario_creacion`, `usuario_modificacion`, `fecha_creacion`, `fecha_modificacion`, `activo`) VALUES
(1, 'PED0001', 1, '2025-06-17', NULL, NULL, 1, 1, 0.00, 0.00, 0.00, 0.00, 'Migrado desde pedido ID: 2', NULL, NULL, '2025-09-15 18:27:10', NULL, 1);

--
-- Disparadores `pedidos_new`
--
DELIMITER $$
CREATE TRIGGER `trg_pedidos_new_codigo_auto` BEFORE INSERT ON `pedidos_new` FOR EACH ROW BEGIN
    IF NEW.numero_pedido IS NULL OR NEW.numero_pedido = '' THEN
        SET NEW.numero_pedido = generar_codigo_pedido();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perfiles`
--

INSERT INTO `perfiles` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Cliente'),
(3, 'Empleado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles_modulos`
--

CREATE TABLE `perfiles_modulos` (
  `perfiles_id` int(11) NOT NULL,
  `modulos_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perfiles_modulos`
--

INSERT INTO `perfiles_modulos` (`perfiles_id`, `modulos_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `id` int(11) NOT NULL,
  `tipo_documento` enum('DNI','CUIL','CUIT','PASAPORTE','CEDULA') NOT NULL DEFAULT 'DNI',
  `numero_documento` varchar(20) DEFAULT NULL,
  `apellidos` varchar(100) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `razon_social` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`id`, `tipo_documento`, `numero_documento`, `apellidos`, `nombres`, `razon_social`, `telefono`, `email`, `direccion`, `localidad`, `provincia`, `codigo_postal`, `activo`, `fecha_creacion`, `fecha_modificacion`) VALUES
(1, 'DNI', '00000000', 'Administrador', 'Sistema', NULL, NULL, 'admin@sistema.com', NULL, NULL, NULL, NULL, 1, '2025-09-14 15:56:00', '2025-09-14 15:56:00'),
(2, 'DNI', '11111111', 'Cliente', 'Ejemplo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-09-14 15:56:00', '2025-09-14 15:56:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas_backup`
--

CREATE TABLE `personas_backup` (
  `id` int(10) UNSIGNED NOT NULL,
  `apellidos` varchar(45) NOT NULL,
  `nombres` varchar(45) NOT NULL,
  `telefono` varchar(45) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `usuarios_id` int(11) NOT NULL,
  `clientes_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produccion`
--

CREATE TABLE `produccion` (
  `id` int(10) UNSIGNED NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `estado_produccion_id` int(11) NOT NULL,
  `reserva_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `produccion`
--

INSERT INTO `produccion` (`id`, `fecha_inicio`, `fecha_entrega`, `cantidad`, `estado_produccion_id`, `reserva_id`) VALUES
(2, '2025-06-26', '2025-06-23', 9, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produccion_materiales`
--

CREATE TABLE `produccion_materiales` (
  `id` int(11) NOT NULL,
  `produccion_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `cantidad_planificada` decimal(10,3) NOT NULL,
  `cantidad_usada` decimal(10,3) NOT NULL DEFAULT 0.000,
  `costo_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `costo_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observaciones` varchar(255) DEFAULT NULL,
  `fecha_uso` date DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Materiales utilizados en producci├│n (BOM - Bill of Materials)';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produccion_new`
--

CREATE TABLE `produccion_new` (
  `id` int(11) NOT NULL,
  `numero_produccion` varchar(20) DEFAULT NULL COMMENT 'C├│digo autom├ítico PROD####',
  `pedido_id` int(11) DEFAULT NULL,
  `reserva_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad_programada` decimal(10,3) NOT NULL,
  `cantidad_producida` decimal(10,3) NOT NULL DEFAULT 0.000,
  `fecha_inicio` date NOT NULL,
  `fecha_fin_programada` date DEFAULT NULL,
  `fecha_fin_real` date DEFAULT NULL,
  `estado_produccion_id` int(11) NOT NULL,
  `costo_materiales` decimal(12,2) NOT NULL DEFAULT 0.00,
  `costo_mano_obra` decimal(12,2) NOT NULL DEFAULT 0.00,
  `costo_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `usuario_creacion` int(11) DEFAULT NULL,
  `usuario_modificacion` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla normalizada para gesti├│n de producci├│n - Fase 4';

--
-- Volcado de datos para la tabla `produccion_new`
--

INSERT INTO `produccion_new` (`id`, `numero_produccion`, `pedido_id`, `reserva_id`, `producto_id`, `cantidad_programada`, `cantidad_producida`, `fecha_inicio`, `fecha_fin_programada`, `fecha_fin_real`, `estado_produccion_id`, `costo_materiales`, `costo_mano_obra`, `costo_total`, `observaciones`, `usuario_creacion`, `usuario_modificacion`, `fecha_creacion`, `fecha_modificacion`, `activo`) VALUES
(1, 'PROD0001', NULL, 1, NULL, 9.000, 9.000, '2025-06-26', '2025-06-23', NULL, 1, 0.00, 0.00, 0.00, 'Migrado desde produccion ID: 2', NULL, NULL, '2025-09-15 18:27:10', NULL, 1),
(2, 'PROD0002', NULL, NULL, 16, 20.000, 0.000, '2025-09-23', '2025-09-24', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-23 13:34:20', NULL, 1),
(3, 'PROD0003', NULL, NULL, 16, 20.000, 0.000, '2025-09-23', '2025-09-24', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-23 13:34:31', NULL, 1),
(4, 'PROD0004', NULL, NULL, 10, 20.000, 0.000, '2025-09-24', '2025-09-27', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-24 01:22:05', NULL, 1),
(5, 'PROD0005', NULL, NULL, 10, 20.000, 0.000, '2025-09-24', '2025-09-27', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-24 01:22:12', NULL, 1),
(6, 'PROD0006', NULL, NULL, 15, 15.000, 0.000, '2025-09-24', '2025-09-27', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-24 01:22:43', NULL, 1),
(7, 'PROD0007', NULL, NULL, 9, 50.000, 0.000, '2025-09-24', '2025-09-30', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-24 01:27:01', NULL, 1),
(8, 'PROD0008', NULL, NULL, 10, 15.000, 0.000, '2025-09-24', '2025-09-27', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-24 01:31:06', NULL, 1),
(9, 'PROD0009', NULL, NULL, 10, 15.000, 0.000, '2025-09-24', '2025-09-27', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-24 01:40:33', NULL, 1),
(10, 'PROD0010', NULL, NULL, 14, 10.000, 0.000, '2025-09-24', '2025-10-23', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-24 01:40:52', NULL, 1),
(11, 'PROD0011', NULL, NULL, 15, 10.000, 0.000, '2025-09-24', '2025-10-23', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-24 01:44:01', NULL, 1),
(12, 'PROD0012', NULL, NULL, 15, 25.000, 0.000, '2025-09-26', '2025-09-28', NULL, 1, 0.00, 0.00, 0.00, '', 1, 1, '2025-09-24 03:02:41', '2025-09-24 16:46:21', 0),
(13, 'PROD0013', NULL, NULL, 11, 50.000, 0.000, '2025-09-24', '2025-11-23', NULL, 1, 0.00, 0.00, 0.00, '', 1, NULL, '2025-09-24 03:06:13', NULL, 1),
(14, 'PROD0014', NULL, NULL, NULL, 10000.000, 0.000, '2025-09-25', '2025-09-25', NULL, 3, 0.00, 0.00, 0.00, '', 1, 1, '2025-09-24 04:12:38', '2025-09-25 00:00:43', 1),
(15, 'PROD0015', NULL, NULL, 10, 10.000, 0.000, '2025-09-24', '2026-09-26', NULL, 1, 0.00, 0.00, 0.00, '', 1, 1, '2025-09-24 14:42:07', '2025-09-24 23:30:09', 0),
(16, 'PROD0016', NULL, NULL, 10, 10.000, 0.000, '2025-09-24', '2026-09-26', NULL, 1, 0.00, 0.00, 0.00, '', 1, 1, '2025-09-24 15:47:37', '2025-09-24 23:30:06', 0),
(17, 'PROD0017', NULL, NULL, 10, 10.000, 0.000, '2025-09-24', '2026-09-26', NULL, 1, 0.00, 0.00, 0.00, '', 1, 1, '2025-09-24 16:01:46', '2025-09-24 18:32:37', 0);

--
-- Disparadores `produccion_new`
--
DELIMITER $$
CREATE TRIGGER `trg_produccion_new_codigo_auto` BEFORE INSERT ON `produccion_new` FOR EACH ROW BEGIN
    IF NEW.numero_produccion IS NULL OR NEW.numero_produccion = '' THEN
        SET NEW.numero_produccion = generar_codigo_produccion();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `ancho` varchar(45) NOT NULL,
  `largo` varchar(45) NOT NULL,
  `cantidad_disponible` int(11) NOT NULL,
  `stock_minimo` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `activo` varchar(45) NOT NULL DEFAULT '1',
  `tipo_producto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `ancho`, `largo`, `cantidad_disponible`, `stock_minimo`, `precio_unitario`, `activo`, `tipo_producto_id`) VALUES
(4, '40', '1', 9, 6, 40.00, '0', 1),
(5, '60', '12', 6, 45, 70.00, '0', 2),
(6, '40', '12', 3, 9, 32.00, '1', 2),
(7, '60x1,25', '1,25', 2, 1, 12.00, '1', 1),
(9, '12', '34', 5, 12, 21.00, '1', 2),
(10, '40', '34', 9, 78, 4.00, '1', 2),
(11, '60x1,25', '1,25', 50, 10, 70.00, '1', 2),
(12, '60x1,25', '34', 5, 2, 21.00, '0', 2),
(13, '60', '2', 50, 10, 50000.00, '0', 1),
(14, '20', '2', 12, 15, 60000.00, '1', 2),
(15, '80', '2', 50, 20, 80000.00, '1', 6),
(16, '1', '2', 5, 5, 80000.00, '1', 1),
(17, '80', '2', 50, 15, 20000.00, '1', 3),
(18, '50', '20', 500, 10, 50000.00, '1', 1),
(19, '60', '1', 50, 15, 20000.00, '1', 2),
(20, '85', '90', 45, 20, 10000.00, '1', 5),
(21, '60', '1.25', 50, 10, 45000.00, '0', 3),
(22, '60', '1.25', 50, 10, 45000.00, '0', 3),
(23, '40', '1', 10, 100, 20000.00, '0', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `codigo_proveedor` varchar(20) DEFAULT NULL,
  `cuit` varchar(15) DEFAULT NULL,
  `condicion_iva` enum('RESPONSABLE_INSCRIPTO','MONOTRIBUTISTA','EXENTO') NOT NULL DEFAULT 'RESPONSABLE_INSCRIPTO',
  `tipo_proveedor` enum('MATERIALES','SERVICIOS','TRANSPORTE','EQUIPOS','OTROS') NOT NULL DEFAULT 'MATERIALES',
  `condicion_pago` enum('CONTADO','15_DIAS','30_DIAS','45_DIAS','60_DIAS','90_DIAS') NOT NULL DEFAULT '30_DIAS',
  `descuento_pronto_pago` decimal(5,2) NOT NULL DEFAULT 0.00,
  `plazo_entrega_dias` int(11) DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor_backup`
--

CREATE TABLE `proveedor_backup` (
  `id` int(11) NOT NULL,
  `condicion_iva` varchar(45) NOT NULL,
  `iva` varchar(45) NOT NULL,
  `fecha_registro` date NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `personas_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `id` int(11) NOT NULL,
  `fecha_reserva` date NOT NULL,
  `cantidad` int(11) NOT NULL,
  `senia` decimal(10,2) NOT NULL,
  `pedidos_id` int(11) NOT NULL,
  `reserva_estado_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reserva`
--

INSERT INTO `reserva` (`id`, `fecha_reserva`, `cantidad`, `senia`, `pedidos_id`, `reserva_estado_id`) VALUES
(1, '2025-06-28', 9, 10.00, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva_estado`
--

CREATE TABLE `reserva_estado` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reserva_estado`
--

INSERT INTO `reserva_estado` (`id`, `nombre`) VALUES
(1, 'ejecutando');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_producto`
--

CREATE TABLE `tipo_producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `activo` varchar(45) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_producto`
--

INSERT INTO `tipo_producto` (`id`, `nombre`, `activo`) VALUES
(1, 'Alcantarilla', '1'),
(2, 'Pilar', '1'),
(3, 'Camara', '1'),
(4, 'CAMARA SEP', '0'),
(5, 'Camara Septica', '1'),
(6, 'Camara Septica', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `id` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidades_medida`
--

INSERT INTO `unidades_medida` (`id`, `codigo`, `nombre`, `descripcion`, `activo`) VALUES
(1, 'm', 'Metro', 'Metro lineal', 1),
(2, 'm2', 'Metro²', 'Metro cuadrado', 1),
(3, 'm3', 'Metro³', 'Metro cúbico', 1),
(4, 'kg', 'Kilogramo', 'Kilogramo', 1),
(5, 'ton', 'Tonelada', 'Tonelada métrica', 1),
(6, 'bolsa', 'Bolsa', 'Bolsa/Saco', 1),
(7, 'unid', 'Unidad', 'Unidad individual', 1),
(8, 'lts', 'Litros', 'Litros', 1),
(9, 'pcs', 'Piezas', 'Piezas/Elementos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `domicilio` varchar(200) DEFAULT NULL,
  `perfil_id` int(11) NOT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `intentos_fallidos` int(11) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `persona_id`, `nombre_usuario`, `email`, `password`, `domicilio`, `perfil_id`, `ultimo_acceso`, `intentos_fallidos`, `bloqueado_hasta`, `activo`, `fecha_creacion`) VALUES
(1, 1, 'admin', 'admin@sistema.com', '$2a$12$BjHvL.SKljBTCpzxuGskreX6VwK7RaResvGruGC94yY3NCdluRfla', NULL, 1, NULL, 0, NULL, 1, '2025-09-14 15:56:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_backup`
--

CREATE TABLE `usuarios_backup` (
  `id` int(11) NOT NULL,
  `nombre_usuarios` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `domicilio` varchar(45) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `perfiles_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_backup`
--

INSERT INTO `usuarios_backup` (`id`, `nombre_usuarios`, `email`, `password`, `domicilio`, `activo`, `perfiles_id`) VALUES
(1, 'admin', 'admin@sistema.com', '$2a$12$BjHvL.SKljBTCpzxuGskreX6VwK7RaResvGruGC94yY3NCdluRfla', NULL, 1, 1),
(2, 'Lili', 'lililopez@gmail', '$2y$10$WGyVd0n1TQUBININbbhGXuNXBUVLWF5zcpH9jg5blX7iSS2PC3OJ.', NULL, 1, 2),
(3, 'Cartaman', 'cartaman@gmail', '$2y$10$qviR4Mqa7a69cIY/gM687ueGNvqDujNM93m4IRd8L8lmJILv8Yx7C', NULL, 1, 3),
(6, 'Cartaman', 'cartaman@gmail', '$2y$10$RDv30x0W5hrsGhOnJqu2WeIWgjY/IyAimQiaeK75sNonjG2q3speW', NULL, 1, 2),
(7, 'Cartaman', 'cartaman@gmail', '$2y$10$x5xdLXmopFOnD8JkS9czwuMOwXZd7Zm9K.5Q2z5D4FzlkjMGBuwi6', NULL, 1, 2),
(8, 'Cartaman', 'cartaman@gmail', '$2y$10$e/J5um7ZX4sgI2AxUm27uOkFzZpThqt.9R50OkXXsho.CX6Qw7Ipi', NULL, 1, 2),
(9, 'Cartaman', 'cartaman@gmail', '$2y$10$im/xfaLloU/i92BybhkBpuP5rS8dhBE4.QYRAFfzNjaSvoxJePG02', NULL, 1, 2),
(10, 'Cartaman', 'cartaman@gmail', '$2y$10$.9pqhqhrgXsqiDc1cUkP5ezD2uvhdnnjjuTo8.IVaZWKbsiFSILuW', NULL, 1, 2),
(11, 'Cartaman', 'cartaman@gmail', '$2y$10$wZqI/P3e0U.b/AGtYKtuY.NlWYVWqjhgvtgOFrrMQuuuidsS.dReC', NULL, 1, 2),
(12, 'Cartaman', 'cartaman@gmail', '$2y$10$7NT8TnvJJgVM7r8DkCPi9OQ.Os0j9nHsR7FyKXicb0wgO1hVCFBNi', NULL, 1, 2),
(13, 'Cartaman', 'cartaman@gmail', '$2y$10$HCDbmfMTSx5sAEbdFldIm.d6K6Hfxks1HcyctFJhl5ZclVPtnAtwG', NULL, 1, 2),
(14, 'Cartaman', 'cartaman@gmail', '$2y$10$ikaB7FAhfMmsfTcFB52nhescoICBgLSc4T2lfCcjWAGxC1stA.fXy', NULL, 1, 2),
(15, 'sabri', 'sabrina@gmail.com', '$2y$10$lDwvWDc0QM8Y0in4eON5F.Opm4.rJhZYmZBWfaVZfoLkQZZPNe9YW', NULL, 1, 3);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_materiales_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_materiales_completa` (
`material_id` int(11)
,`codigo_material` varchar(20)
,`nombre` varchar(100)
,`descripcion` text
,`unidad_medida_id` int(11)
,`unidad_nombre` varchar(50)
,`unidad_simbolo` varchar(50)
,`stock_actual` decimal(10,3)
,`stock_minimo` decimal(10,3)
,`stock_maximo` decimal(10,3)
,`costo_unitario` decimal(12,2)
,`precio_venta` decimal(12,2)
,`ubicacion_deposito` varchar(50)
,`material_activo` tinyint(1)
,`fecha_creacion` timestamp
,`estado_stock` varchar(7)
,`stock_disponible` decimal(10,3)
,`categoria` varchar(7)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_pedidos_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_pedidos_completa` (
`id` int(11)
,`numero_pedido` varchar(20)
,`fecha_pedido` date
,`fecha_entrega_solicitada` date
,`fecha_entrega_real` date
,`subtotal` decimal(12,2)
,`descuentos` decimal(12,2)
,`impuestos` decimal(12,2)
,`total` decimal(12,2)
,`observaciones` text
,`fecha_creacion` timestamp
,`activo` tinyint(1)
,`cliente_id` int(11)
,`cliente_fecha_alta` date
,`cliente_observaciones` text
,`estado_pedido_id` int(11)
,`estado_pedido` varchar(45)
,`forma_entrega_id` int(11)
,`forma_entrega` varchar(45)
,`usuario_creacion` varchar(50)
,`prioridad` varchar(9)
,`dias_transcurridos` int(7)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_produccion_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_produccion_completa` (
`id` int(10) unsigned
,`fecha_inicio` date
,`fecha_entrega` date
,`cantidad` int(11)
,`estado_produccion_id` int(11)
,`estado_nombre` varchar(45)
,`reserva_id` int(11)
,`producto_id` int(11)
,`producto_nombre` varchar(91)
,`activo` varchar(45)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_materiales_completa`
--
DROP TABLE IF EXISTS `vista_materiales_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_materiales_completa`  AS SELECT `m`.`id` AS `material_id`, `m`.`codigo_material` AS `codigo_material`, `m`.`nombre` AS `nombre`, `m`.`descripcion` AS `descripcion`, `m`.`unidad_medida_id` AS `unidad_medida_id`, `um`.`nombre` AS `unidad_nombre`, `um`.`nombre` AS `unidad_simbolo`, `m`.`cantidad_stock` AS `stock_actual`, `m`.`stock_minimo` AS `stock_minimo`, `m`.`stock_maximo` AS `stock_maximo`, `m`.`costo_unitario` AS `costo_unitario`, `m`.`precio_venta` AS `precio_venta`, `m`.`ubicacion_deposito` AS `ubicacion_deposito`, `m`.`activo` AS `material_activo`, `m`.`fecha_creacion` AS `fecha_creacion`, CASE WHEN `m`.`cantidad_stock` <= 0 THEN 'AGOTADO' WHEN `m`.`cantidad_stock` <= `m`.`stock_minimo` THEN 'BAJO' ELSE 'OK' END AS `estado_stock`, `m`.`cantidad_stock` AS `stock_disponible`, 'General' AS `categoria` FROM (`materiales` `m` left join `unidades_medida` `um` on(`m`.`unidad_medida_id` = `um`.`id`)) WHERE `m`.`activo` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_pedidos_completa`
--
DROP TABLE IF EXISTS `vista_pedidos_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_pedidos_completa`  AS SELECT `p`.`id` AS `id`, `p`.`numero_pedido` AS `numero_pedido`, `p`.`fecha_pedido` AS `fecha_pedido`, `p`.`fecha_entrega_solicitada` AS `fecha_entrega_solicitada`, `p`.`fecha_entrega_real` AS `fecha_entrega_real`, `p`.`subtotal` AS `subtotal`, `p`.`descuentos` AS `descuentos`, `p`.`impuestos` AS `impuestos`, `p`.`total` AS `total`, `p`.`observaciones` AS `observaciones`, `p`.`fecha_creacion` AS `fecha_creacion`, `p`.`activo` AS `activo`, `c`.`id` AS `cliente_id`, `c`.`fecha_alta` AS `cliente_fecha_alta`, `c`.`observaciones` AS `cliente_observaciones`, `ep`.`id` AS `estado_pedido_id`, `ep`.`nombre` AS `estado_pedido`, `fe`.`id` AS `forma_entrega_id`, `fe`.`nombre` AS `forma_entrega`, `u`.`nombre_usuario` AS `usuario_creacion`, CASE WHEN `p`.`fecha_entrega_real` is not null THEN 'ENTREGADO' WHEN `p`.`fecha_entrega_solicitada` < curdate() THEN 'VENCIDO' WHEN `p`.`fecha_entrega_solicitada` = curdate() THEN 'VENCE_HOY' WHEN to_days(`p`.`fecha_entrega_solicitada`) - to_days(curdate()) <= 3 THEN 'URGENTE' ELSE 'NORMAL' END AS `prioridad`, to_days(coalesce(`p`.`fecha_entrega_real`,curdate())) - to_days(`p`.`fecha_pedido`) AS `dias_transcurridos` FROM ((((`pedidos_new` `p` left join `clientes` `c` on(`p`.`cliente_id` = `c`.`id`)) left join `estado_pedido` `ep` on(`p`.`estado_pedido_id` = `ep`.`id`)) left join `forma_entrega` `fe` on(`p`.`forma_entrega_id` = `fe`.`id`)) left join `usuarios` `u` on(`p`.`usuario_creacion` = `u`.`id`)) WHERE `p`.`activo` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_produccion_completa`
--
DROP TABLE IF EXISTS `vista_produccion_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_produccion_completa`  AS SELECT `p`.`id` AS `id`, `p`.`fecha_inicio` AS `fecha_inicio`, `p`.`fecha_entrega` AS `fecha_entrega`, `p`.`cantidad` AS `cantidad`, `p`.`estado_produccion_id` AS `estado_produccion_id`, `ep`.`nombre` AS `estado_nombre`, `p`.`reserva_id` AS `reserva_id`, `prod`.`id` AS `producto_id`, concat(`prod`.`ancho`,'x',`prod`.`largo`) AS `producto_nombre`, `prod`.`activo` AS `activo` FROM ((((`produccion` `p` join `estado_produccion` `ep` on(`p`.`estado_produccion_id` = `ep`.`id`)) join `reserva` `r` on(`p`.`reserva_id` = `r`.`id`)) join `detalle_pedidos` `dp` on(`r`.`pedidos_id` = `dp`.`pedidos_id`)) join `productos` `prod` on(`dp`.`productos_id` = `prod`.`id`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD UNIQUE KEY `idauditorias_UNIQUE` (`id`),
  ADD KEY `fk_auditorias_usuarios1_idx` (`usuarios_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_persona` (`persona_id`),
  ADD UNIQUE KEY `uk_codigo` (`codigo_cliente`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_tipo` (`tipo_cliente`);

--
-- Indices de la tabla `clientes_backup`
--
ALTER TABLE `clientes_backup`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD KEY `fk_compras_proveedor1_idx` (`proveedor_id`);

--
-- Indices de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`id`,`factura_id`,`productos_id`),
  ADD UNIQUE KEY `iddetalle_factura_UNIQUE` (`id`),
  ADD KEY `fk_detalle_factura_factura1_idx` (`factura_id`),
  ADD KEY `fk_detalle_factura_productos1_idx` (`productos_id`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`productos_id`,`pedidos_id`),
  ADD KEY `fk_productos_has_pedidos_pedidos1_idx` (`pedidos_id`),
  ADD KEY `fk_productos_has_pedidos_productos1_idx` (`productos_id`);

--
-- Indices de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  ADD UNIQUE KEY `iddevolucion_UNIQUE` (`id`),
  ADD KEY `fk_devolucion_productos1_idx` (`productos_id`),
  ADD KEY `fk_devolucion_estado_devolucion1_idx` (`estado_devolucion_id`),
  ADD KEY `fk_devolucion_clientes_new` (`clientes_id`);

--
-- Indices de la tabla `estado_devolucion`
--
ALTER TABLE `estado_devolucion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idestado_devolucion_UNIQUE` (`id`);

--
-- Indices de la tabla `estado_pedido`
--
ALTER TABLE `estado_pedido`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idestado_pedido_UNIQUE` (`id`);

--
-- Indices de la tabla `estado_produccion`
--
ALTER TABLE `estado_produccion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idestado_produccion_UNIQUE` (`id`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD UNIQUE KEY `idfactura_UNIQUE` (`id`),
  ADD KEY `fk_factura_clientes1_idx` (`clientes_id`),
  ADD KEY `fk_factura_metodo_pago1_idx` (`metodo_pago_id`),
  ADD KEY `fk_factura_pedidos1_idx` (`pedidos_id`);

--
-- Indices de la tabla `forma_entrega`
--
ALTER TABLE `forma_entrega`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_codigo` (`codigo_material`),
  ADD KEY `unidad_medida_id` (`unidad_medida_id`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_stock_bajo` (`cantidad_stock`,`stock_minimo`);

--
-- Indices de la tabla `materiales_compras`
--
ALTER TABLE `materiales_compras`
  ADD PRIMARY KEY (`materiales_id`,`compras_id`),
  ADD KEY `fk_materiales_has_compras_compras1_idx` (`compras_id`),
  ADD KEY `fk_materiales_has_compras_materiales1_idx` (`materiales_id`);

--
-- Indices de la tabla `materiales_new`
--
ALTER TABLE `materiales_new`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_materiales_nombre` (`nombre`),
  ADD KEY `fk_materiales_unidad` (`unidad_medida_id`);

--
-- Indices de la tabla `materiales_produccion`
--
ALTER TABLE `materiales_produccion`
  ADD PRIMARY KEY (`materiales_id`,`produccion_id`),
  ADD KEY `fk_materiales_has_produccion_produccion1_idx` (`produccion_id`),
  ADD KEY `fk_materiales_has_produccion_materiales1_idx` (`materiales_id`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD UNIQUE KEY `idpedidos_UNIQUE` (`id`),
  ADD KEY `fk_pedidos_forma_entrega1_idx` (`forma_entrega_id`),
  ADD KEY `fk_pedidos_estado_pedido1_idx` (`estado_pedido_id`),
  ADD KEY `fk_pedidos_clientes1_idx` (`clientes_id`);

--
-- Indices de la tabla `pedidos_new`
--
ALTER TABLE `pedidos_new`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_numero_pedido` (`numero_pedido`),
  ADD KEY `idx_cliente_fecha` (`cliente_id`,`fecha_pedido`),
  ADD KEY `idx_estado_fecha` (`estado_pedido_id`,`fecha_pedido`),
  ADD KEY `fk_pedidos_new_estados` (`estado_pedido_id`),
  ADD KEY `fk_pedidos_new_forma_entrega` (`forma_entrega_id`),
  ADD KEY `fk_pedidos_new_usuario_creacion` (`usuario_creacion`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idperfiles_UNIQUE` (`id`);

--
-- Indices de la tabla `perfiles_modulos`
--
ALTER TABLE `perfiles_modulos`
  ADD PRIMARY KEY (`perfiles_id`,`modulos_id`),
  ADD KEY `fk_perfiles_has_modulos_modulos1_idx` (`modulos_id`),
  ADD KEY `fk_perfiles_has_modulos_perfiles1_idx` (`perfiles_id`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_documento` (`tipo_documento`,`numero_documento`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_nombre` (`apellidos`,`nombres`);

--
-- Indices de la tabla `personas_backup`
--
ALTER TABLE `personas_backup`
  ADD PRIMARY KEY (`id`,`usuarios_id`,`clientes_id`),
  ADD KEY `fk_personas_clientes1_idx` (`clientes_id`),
  ADD KEY `fk_personas_usuarios1_idx` (`usuarios_id`);

--
-- Indices de la tabla `produccion`
--
ALTER TABLE `produccion`
  ADD PRIMARY KEY (`id`,`estado_produccion_id`,`reserva_id`),
  ADD KEY `fk_produccion_estado_produccion1_idx` (`estado_produccion_id`),
  ADD KEY `fk_produccion_reserva1_idx` (`reserva_id`);

--
-- Indices de la tabla `produccion_materiales`
--
ALTER TABLE `produccion_materiales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_produccion_material` (`produccion_id`,`material_id`),
  ADD KEY `idx_material_produccion` (`material_id`);

--
-- Indices de la tabla `produccion_new`
--
ALTER TABLE `produccion_new`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_numero_produccion` (`numero_produccion`),
  ADD KEY `idx_pedido_produccion` (`pedido_id`),
  ADD KEY `idx_estado_fecha` (`estado_produccion_id`,`fecha_inicio`),
  ADD KEY `idx_reserva` (`reserva_id`),
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `fk_produccion_new_usuario_creacion` (`usuario_creacion`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`,`tipo_producto_id`) USING BTREE,
  ADD UNIQUE KEY `idproductos_UNIQUE` (`id`),
  ADD KEY `fk_productos_tipo_producto1_idx` (`tipo_producto_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_persona` (`persona_id`),
  ADD UNIQUE KEY `uk_codigo` (`codigo_proveedor`),
  ADD UNIQUE KEY `uk_cuit` (`cuit`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_tipo` (`tipo_proveedor`);

--
-- Indices de la tabla `proveedor_backup`
--
ALTER TABLE `proveedor_backup`
  ADD PRIMARY KEY (`id`,`personas_id`),
  ADD UNIQUE KEY `idproveedor_UNIQUE` (`id`),
  ADD KEY `fk_proveedor_personas1_idx` (`personas_id`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`,`pedidos_id`,`reserva_estado_id`),
  ADD UNIQUE KEY `idreserva_UNIQUE` (`id`),
  ADD KEY `fk_reserva_pedidos1_idx` (`pedidos_id`),
  ADD KEY `fk_reserva_reserva_estado1_idx` (`reserva_estado_id`);

--
-- Indices de la tabla `reserva_estado`
--
ALTER TABLE `reserva_estado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idreserva_estado_UNIQUE` (`id`);

--
-- Indices de la tabla `tipo_producto`
--
ALTER TABLE `tipo_producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idtipo_producto_UNIQUE` (`id`);

--
-- Indices de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_codigo` (`codigo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `uk_email` (`email`),
  ADD UNIQUE KEY `uk_persona` (`persona_id`),
  ADD KEY `perfil_id` (`perfil_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `usuarios_backup`
--
ALTER TABLE `usuarios_backup`
  ADD PRIMARY KEY (`id`,`perfiles_id`),
  ADD UNIQUE KEY `id_usuarios_UNIQUE` (`id`),
  ADD KEY `fk_usuarios_perfiles1_idx` (`perfiles_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clientes_backup`
--
ALTER TABLE `clientes_backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_devolucion`
--
ALTER TABLE `estado_devolucion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `estado_pedido`
--
ALTER TABLE `estado_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estado_produccion`
--
ALTER TABLE `estado_produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `forma_entrega`
--
ALTER TABLE `forma_entrega`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `materiales_new`
--
ALTER TABLE `materiales_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pedidos_new`
--
ALTER TABLE `pedidos_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `personas_backup`
--
ALTER TABLE `personas_backup`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `produccion`
--
ALTER TABLE `produccion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `produccion_materiales`
--
ALTER TABLE `produccion_materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `produccion_new`
--
ALTER TABLE `produccion_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor_backup`
--
ALTER TABLE `proveedor_backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reserva_estado`
--
ALTER TABLE `reserva_estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipo_producto`
--
ALTER TABLE `tipo_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios_backup`
--
ALTER TABLE `usuarios_backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD CONSTRAINT `fk_auditorias_usuarios_new` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_compras_proveedores_new` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE NO ACTION;

--
-- Filtros para la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD CONSTRAINT `fk_detalle_factura_factura1` FOREIGN KEY (`factura_id`) REFERENCES `factura` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_detalle_factura_productos1` FOREIGN KEY (`productos_id`) REFERENCES `productos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `fk_productos_has_pedidos_pedidos1` FOREIGN KEY (`pedidos_id`) REFERENCES `pedidos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_productos_has_pedidos_productos1` FOREIGN KEY (`productos_id`) REFERENCES `productos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `devolucion`
--
ALTER TABLE `devolucion`
  ADD CONSTRAINT `fk_devolucion_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes_backup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_devolucion_clientes_new` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_devolucion_estado_devolucion1` FOREIGN KEY (`estado_devolucion_id`) REFERENCES `estado_devolucion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_devolucion_productos1` FOREIGN KEY (`productos_id`) REFERENCES `productos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `fk_factura_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes_backup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_factura_clientes_new` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_factura_metodo_pago1` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodo_pago` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_factura_pedidos1` FOREIGN KEY (`pedidos_id`) REFERENCES `pedidos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_ibfk_1` FOREIGN KEY (`unidad_medida_id`) REFERENCES `unidades_medida` (`id`);

--
-- Filtros para la tabla `materiales_compras`
--
ALTER TABLE `materiales_compras`
  ADD CONSTRAINT `fk_materiales_compras_materiales_new` FOREIGN KEY (`materiales_id`) REFERENCES `materiales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_materiales_has_compras_compras1` FOREIGN KEY (`compras_id`) REFERENCES `compras` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `materiales_new`
--
ALTER TABLE `materiales_new`
  ADD CONSTRAINT `fk_materiales_unidad` FOREIGN KEY (`unidad_medida_id`) REFERENCES `unidades_medida` (`id`);

--
-- Filtros para la tabla `materiales_produccion`
--
ALTER TABLE `materiales_produccion`
  ADD CONSTRAINT `fk_materiales_has_produccion_produccion1` FOREIGN KEY (`produccion_id`) REFERENCES `produccion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_materiales_produccion_materiales_new` FOREIGN KEY (`materiales_id`) REFERENCES `materiales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes_backup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pedidos_clientes_new` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_pedidos_estado_pedido1` FOREIGN KEY (`estado_pedido_id`) REFERENCES `estado_pedido` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pedidos_forma_entrega1` FOREIGN KEY (`forma_entrega_id`) REFERENCES `forma_entrega` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pedidos_new`
--
ALTER TABLE `pedidos_new`
  ADD CONSTRAINT `fk_pedidos_new_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedidos_new_estados` FOREIGN KEY (`estado_pedido_id`) REFERENCES `estado_pedido` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedidos_new_forma_entrega` FOREIGN KEY (`forma_entrega_id`) REFERENCES `forma_entrega` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedidos_new_usuario_creacion` FOREIGN KEY (`usuario_creacion`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `perfiles_modulos`
--
ALTER TABLE `perfiles_modulos`
  ADD CONSTRAINT `fk_perfiles_has_modulos_modulos1` FOREIGN KEY (`modulos_id`) REFERENCES `modulos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_perfiles_has_modulos_perfiles1` FOREIGN KEY (`perfiles_id`) REFERENCES `perfiles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `personas_backup`
--
ALTER TABLE `personas_backup`
  ADD CONSTRAINT `fk_personas_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes_backup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_personas_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios_backup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `produccion`
--
ALTER TABLE `produccion`
  ADD CONSTRAINT `fk_produccion_estado_produccion1` FOREIGN KEY (`estado_produccion_id`) REFERENCES `estado_produccion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_produccion_reserva1` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `produccion_materiales`
--
ALTER TABLE `produccion_materiales`
  ADD CONSTRAINT `fk_produccion_materiales_material` FOREIGN KEY (`material_id`) REFERENCES `materiales_new` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produccion_materiales_produccion` FOREIGN KEY (`produccion_id`) REFERENCES `produccion_new` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `produccion_new`
--
ALTER TABLE `produccion_new`
  ADD CONSTRAINT `fk_produccion_new_estado` FOREIGN KEY (`estado_produccion_id`) REFERENCES `estado_produccion` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produccion_new_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos_new` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produccion_new_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produccion_new_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produccion_new_usuario_creacion` FOREIGN KEY (`usuario_creacion`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_tipo_producto1` FOREIGN KEY (`tipo_producto_id`) REFERENCES `tipo_producto` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proveedor_backup`
--
ALTER TABLE `proveedor_backup`
  ADD CONSTRAINT `fk_proveedor_personas1` FOREIGN KEY (`personas_id`) REFERENCES `personas_backup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `fk_reserva_pedidos1` FOREIGN KEY (`pedidos_id`) REFERENCES `pedidos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserva_reserva_estado1` FOREIGN KEY (`reserva_estado_id`) REFERENCES `reserva_estado` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`perfil_id`) REFERENCES `perfiles` (`id`);

--
-- Filtros para la tabla `usuarios_backup`
--
ALTER TABLE `usuarios_backup`
  ADD CONSTRAINT `fk_usuarios_perfiles1` FOREIGN KEY (`perfiles_id`) REFERENCES `perfiles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
