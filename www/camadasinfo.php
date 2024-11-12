<?php include("includes/header.php");?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Mostrar Camadas</title>
</head>
<body>

<div class="container mt-5">
    <h2>Seleccionar Lote</h2>
    
    <?php
    $servername = "database";
    $username = "root";
    $password = "root";
    $dbname = "Granja";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = "SELECT id_camada, id_corral FROM Camadas";
    $result = $conn->query($sql);
    ?>
</div>

<!-- Bootstrap Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-bz3htznfnCJUiN+ouzWEhA0J6i/DOTt8Y5FzhKG6z13MiWBKRl0pMb7OoBydSMIk" crossorigin="anonymous"></script>
</body>
</html>
