<?php
include("includes/header.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Añadir Camada</title>
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

$sql_aretes = "SELECT arete FROM Animales";
$aretes_result = $conn->query($sql_aretes);

$sql_corrales = "SELECT id_corral, corral FROM Corrales";
$corrales_result = $conn->query($sql_corrales);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $arete = $_POST["arete"];
    $id_corral = $_POST["id_corral"];
    $cantidad = $_POST["cantidad"];
    $fechanaci = $_POST["fechanaci"];
    $id_fase = 2;  // Fase por defecto

    // Insertar la nueva camada en la base de datos
    $sql_insert = "INSERT INTO Camadas (arete, id_corral, cantidad, fechanaci, id_fase)
                   VALUES ('$arete', '$id_corral', '$cantidad', '$fechanaci', '$id_fase')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "<div class='alert alert-success' role='alert'>Camada agregada exitosamente.</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error al agregar la camada: " . $conn->error . "</div>";
    }
}
$conn->close();
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Añadir Camada</h2>
        </div>
        <div class="card-body">
    <form action="agregarcam.php" method="post">

        <div class="form-group">
            <label for="arete">Arete:</label>
            <select class="form-control" id="arete" name="arete" required>
                <option value="" disabled selected>Selecciona un arete</option>
                <?php
                if ($aretes_result->num_rows > 0) {
                    while ($row = $aretes_result->fetch_assoc()) {
                        echo "<option value='{$row['arete']}'>{$row['arete']}</option>";
                    }
                } else {
                    echo "<option value='' disabled>No hay aretes disponibles</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_corral">Corral:</label>
            <select class="form-control" id="id_corral" name="id_corral" required>
                <option value="" disabled selected>Selecciona un corral</option>
                <?php
                if ($corrales_result->num_rows > 0) {
                    while ($row = $corrales_result->fetch_assoc()) {
                        echo "<option value='{$row['id_corral']}'>{$row['corral']}</option>";
                    }
                } else {
                    echo "<option value='' disabled>No hay corrales disponibles</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
        </div>

        <div class="form-group">
            <label for="fechanaci">Fecha de Nacimiento:</label>
            <input type="date" class="form-control" id="fechanaci" name="fechanaci" required>
        </div>

        <div class="form-group">
            <input type="hidden" name="id_fase" value="2"> <!-- Fase por defecto es 2 -->
        </div>

        <button type="submit" class="btn btn-primary">Agregar Camada</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>
</html>
