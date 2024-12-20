<?php
include("includes/header.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Compra de Alimentos</title>

</head>
<body>

<?php
include("includes/conexion.php");
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Compra de Alimentos</h2>
        </div>
        <div class="card-body">

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comprarAlimentos"])) {
                
                $idAlimento = $_POST["idAlimento"];
                $cantidadCompra = $_POST["cantidadCompra"];
                $precio = $_POST["precio"];
                $fecha = date('Y-m-d');

                $sql = "INSERT INTO CompraAlim (id_alimento, cantidadcompra, preciouni, fecha_compra) 
                        VALUES ($idAlimento, $cantidadCompra, $precio, '$fecha')";

                if ($conn->query($sql) === TRUE) {
                    echo "<div class='alert alert-success' role='alert'>Compra de alimentos realizada con éxito.</div>";
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Error al realizar la compra de alimentos: " . $conn->error . "</div>";
                }
            }
            ?>

            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="mt-3">
                <div class="form-group">
                    <label for="idAlimento">Selecciona Alimento:</label>
                    <select class="form-control" id="idAlimento" name="idAlimento" required>
                        <?php
                        $sql = "SELECT id_alimento, nombre FROM Alimentos";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row["id_alimento"] . "'>" . $row["nombre"] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No hay alimentos disponibles</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cantidadCompra">Cantidad de bultos:</label>
                    <input type="number" class="form-control" id="cantidadCompra" name="cantidadCompra" required>
                </div>

                <div class="form-group">
                    <label for="precio">Precio por Bulto:</label>
                    <input type="number" class="form-control" id="precio" name="precio" required>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary" name="comprarAlimentos">Comprar</button>
                    <a href="alimentos.php" class="btn btn-warning">Volver</a>
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
$conn->close();
?>
