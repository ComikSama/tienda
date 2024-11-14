<?php
session_start(); // Asegúrate de iniciar la sesión
include 'conexion/conexion.php'; 

// Establecer el juego de caracteres a utf8
$conn->set_charset("utf8");

// Obtener todos los productos de la base de datos
$sql = "SELECT * FROM productos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Tienda de Productos</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Tienda de Productos</h1>
        <div class="row">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="img/<?php echo $row['imagen']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="card-text">$<?php echo number_format($row['precio'], 0, ',', '.'); ?></p>
                        <form method="post" action="carrito.php">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="cantidad" value="1" min="1" class="form-control mb-2">
                            <button type="submit" name="agregar" class="btn btn-success">Agregar al carrito</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <a href="carrito.php" class="btn btn-primary">Ver Carrito</a>
    </div>
</body>
</html>
