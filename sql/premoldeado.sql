-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-10-2025 a las 19:16:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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
(1, 2, 'CLI001', 'MINORISTA', 'CONSUMIDOR_FINAL', 0.00, 0.00, '2025-09-30', 'Cliente de prueba', 1),
(2, 9, NULL, 'MINORISTA', 'CONSUMIDOR_FINAL', 0.00, 0.00, '2025-10-03', '', 0);

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
(1, 1, '2', '1000');

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
(1, 'En proceso');

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
(1, 'Pendiente');

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
(1, 'A domicilio');

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
(1, 'MAT001', 'Cemento', NULL, 1, 100.000, 500.00, NULL, 10.000, NULL, NULL, 1, '2025-09-30 17:21:26');

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
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(9, 'Ventas');

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
(1, '2025-09-30', 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado del perfil: 1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perfiles`
--

INSERT INTO `perfiles` (`id`, `nombre`, `estado`) VALUES
(1, 'Administrador', 1),
(2, 'Cliente', 1);

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
(1, 9);

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
(1, 'DNI', '00000000', 'Administrador', 'Sistema', NULL, NULL, 'admin@sistema.com', NULL, NULL, NULL, NULL, 1, '2025-09-30 17:21:26', '2025-09-30 17:21:26'),
(2, 'DNI', '11111111', 'Cliente', 'Ejemplo', NULL, NULL, 'cliente@ejemplo.com', NULL, NULL, NULL, NULL, 0, '2025-09-30 17:21:26', '2025-10-03 14:01:34'),
(5, 'DNI', 'AUTO1759451649673', 'Usuario', 'sabrinacliente', NULL, NULL, 'sabbaez@gmail.com', NULL, NULL, NULL, NULL, 1, '2025-10-03 00:34:09', '2025-10-03 00:34:09'),
(6, 'DNI', 'AUTO1759451964903', 'Usuario', 'fabriciocliente', NULL, NULL, 'fabricio@gmail.com', NULL, NULL, NULL, NULL, 1, '2025-10-03 00:39:24', '2025-10-03 00:39:24'),
(9, 'DNI', '23002682', 'Baez', 'Myrian', NULL, '3704811284', 'baezmyrianb@gmail.com', NULL, NULL, NULL, NULL, 1, '2025-10-03 13:58:49', '2025-10-03 13:58:49'),
(10, 'DNI', 'AUTO1759500833634', 'Usuario', 'Salvacliente', NULL, NULL, 'salvarand@gmail.com', NULL, NULL, NULL, NULL, 1, '2025-10-03 14:13:53', '2025-10-03 14:13:53'),
(11, 'DNI', 'AUTO1759501226420', 'Usuario', 'alanbeckadmin', NULL, NULL, 'alanbeck@gmail.com', NULL, NULL, NULL, NULL, 1, '2025-10-03 14:20:26', '2025-10-03 14:20:26'),
(12, 'DNI', 'AUTO1759501833585', 'Usuario', 'juanperezadmin', NULL, NULL, 'jperez@gmail.com', NULL, NULL, NULL, NULL, 1, '2025-10-03 14:30:33', '2025-10-03 14:30:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produccion`
--

CREATE TABLE `produccion` (
  `id` int(11) NOT NULL,
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
(1, '2025-09-30', '2025-09-30', 2, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produccion_materiales`
--

CREATE TABLE `produccion_materiales` (
  `id` int(11) NOT NULL,
  `produccion_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `cantidad` decimal(10,3) NOT NULL,
  `costo_unitario` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `produccion_materiales`
--

INSERT INTO `produccion_materiales` (`id`, `produccion_id`, `material_id`, `cantidad`, `costo_unitario`) VALUES
(1, 1, 1, 10.000, 500.00);

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
(1, '40', '100', 10, 2, 1000.00, '1', 1);

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

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `persona_id`, `codigo_proveedor`, `cuit`, `condicion_iva`, `tipo_proveedor`, `condicion_pago`, `descuento_pronto_pago`, `plazo_entrega_dias`, `fecha_registro`, `observaciones`, `activo`) VALUES
(1, 1, 'PROV001', '20-12345678-9', 'RESPONSABLE_INSCRIPTO', 'MATERIALES', 'CONTADO', 0.00, NULL, '2025-09-30', NULL, 1);

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
(1, '2025-09-30', 2, 100.00, 1, 1);

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
(1, 'Alcantarilla', '1');

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
(1, 'kg', 'Kilogramo', NULL, 1);

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
(1, 1, 'admin', 'admin@sistema.com', '$2a$12$BjHvL.SKljBTCpzxuGskreX6VwK7RaResvGruGC94yY3NCdluRfla', NULL, 1, NULL, 0, NULL, 1, '2025-09-30 17:21:26'),
(7, 5, 'sabrinacliente', 'sabbaez@gmail.com', '$2y$10$ghUxJZ7Z9C3ZMY67i3QRxu7mnlcSydt3i35O7yi0II4l65k34wkMe', NULL, 2, NULL, 0, NULL, 1, '2025-10-03 00:34:09'),
(8, 6, 'fabriciocliente', 'fabricio@gmail.com', '$2y$10$cDXFUSjqObpli0UAh5q5CeuKRvT7GfKSqL6fqB5.7PDS3QzWvHq9S', NULL, 2, NULL, 0, NULL, 1, '2025-10-03 00:39:24'),
(9, 10, 'Salvacliente', 'salvarand@gmail.com', '$2y$10$xpJbg2XmCewX5ulx6/6Vu.QjJsSeMrecO3/Wa.CE.hTKnIOQbA0ym', NULL, 2, NULL, 0, NULL, 1, '2025-10-03 14:13:53'),
(10, 11, 'alanbeckadmin', 'alanbeck@gmail.com', '$2y$10$Vb/osnBeA9jIDlGDHy6LjOK/3AqHyvqNhS.UFmYaUnCUPcz3m8ez6', NULL, 1, NULL, 0, NULL, 1, '2025-10-03 14:20:27'),
(11, 12, 'juanperezadmin', 'jperez@gmail.com', '$2y$10$epPN7NK7wxkgU/kfuFD7XO2oGSI5o6Rty/IOqUQQYth3YYfS1xbdS', NULL, 1, NULL, 0, NULL, 1, '2025-10-03 14:30:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `produccion_id` int(11) DEFAULT NULL,
  `numero_venta` varchar(20) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_venta` date NOT NULL,
  `monto_total` decimal(12,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_eliminacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `cliente_id`, `produccion_id`, `numero_venta`, `cantidad`, `fecha_venta`, `monto_total`, `activo`, `fecha_creacion`, `fecha_eliminacion`) VALUES
(1, 1, 1, 'V-0001', 1, '2025-09-30', 2000.00, 1, '2025-09-30 17:21:27', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_items`
--

CREATE TABLE `venta_items` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta_items`
--

INSERT INTO `venta_items` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, 1, 2000.00, 2000.00);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_clientes_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_clientes_completa` (
`cliente_id` int(11)
,`nombres` varchar(100)
,`apellidos` varchar(100)
,`numero_documento` varchar(20)
,`nombre_completo` varchar(201)
,`telefono` varchar(20)
,`fecha_alta` date
,`observaciones` text
,`cliente_activo` tinyint(1)
);

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
,`unidad_codigo` varchar(10)
,`stock_actual` decimal(10,3)
,`stock_minimo` decimal(10,3)
,`stock_maximo` decimal(10,3)
,`costo_unitario` decimal(12,2)
,`precio_venta` decimal(12,2)
,`ubicacion_deposito` varchar(50)
,`material_activo` tinyint(1)
,`fecha_creacion` timestamp
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_pedidos_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_pedidos_completa` (
`id` int(11)
,`fecha` date
,`forma_entrega_id` int(11)
,`forma_entrega` varchar(45)
,`estado_pedido_id` int(11)
,`estado_pedido` varchar(45)
,`clientes_id` int(11)
,`codigo_cliente` varchar(20)
,`tipo_cliente` enum('MINORISTA','MAYORISTA','EMPRESARIAL')
,`condicion_iva` enum('RESPONSABLE_INSCRIPTO','MONOTRIBUTISTA','CONSUMIDOR_FINAL','EXENTO')
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_proveedores_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_proveedores_completa` (
`proveedor_id` int(11)
,`codigo_proveedor` varchar(20)
,`cuit` varchar(15)
,`condicion_iva` enum('RESPONSABLE_INSCRIPTO','MONOTRIBUTISTA','EXENTO')
,`tipo_proveedor` enum('MATERIALES','SERVICIOS','TRANSPORTE','EQUIPOS','OTROS')
,`condicion_pago` enum('CONTADO','15_DIAS','30_DIAS','45_DIAS','60_DIAS','90_DIAS')
,`descuento_pronto_pago` decimal(5,2)
,`plazo_entrega_dias` int(11)
,`fecha_registro` date
,`observaciones` text
,`proveedor_activo` tinyint(1)
,`persona_id` int(11)
,`apellidos` varchar(100)
,`nombres` varchar(100)
,`razon_social` varchar(150)
,`telefono` varchar(20)
,`email` varchar(150)
,`direccion` varchar(200)
,`localidad` varchar(100)
,`provincia` varchar(100)
,`codigo_postal` varchar(10)
,`persona_activa` tinyint(1)
,`nombre_completo` varchar(202)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_usuarios_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_usuarios_completa` (
`usuario_id` int(11)
,`nombre_usuario` varchar(50)
,`email` varchar(150)
,`activo` tinyint(1)
,`perfil_id` int(11)
,`perfil_nombre` varchar(45)
,`nombres` varchar(100)
,`apellidos` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_clientes_completa`
--
DROP TABLE IF EXISTS `vista_clientes_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes_completa`  AS SELECT `c`.`id` AS `cliente_id`, `p`.`nombres` AS `nombres`, `p`.`apellidos` AS `apellidos`, `p`.`numero_documento` AS `numero_documento`, concat(`p`.`nombres`,' ',`p`.`apellidos`) AS `nombre_completo`, `p`.`telefono` AS `telefono`, `c`.`fecha_alta` AS `fecha_alta`, `c`.`observaciones` AS `observaciones`, `c`.`activo` AS `cliente_activo` FROM (`clientes` `c` join `personas` `p` on(`c`.`persona_id` = `p`.`id`)) WHERE `c`.`activo` = 1 AND `p`.`activo` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_materiales_completa`
--
DROP TABLE IF EXISTS `vista_materiales_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_materiales_completa`  AS SELECT `m`.`id` AS `material_id`, `m`.`codigo_material` AS `codigo_material`, `m`.`nombre` AS `nombre`, `m`.`descripcion` AS `descripcion`, `m`.`unidad_medida_id` AS `unidad_medida_id`, `um`.`nombre` AS `unidad_nombre`, `um`.`codigo` AS `unidad_codigo`, `m`.`cantidad_stock` AS `stock_actual`, `m`.`stock_minimo` AS `stock_minimo`, `m`.`stock_maximo` AS `stock_maximo`, `m`.`costo_unitario` AS `costo_unitario`, `m`.`precio_venta` AS `precio_venta`, `m`.`ubicacion_deposito` AS `ubicacion_deposito`, `m`.`activo` AS `material_activo`, `m`.`fecha_creacion` AS `fecha_creacion` FROM (`materiales` `m` join `unidades_medida` `um` on(`m`.`unidad_medida_id` = `um`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_pedidos_completa`
--
DROP TABLE IF EXISTS `vista_pedidos_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_pedidos_completa`  AS SELECT `p`.`id` AS `id`, `p`.`fecha` AS `fecha`, `p`.`forma_entrega_id` AS `forma_entrega_id`, `fe`.`nombre` AS `forma_entrega`, `p`.`estado_pedido_id` AS `estado_pedido_id`, `ep`.`nombre` AS `estado_pedido`, `p`.`clientes_id` AS `clientes_id`, `c`.`codigo_cliente` AS `codigo_cliente`, `c`.`tipo_cliente` AS `tipo_cliente`, `c`.`condicion_iva` AS `condicion_iva` FROM (((`pedidos` `p` join `forma_entrega` `fe` on(`p`.`forma_entrega_id` = `fe`.`id`)) join `estado_pedido` `ep` on(`p`.`estado_pedido_id` = `ep`.`id`)) join `clientes` `c` on(`p`.`clientes_id` = `c`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_proveedores_completa`
--
DROP TABLE IF EXISTS `vista_proveedores_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_proveedores_completa`  AS SELECT `pr`.`id` AS `proveedor_id`, `pr`.`codigo_proveedor` AS `codigo_proveedor`, `pr`.`cuit` AS `cuit`, `pr`.`condicion_iva` AS `condicion_iva`, `pr`.`tipo_proveedor` AS `tipo_proveedor`, `pr`.`condicion_pago` AS `condicion_pago`, `pr`.`descuento_pronto_pago` AS `descuento_pronto_pago`, `pr`.`plazo_entrega_dias` AS `plazo_entrega_dias`, `pr`.`fecha_registro` AS `fecha_registro`, `pr`.`observaciones` AS `observaciones`, `pr`.`activo` AS `proveedor_activo`, `p`.`id` AS `persona_id`, `p`.`apellidos` AS `apellidos`, `p`.`nombres` AS `nombres`, `p`.`razon_social` AS `razon_social`, `p`.`telefono` AS `telefono`, `p`.`email` AS `email`, `p`.`direccion` AS `direccion`, `p`.`localidad` AS `localidad`, `p`.`provincia` AS `provincia`, `p`.`codigo_postal` AS `codigo_postal`, `p`.`activo` AS `persona_activa`, concat(`p`.`apellidos`,', ',`p`.`nombres`) AS `nombre_completo` FROM (`proveedores` `pr` join `personas` `p` on(`pr`.`persona_id` = `p`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_usuarios_completa`
--
DROP TABLE IF EXISTS `vista_usuarios_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_usuarios_completa`  AS SELECT `u`.`id` AS `usuario_id`, `u`.`nombre_usuario` AS `nombre_usuario`, `u`.`email` AS `email`, `u`.`activo` AS `activo`, `u`.`perfil_id` AS `perfil_id`, `p`.`nombre` AS `perfil_nombre`, `per`.`nombres` AS `nombres`, `per`.`apellidos` AS `apellidos` FROM ((`usuarios` `u` join `perfiles` `p` on(`u`.`perfil_id` = `p`.`id`)) join `personas` `per` on(`u`.`persona_id` = `per`.`id`)) ;

--
-- Índices para tablas volcadas
--

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
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD KEY `fk_compras_proveedor1_idx` (`proveedor_id`);

--
-- Indices de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`id`),
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
  ADD PRIMARY KEY (`id`),
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
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id`),
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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idpedidos_UNIQUE` (`id`),
  ADD KEY `fk_pedidos_forma_entrega1_idx` (`forma_entrega_id`),
  ADD KEY `fk_pedidos_estado_pedido1_idx` (`estado_pedido_id`),
  ADD KEY `fk_pedidos_clientes1_idx` (`clientes_id`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idperfiles_UNIQUE` (`id`),
  ADD KEY `idx_perfiles_estado` (`estado`);

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
-- Indices de la tabla `produccion`
--
ALTER TABLE `produccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produccion_estado_produccion1_idx` (`estado_produccion_id`),
  ADD KEY `fk_produccion_reserva1_idx` (`reserva_id`);

--
-- Indices de la tabla `produccion_materiales`
--
ALTER TABLE `produccion_materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produccion_id` (`produccion_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
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
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reserva_pedidos1_idx` (`pedidos_id`),
  ADD KEY `fk_reserva_reserva_estado1_idx` (`reserva_estado_id`);

--
-- Indices de la tabla `reserva_estado`
--
ALTER TABLE `reserva_estado`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `produccion_id` (`produccion_id`);

--
-- Indices de la tabla `venta_items`
--
ALTER TABLE `venta_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_pedido`
--
ALTER TABLE `estado_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estado_produccion`
--
ALTER TABLE `estado_produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `forma_entrega`
--
ALTER TABLE `forma_entrega`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `produccion`
--
ALTER TABLE `produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `produccion_materiales`
--
ALTER TABLE `produccion_materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `venta_items`
--
ALTER TABLE `venta_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_compras_proveedores_new` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);

--
-- Filtros para la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD CONSTRAINT `fk_detalle_factura_factura1` FOREIGN KEY (`factura_id`) REFERENCES `factura` (`id`),
  ADD CONSTRAINT `fk_detalle_factura_productos1` FOREIGN KEY (`productos_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `fk_productos_has_pedidos_pedidos1` FOREIGN KEY (`pedidos_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `fk_productos_has_pedidos_productos1` FOREIGN KEY (`productos_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `devolucion`
--
ALTER TABLE `devolucion`
  ADD CONSTRAINT `fk_devolucion_clientes_new` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `fk_devolucion_estado_devolucion1` FOREIGN KEY (`estado_devolucion_id`) REFERENCES `estado_devolucion` (`id`),
  ADD CONSTRAINT `fk_devolucion_productos1` FOREIGN KEY (`productos_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `fk_factura_clientes_new` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `fk_factura_metodo_pago1` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodo_pago` (`id`),
  ADD CONSTRAINT `fk_factura_pedidos1` FOREIGN KEY (`pedidos_id`) REFERENCES `pedidos` (`id`);

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
  ADD CONSTRAINT `fk_materiales_has_compras_compras1` FOREIGN KEY (`compras_id`) REFERENCES `compras` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_clientes_new` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `fk_pedidos_estado_pedido1` FOREIGN KEY (`estado_pedido_id`) REFERENCES `estado_pedido` (`id`),
  ADD CONSTRAINT `fk_pedidos_forma_entrega1` FOREIGN KEY (`forma_entrega_id`) REFERENCES `forma_entrega` (`id`);

--
-- Filtros para la tabla `perfiles_modulos`
--
ALTER TABLE `perfiles_modulos`
  ADD CONSTRAINT `fk_perfiles_has_modulos_modulos1` FOREIGN KEY (`modulos_id`) REFERENCES `modulos` (`id`),
  ADD CONSTRAINT `fk_perfiles_has_modulos_perfiles1` FOREIGN KEY (`perfiles_id`) REFERENCES `perfiles` (`id`);

--
-- Filtros para la tabla `produccion`
--
ALTER TABLE `produccion`
  ADD CONSTRAINT `fk_produccion_estado_produccion1` FOREIGN KEY (`estado_produccion_id`) REFERENCES `estado_produccion` (`id`),
  ADD CONSTRAINT `fk_produccion_reserva1` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`);

--
-- Filtros para la tabla `produccion_materiales`
--
ALTER TABLE `produccion_materiales`
  ADD CONSTRAINT `produccion_materiales_ibfk_1` FOREIGN KEY (`produccion_id`) REFERENCES `produccion` (`id`),
  ADD CONSTRAINT `produccion_materiales_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_tipo_producto1` FOREIGN KEY (`tipo_producto_id`) REFERENCES `tipo_producto` (`id`);

--
-- Filtros para la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `fk_reserva_pedidos1` FOREIGN KEY (`pedidos_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `fk_reserva_reserva_estado1` FOREIGN KEY (`reserva_estado_id`) REFERENCES `reserva_estado` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`perfil_id`) REFERENCES `perfiles` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`produccion_id`) REFERENCES `produccion` (`id`);

--
-- Filtros para la tabla `venta_items`
--
ALTER TABLE `venta_items`
  ADD CONSTRAINT `venta_items_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `venta_items_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
