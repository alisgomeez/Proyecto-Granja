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
    <h2>Listado de Camadas</h2>
    
    <?php
    $servername = "database";
    $username = "root";
    $password = "root";
    $dbname = "Granja";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    $sql = "SELECT id_camada, arete, id_corral FROM Camadas";
    $result = $conn->query($sql);
    ?>
    <table class="table">
        <thead>
            <tr>
                <th>Corral</th>
                <th>Arete</th>
                <th>Acción</th> <!-- Columna para el botón -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Verificar si hay resultados y mostrarlos
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id_corral"] . "</td>";
                    echo "<td>" . $row["arete"] . "</td>";
                    // El id_camada se pasa en el enlace, pero no se muestra en la página
                    echo "<td><a href='verinfo.php?id=" . $row["id_camada"] . "' class='btn btn-primary'>" .
                         "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-eye' viewBox='0 0 16 16'>" .
                         "<path d='M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z'/>" .
                         "<path d='M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0'/>" .
                         "</svg> Ver</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No hay datos disponibles</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-bz3htznfnCJUiN+ouzWEhA0J6i/DOTt8Y5FzhKG6z13MiWBKRl0pMb7OoBydSMIk" crossorigin="anonymous"></script>
</body>
</html>
