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
include("includes/header.php");

// se obtiene el ID de la camada desde la pagina 
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

// consulta en la base para la camada seleccionada
$sql = "SELECT 
            id_camada,
            arete,
            cantidad,
            fechanaci,
            TIMESTAMPDIFF(MONTH, fechanaci, CURDATE()) AS meses,
            TIMESTAMPDIFF(DAY, ADDDATE(fechanaci, INTERVAL TIMESTAMPDIFF(MONTH, fechanaci, CURDATE()) MONTH), CURDATE()) AS dias
        FROM Camadas
        WHERE id_camada = $id_camada";$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $edad_meses = $row['meses'];
    $edad_dias = $row['dias'];    ?>
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
                <td><?php echo $edad_meses . " mes y " . $edad_dias . " días"; ?></td>
            </tr>

        </table>
    </div>
    <?php
} else {
    echo "<div class='container mt-5'><p>No se encontró información para la camada seleccionada.</p></div>";
}
//agregar si ya tienen las dosis de hierro y bycox
$conn->close();
?>

<!-- Bootstrap Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-bz3htznfnCJUiN+ouzWEhA0J6i/DOTt8Y5FzhKG6z13MiWBKRl0pMb7OoBydSMIk" crossorigin="anonymous"></script>
</body>
</html>
