-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: database:3306
-- Tiempo de generación: 07-12-2024 a las 07:14:07
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `Granja`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`%` PROCEDURE `agregarDetalleDieta` (IN `p_id_fase` INT)   BEGIN
    DECLARE cantidad_a_restar DECIMAL(10, 2);
    DECLARE id_alimento_fase INT;

    -- Obtener el id_alimento asociado a la fase
    SELECT id_alimento INTO id_alimento_fase
    FROM Fases
    WHERE id_fase = p_id_fase;

    -- Validar que la fase exista
    IF id_alimento_fase IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La fase proporcionada no existe o no tiene un alimento asociado';
    END IF;

    -- Determinar la cantidad requerida según el id_alimento
    CASE id_alimento_fase
        WHEN 1 THEN SET cantidad_a_restar = 2.700;
        WHEN 2 THEN SET cantidad_a_restar = 1.300;
        WHEN 3 THEN SET cantidad_a_restar = 1.500;
        WHEN 4 THEN SET cantidad_a_restar = 2.100;
        WHEN 5 THEN SET cantidad_a_restar = 1.500;
        WHEN 6 THEN SET cantidad_a_restar = 1.800;
        ELSE
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ID de alimento no válido para la fase';
    END CASE;

    -- Verificar si hay suficiente cantidad en el total del alimento
    IF (SELECT total FROM Alimentos WHERE id_alimento = id_alimento_fase) < cantidad_a_restar THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No hay suficiente cantidad en el total de Alimentos para esta operación';
    END IF;

    -- Insertar el detalle de la dieta
    INSERT INTO DetalleDieta (id_fase, CantidadRequerida)
    VALUES (p_id_fase, cantidad_a_restar);

    -- Actualizar el total en la tabla Alimentos
    UPDATE Alimentos
    SET total = total - cantidad_a_restar
    WHERE id_alimento = id_alimento_fase;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Alimentos`
--

CREATE TABLE `Alimentos` (
  `id_alimento` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `cantidad` int NOT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Alimentos`
--

