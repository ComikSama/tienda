<?php
$servername = "localhost";
$username = "lswebcl_tienda";
$password = "ychuqa7hVNEZNE8dTJQH";
$dbname = "lswebcl_tienda";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta de productos
$sql = "SELECT * FROM productos";
$result = $conn->query($sql);
?>