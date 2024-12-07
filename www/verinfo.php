<?php
include("includes/header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Detalles de la Camada</title>
</head>
<body>

<?php
$id_camada = isset($_GET['id']) ? $_GET['id'] : 0;

if ($id_camada == 0) {
    echo "<div class='container mt-5'><p>No se encontró el ID de la camada.</p></div>";
    exit;
}

$servername = "database";
$username = "root";
$password = "root";
$dbname = "Granja";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_fase"])) {
    $nueva_fase = $_POST["nueva_fase"];
    $sql_update = "UPDATE Camadas SET id_fase = $nueva_fase WHERE id_camada = $id_camada";

    if ($conn->query($sql_update) === TRUE) {
        echo "<div class='alert alert-success text-center'>Fase actualizada correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error al actualizar la fase: " . $conn->error . "</div>";
    }
}

// Consulta para obtener información de la camada
$sql = "SELECT 
            id_camada,
            arete,
            cantidad,
            fechanaci,
            id_fase,
            TIMESTAMPDIFF(MONTH, fechanaci, CURDATE()) AS meses,
            TIMESTAMPDIFF(DAY, ADDDATE(fechanaci, INTERVAL TIMESTAMPDIFF(MONTH, fechanaci, CURDATE()) MONTH), CURDATE()) AS dias
        FROM Camadas
        WHERE id_camada = $id_camada";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $edad_meses = $row['meses'];
    $edad_dias = $row['dias'];    

    $sql_fase = "SELECT fase FROM Fases WHERE id_fase = " . $row['id_fase'];
    $fase_result = $conn->query($sql_fase);
    $fase_row = $fase_result->fetch_assoc();
    $nombre_fase = $fase_row['fase'];

    // Obtener lista de fases para el menú desplegable
    $sql_fases = "SELECT id_fase, fase FROM Fases";
    $fases_result = $conn->query($sql_fases);
    ?>

    <div class="container mt-5">
        <h2>Información de la Camada</h2>
        <table class="table">            
            <tr>
                <th>Cantidad de Animales</th>
                <td><?php echo $row["cantidad"]; ?></td>
            </tr>
            <tr>
                <th>Fecha de Nacimiento</th>
                <td><?php echo $row["fechanaci"]; ?></td>
            </tr>
            <tr>
                <th>Arete de la Madre</th>
                <td><?php echo $row["arete"]; ?></td>
            </tr>
            <tr>
                <th>Edad</th>
                <td><?php echo $edad_meses . " mes(es) y " . $edad_dias . " día(s)"; ?></td>
            </tr>
            <tr>
                <th>Fase</th>
                <td><?php echo $nombre_fase; ?></td>
            </tr>
        </table>
        <h3>Actualizar</h3>
        <form action="" method="post" class="mt-3">
            <div class="mb-3">
                <label for="nueva_fase" class="form-label">Cambiar Fase:</label>
                <select name="nueva_fase" id="nueva_fase" class="form-select" required>
                    <option value="" disabled selected>Selecciona una nueva fase</option>
                    <?php
                    if ($fases_result->num_rows > 0) {
                        while ($fase = $fases_result->fetch_assoc()) {
                            echo "<option value='" . $fase["id_fase"] . "'>" . $fase["fase"] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="editar_fase" class="btn btn-primary">Actualizar Fase</button>
        </form>
    </div>
    <?php
} else {
    echo "<div class='container mt-5'><p>No se encontró información para la camada seleccionada.</p></div>";
}

$conn->close();
?>

<!-- Bootstrap Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-bz3htznfnCJUiN+ouzWEhA0J6i/DOTt8Y5FzhKG6z13MiWBKRl0pMb7OoBydSMIk" crossorigin="anonymous"></script>
</body>
</html>
