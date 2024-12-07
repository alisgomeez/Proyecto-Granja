<?php
    $servername = "database";  
    $username = "root";        
    $password = "root";        
    $dbname = "Granja";        

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
?>