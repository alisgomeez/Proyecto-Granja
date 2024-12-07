<?php
include("includes/header.php"); // Asegúrate de que este archivo contiene la cabecera y la conexión a la base de datos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Compras</title>
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

// Consulta para obtener las compras de CompraAlim
$sql_compras_alim = "SELECT id_compralim, fecha_compra, preciototal FROM CompraAlim"; // Cambia el nombre de la tabla y campos si es necesario
$compras_alim_result = $conn->query($sql_compras_alim);

// Consulta para obtener las compras de CompraMedi
$sql_compras_medi = "SELECT id_compramedi, fecha_compra, preciototal FROM CompraMedi"; // Cambia el nombre de la tabla y campos si es necesario
$compras_medi_result = $conn->query($sql_compras_medi);

// Consulta para obtener la suma total de la columna 'preciototal' de CompraAlim
$sql_suma_total_compras_alim = "SELECT SUM(preciototal) AS suma_total_compras_alim FROM CompraAlim";
$suma_result_compras_alim = $conn->query($sql_suma_total_compras_alim);
$total_suma_compras_alim = 0;

if ($suma_result_compras_alim->num_rows > 0) {
    $row = $suma_result_compras_alim->fetch_assoc();
    $total_suma_compras_alim = $row['suma_total_compras_alim'];
}

// Consulta para obtener la suma total de la columna 'preciototal' de CompraMedi
$sql_suma_total_compras_medi = "SELECT SUM(preciototal) AS suma_total_compras_medi FROM CompraMedi";
$suma_result_compras_medi = $conn->query($sql_suma_total_compras_medi);
$total_suma_compras_medi = 0;

if ($suma_result_compras_medi->num_rows > 0) {
    $row = $suma_result_compras_medi->fetch_assoc();
    $total_suma_compras_medi = $row['suma_total_compras_medi'];
}

// Sumar ambas compras
$total_suma_compras = $total_suma_compras_alim + $total_suma_compras_medi;
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Compras</h2>
        </div>
        <div class="card-body">
            <!-- Mostrar el total combinado de compras antes de las tablas -->
            <div class="mt-4 text-center">
                <h3>Total de Compras: <span class="text-success"><?php echo number_format($total_suma_compras, 2); ?> </span></h4>
            </div>


            <div class="row">
                <!-- Tabla de Compras de Alimentos (CompraAlim) -->
                <div class="col-md-6 text-center">
                    <h4>Compras de Alimentos</h4>
                    <?php
                    if ($compras_alim_result->num_rows > 0) {
                        echo "<table class='table table-bordered'>";
                        echo "<thead class='thead-light'>
                                <tr>
                                    <th>Fecha de Compra</th>
                                    <th>Precio Total</th>
                                </tr>
                              </thead>
                              <tbody>";

                        while ($row = $compras_alim_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['fecha_compra']}</td>
                                    <td>{$row['preciototal']}</td>
                                  </tr>";
                        }

                        echo "</tbody></table>";
                    } else {
                        echo "<p class='alert alert-warning'>No se han encontrado compras en Alimentos.</p>";
                    }
                    ?>

                    <!-- Mostrar el total de compras de alimentos -->
                    <div class="mt-4 text-center">
                        <h4>Total de Compras de Alimentos: <span class="text-success"><?php echo number_format($total_suma_compras_alim, 2); ?> </span></h4>
                    </div>
                </div>

                <!-- Tabla de Compras de Medicamentos (CompraMedi) -->
                <div class="col-md-6 text-center">
                    <h4>Compras de Medicamentos</h4>
                    <?php
                    if ($compras_medi_result->num_rows > 0) {
                        echo "<table class='table table-bordered'>";
                        echo "<thead class='thead-light'>
                                <tr>
                                    <th>Fecha de Compra</th>
                                    <th>Precio Total</th>
                                </tr>
                              </thead>
                              <tbody>";

                        while ($row = $compras_medi_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['fecha_compra']}</td>
                                    <td>{$row['preciototal']}</td>
                                  </tr>";
                        }

                        echo "</tbody></table>";
                    } else {
                        echo "<p class='alert alert-warning'>No se han encontrado compras en Medicamentos.</p>";
                    }
                    ?>

                    <!-- Mostrar el total de compras de medicamentos -->
                    <div class="mt-4">
                        <h4>Total de Compras de Medicamentos: <span class="text-success"><?php echo number_format($total_suma_compras_medi, 2); ?> </span></h4>
                    </div>
                </div>
            </div>
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
