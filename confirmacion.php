<?php
// Iniciar la sesión y cargar la configuración
session_start();
include 'conexion/conexion.php';

// Función para realizar una llamada a la API de Flow
function llamarAPI($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('Error en la solicitud: ' . $error);
    }

    curl_close($ch);
    return json_decode($response, true);
}

// Obtener el token enviado por Flow
$token = $_POST['token'] ?? null;

if ($token) {
    // API de Flow
    $apiKey = '5BF55F84-3254-4076-9FCE-73B20A5L56DB';
    $urlGetStatus = 'https://sandbox.flow.cl/api/payment/getStatus';  // URL del sandbox para obtener el estado

    // Preparar los datos para la solicitud
    $data = array(
        "token" => $token,
        "apiKey" => $apiKey,
    );

    try {
        // Llamar al servicio para obtener el estado del pago
        $resultado = llamarAPI($urlGetStatus, $data);

        // Procesar el resultado
        if (isset($resultado['status'])) {
            // Aquí puedes manejar el estado de la transacción
            // Por ejemplo, guardar el estado en tu base de datos o actualizar el estado en la sesión
            switch ($resultado['status']) {
                case 'success':
                    // Transacción exitosa
                    // Actualiza el estado en tu base de datos
                    break;
                case 'failed':
                    // Transacción fallida
                    // Manejar la falla
                    break;
                case 'pending':
                    // Transacción pendiente
                    // Manejar el estado pendiente
                    break;
                default:
                    // Estado no reconocido
                    break;
            }
        }
    } catch (Exception $e) {
        // Manejo de errores
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo "Token no recibido.";
}
?>
