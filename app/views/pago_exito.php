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
    <link rel="stylesheet" href="css/pago_exito.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php 
        include ("layouts/header1.php");
    ?>

<main>
    <div class="difuminado"></div>

    <section class="pago-exito-container">
        <h1>¡Gracias por tu compra!</h1>

        <div class="pago-box">
            <h2>Tu cuenta ahora es Premium</h2>
            <p>Disfruta de la música sin anuncios, con calidad máxima y acceso ilimitado.</p>

            <ul>
                <li>✅ Acceso a todas las canciones</li>
                <li>✅ Reproducción sin interrupciones</li>
                <li>✅ Soporte prioritario</li>
            </ul>

            <a href="/home" class="btn-volver">Ir al inicio</a>
        </div>
    </section>
</main>
    <footer>
        <?php 
            include ("layouts/footer.php");
        ?>
    </footer>
</body>
</html>
