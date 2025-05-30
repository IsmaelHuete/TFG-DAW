<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: /login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Cancelado</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/premium_exito.css">
    <link rel="stylesheet" href="css/header1.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <?php include("layouts/header1.php"); ?>
    <main style="padding: 40px; text-align:center;">
        <h1>❌ Pago cancelado</h1>
        <p>Tu suscripción no se ha completado. Puedes intentarlo de nuevo cuando quieras.</p>
        <a href="/premium" style="display:inline-block; margin-top:20px;">Volver a elegir plan</a>
    </main>
    <?php include("layouts/footer.php"); ?>
</body>
</html>
