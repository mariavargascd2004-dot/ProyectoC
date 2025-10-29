-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-10-2025 a las 02:18:35
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
-- Base de datos: `lojasolidariabd`
--

CREATE DATABASE lojasolidariabd;
USE lojasolidariabd;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adminassociado`
--

CREATE TABLE `adminassociado` (
  `apellido` varchar(45) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `fotoPerfil` varchar(255) DEFAULT NULL,
  `adminAssociado_idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admingeneral`
--

CREATE TABLE `admingeneral` (
  `idAdminGeneral` int(11) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `password` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admingeneral`
--

INSERT INTO `admingeneral` (`idAdminGeneral`, `usuario`, `nombre`, `password`) VALUES
(1, 'admin', 'Admin', '$2y$10$HupEfkeShCf.B/5qWCfr2eRRAr38s.WvBpQzUpHE6qZPVXCYsdQ4.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `idCategoria` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `emprendimento`
--

CREATE TABLE `emprendimento` (
  `idEmprendimento` int(11) NOT NULL,
  `adminAssociado_idUsuario` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `historia` text NOT NULL,
  `processoFabricacao` text NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `celular` varchar(20) NOT NULL,
  `ubicacao` varchar(500) NOT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `aprovado` tinyint(4) DEFAULT 0,
  `dataCriacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento`
--

CREATE TABLE `evento` (
  `idEvento` int(11) NOT NULL,
  `titulo` varchar(45) NOT NULL,
  `descripcion` varchar(1000) NOT NULL,
  `fechaInicio` datetime NOT NULL,
  `fechaFinal` datetime NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT NULL,
  `imagen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagem_fabricacao`
--

CREATE TABLE `imagem_fabricacao` (
  `idImagem` int(11) NOT NULL,
  `emprendimento_id` int(11) DEFAULT NULL,
  `caminho_imagem` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagem_galeria`
--

CREATE TABLE `imagem_galeria` (
  `idImagem` int(11) NOT NULL,
  `emprendimento_id` int(11) DEFAULT NULL,
  `caminho_imagem` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagina_principal`
--

CREATE TABLE `pagina_principal` (
  `id` int(11) NOT NULL,
  `portada` varchar(255) DEFAULT NULL,
  `historia` text DEFAULT NULL,
  `mision` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `primerafotogaleria` varchar(255) DEFAULT NULL,
  `segundafotogaleria` varchar(255) DEFAULT NULL,
  `tercerafotogaleria` varchar(255) DEFAULT NULL,
  `cuartafotogaleria` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `horarios` varchar(100) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagina_principal`
--
INSERT INTO `pagina_principal` (`id`, `portada`, `historia`, `mision`, `vision`, `primerafotogaleria`, `segundafotogaleria`, `tercerafotogaleria`, `cuartafotogaleria`, `telefono`, `direccion`, `horarios`, `celular`, `facebook`, `instagram`, `fecha_actualizacion`) VALUES (1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `idProducto` int(11) NOT NULL,
  `titulo` varchar(45) NOT NULL,
  `producto_idCategoria` int(11) NOT NULL,
  `producto_idSubcategoria` int(11) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `tamano` varchar(45) DEFAULT NULL,
  `color` varchar(45) DEFAULT NULL,
  `precio` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategoria`
--

CREATE TABLE `subcategoria` (
  `idSubcategoria` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(500) NOT NULL,
  `tipo` enum('cliente','associado') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adminassociado`
--
ALTER TABLE `adminassociado`
  ADD PRIMARY KEY (`adminAssociado_idUsuario`);

--
-- Indices de la tabla `admingeneral`
--
ALTER TABLE `admingeneral`
  ADD PRIMARY KEY (`idAdminGeneral`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idCategoria`);

--
-- Indices de la tabla `emprendimento`
--
ALTER TABLE `emprendimento`
  ADD PRIMARY KEY (`idEmprendimento`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD KEY `adminAssociado_idUsuario` (`adminAssociado_idUsuario`);

--
-- Indices de la tabla `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`idEvento`);

--
-- Indices de la tabla `imagem_fabricacao`
--
ALTER TABLE `imagem_fabricacao`
  ADD PRIMARY KEY (`idImagem`),
  ADD KEY `emprendimento_id` (`emprendimento_id`);

--
-- Indices de la tabla `imagem_galeria`
--
ALTER TABLE `imagem_galeria`
  ADD PRIMARY KEY (`idImagem`),
  ADD KEY `emprendimento_id` (`emprendimento_id`);

--
-- Indices de la tabla `pagina_principal`
--
ALTER TABLE `pagina_principal`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`idProducto`),
  ADD KEY `producto_idCategoria` (`producto_idCategoria`),
  ADD KEY `producto_idSubcategoria` (`producto_idSubcategoria`);

--
-- Indices de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  ADD PRIMARY KEY (`idSubcategoria`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admingeneral`
--
ALTER TABLE `admingeneral`
  MODIFY `idAdminGeneral` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `emprendimento`
--
ALTER TABLE `emprendimento`
  MODIFY `idEmprendimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `evento`
--
ALTER TABLE `evento`
  MODIFY `idEvento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `imagem_fabricacao`
--
ALTER TABLE `imagem_fabricacao`
  MODIFY `idImagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `imagem_galeria`
--
ALTER TABLE `imagem_galeria`
  MODIFY `idImagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `pagina_principal`
--
ALTER TABLE `pagina_principal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  MODIFY `idSubcategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adminassociado`
--
ALTER TABLE `adminassociado`
  ADD CONSTRAINT `adminassociado_ibfk_1` FOREIGN KEY (`adminAssociado_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `emprendimento`
--
ALTER TABLE `emprendimento`
  ADD CONSTRAINT `emprendimento_ibfk_1` FOREIGN KEY (`adminAssociado_idUsuario`) REFERENCES `adminassociado` (`adminAssociado_idUsuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `imagem_fabricacao`
--
ALTER TABLE `imagem_fabricacao`
  ADD CONSTRAINT `imagem_fabricacao_ibfk_1` FOREIGN KEY (`emprendimento_id`) REFERENCES `emprendimento` (`idEmprendimento`) ON DELETE CASCADE;

--
-- Filtros para la tabla `imagem_galeria`
--
ALTER TABLE `imagem_galeria`
  ADD CONSTRAINT `imagem_galeria_ibfk_1` FOREIGN KEY (`emprendimento_id`) REFERENCES `emprendimento` (`idEmprendimento`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`producto_idCategoria`) REFERENCES `categoria` (`idCategoria`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`producto_idSubcategoria`) REFERENCES `subcategoria` (`idSubcategoria`);

--
-- Filtros para la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  ADD CONSTRAINT `subcategoria_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`idCategoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
