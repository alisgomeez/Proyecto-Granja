<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Mostrar Lotes</title>
</head>
<body>

<?php
include("includes/header.php");
?>

<div class="container mt-5">
    <h2>Listado de Madres</h2>
    
    <?php
    $servername = "database";
    $username = "root";
    $password = "root";
    $dbname = "Granja";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    $sql = "SELECT arete, Id_corral FROM Animales";
    $result = $conn->query($sql);
    ?>
<table class="table">
        <thead>
            <tr>
                <th>Corral</th>
                <th>Arete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Verificar si hay resultados y mostrarlos
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["Id_corral"] . "</td>";
                    echo "<td>" . $row["arete"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No hay datos disponibles</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
        </tbody>
    </table>
</div>

<!-- Bootstrap Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-bz3htznfnCJUiN+ouzWEhA0J6i/DOTt8Y5FzhKG6z13MiWBKRl0pMb7OoBydSMIk" crossorigin="anonymous"></script>
</body>
</html>