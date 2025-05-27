<?php
session_start();
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/usuario.php';

if (!isset($_SESSION['email'])) {
    header('Location: /login');
    exit;
}

$email = $_SESSION['email'];
$usuarioModel = new Usuario($pdo);

// Cambia el plan a "premium"
$usuarioModel->actualizarPlan($email, 'premium');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Exitoso</title>
    <link rel="stylesheet" href="css/comun.css">
</head>
<body>
    <main style="padding: 40px; text-align:center;">
        <h1>✅ ¡Gracias por tu compra!</h1>
        <p>Tu cuenta ha sido actualizada a <strong>Premium</strong>.</p>
        <a href="/index">Volver al inicio</a>
    </main>
</body>
</html>