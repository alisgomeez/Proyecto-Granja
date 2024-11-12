<?php
include("includes/header.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Compra de Medicamentos</title>

</head>
<body>

<?php
    $servername = "database";  
    $username = "root";        
    $password = "root";        
    $dbname = "Granja";        

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Compra de Medicamentos</h2>
        </div>
        <div class="card-body">

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comprarMedicamentos"])) {
                
                $idMedicamento = $_POST["idMedicamento"];
                $cantidadCompra = $_POST["cantidadCompra"];
                $precio = $_POST["precio"];
                $fecha = date('Y-m-d');

                $sql = "INSERT INTO CompraMedi (id_medicamento, cantidadcompra, preciouni, fecha_compra) 
                        VALUES ($idMedicamento, $cantidadCompra, $precio, '$fecha')";

                if ($conn->query($sql) === TRUE) {
                    echo "<div class='alert alert-success' role='alert'>Compra de Medicamentos realizada con éxito.</div>";
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Error al realizar la compra de Medicamentos: " . $conn->error . "</div>";
                }
            }
            ?>

            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="mt-3">
                <div class="form-group">
                    <label for="idMedicamento">Selecciona Medicamento:</label>
                    <select class="form-control" id="idMedicamento" name="idMedicamento" required>
                        <?php
                        $sql = "SELECT id_medicamento, nombre FROM Medicamentos";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row["id_medicamento"] . "'>" . $row["nombre"] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No hay Medicamentos disponibles</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cantidadCompra">Cantidad a Comprar:</label>
                    <input type="number" class="form-control" id="cantidadCompra" name="cantidadCompra" required>
                </div>

                <div class="form-group">
                    <label for="precio">Precio por unidad:</label>
                    <input type="number" class="form-control" id="precio" name="precio" required>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary" name="comprarMedicamentos">Comprar</button>
                    <a href="medicamentos.php" class="btn btn-warning">Volver</a>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Cerrar la conexión después de la operación
$conn->close();
?>
