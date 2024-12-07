<?php
include("includes/header.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Alimentar</title>
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

$id_fase = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["camada"])) {
        $camadaId = $_POST["camada"];

        // Obtener la fase de la camada 
        $sql = "SELECT id_fase FROM Camadas WHERE id_camada = $camadaId";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_fase = $row['id_fase'];
        } else {
            $id_fase = "No se encontró la fase.";
        }

        // Obtener los datos para la tabla de la camada 
        $sql_camada_data = "SELECT c.id_camada, c.id_corral, c.id_fase, c.arete, f.fase 
                            FROM Camadas c 
                            JOIN Fases f ON c.id_fase = f.id_fase 
                            WHERE c.id_camada = $camadaId";
        $camada_data_result = $conn->query($sql_camada_data);
    }

    // Si se presiona el botón "Alimentar"
    if (isset($_POST["alimentar"])) {
        $fase = $_POST["id_fase"];
        
        // Llamar al procedure
        $sql_call = "CALL agregarDetalleDieta($fase)";
        
        if ($conn->query($sql_call) === TRUE) {
            echo "<div class='alert alert-success' role='alert'>Alimentación procesada exitosamente.</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error al procesar la alimentación: " . $conn->error . "</div>";
        }
    }
}

$sql_camadas = "SELECT id_camada, id_corral, id_fase FROM Camadas";
$camadas_result = $conn->query($sql_camadas);

$conn->close();
?>


<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Alimentar</h2>
        </div>
        <div class="card-body">
            <form action="alimentar.php" method="post">
                <div class="form-group">
                    <label for="camada">Seleccione una Camada:</label>
                    <select class="form-control" id="id_camada" name="camada" required>
                        <option value="" disabled selected>Selecciona la camada</option>
                        <?php
                        if ($camadas_result->num_rows > 0) {
                            while ($row = $camadas_result->fetch_assoc()) {
                                echo "<option value='{$row['id_camada']}'>
                                        Corral {$row['id_corral']}
                                      </option>";
                            }
                        } else {
                            echo "<option value='' disabled>No hay camadas disponibles</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary">Ver Detalles</button>
                    <a href="alimentos.php" class="btn btn-warning">Volver</a>
                </div>
            </form>

            <?php
            if (isset($camada_data_result) && $camada_data_result->num_rows > 0) {
                echo "<table class='table table-bordered mt-4'>
                        <thead>
                            <tr>
                                <th>Corral</th>
                                <th>Arete de la madre</th>
                                <th>Fase</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = $camada_data_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id_corral"] . "</td>";
                    echo "<td>" . $row["arete"] . "</td>";
                    echo "<td>" . $row["fase"] . "</td>";
                    echo "<td>
                            <form action='alimentar.php' method='post'>
                                <input type='hidden' name='id_fase' value='" . $row["id_fase"] . "'>
                                <button type='submit' name='alimentar' class='btn btn-success'>Alimentar</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                echo "</tbody>
                      </table>";
            } elseif (isset($camada_data_result)) {
                echo "<div class='mt-3 alert alert-warning' role='alert'>
                        No se encontraron datos para la camada seleccionada.
                      </div>";
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
