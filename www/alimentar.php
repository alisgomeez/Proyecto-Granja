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
include("includes/header.php");

$servername = "database";
$username = "root";
$password = "root";
$dbname = "Granja";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id_fase = "";
$id_vacuna = "";
$cantidad_vacuna = 0;
$mensaje_exito = ""; 

// Consultas iniciales
$sql_camadas = "SELECT id_camada, id_corral, id_fase FROM Camadas";
$camadas_result = $conn->query($sql_camadas);

$sql_camadas_alimentar = "SELECT id_camada, id_corral, id_fase FROM Camadas";
$camadas_result_alimentar = $conn->query($sql_camadas_alimentar);

$sql_camadas_vacunar = "SELECT id_camada, id_corral, id_fase FROM Camadas";
$camadas_result_vacunar = $conn->query($sql_camadas_vacunar);

// Consulta de vacunas
$sql_vacunas = "SELECT id_medicamento, nombre, cantidad FROM Medicamentos";
$vacunas_result = $conn->query($sql_vacunas);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Procesamiento de la selección de camada y fase
    if (isset($_POST["camada"])) {
        $camadaId = $_POST["camada"];

        // Obtener la fase de la camada seleccionada
        $sql = "SELECT id_fase FROM Camadas WHERE id_camada = $camadaId";
        $result = $conn->query($sql);
        $id_fase = ($result->num_rows > 0) ? $result->fetch_assoc()['id_fase'] : "No se encontró la fase.";

        // Obtener los datos de la camada seleccionada
        $sql_camada_data = "SELECT c.id_camada, c.id_corral, c.id_fase, c.arete, f.fase 
                            FROM Camadas c 
                            JOIN Fases f ON c.id_fase = f.id_fase 
                            WHERE c.id_camada = $camadaId";
        $camada_data_result = $conn->query($sql_camada_data);
    }

    // Procesamiento del botón "Alimentar"
    if (isset($_POST["alimentar"])) {
        $fase = $_POST["id_fase"];
        
        // Llamar al procedimiento almacenado
        $sql_call = "CALL agregarDetalleDieta($fase)";
        if ($conn->query($sql_call) === TRUE) {
            echo "<div class='alert alert-success' role='alert'>Alimentación procesada exitosamente.</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error al procesar la alimentación: " . $conn->error . "</div>";
        }
    }

    // Procesamiento de la vacunación
    if (isset($_POST["vacunar"])) {
        $vacunaId = $_POST["vacuna"];
        $camadaId = $_POST["camada_vacuna"];

        // Obtener la cantidad actual de la vacuna
        $sql_vacuna = "SELECT cantidad FROM Medicamentos WHERE id_medicamento = $vacunaId";
        $vacuna_result = $conn->query($sql_vacuna);
        if ($vacuna_result->num_rows > 0) {
            $vacuna_row = $vacuna_result->fetch_assoc();
            $cantidad_vacuna = $vacuna_row['cantidad'];

            // Verificar si hay suficiente vacuna
            if ($cantidad_vacuna > 0) {
                // Restar 1 de la cantidad de vacunas
                $nueva_cantidad = $cantidad_vacuna - 1;
                $sql_update_vacuna = "UPDATE Medicamentos SET cantidad = $nueva_cantidad WHERE id_medicamento = $vacunaId";
                
                if ($conn->query($sql_update_vacuna) === TRUE) {
                    $mensaje_exito = "<div class='alert alert-success' role='alert'>Operación procesada exitosamente.</div>";
                } else {
                    $mensaje_exito = "<div class='alert alert-danger' role='alert'>Error al procesar la vacunación: " . $conn->error . "</div>";
                }
            } else {
                $mensaje_exito = "<div class='alert alert-warning' role='alert'>No hay suficiente vacuna disponible.</div>";
            }
        }
    }
}

$conn->close();
?>

<div class="container mt-5">
    <!-- Sección de Alimentar -->
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
                                echo "<option value='{$row['id_camada']}'>Corral {$row['id_corral']}</option>";
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

    <!-- Sección de Vacunar -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Vacunar</h2>
        </div>
        <div class="card-body">
            <form action="alimentar.php" method="post">
                <div class="form-group">
                    <label for="camada_vacuna">Seleccione una Camada:</label>
                    <select class="form-control" id="camada_vacuna" name="camada_vacuna" required>
                        <option value="" disabled selected>Selecciona la camada</option>
                        <?php
                        if ($camadas_result_vacunar->num_rows > 0) {
                            while ($row = $camadas_result_vacunar->fetch_assoc()) {
                                echo "<option value='{$row['id_camada']}'>Corral {$row['id_corral']}</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No hay camadas disponibles</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vacuna">Seleccione una Vacuna:</label>
                    <select class="form-control" id="vacuna" name="vacuna" required>
                        <option value="" disabled selected>Selecciona la vacuna</option>
                        <?php
                        if ($vacunas_result->num_rows > 0) {
                            while ($row = $vacunas_result->fetch_assoc()) {
                                echo "<option value='{$row['id_medicamento']}'>
                                        {$row['nombre']} (Cantidad disponible: {$row['cantidad']})
                                      </option>";
                            }
                        } else {
                            echo "<option value='' disabled>No hay vacunas disponibles</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" name="vacunar" class="btn btn-success">Vacunar</button>
                    <a href="alimentos.php" class="btn btn-warning">Volver</a>
                </div>
            </form>
            <?php if ($mensaje_exito) echo $mensaje_exito; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>
</html>
