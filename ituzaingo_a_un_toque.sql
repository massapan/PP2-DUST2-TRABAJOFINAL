-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-09-2026 a las 00:00:05
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
-- Base de datos: `ituzaingo_a_un_toque`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_local`
--

CREATE TABLE `categorias_local` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_local`
--

INSERT INTO `categorias_local` (`id`, `nombre`) VALUES
(5, 'Accesorios'),
(3, 'Casual'),
(2, 'Deportiva'),
(4, 'Formal'),
(1, 'Urbana');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_producto`
--

CREATE TABLE `categorias_producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_producto`
--

INSERT INTO `categorias_producto` (`id`, `nombre`) VALUES
(6, 'Accesorios'),
(5, 'Calzado'),
(3, 'Camperas'),
(2, 'Pantalones'),
(1, 'Remeras'),
(4, 'Vestidos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos_locales`
--

CREATE TABLE `favoritos_locales` (
  `usuario_id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos_productos`
--

CREATE TABLE `favoritos_productos` (
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_producto`
--

CREATE TABLE `imagenes_producto` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `imagenes_producto`
--

INSERT INTO `imagenes_producto` (`id`, `producto_id`, `ruta`, `orden`, `creado_en`) VALUES
(1, 1, 'https://picsum.photos/seed/prod1/400/400', 0, '2026-09-08 20:41:47'),
(2, 2, 'https://picsum.photos/seed/prod2/400/400', 0, '2026-09-08 20:41:47'),
(3, 3, 'https://picsum.photos/seed/prod3/400/400', 0, '2026-09-08 20:41:47'),
(4, 4, 'https://picsum.photos/seed/prod4/400/400', 0, '2026-09-08 20:41:47'),
(5, 5, 'https://picsum.photos/seed/prod5/400/400', 0, '2026-09-08 20:41:47'),
(6, 6, 'https://picsum.photos/seed/prod6/400/400', 0, '2026-09-08 20:41:47'),
(7, 7, 'https://picsum.photos/seed/prod7/400/400', 0, '2026-09-08 20:41:47'),
(8, 8, 'https://picsum.photos/seed/prod8/400/400', 0, '2026-09-08 20:41:47'),
(9, 9, 'https://picsum.photos/seed/prod9/400/400', 0, '2026-09-08 20:41:47'),
(10, 10, 'https://picsum.photos/seed/prod10/400/400', 0, '2026-09-08 20:41:47'),
(11, 11, 'https://picsum.photos/seed/prod11/400/400', 0, '2026-09-08 20:41:47'),
(12, 12, 'https://picsum.photos/seed/prod12/400/400', 0, '2026-09-08 20:41:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locales`
--

CREATE TABLE `locales` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre_local` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `entre_calles` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `horario_texto` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `locales`
--

INSERT INTO `locales` (`id`, `usuario_id`, `nombre_local`, `direccion`, `entre_calles`, `descripcion`, `imagen_portada`, `latitud`, `longitud`, `horario_texto`, `categoria_id`, `creado_en`) VALUES
(1, 41, 'Urban Style Ituzaingó', 'Av. Rivadavia 1200, Ituzaingó', NULL, 'Ropa urbana y streetwear', NULL, -34.6659000, -58.6702000, 'Lun a Sáb 10 a 19hs', 1, '2026-09-08 20:41:47'),
(2, 45, 'DeporteYa', 'Belgrano 450, Ituzaingó', NULL, 'Indumentaria deportiva y calzado', NULL, -34.6612000, -58.6745000, 'Lun a Sáb 9 a 20hs', 2, '2026-09-08 20:41:47'),
(3, 46, 'Boutique Elegance', 'San Martín 780, Ituzaingó', NULL, 'Ropa formal y de fiesta', NULL, -34.6690000, -58.6680000, 'Mar a Sáb 11 a 19hs', 4, '2026-09-08 20:41:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `local_publico`
--

CREATE TABLE `local_publico` (
  `local_id` int(11) NOT NULL,
  `publico_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expira` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `local_id`, `nombre_producto`, `descripcion`, `precio`, `categoria_id`, `creado_en`) VALUES
(1, 1, 'Remera oversize básica', NULL, 8500.00, 1, '2026-09-08 20:41:47'),
(2, 2, 'Remera estampada', NULL, 7200.00, 1, '2026-09-08 20:41:47'),
(3, 1, 'Pantalón cargo urbano', NULL, 15000.00, 2, '2026-09-08 20:41:47'),
(4, 3, 'Jean recto', NULL, 18500.00, 2, '2026-09-08 20:41:47'),
(5, 1, 'Campera bomber', NULL, 32000.00, 3, '2026-09-08 20:41:47'),
(6, 2, 'Campera de jean', NULL, 27500.00, 3, '2026-09-08 20:41:47'),
(7, 3, 'Vestido floreado', NULL, 21000.00, 4, '2026-09-08 20:41:47'),
(8, 3, 'Vestido de fiesta', NULL, 45000.00, 4, '2026-09-08 20:41:47'),
(9, 2, 'Zapatillas urbanas', NULL, 38000.00, 5, '2026-09-08 20:41:47'),
(10, 3, 'Botas de cuero', NULL, 52000.00, 5, '2026-09-08 20:41:47'),
(11, 1, 'Cinturón de cuero', NULL, 9500.00, 6, '2026-09-08 20:41:47'),
(12, 2, 'Gorra snapback', NULL, 6000.00, 6, '2026-09-08 20:41:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicos`
--

CREATE TABLE `publicos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicos`
--

INSERT INTO `publicos` (`id`, `nombre`) VALUES
(2, 'Hombre'),
(1, 'Mujer'),
(4, 'Niños'),
(3, 'Unisex');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordarme_tokens`
--

CREATE TABLE `recordarme_tokens` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `validador_hash` varchar(255) NOT NULL,
  `expira` datetime NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('comprador','vendedor') NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `email`, `password`, `rol`, `creado_en`) VALUES
(38, 'steiningergerman@gmail.com', '$2y$10$T5.xmjbcBXgaszgL9wYiZ.mCz2nkYHUKJpAzNBnCXwgdV2qJrV0jC', 'comprador', '2026-08-24 00:35:15'),
(41, 'gerluchy@gmail.com', '$2y$10$2M./qLKidqOqP5U4zzw3oumKKzqPmhVacZaIQeDDOZTARcCqZcUgu', 'vendedor', '2026-08-24 00:36:12'),
(43, 'massarigaudi21@gmail.com', '$2y$10$87ZcqtQ2wmCHzc/FBqlcr.L/ce5xFAEc591owSRKM0UImmzjI84mG', 'comprador', '2026-08-24 22:13:30'),
(44, 'messi@gmail.com', '$2y$10$jYsUro8WV/H5mqpO1nGjDexbncPVOccTYVVSH/V8Nohm9WgcXeE8u', 'comprador', '2026-08-25 17:48:29'),
(45, 'ger@gmail.com', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-08-26 22:20:51'),
(46, 'massa@gmail.com', '$2y$10$P3v4onCJmn2Z6e42p5Gs8eDpM/TPK5xFb0eYSEHlbo3jRzQfcsxEG', 'vendedor', '2026-08-29 01:11:06'),
(47, 'man@gmail.com', '$2y$10$oYj0KlSM8WEWhYboNyAcIO9hA7pFsrdcE7DkvER5Q3USYeO5o/K1e', 'comprador', '2026-09-10 23:06:57'),
(48, '1@gmail.com', '$2y$10$IiW90BAi5rKfIgAWKrgXluLZBJuJoaCIt6nZ83QdcRGNmLosHrZym', 'comprador', '2026-09-10 23:19:20'),
(49, 'rossi@gmail.com', '$2y$10$ThtV.os7EfYc4g4I1U3CfOoPr7IYHmAK8SQ5iYVtyurkOimbUBxxm', 'comprador', '2026-09-10 23:34:51'),
(50, 'profe@gmail.com', '$2y$10$.DVde0ixW.4bgTsG0QacuuGRSb.c8Vf.ru08xK1Tv6/KECDOiOLZu', 'vendedor', '2026-09-14 21:56:49');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias_local`
--
ALTER TABLE `categorias_local`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `categorias_producto`
--
ALTER TABLE `categorias_producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `favoritos_locales`
--
ALTER TABLE `favoritos_locales`
  ADD PRIMARY KEY (`usuario_id`,`local_id`),
  ADD KEY `local_id` (`local_id`);

--
-- Indices de la tabla `favoritos_productos`
--
ALTER TABLE `favoritos_productos`
  ADD PRIMARY KEY (`usuario_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `imagenes_producto`
--
ALTER TABLE `imagenes_producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `categoria_id` (`categoria_id`);
ALTER TABLE `locales` ADD FULLTEXT KEY `ft_local_busqueda` (`nombre_local`,`descripcion`);

--
-- Indices de la tabla `local_publico`
--
ALTER TABLE `local_publico`
  ADD PRIMARY KEY (`local_id`,`publico_id`),
  ADD KEY `publico_id` (`publico_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `local_id` (`local_id`),
  ADD KEY `categoria_id` (`categoria_id`);
ALTER TABLE `productos` ADD FULLTEXT KEY `ft_producto_busqueda` (`nombre_producto`);

--
-- Indices de la tabla `publicos`
--
ALTER TABLE `publicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `recordarme_tokens`
--
ALTER TABLE `recordarme_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias_local`
--
ALTER TABLE `categorias_local`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categorias_producto`
--
ALTER TABLE `categorias_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `imagenes_producto`
--
ALTER TABLE `imagenes_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `locales`
--
ALTER TABLE `locales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `publicos`
--
ALTER TABLE `publicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `recordarme_tokens`
--
ALTER TABLE `recordarme_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `favoritos_locales`
--
ALTER TABLE `favoritos_locales`
  ADD CONSTRAINT `favoritos_locales_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoritos_locales_ibfk_2` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `favoritos_productos`
--
ALTER TABLE `favoritos_productos`
  ADD CONSTRAINT `favoritos_productos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoritos_productos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `imagenes_producto`
--
ALTER TABLE `imagenes_producto`
  ADD CONSTRAINT `imagenes_producto_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `locales`
--
ALTER TABLE `locales`
  ADD CONSTRAINT `locales_categoria_fk` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_local` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locales_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `local_publico`
--
ALTER TABLE `local_publico`
  ADD CONSTRAINT `local_publico_ibfk_1` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `local_publico_ibfk_2` FOREIGN KEY (`publico_id`) REFERENCES `publicos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_categoria_fk` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_producto` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recordarme_tokens`
--
ALTER TABLE `recordarme_tokens`
  ADD CONSTRAINT `recordarme_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