INSERT INTO `Alimentos` (`id_alimento`, `nombre`, `cantidad`, `total`) VALUES
(1, 'Gestación', 15, 285.60),
(2, 'Iniciación ', 21, 397.90),
(5, 'Premium 1', 1, 3.10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Animales`
--

CREATE TABLE `Animales` (
  `arete` int NOT NULL,
  `Id_corral` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Animales`
--

INSERT INTO `Animales` (`arete`, `Id_corral`) VALUES
(2321, 1),
(1231, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Camadas`
--

CREATE TABLE `Camadas` (
  `id_camada` int NOT NULL,
  `arete` int NOT NULL,
  `id_corral` int NOT NULL,
  `cantidad` int NOT NULL,
  `fechanaci` date NOT NULL,
  `id_fase` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Camadas`
--

INSERT INTO `Camadas` (`id_camada`, `arete`, `id_corral`, `cantidad`, `fechanaci`, `id_fase`) VALUES
(19, 2321, 1, 12, '2024-11-06', 1),
(20, 1231, 2, 12, '2024-11-10', 2),
(21, 2321, 1, 21, '2024-11-07', 5),
(22, 2321, 3, 12, '2024-11-16', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `CompraAlim`
--

CREATE TABLE `CompraAlim` (
  `id_compralim` int NOT NULL,
  `id_alimento` int NOT NULL,
  `cantidadcompra` int NOT NULL,
  `preciouni` decimal(10,2) NOT NULL,
  `preciototal` decimal(10,2) GENERATED ALWAYS AS ((`cantidadcompra` * `preciouni`)) STORED,
  `fecha_compra` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `CompraAlim`
--

INSERT INTO `CompraAlim` (`id_compralim`, `id_alimento`, `cantidadcompra`, `preciouni`, `fecha_compra`) VALUES
(1, 1, 10, 10.00, '2024-11-21'),
(2, 2, 2, 50.00, '2024-12-06'),
(3, 1, 2, 12.00, '2024-12-06'),
(4, 1, 1, 12.00, '2024-12-06'),
(5, 3, 1, 1.00, '2024-12-06'),
(6, 4, 1, 1.00, '2024-12-06'),
(7, 1, 1, 1.00, '2024-12-06'),
(8, 5, 1, 1.00, '2024-12-06'),
(9, 6, 1, 1.00, '2024-12-06'),
(10, 1, 1, 43.00, '2024-12-06'),
(11, 2, 20, 390.00, '2024-12-07');

--
-- Disparadores `CompraAlim`
--
DELIMITER $$
CREATE TRIGGER `actualizar_alimento` AFTER INSERT ON `CompraAlim` FOR EACH ROW BEGIN
    -- Actualizar la cantidad en la tabla Alimentos
    UPDATE Alimentos
    SET 
        cantidad = cantidad + NEW.cantidadcompra,  -- Suma la cantidad comprada
        total = total + (NEW.cantidadcompra * 20)  -- Agrega los kilos correspondientes
    WHERE id_alimento = NEW.id_alimento;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `CompraMedi`
--

CREATE TABLE `CompraMedi` (
  `id_compramedi` int NOT NULL,
  `id_medicamento` int NOT NULL,
  `cantidadcompra` int NOT NULL,
  `preciouni` decimal(10,2) NOT NULL,
  `preciototal` decimal(10,2) GENERATED ALWAYS AS ((`cantidadcompra` * `preciouni`)) STORED,
  `fecha_compra` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `CompraMedi`
--

INSERT INTO `CompraMedi` (`id_compramedi`, `id_medicamento`, `cantidadcompra`, `preciouni`, `fecha_compra`) VALUES
(1, 2, 12, 12.00, '2024-12-07'),
(2, 1, 43, 12.00, '2024-12-07'),
(3, 3, 33, 12.00, '2024-12-07'),
(4, 4, 43, 12.00, '2024-12-07'),
(5, 5, 23, 12.00, '2024-12-07');

--
-- Disparadores `CompraMedi`
--
DELIMITER $$
CREATE TRIGGER `actualizar_cantidad_medicamento` AFTER INSERT ON `CompraMedi` FOR EACH ROW BEGIN
    UPDATE Medicamentos
    SET cantidad = cantidad + NEW.cantidadcompra
    WHERE id_medicamento = NEW.id_medicamento;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Corrales`
--

CREATE TABLE `Corrales` (
  `Id_corral` int NOT NULL,
  `corral` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Corrales`
--

INSERT INTO `Corrales` (`Id_corral`, `corral`) VALUES
(1, '1'),
(2, '2'),
(3, '3'),
(4, '4'),
(5, '5');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `DetalleDieta`
--

CREATE TABLE `DetalleDieta` (
  `id_detalle` int NOT NULL,
  `id_fase` int NOT NULL,
  `CantidadRequerida` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `DetalleDieta`
--

INSERT INTO `DetalleDieta` (`id_detalle`, `id_fase`, `CantidadRequerida`) VALUES
(1, 3, 1.50),
(2, 1, 2.70),
(3, 1, 2.70),
(4, 1, 2.70),
(5, 2, 3.50),
(6, 1, 2.70),
(7, 1, 2.70),
(8, 2, 3.50),
(9, 3, 1.50),
(10, 3, 1.50),
(11, 5, 4.00),
(12, 3, 1.50),
(13, 2, 3.50),
(14, 5, 4.00),
(15, 1, 2.70),
(16, 1, 2.70),
(17, 5, 4.00),
(18, 2, 3.50),
(19, 2, 3.50),
(20, 5, 4.00),
(21, 3, 1.50),
(22, 3, 1.50),
(23, 3, 1.50),
(24, 5, 0.90),
(25, 2, 1.30),
(26, 2, 1.30),
(27, 1, 2.70),
(28, 2, 1.30),
(29, 2, 1.30),
(30, 2, 1.30),
(31, 2, 1.30),
(32, 2, 1.30);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Fases`
--

CREATE TABLE `Fases` (
  `id_fase` int NOT NULL,
  `fase` varchar(50) NOT NULL,
  `id_alimento` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Fases`
--

INSERT INTO `Fases` (`id_fase`, `fase`, `id_alimento`) VALUES
(1, 'Gestación', 1),
(2, 'Iniciación', 2),
(5, 'Premium 1', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Medicamentos`
--

CREATE TABLE `Medicamentos` (
  `id_medicamento` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `cantidad` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Medicamentos`
--

INSERT INTO `Medicamentos` (`id_medicamento`, `nombre`, `cantidad`) VALUES
(1, 'Enroxil', 43),
(2, 'Ivermectina', 5),
(3, 'Penicilina', 33),
(4, 'Hierro', 43),
(5, 'Baycox', 23);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `VentaCam`
--

CREATE TABLE `VentaCam` (
  `id_venta` int NOT NULL,
  `id_camada` int NOT NULL,
  `preciodes` decimal(10,2) NOT NULL,
  `cantidad` int DEFAULT NULL,
  `preciototal` decimal(10,2) GENERATED ALWAYS AS ((`preciodes` * `cantidad`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ventas`
--

CREATE TABLE `Ventas` (
  `id_venta` int NOT NULL,
  `id_camada` int NOT NULL,
  `fechaventa` date NOT NULL,
  `dias_totales` int NOT NULL,
  `costo_iniciacion` decimal(10,2) NOT NULL,
  `costo_premium` decimal(10,2) NOT NULL,
  `gastos_extras` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Ventas`
--

INSERT INTO `Ventas` (`id_venta`, `id_camada`, `fechaventa`, `dias_totales`, `costo_iniciacion`, `costo_premium`, `gastos_extras`, `total`) VALUES
(1, 2, '2024-12-06', 31, 495.00, 195.75, 150.00, 840.75),
(2, 6, '2024-12-06', 22, 495.00, 0.00, 150.00, 645.00),
(3, 1, '2024-12-06', 16, 495.00, 0.00, 150.00, 645.00),
(4, 12, '2024-12-06', 27, 495.00, 112.50, 120.00, 727.50),
(5, 3, '2025-01-04', 29, 330.00, 63.00, 150.00, 543.00),
(6, 7, '2025-01-04', 29, 330.00, 63.00, 150.00, 543.00),
(7, 8, '2025-01-05', 30, 4954.95, 739.20, 123.00, 5817.15),
(8, 9, '2024-12-14', 8, 400.95, 0.00, 232.00, 632.95),
(9, 10, '2025-01-05', 30, 382.80, 139.20, 232.00, 754.00),
(10, 11, '2025-01-05', 30, 3832.95, 139.20, 232.00, 4204.15),
(11, 13, '2024-12-06', 30, 199.65, 72.60, 121.00, 393.25),
(12, 14, '2024-12-06', 56, 199.65, 308.55, 121.00, 629.20),
(13, 15, '2024-12-06', 28, 199.65, 54.45, 121.00, 375.10),
(14, 16, '2024-12-06', 30, 199.65, 79.20, 0.00, 278.85),
(15, 17, '2024-12-06', 30, 199.65, 139.20, 34.00, 372.85),
(16, 18, '2024-12-06', 25, 349.80, 250.20, 232.00, 832.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Alimentos`
--
ALTER TABLE `Alimentos`
  ADD PRIMARY KEY (`id_alimento`);

--
-- Indices de la tabla `Animales`
--
ALTER TABLE `Animales`
  ADD PRIMARY KEY (`arete`),
  ADD KEY `Id_corral` (`Id_corral`);

--
-- Indices de la tabla `Camadas`
--
ALTER TABLE `Camadas`
  ADD PRIMARY KEY (`id_camada`),
  ADD KEY `id_corral` (`id_corral`),
  ADD KEY `arete` (`arete`),
  ADD KEY `fk_id_fase` (`id_fase`);

--
-- Indices de la tabla `CompraAlim`
--
ALTER TABLE `CompraAlim`
  ADD PRIMARY KEY (`id_compralim`),
  ADD KEY `id_alimento` (`id_alimento`);

--
-- Indices de la tabla `CompraMedi`
--
ALTER TABLE `CompraMedi`
  ADD PRIMARY KEY (`id_compramedi`),
  ADD KEY `id_medicamento` (`id_medicamento`);

--
-- Indices de la tabla `Corrales`
--
ALTER TABLE `Corrales`
  ADD PRIMARY KEY (`Id_corral`);

--
-- Indices de la tabla `DetalleDieta`
--
ALTER TABLE `DetalleDieta`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_fase` (`id_fase`);

--
-- Indices de la tabla `Fases`
--
ALTER TABLE `Fases`
  ADD PRIMARY KEY (`id_fase`),
  ADD KEY `id_alimento` (`id_alimento`);

--
-- Indices de la tabla `Medicamentos`
--
ALTER TABLE `Medicamentos`
  ADD PRIMARY KEY (`id_medicamento`);

--
-- Indices de la tabla `VentaCam`
--
ALTER TABLE `VentaCam`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_camada` (`id_camada`);

--
-- Indices de la tabla `Ventas`
--
ALTER TABLE `Ventas`
  ADD PRIMARY KEY (`id_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Alimentos`
--
ALTER TABLE `Alimentos`
  MODIFY `id_alimento` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `Animales`
--
ALTER TABLE `Animales`
  MODIFY `arete` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2322;

--
-- AUTO_INCREMENT de la tabla `Camadas`
--
ALTER TABLE `Camadas`
  MODIFY `id_camada` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `CompraAlim`
--
ALTER TABLE `CompraAlim`
  MODIFY `id_compralim` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `CompraMedi`
--
ALTER TABLE `CompraMedi`
  MODIFY `id_compramedi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `Corrales`
--
ALTER TABLE `Corrales`
  MODIFY `Id_corral` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `DetalleDieta`
--
ALTER TABLE `DetalleDieta`
  MODIFY `id_detalle` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `Fases`
--
ALTER TABLE `Fases`
  MODIFY `id_fase` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `Medicamentos`
--
ALTER TABLE `Medicamentos`
  MODIFY `id_medicamento` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `VentaCam`
--
ALTER TABLE `VentaCam`
  MODIFY `id_venta` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Ventas`
--
ALTER TABLE `Ventas`
  MODIFY `id_venta` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Animales`
--
ALTER TABLE `Animales`
  ADD CONSTRAINT `Animales_ibfk_1` FOREIGN KEY (`Id_corral`) REFERENCES `Corrales` (`Id_corral`);

--
-- Filtros para la tabla `Camadas`
--
ALTER TABLE `Camadas`
  ADD CONSTRAINT `Camadas_ibfk_1` FOREIGN KEY (`id_corral`) REFERENCES `Corrales` (`Id_corral`),
  ADD CONSTRAINT `Camadas_ibfk_2` FOREIGN KEY (`arete`) REFERENCES `Animales` (`arete`),
  ADD CONSTRAINT `fk_id_fase` FOREIGN KEY (`id_fase`) REFERENCES `Fases` (`id_fase`);

--
-- Filtros para la tabla `CompraAlim`
--
ALTER TABLE `CompraAlim`
  ADD CONSTRAINT `CompraAlim_ibfk_1` FOREIGN KEY (`id_alimento`) REFERENCES `Alimentos` (`id_alimento`);

--
-- Filtros para la tabla `CompraMedi`
--
ALTER TABLE `CompraMedi`
  ADD CONSTRAINT `CompraMedi_ibfk_1` FOREIGN KEY (`id_medicamento`) REFERENCES `Medicamentos` (`id_medicamento`);

--
-- Filtros para la tabla `DetalleDieta`
--
ALTER TABLE `DetalleDieta`
  ADD CONSTRAINT `DetalleDieta_ibfk_1` FOREIGN KEY (`id_fase`) REFERENCES `Fases` (`id_fase`);

--
-- Filtros para la tabla `Fases`
--
ALTER TABLE `Fases`
  ADD CONSTRAINT `Fases_ibfk_1` FOREIGN KEY (`id_alimento`) REFERENCES `Alimentos` (`id_alimento`);

--
-- Filtros para la tabla `VentaCam`
--
ALTER TABLE `VentaCam`
  ADD CONSTRAINT `VentaCam_ibfk_1` FOREIGN KEY (`id_camada`) REFERENCES `Camadas` (`id_camada`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
