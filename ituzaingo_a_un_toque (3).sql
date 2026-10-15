-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-10-2026 a las 21:15:13
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

--
-- Volcado de datos para la tabla `favoritos_productos`
--

INSERT INTO `favoritos_productos` (`usuario_id`, `producto_id`, `creado_en`) VALUES
(45, 3, '2026-09-14 22:36:58'),
(45, 5, '2026-09-14 22:37:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_local`
--

CREATE TABLE `imagenes_local` (
  `id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `imagenes_local`
--

INSERT INTO `imagenes_local` (`id`, `local_id`, `ruta`, `orden`, `creado_en`) VALUES
(1, 6, 'uploads/1789891225_voz_chaco.750.jpg', 0, '2026-09-20 08:00:25'),
(2, 6, 'uploads/1789891225_1_e854d1b28d4966e74b1bdff3aed6c5bf.jpg', 1, '2026-09-20 08:00:25'),
(3, 6, 'uploads/1789891225_2_Imagen de WhatsApp 2024-03-23 a las 16.34.45_99f91f29.jpg', 2, '2026-09-20 08:00:25'),
(4, 6, 'uploads/1789891225_3_Imagen de WhatsApp 2024-03-23 a las 16.34.45_1339d102.jpg', 3, '2026-09-20 08:00:25'),
(5, 6, 'uploads/1789891225_4_Imagen de WhatsApp 2023-11-30 a las 19.31.41_57919e94.jpg', 4, '2026-09-20 08:00:25'),
(6, 9, 'uploads/1790377192_LogoPP2.jpeg', 0, '2026-09-23 17:44:09'),
(7, 9, 'uploads/1790185449_1_WIN_20251130_12_26_07_Pro.jpg', 1, '2026-09-23 17:44:09'),
(9, 24, 'uploads/1791308359_solo_deportes_logo.jfif', 0, '2026-10-06 17:39:19'),
(10, 19, 'uploads/1791999326_somos-todo-pasa-local.jfif', 0, '2026-10-14 17:35:26'),
(11, 19, 'uploads/1791999326_1_todo-pasa-local.jfif', 1, '2026-10-14 17:35:26'),
(12, 20, 'uploads/1792000127_maria-eva-local.webp', 0, '2026-10-14 17:48:47'),
(13, 21, 'uploads/1792000369_lola-informal-local.jfif', 0, '2026-10-14 17:52:49'),
(14, 22, 'uploads/1792000531_aniss-local.jfif', 0, '2026-10-14 17:55:31'),
(15, 23, 'uploads/1792000749_ocn-ituzaingo.jfif', 0, '2026-10-14 17:59:09'),
(16, 23, 'uploads/1792000749_1_ocn-local.jfif', 1, '2026-10-14 17:59:09'),
(17, 25, 'uploads/1792001387_todo-tipo-local.jfif', 0, '2026-10-14 18:09:47'),
(18, 26, 'uploads/1792001682_anna-clothing.jfif', 0, '2026-10-14 18:14:43'),
(19, 27, 'uploads/1792001828_las-julietas.jfif', 0, '2026-10-14 18:17:08'),
(20, 28, 'uploads/1792001977_tcg-ituzaingo.jfif', 0, '2026-10-14 18:19:37'),
(21, 28, 'uploads/1792001977_1_tcg-ituzaingo-localjfif.jfif', 1, '2026-10-14 18:19:37'),
(22, 29, 'uploads/1792002251_Aniss-Dynamic.jfif', 0, '2026-10-14 18:24:11'),
(23, 29, 'uploads/1792002251_1_Aniss-Dynamic-local.jfif', 1, '2026-10-14 18:24:11'),
(24, 30, 'uploads/1792002484_Bullfit-Deportes.jfif', 0, '2026-10-14 18:28:04'),
(25, 30, 'uploads/1792002484_1_Bullfit-Deportes-local.jfif', 1, '2026-10-14 18:28:04');

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
(12, 12, 'https://picsum.photos/seed/prod12/400/400', 0, '2026-09-08 20:41:47'),
(13, 13, 'uploads/1789966077_HNXhcvLXUAAgNAp.jpg', 0, '2026-09-21 04:47:57'),
(14, 14, 'uploads/1789966724_HLxS0tlXEAA952E.jpg', 0, '2026-09-21 04:58:44'),
(15, 15, 'uploads/1789967143_HLxS0tlXEAA952E.jpg', 0, '2026-09-21 05:05:43'),
(16, 16, 'uploads/1789969178_HLxS0tlXEAA952E.jpg', 0, '2026-09-21 05:39:38'),
(17, 17, 'uploads/1790185638_auto.jpeg', 0, '2026-09-23 17:47:18'),
(18, 17, 'uploads/1790185638_1_Screenshot_1.png', 1, '2026-09-23 17:47:18'),
(19, 17, 'uploads/1790377110_2_portada-windows-11.png', 2, '2026-09-25 22:58:30'),
(20, 18, 'uploads/1791999612_todo-pasa-prendajfif.jfif', 0, '2026-10-14 17:40:12'),
(21, 19, 'uploads/1792000285_maria-eva-prenda.jfif', 0, '2026-10-14 17:51:25'),
(22, 20, 'uploads/1792000452_lola-informal-prenda.jpg', 0, '2026-10-14 17:54:12'),
(23, 21, 'uploads/1792000649_aniss-prenda.jfif', 0, '2026-10-14 17:57:29'),
(24, 22, 'uploads/1792000970_ocn-prenda.jfif', 0, '2026-10-14 18:02:50'),
(25, 22, 'uploads/1792000970_1_NICT8529-001-6.jfif', 1, '2026-10-14 18:02:50'),
(26, 23, 'uploads/1792001271_solo-deportes-prendajfif.jfif', 0, '2026-10-14 18:07:51'),
(27, 24, 'uploads/1792001502_todo-tipo-prenda.webp', 0, '2026-10-14 18:11:42'),
(28, 25, 'uploads/1792001776_anna-prenda.webp', 0, '2026-10-14 18:16:16'),
(29, 26, 'uploads/1792001917_las-julietas-prenda.webp', 0, '2026-10-14 18:18:37'),
(30, 27, 'uploads/1792002148_tcg-prenda.webp', 0, '2026-10-14 18:22:28'),
(31, 28, 'uploads/1792002414_anyss-prenda.jfif', 0, '2026-10-14 18:26:54'),
(32, 29, 'uploads/1792002644_bullfit-prendawebp.webp', 0, '2026-10-14 18:30:44');

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
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `instagram` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `locales`
--

INSERT INTO `locales` (`id`, `usuario_id`, `nombre_local`, `direccion`, `entre_calles`, `descripcion`, `imagen_portada`, `latitud`, `longitud`, `horario_texto`, `categoria_id`, `creado_en`, `instagram`, `whatsapp`, `facebook`, `tiktok`) VALUES
(1, 41, 'Urban Style Ituzaingó', 'Av. Rivadavia 1200, Ituzaingó', NULL, 'Ropa urbana y streetwear', NULL, -34.6659000, -58.6702000, 'Lun a Sáb 10 a 19hs', 1, '2026-09-08 20:41:47', NULL, NULL, NULL, NULL),
(2, 45, 'DeporteYa', 'Belgrano 450, Ituzaingó', NULL, 'Indumentaria deportiva y calzado', NULL, -34.6612000, -58.6745000, 'Lun a Sáb 9 a 20hs', 2, '2026-09-08 20:41:47', NULL, NULL, NULL, NULL),
(3, 46, 'Boutique Elegance', 'San Martín 780, Ituzaingó', NULL, 'Ropa formal y de fiesta', NULL, -34.6690000, -58.6680000, 'Mar a Sáb 11 a 19hs', 4, '2026-09-08 20:41:47', NULL, NULL, NULL, NULL),
(5, 52, 'probando2022', '', '', 'probando 222222222222222', 'uploads/1789882656_messifondo.jpg', NULL, NULL, NULL, NULL, '2026-09-20 05:37:36', 'probando2022', NULL, NULL, NULL),
(6, 53, 'Probando local ananashe', 'Justo ahi xd 6769', 'esta y la otra', 'Café El Faro es un rincón acogedor en el corazón de la ciudad, ideal para quienes buscan escapar del ruido diario. Este establecimiento combina una decoración rústica con un ambiente cálido, musicalizado por suaves acordes de jazz de fondo. Es el espacio perfecto para trabajar, leer un buen libro o disfrutar de una charla tranquila.El local se destaca principalmente por:Café de especialidad: Granos seleccionados de origen orgánico.Pastelería artesanal: Horneada cada mañana en el lugar.Espacio cultural: Una pequeña biblioteca libre para los clientes.Terraza verde: Un sector al aire libre lleno de plantas.', 'uploads/1789885428_e854d1b28d4966e74b1bdff3aed6c5bf.jpg', NULL, NULL, NULL, NULL, '2026-09-20 06:23:48', 'amamamamamamaaaaa', '6969696996969+54xd', 'zukember', 'adorni troll'),
(9, 55, 'Los proyectadores', 'Blas Parera', 'miro y 14 de julio', 'adadasdsadsadasda adadasdsadsadasda adadasdsadsadasda adadasdsadsadasda adadasdsadsadasda adadasdsadsadasda adadasdsadsadasda v adadasdsadsadasda adadasdsadsadasda', 'uploads/1790185449_WIN_20260914_22_08_37_Pro.jpg', NULL, NULL, NULL, NULL, '2026-09-23 17:44:09', 'massalautaroo7', '1126969101', NULL, NULL),
(19, 56, 'Somos todo pasa', 'Gral. Miguel Soler 391, Local 2, Ituzaingó', '', 'Ropa de tendencia a precios accesibles, muy elegida por el público joven.', 'uploads/1791999326_somos-todo-pasa-local.jfif', -34.6564486, -58.6687316, 'Lun a Sáb 10 a 20hs', 1, '2026-10-06 17:31:12', 'somos.todopasa', NULL, NULL, NULL),
(20, 57, 'María Eva Talles Reales', 'Gral. Las Heras 93, Ituzaingó', '', 'Ropa femenina moderna con talles reales y envíos a todo el país.', 'uploads/1792000127_maria-eva-local.webp', -34.6585775, -58.6660803, 'Lun y Mié a Sáb 9 a 20hs; Mar 9 a 20:30hs', 3, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL),
(21, 58, 'Lola estilo informal', 'José María Paz 484, Ituzaingó', '', 'Ropa informal de producción propia para mujer, comercio familiar con mucha trayectoria en el barrio.', 'uploads/1792000369_lola-informal-local.jfif', -34.6474675, -58.6656703, 'Lun a Vie 10 a 13hs y 17 a 20hs; Sáb 10 a 20hs', 3, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL),
(22, 59, 'ANISS', 'Gral. Miguel Soler 99, Ituzaingó', '', 'Local multimarca de ropa femenina, de los más tradicionales del centro.', 'uploads/1792000531_aniss-local.jfif', -34.6589908, -58.6675630, 'Lun a Vie 9 a 14hs y 15 a 20hs; Sáb 9 a 20hs', 3, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL),
(23, 60, 'OCN Ituzaingó', 'Cnel. Pablo Zufriategui 753, Ituzaingó', '', 'Marca de estilo urbano y deportivo, conocida por la durabilidad de sus prendas.', 'uploads/1792000749_ocn-ituzaingo.jfif', -34.6581781, -58.6656044, 'Lun a Sáb 9:30 a 20hs', 1, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL),
(24, 61, 'Solo Deportes', 'Cnel. Pablo Zufriategui 930, Ituzaingó', '', 'Casa de deportes histórica del centro, con indumentaria y gran variedad de zapatillas.', 'uploads/1791308359_solo_deportes_logo.jfif', -34.6592131, -58.6681397, 'Lun a Sáb 8:30 a 20:30hs', 2, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL),
(25, 62, 'TODO TIPO', 'Peatonal Eva Perón 839, Ituzaingó', '', 'Indumentaria masculina sobre la peatonal.', 'uploads/1792001387_todo-tipo-local.jfif', -34.6590090, -58.6666761, 'Lun a Sáb 9 a 13:30hs y 16 a 20hs', 3, '2026-10-06 17:31:12', 'todotipoindumentaria', NULL, NULL, NULL),
(26, 63, 'Anna Clothing 🖤', 'Gral. Miguel Soler 54, Ituzaingó', '', 'Boutique de ropa y accesorios para mujer.', 'uploads/1792001682_anna-clothing.jfif', -34.6593876, -58.6676517, 'Lun a Sáb 9:30 a 20hs', 3, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL),
(27, 64, 'Las Julietas', 'Cnel. Pablo Zufriategui 780, Ituzaingó', '', 'Boutique de moda femenina de línea elegante.', 'uploads/1792001828_las-julietas.jfif', -34.6585089, -58.6658369, 'Mar y Jue a Vie 15 a 19hs; Mié 10:30 a 13hs; Sáb 10:30 a 13hs y 16 a 19hs', 4, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL),
(28, 65, 'TCG Ituzaingó', 'Gral. Lavalle 1048, Ituzaingó', '', 'Boutique de moda femenina con prendas actuales y clásicas.', 'uploads/1792001977_tcg-ituzaingo.jfif', -34.6570920, -58.6709457, 'Lun a Sáb 10 a 13hs y 16 a 20hs', 3, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL),
(29, 66, 'Aniss Dynamic', 'Cnel. Pablo Zufriategui 912, Ituzaingó', '', 'Indumentaria deportiva femenina: trajes de baño, ropa de danza y fitness.', 'uploads/1792002251_Aniss-Dynamic.jfif', -34.6591287, -58.6678758, 'Lun a Sáb 9 a 13:30hs y 15:30 a 20hs', 2, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL),
(30, 67, 'Bullfit Deportes', 'Gral. José Rondeau 12, Ituzaingó', '', 'Ropa y artículos deportivos a pocos metros de la estación.', 'uploads/1792002484_Bullfit-Deportes.jfif', -34.6598000, -58.6654971, 'Lun a Vie 9 a 13:30hs y 16 a 20hs; Sáb 9:30 a 13:30hs y 15 a 20hs', 2, '2026-10-06 17:31:12', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `local_publico`
--

CREATE TABLE `local_publico` (
  `local_id` int(11) NOT NULL,
  `publico_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `local_publico`
--

INSERT INTO `local_publico` (`local_id`, `publico_id`) VALUES
(19, 3),
(20, 1),
(21, 1),
(22, 1),
(23, 3),
(24, 3),
(25, 2),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 3);

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
  `talle` varchar(30) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `local_id`, `nombre_producto`, `descripcion`, `talle`, `precio`, `categoria_id`, `creado_en`) VALUES
