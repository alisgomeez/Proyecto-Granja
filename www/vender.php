<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Vender Camada</title>
</head>
<body>

<?php
include("includes/header.php");
?>

function obtenerOpcionesLotes() {
    $conexion = new Conexion();
    $conn = $conexion->conectar();

    if (!$conn) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    $sqlLotes = "SELECT Id_lote, CantidadAnimales FROM Lotes";
    $resultLotes = $conn->query($sqlLotes);

    $opciones = [];

    while ($rowLote = $resultLotes->fetch_assoc()) {
        $idLote = $rowLote['Id_lote'];
        $cantidadAnimales = $rowLote['CantidadAnimales'];

        $opciones[] = [
            'id' => $idLote,
            'label' => "(Animales: $cantidadAnimales)"
        ];
    }

    $conn->close();

    return $opciones;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idLote = $_POST["idLote"];
    $precioKilo = $_POST["precioKilo"];
    $animalesVenta = $_POST["animalesVenta"];

    // procedure VenderLote
    $conexion = new Conexion();
    $conn = $conexion->conectar();

    if (!$conn) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    // procedure almacenado
    $sqlCallProcedure = "CALL VenderLote(?, ?, ?)";
    $stmtCallProcedure = $conn->prepare($sqlCallProcedure);

    if ($stmtCallProcedure) {
        $stmtCallProcedure->bind_param("idi", $idLote, $precioKilo, $animalesVenta);

        if ($stmtCallProcedure->execute()) {
            echo "<div class='container mt-5'><div class='alert alert-success' role='alert'>Venta registrada con éxito.</div></div>";
        } else {
            echo "<div class='container mt-5'><div class='alert alert-danger' role='alert'>Error al registrar la venta.</div></div>";
        }

        $stmtCallProcedure->close();
    } else {
        echo "<div class='container mt-5'><div class='alert alert-danger' role='alert'>Error en la preparación del procedimiento almacenado.</div></div>";
    }

    $conn->close();
}
?>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>