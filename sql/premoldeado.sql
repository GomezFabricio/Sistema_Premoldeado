-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-09-2025 a las 04:41:44
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
  `fecha_alta` date NOT NULL,
  `observaciones` varchar(45) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `fecha_alta`, `observaciones`, `activo`) VALUES
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
(8, 'pendiente de revision'),
(10, 'Aprobada');

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
(1, 'Pendiente ');

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
(3, 'Retiro en fabrica'),
(5, 'A domicilio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `unidad_medida` varchar(45) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `stock_minimo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `nombre`, `unidad_medida`, `cantidad`, `costo_unitario`, `stock_minimo`) VALUES
(1, 'Piedra', 'metros', 10.00, 30000.00, 10),
(3, 'Cemento', 'bolsa', 10.00, 10000.00, 15),
(4, 'arena fina', 'm³', 25.00, 100000.00, 15),
(6, 'Hierro', 'metro', 5.00, 10000.00, 10),
(7, 'piedra', 'bmetro', 56.00, 2000.00, 5),
(8, 'arena', 'metros', 10.00, 200000.00, 25);

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
(5, 'tarjeta de debito'),
(6, 'cheque'),
(7, 'efectivo');

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
(11, '60x1,25', '1,25', 5, 2, 70.00, '1', 2),
(12, '60x1,25', '34', 5, 2, 21.00, '0', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
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
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_usuarios` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `domicilio` varchar(45) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `perfiles_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuarios`, `email`, `password`, `domicilio`, `activo`, `perfiles_id`) VALUES
(1, 'admin', 'admin@sistema.com', '$2a$12$BjHvL.SKljBTCpzxuGskreX6VwK7RaResvGruGC94yY3NCdluRfla', NULL, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD PRIMARY KEY (`id`,`usuarios_id`),
  ADD UNIQUE KEY `idauditorias_UNIQUE` (`id`),
  ADD KEY `fk_auditorias_usuarios1_idx` (`usuarios_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`,`proveedor_id`),
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
  ADD PRIMARY KEY (`clientes_id`,`productos_id`,`estado_devolucion_id`),
  ADD UNIQUE KEY `iddevolucion_UNIQUE` (`id`),
  ADD KEY `fk_devolucion_productos1_idx` (`productos_id`),
  ADD KEY `fk_devolucion_estado_devolucion1_idx` (`estado_devolucion_id`);

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
  ADD PRIMARY KEY (`id`,`clientes_id`,`metodo_pago_id`,`pedidos_id`),
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
  ADD UNIQUE KEY `idmateriales_UNIQUE` (`id`);

--
-- Indices de la tabla `materiales_compras`
--
ALTER TABLE `materiales_compras`
  ADD PRIMARY KEY (`materiales_id`,`compras_id`),
  ADD KEY `fk_materiales_has_compras_compras1_idx` (`compras_id`),
  ADD KEY `fk_materiales_has_compras_materiales1_idx` (`materiales_id`);

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
  ADD PRIMARY KEY (`id`,`forma_entrega_id`,`estado_pedido_id`,`clientes_id`),
  ADD UNIQUE KEY `idpedidos_UNIQUE` (`id`),
  ADD KEY `fk_pedidos_forma_entrega1_idx` (`forma_entrega_id`),
  ADD KEY `fk_pedidos_estado_pedido1_idx` (`estado_pedido_id`),
  ADD KEY `fk_pedidos_clientes1_idx` (`clientes_id`);

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
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`,`tipo_producto_id`) USING BTREE,
  ADD UNIQUE KEY `idproductos_UNIQUE` (`id`),
  ADD KEY `fk_productos_tipo_producto1_idx` (`tipo_producto_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
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
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `produccion`
--
ALTER TABLE `produccion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
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
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD CONSTRAINT `fk_auditorias_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_compras_proveedor1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
  ADD CONSTRAINT `fk_devolucion_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_devolucion_estado_devolucion1` FOREIGN KEY (`estado_devolucion_id`) REFERENCES `estado_devolucion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_devolucion_productos1` FOREIGN KEY (`productos_id`) REFERENCES `productos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `fk_factura_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_factura_metodo_pago1` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodo_pago` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_factura_pedidos1` FOREIGN KEY (`pedidos_id`) REFERENCES `pedidos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `materiales_compras`
--
ALTER TABLE `materiales_compras`
  ADD CONSTRAINT `fk_materiales_has_compras_compras1` FOREIGN KEY (`compras_id`) REFERENCES `compras` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_materiales_has_compras_materiales1` FOREIGN KEY (`materiales_id`) REFERENCES `materiales` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `materiales_produccion`
--
ALTER TABLE `materiales_produccion`
  ADD CONSTRAINT `fk_materiales_has_produccion_materiales1` FOREIGN KEY (`materiales_id`) REFERENCES `materiales` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_materiales_has_produccion_produccion1` FOREIGN KEY (`produccion_id`) REFERENCES `produccion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pedidos_estado_pedido1` FOREIGN KEY (`estado_pedido_id`) REFERENCES `estado_pedido` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pedidos_forma_entrega1` FOREIGN KEY (`forma_entrega_id`) REFERENCES `forma_entrega` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `perfiles_modulos`
--
ALTER TABLE `perfiles_modulos`
  ADD CONSTRAINT `fk_perfiles_has_modulos_modulos1` FOREIGN KEY (`modulos_id`) REFERENCES `modulos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_perfiles_has_modulos_perfiles1` FOREIGN KEY (`perfiles_id`) REFERENCES `perfiles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `personas`
--
ALTER TABLE `personas`
  ADD CONSTRAINT `fk_personas_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_personas_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `produccion`
--
ALTER TABLE `produccion`
  ADD CONSTRAINT `fk_produccion_estado_produccion1` FOREIGN KEY (`estado_produccion_id`) REFERENCES `estado_produccion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_produccion_reserva1` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_tipo_producto1` FOREIGN KEY (`tipo_producto_id`) REFERENCES `tipo_producto` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `fk_proveedor_personas1` FOREIGN KEY (`personas_id`) REFERENCES `personas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
  ADD CONSTRAINT `fk_usuarios_perfiles1` FOREIGN KEY (`perfiles_id`) REFERENCES `perfiles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE OR REPLACE VIEW vista_produccion_completa AS
SELECT
  p.id,
  p.codigo_produccion,
  p.producto_id,
  prod.nombre AS producto_nombre,
  p.cantidad_planificada,
  p.fecha_inicio_planificada,
  p.fecha_fin_planificada,
  p.observaciones,
  p.estado_produccion_id,
  ep.nombre AS estado_nombre
FROM
  produccion p
  INNER JOIN producto prod ON p.producto_id = prod.id
  INNER JOIN estado_produccion ep ON p.estado_produccion_id = ep.id;
  prod.activo
