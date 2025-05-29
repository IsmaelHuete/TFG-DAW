<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: /login'); // o la ruta real de tu login
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Cancelado</title>
    <link rel="stylesheet" href="css/comun.css">
</head>
<body>
    <main style="padding: 40px; text-align:center;">
        <h1>❌ Pago cancelado</h1>
        <p>Tu suscripción no se ha completado. Puedes intentarlo de nuevo cuando quieras.</p>
        <a href="/premium" style="display:inline-block; margin-top:20px;">Volver a elegir plan</a>
    </main>
</body>
</html>
