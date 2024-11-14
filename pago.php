<?php
session_start();
include 'conexion/conexion.php'; // Asegúrate de que este archivo contiene la conexión

// Asegúrate de que las siguientes líneas estén antes de cualquier salida
header('Content-Type: text/html; charset=UTF-8');

// Verifica si el carrito está vacío
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    die("Error: El carrito está vacío.");
}

// Calcular el total del carrito
$total = 0;
foreach ($_SESSION['carrito'] as $producto) {
    if (isset($producto['precio']) && isset($producto['cantidad'])) {
        $total += $producto['precio'] * $producto['cantidad'];
    } else {
        die("Error: Producto no válido en el carrito.");
    }
}
$total = round($total); // Asegúrate de que el total sea un número entero

// Datos de la API de Flow
$apiKey = "5BF55F84-3254-4076-9FCE-73B20A5L56DB";
$secretKey = "6beb75a6c602d09ae0bb9c9e638b703d7b9d1fe3";
$url = "https://sandbox.flow.cl/api/payment/create"; // URL del sandbox

// Datos de la transacción, los valores se obtienen de la sesión y otros parámetros
$params = array(
    "amount" => $total,  // Monto total del carrito
    "apiKey" => $apiKey,
    "currency" => "CLP",
    "commerceOrder" => uniqid(),  // Identificador único para la transacción
    "subject" => "Compra en tienda",
    "email" => "comik007@gmail.com",
    "urlConfirmation" => "http://www.lsweb.cl/tienda/confirmacion.php",  // URL de confirmación
    "urlReturn" => "http://www.lsweb.cl/tienda"  // URL de retorno
);

// Ordenar los parámetros alfabéticamente
ksort($params);

// Crear el string para la firma
$dataString = '';
foreach ($params as $key => $value) {
    $dataString .= $key . $value; // Concatenar nombre y valor de los parámetros
}

// Calcular la firma (signature)
$signature = hash_hmac('sha256', $dataString, $secretKey);

// Agregar la firma a los parámetros
$params["s"] = $signature;

try {
    // Inicializar cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)); // Enviar los parámetros como query string

    // Ejecutar la solicitud
    $response = curl_exec($ch);
    
    // Manejo de errores
    if ($response === false) {
        $error = curl_error($ch);
        throw new Exception('Error en la solicitud: ' . $error);
    } 

    // Obtener información de la respuesta
    $info = curl_getinfo($ch);
    
    // Verificar que el código HTTP sea 200, 400 o 401
    if (!in_array($info['http_code'], array(200, 400, 401))) {
        throw new Exception('Error inesperado. Código HTTP: ' . $info['http_code'], $info['http_code']);
    }

    // Decodificar la respuesta
    $responseData = json_decode($response, true);

    // Imprimir la respuesta para depuración
    echo '<pre>';
    print_r($responseData);
    echo '</pre>';

    // Procesar la respuesta
    if (isset($responseData['token']) && isset($responseData['url'])) {
        // Redirigir a la URL de pago
        header("Location: " . $responseData['url']);
        exit; // Asegúrate de salir después de redirigir
    } else {
        echo "Error en el proceso de pago: ";
        if (isset($responseData['errorMessage'])) {
            echo $responseData['errorMessage'];
        } else {
            print_r($responseData); // Mostrar los detalles de la respuesta
        }
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
} finally {
    curl_close($ch);
}
?>
