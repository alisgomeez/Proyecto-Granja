<?php
include("includes/header.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Caja</title>
</head>
<body>

<?php
$servername = "database";
$username = "root";
$password = "root";
$dbname = "Granja"; // Ajusta el nombre de la base de datos según sea necesario

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener las ventas
$sql_ventas = "SELECT SUM(total) AS total_ventas FROM Ventas"; // Obtiene la suma de la columna 'total' de la tabla ventas
$ventas_result = $conn->query($sql_ventas);
$total_ventas = 0;

if ($ventas_result->num_rows > 0) {
    $row = $ventas_result->fetch_assoc();
    $total_ventas = $row['total_ventas'];
}

// Consulta para obtener las compras
$sql_compras = "SELECT SUM(preciototal) AS total_compras FROM CompraAlim"; // Obtiene la suma de la columna 'preciototal' de la tabla Compras
$compras_result = $conn->query($sql_compras);
$total_compras = 0;

if ($compras_result->num_rows > 0) {
    $row = $compras_result->fetch_assoc();
    $total_compras = $row['total_compras'];
}

// Calcular el total en caja
$total_en_caja = $total_ventas - $total_compras;
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Caja</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h4>Total de Ventas: <span class="text-success"><?php echo number_format($total_ventas, 2); ?> </span></h4>
                </div>
                <div class="col-md-4">
                    <a href="ventas.php" class="btn btn-info mt-3">Ver Detalles</a>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <h4>Total de Compras: <span class="text-danger"><?php echo number_format($total_compras, 2); ?> </span></h4>
                </div>
                <div class="col-md-4">
                    <a href="compras.php" class="btn btn-info mt-3">Ver Detalles</a>
                </div>
            </div>

            <h4 class="mt-4">Total en Caja: <span class="text-info"><?php echo number_format($total_en_caja, 2); ?> </span></h4>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>
</html>

<?php
$conn->close();
?>
