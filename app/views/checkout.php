<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$plan = $_GET['plan'] ?? 'mensual';

$detalles = [
    'mensual' => ['precio' => .99, 'beneficios' => 'Acceso ilimitado sin anuncios.'],
    'anual' => ['precio' => 99.99, 'beneficios' => '12 meses por el precio de 10.']
];

if (!isset($detalles[$plan])) {
    echo "Plan no válido.";
    exit;
}

$precio = $detalles[$plan]['precio'];
$beneficios = $detalles[$plan]['beneficios'];
?>

<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/header1.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/privacidad.css">
    <link rel="stylesheet" href="css/checkout.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php 
        include ("layouts/header1.php");
    ?>

    <main>
        <div class="difuminado"></div>
        <section class="checkout-container">
            <h1>Resumen de tu suscripción</h1>

            <div class="checkout-box">
                <h2 class="checkout-plan"><?= ucfirst($plan) ?> Premium</h2>
                <p class="checkout-beneficios"><?= $beneficios ?></p>
                <div class="checkout-precio">
                    <span>Total:</span>
                    <strong><?= number_format($precio, 2) ?> €</strong>
                </div>

                <form action="/pagar.php" method="POST">
                    <input type="hidden" name="plan" value="<?= htmlspecialchars($plan) ?>">
                    <button type="submit" class="btn-confirmar">Confirmar y pagar</button>
                </form>
            </div>

            <a href="/premium" class="checkout-back">← Volver a elegir plan</a>
        </section>
    </main>
<footer>
    <?php 
        include ("layouts/footer.php");
    ?>
</footer>
</html>