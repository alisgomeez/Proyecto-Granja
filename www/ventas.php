<?php
include("includes/header.php"); // Asegúrate de que este archivo contiene la cabecera y la conexión a la base de datos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Ventas - Caja</title>
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

$sql_ventas = "SELECT fechaventa, total FROM Ventas"; // Consulta para obtener las ventas
$ventas_result = $conn->query($sql_ventas);

// Consulta para obtener la suma total de la columna 'total'
$sql_suma_total = "SELECT SUM(total) AS suma_total FROM Ventas";
$suma_result = $conn->query($sql_suma_total);
$total_suma = 0;

if ($suma_result->num_rows > 0) {
    $row = $suma_result->fetch_assoc();
    $total_suma = $row['suma_total'];
}

?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Ventas - Caja</h2>
        </div>
        <div class="mt-4 text-center">
                <h4>Total de Ventas: <span class="text-success"><?php echo number_format($total_suma, 2); ?> </span></h4>
            </div>
        <div class="card-body">
            <?php
            if ($ventas_result->num_rows > 0) {
                echo "<table class='table table-bordered'>";
                echo "<thead class='thead-light'>
                        <tr>
                            <th>Fecha de Venta</th>
                            <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>";

                while ($row = $ventas_result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['fechaventa']}</td>
                            <td>{$row['total']}</td>
                          </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p class='alert alert-warning'>No se han encontrado ventas.</p>";
            }
            ?>           

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
