<?php
session_start(); // Asegúrate de iniciar la sesión
include 'conexion/conexion.php'; // Conexión a la base de datos

// Comprobar si se ha enviado el formulario de agregar al carrito
if (isset($_POST['agregar'])) {
    $id = $_POST['id'];  // ID del producto
    $cantidad = $_POST['cantidad'];  // Cantidad de producto
    
    // Obtener el producto desde la base de datos
    $sql = "SELECT * FROM productos WHERE id = $id";
    $result = $conn->query($sql);
    $producto = $result->fetch_assoc();

    if ($producto) {
        // Preparar el array del producto para el carrito
        $producto_carrito = array(
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'cantidad' => $cantidad
        );

        // Verificar si el carrito ya existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }

        // Si el producto ya existe en el carrito, solo actualiza la cantidad
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
        } else {
            // Agregar el producto al carrito
            $_SESSION['carrito'][$id] = $producto_carrito;
        }
    }

    // Redirigir de vuelta al carrito para mostrar los productos agregados
    header('Location: carrito.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Carrito de Compras</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Carrito de Compras</h1>

        <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($_SESSION['carrito'] as $id => $producto): 
                    $subtotal = $producto['precio'] * $producto['cantidad'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td>$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></td>
                    <td><?php echo $producto['cantidad']; ?></td>
                    <td>$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total a Pagar: $<?php echo number_format($total, 0, ',', '.'); ?></h3>
        <a href="pago.php" class="btn btn-primary">Proceder al Pago</a>
        <a href="index.php" class="btn btn-secondary">Volver a la Tienda</a>

        <?php else: ?>
        <p>El carrito está vacío.</p>
        <a href="index.php" class="btn btn-secondary">Volver a la Tienda</a>
        <?php endif; ?>
    </div>
</body>
</html>