(1, 1, 'Remera oversize básica', NULL, NULL, 8500.00, 1, '2026-09-08 20:41:47'),
(2, 2, 'Remera estampada', NULL, NULL, 7200.00, 1, '2026-09-08 20:41:47'),
(3, 1, 'Pantalón cargo urbano', NULL, NULL, 15000.00, 2, '2026-09-08 20:41:47'),
(4, 3, 'Jean recto', NULL, NULL, 18500.00, 2, '2026-09-08 20:41:47'),
(5, 1, 'Campera bomber', NULL, NULL, 32000.00, 3, '2026-09-08 20:41:47'),
(6, 2, 'Campera de jean', NULL, NULL, 27500.00, 3, '2026-09-08 20:41:47'),
(7, 3, 'Vestido floreado', NULL, NULL, 21000.00, 4, '2026-09-08 20:41:47'),
(8, 3, 'Vestido de fiesta', NULL, NULL, 45000.00, 4, '2026-09-08 20:41:47'),
(9, 2, 'Zapatillas urbanas', NULL, NULL, 38000.00, 5, '2026-09-08 20:41:47'),
(10, 3, 'Botas de cuero', NULL, NULL, 52000.00, 5, '2026-09-08 20:41:47'),
(11, 1, 'Cinturón de cuero', NULL, NULL, 9500.00, 6, '2026-09-08 20:41:47'),
(12, 2, 'Gorra snapback', NULL, NULL, 6000.00, 6, '2026-09-08 20:41:47'),
(13, 6, 'messi pa', 'epicarda', 'talle 9/12', 1000.00, 1, '2026-09-21 04:47:57'),
(14, 6, 'daasd', 'ada', NULL, 6969.00, 5, '2026-09-21 04:58:44'),
(15, 6, 'daasd', 'ada', NULL, 6969.00, 5, '2026-09-21 05:05:43'),
(16, 6, 'ASD', 'ASD', 'DA', 33312221.00, 6, '2026-09-21 05:39:38'),
(17, 9, 'Fiat palio', 'adadasdsad adadasdsad adadasdsad adadasdsad adadasdsadadadasdsad adadasdsad adadasdsad adadasdsad adadasdsad', '1999', 6000000.00, 6, '2026-09-23 17:47:18'),
(18, 19, 'Conjunto Boxy-Fit', '🔥 Dale un upgrade a tu estilo.\r\n\r\nPrendas que combinan comodidad, personalidad y ese toque urbano que nunca falla. 👕💯\r\n\r\nEncontrá tu próximo outfit y marcá la diferencia.', 'Opcional', 80000.00, 4, '2026-10-14 17:40:12'),
(19, 20, 'Saco Mujer Elegante', '👕 Un básico que nunca falla.\r\n\r\nUna buena remera puede cambiar todo tu outfit. Combiná tus colores favoritos y creá tu propio estilo. 🔥', 'Opcional', 24999.00, 3, '2026-10-14 17:51:25'),
(20, 21, 'Jean Mariposas', '👖 El fit perfecto para completar tu look.\r\n\r\nVersátiles, cómodos y fáciles de combinar. Encontrá ese pantalón que va con todo y llevá tu estilo al siguiente nivel. 💯', 'Opcional', 27999.00, 2, '2026-10-14 17:54:12'),
(21, 22, 'Remera Negra Split', 'Cómodo, versátil y listo para cualquier ocasión. 👌🔥', 'Opcional', 21999.00, 1, '2026-10-14 17:57:29'),
(22, 23, 'Zapatillas Jordan Air 6 Retro', '🔥 Tu estilo empieza desde abajo.\r\n\r\nZapatillas para completar tu outfit y marcar la diferencia en cada paso. 👟💯\r\n\r\n📩 Escribinos para consultar disponibilidad.', '43', 170000.00, 5, '2026-10-14 18:02:50'),
(23, 24, 'Zapatillas Urbanas', '🔥 50% DESCUENTO APROVECHA AHORA!!🔥\r\n\r\nZapatillas para completar tu outfit y marcar la diferencia en cada paso. 👟💯\r\n\r\n📩 Escribinos para consultar disponibilidad.', 'Opcional', 99999.00, 5, '2026-10-14 18:07:51'),
(24, 25, 'Casco Cross Mac Virtus Solid Matt Black Mr Motos', '🎒 Todo lo que necesitás para acompañarte.', NULL, 225000.00, 6, '2026-10-14 18:11:42'),
(25, 26, 'Sweater tejido manga corta', 'Esa prenda que estabas buscando está acá. 👕', NULL, 37999.00, 1, '2026-10-14 18:16:16'),
(26, 27, 'Pantalón Palazzo Jogging 3 Rayas', 'Una combinación, mil posibilidades. ✨', NULL, 31578.00, 2, '2026-10-14 18:18:37'),
(27, 28, 'Flavia Cartera Chica', 'Prendas que van con vos, en cualquier momento. 🧥', NULL, 89000.00, 6, '2026-10-14 18:22:28'),
(28, 29, 'Malla de Natacion Classic', '📩 Consultanos por precios y disponibilidad.', NULL, 29000.00, 6, '2026-10-14 18:26:54'),
(29, 30, 'Conjunto Casual Ropa Deportiva', '💥 Cada entrenamiento cuenta.\r\n\r\nEquipate con los accesorios que necesitás para acompañar tus objetivos y mantenerte en movimiento. 💪🔥\r\n\r\n📩 Consultá por los productos disponibles.', NULL, 55000.00, 6, '2026-10-14 18:30:44');

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
(50, 'profe@gmail.com', '$2y$10$.DVde0ixW.4bgTsG0QacuuGRSb.c8Vf.ru08xK1Tv6/KECDOiOLZu', 'vendedor', '2026-09-14 21:56:49'),
(51, 'correo@gmail.com', '$2y$10$0QCmD.WIGJ3nu5nIQ90u.OhG1MllTN1QXc4lZnSYRiU7KCS/ElAWK', 'comprador', '2026-09-14 22:29:42'),
(52, 'vendedor@gmail.com', '$2y$10$8m/J4DO6XVGV8.Hb.ltwVuHVH2dCCXk/tYTo1bwn5A.JfCvF.lY22', 'vendedor', '2026-09-17 22:13:32'),
(53, 'vendedor1@gmail.com', '$2y$10$UZOcMT3sFOajvTuPybZHv.0KzYp.K4yThif/0u2CXAu7cVH/9PS62', 'vendedor', '2026-09-20 06:20:17'),
(54, 'vendedor2@gmail.com', '$2y$10$D6XCw6IGmQny7lhtj2eVH.A9HYNnrLYU4Msfc3nEnqXtQ3FHhacD.', 'vendedor', '2026-09-21 03:18:11'),
(55, 'admin@gmail.com', '$2y$10$EYUj.w9YM1THsYiLWk5iX.iTM23X6YtmZeFDZAt6T4KdFFNM0fGsa', 'vendedor', '2026-09-23 17:40:41'),
(56, 'somostodopasa@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(57, 'mariaeva@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(58, 'lolainformal@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(59, 'aniss@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(60, 'ocn@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(61, 'solodeportes@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(62, 'todotipo@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(63, 'annaclothing@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(64, 'lasjulietas@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(65, 'tcg@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(66, 'anissdynamic@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12'),
(67, 'bullfit@seed.ituzaingo.test', '$2y$10$NaAkuY/I4I3/y1vQwTBebuJp.A/FkGoeMStZj3dA4cHD/isrL9Juu', 'vendedor', '2026-10-06 17:31:12');

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
-- Indices de la tabla `imagenes_local`
--
ALTER TABLE `imagenes_local`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `local_orden` (`local_id`,`orden`),
  ADD KEY `local_id` (`local_id`);

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
  ADD UNIQUE KEY `uq_locales_usuario` (`usuario_id`),
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
-- AUTO_INCREMENT de la tabla `imagenes_local`
--
ALTER TABLE `imagenes_local`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `imagenes_producto`
--
ALTER TABLE `imagenes_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `locales`
--
ALTER TABLE `locales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

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
-- Filtros para la tabla `imagenes_local`
--
ALTER TABLE `imagenes_local`
  ADD CONSTRAINT `imagenes_local_ibfk_1` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE CASCADE;

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
