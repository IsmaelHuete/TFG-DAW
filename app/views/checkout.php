<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['email']) || $_SESSION['plan'] == 'premium') {
    header('Location: /404'); 
    exit;
}
$plan = $_GET['plan'] ?? 'mensual';
$detalles = [
    'mensual' => ['precio' => 9.99, 'titulo' => 'Premium Mensual', 'descripcion' => ['Reproducción ilimitada', 'Sin anuncios', 'Calidad estándar', '1 dispositivo']],
    'anual' => ['precio' => 99.99, 'titulo' => 'Premium Anual', 'descripcion' => ['Reproducción ilimitada', 'Sin anuncios', 'Alta fidelidad', '5 dispositivos']],
];

if (!isset($detalles[$plan])) {
    echo "Plan no válido.";
    exit;
}

$precio = $detalles[$plan]['precio'];
$beneficios = $detalles[$plan]['descripcion'];
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

            <div class="checkout-content">
                <!-- Columna izquierda: Info del plan -->
                <div class="plan">
                    <div class="basico">
                        <h2><?= $detalles[$plan]['titulo'] ?></h2>
                        <span>$<?= number_format($detalles[$plan]['precio'], 2) ?><span class="letra"> / <?= $plan === 'mensual' ? 'mes' : 'año' ?></span></span>
                        <ul>
                            <?php foreach ($detalles[$plan]['descripcion'] as $beneficio): ?>
                                <li>&#10003; <?= $beneficio ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <hr class="checkout-hr">
            <!-- Columna derecha: Formulario de pago -->
                <div class="checkout-form-box">
                    <!-- Stripe.js -->
                    <script src="https://js.stripe.com/v3/"></script>

                    <!-- Botón personalizado -->
                    <button id="checkout-button" class="btn-confirmar">Pagar con Stripe</button>

                    <script>
                    const stripe = Stripe('pk_test_51RTVszDmtgP8bOAlhm7kc0VoXpJYP4J9UxTpbfLrNvb1d2Y24UdpyIh1VaBh8XdIiCYjg27THoOETpgN876LoRba00LhEVmlJg'); // Reemplaza por la tuya

                    document.getElementById("checkout-button").addEventListener("click", function () {
                        fetch("/ajax/crear_sesion_stripe.php?plan=<?= $plan ?>", {
                            method: "POST",
                        })
                         .then((res) => res.json())
                        .then((data) => {
                            return stripe.redirectToCheckout({ sessionId: data.id });
                        })
                        .catch((err) => {
                            console.error("Error en la redirección a Stripe:", err);
                        });
                    });
                    </script>

                    <div class="pago-option">
                        <hr><p>Or</p><hr>
                    </div>
                
                    <!-- Tarjeta de crédito -->

                    <h2>Pago con tarjeta</h2>
                        
                </div>
            </div>

            <a href="/premium" class="checkout-back">← Volver a elegir plan</a>
        </section>
    </main>
<footer>
    <?php 
        include ("layouts/footer.php");
    ?>
</footer>
<script src="js/header.js"></script>
</html>