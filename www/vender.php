<?php
ob_start(); // Inicia el buffering de salida

include("includes/header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Venta de Camadas</title>
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="text-center mb-0">Vender Camada</h2>
            </div>
            <div class="card-body">
    <form action="" method="post" class="mt-4">
        <div class="mb-3">
            <label for="id_camada" class="form-label">Camada:</label>
            <select id="id_camada" name="id_camada" class="form-control" required>
                <option value="">Seleccione una camada</option>
                <?php
                $mensaje = "";
                // Conexión a la base de datos
                $conn = new mysqli("database", "root", "root", "Granja");
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Obtener las camadas disponibles
                $sql = "SELECT id_camada, arete, id_corral, fechanaci FROM Camadas";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Mantener la opción seleccionada
                        $selected = (isset($_POST['id_camada']) && $_POST['id_camada'] == $row['id_camada']) ? 'selected' : '';
                        echo "<option value='" . $row['id_camada'] . "' $selected>Corral: " . $row['id_corral'] . " - Arete: " . $row['arete'] . " - Fecha Nacimiento: " . $row['fechanaci'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No hay camadas disponibles</option>";
                }

                $conn->close();
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="fechaventa" class="form-label">Fecha de Venta:</label>
            <input type="date" id="fechaventa" name="fechaventa" class="form-control" value="<?php echo isset($_POST['fechaventa']) ? $_POST['fechaventa'] : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="valor_iniciacion" class="form-label">Valor actual del alimento Iniciación (por bulto):</label>
            <input type="number" id="valor_iniciacion" name="valor_iniciacion" class="form-control" step="0.01" value="<?php echo isset($_POST['valor_iniciacion']) ? $_POST['valor_iniciacion'] : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="valor_premium" class="form-label">Valor actual del alimento Premium 1 (por bulto):</label>
            <input type="number" id="valor_premium" name="valor_premium" class="form-control" step="0.01" value="<?php echo isset($_POST['valor_premium']) ? $_POST['valor_premium'] : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="gastos_extras" class="form-label">Gastos Extras:</label>
            <input type="number" id="gastos_extras" name="gastos_extras" class="form-control" step="0.01" value="<?php echo isset($_POST['gastos_extras']) ? $_POST['gastos_extras'] : ''; ?>">
        </div>
        <button type="submit" name="calcular" class="btn btn-primary">Calcular Total</button>
        <button type="submit" name="vender" class="btn btn-success">Realizar Venta</button>
    </form>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_camada = $_POST["id_camada"];
    $fechaventa = $_POST["fechaventa"];
    $valor_iniciacion = isset($_POST["valor_iniciacion"]) ? floatval($_POST["valor_iniciacion"]) : 0;
    $valor_premium = isset($_POST["valor_premium"]) ? floatval($_POST["valor_premium"]) : 0;
    $gastos_extras = isset($_POST["gastos_extras"]) ? floatval($_POST["gastos_extras"]) : 0;

    // Conexión a la base de datos
    $conn = new mysqli("database", "root", "root", "Granja");
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener la fecha de nacimiento de la camada
    $sql = "SELECT fechanaci FROM Camadas WHERE id_camada = $id_camada";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fechanaci = $row["fechanaci"];

        // Calcular días entre nacimiento y venta
        $dias_totales = (strtotime($fechaventa) - strtotime($fechanaci)) / (60 * 60 * 24);

        if ($dias_totales <= 0) {
            echo "<div class='alert alert-danger text-center'>La fecha de venta debe ser posterior a la fecha de nacimiento.</div>";
            exit;
        }

        // primers 22 dias
        $costo_iniciacion = ($valor_iniciacion / 20) * 33;

        // días después de los primeros 22 
        $dias_adicionales = $dias_totales > 22 ? $dias_totales - 22 : 0;
        $costo_premium = ($valor_premium / 20) * ($dias_adicionales * 1.5);

        // total
        $total = $costo_iniciacion + $costo_premium + $gastos_extras;

        // si se oprime calcular (el botón) se muestran los datos
        if (isset($_POST["calcular"])) {
            echo "<div class='container mt-4'>";
            echo "<h3>Total Calculado</h3>";
            echo "<ul class='list-group'>";
            echo "<li class='list-group-item'>Días Totales: $dias_totales días</li>";
            echo "<li class='list-group-item'>Costo Alimento Iniciación (22 días): $" . number_format($costo_iniciacion, 2) . "</li>";
            echo "<li class='list-group-item'>Días adicionales: $dias_adicionales días</li>";
            echo "<li class='list-group-item'>Costo Alimento Premium: $" . number_format($costo_premium, 2) . "</li>";
            echo "<li class='list-group-item'>Gastos Extras: $" . number_format($gastos_extras, 2) . "</li>";
            echo "<li class='list-group-item fw-bold'>Total: $" . number_format($total, 2) . "</li>";
            echo "</ul>";
            echo "</div>";
        }

        // Si se presiona btón de realizar venta, se guarda la cventa y elimina la camada
        if (isset($_POST["vender"])) {
            // Insertar venta en la tabla
            $sql_insert = "INSERT INTO Ventas (id_camada, fechaventa, dias_totales, costo_iniciacion, costo_premium, gastos_extras, total) 
                            VALUES ($id_camada, '$fechaventa', $dias_totales, $costo_iniciacion, $costo_premium, $gastos_extras, $total)";

            if ($conn->query($sql_insert) === TRUE) {
                echo "<div class='alert alert-success text-center'>Venta realizada y camada eliminada exitosamente.</div>";

                
                // Eliminar camada
                $sql_delete = "DELETE FROM Camadas WHERE id_camada = $id_camada";
                $conn->query($sql_delete);


                header("Location: " . $_SERVER['PHP_SELF']);
                exit;  // Detener la ejecución del script después de la redirección
            } else {
                echo "<div class='alert alert-danger text-center'>Error al registrar la venta: " . $conn->error . "</div>";
            }
        }
    }

    $conn->close();
}
?>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>
</html>

<?php
ob_end_flush(); // Finaliza el buffering y envía la salida al navegador
?>
