<?php
    session_start();
    require_once __DIR__ . '/../models/artista.php';

    $artistaModel = new Artista($pdo);
    $topArtistas = $artistaModel->getTopArtistasConReproducciones(4);
?>
<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" type="image/png" href="img/image-brand.png">
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<body>
    <div class="oferta">
        <p>Musicfy Premium te lleva más lejos. Escucha sin conexión, sin cortes y con calidad HD. Suscríbete y transforma tu forma de escuchar música.</p>
    </div>
    <?php 
        include ("layouts/header.php");
    ?>
    <main>
         <div class="wrap">
        <div class="container">
            <?php foreach ($topArtistas as $i => $artista): ?>
                <input type="radio" name="slide" id="c<?= $i+1 ?>" <?= $i === 0 ? 'checked' : '' ?>>
                <label for="c<?= $i+1 ?>" class="card">
                    <div class="row">
                        <div class="description">
                            <h4><?= htmlspecialchars($artista['nombre_artista']) ?></h4>
                            <p><?= number_format($artista['total_reproducciones'], 0, ',', '.') ?></p>
                        </div>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    </main>
    <div class="banners">
        <div class="container-banner">

            <div class="section-banner">
                <div class="text">
                    <h2>📣 Obtén Musicfy Premium</h2>
                    <span>Escucha música sin anuncios</span>
                </div>
                <a href="premium"><button>Suscribirse</button></a>
            </div>

            <div class="section-banner">
                <div class="text">
                    <h2>🎧 Crea tu playlist personalizada</h2>
                    <span>En segundos, según tu mood</span>
                </div>
                <button>Empieza ahora</button>
            </div>

            <div class="section-banner">
                <div class="text">
                    <h2>📈 Lo más escuchado esta semana</h2>
                    <span>Reggaetón, Pop, Electrónica...</span>
                </div>
            </div>

            <div class="section-banner">
                <div class="text">
                    <h2>💬 Testimonio destacado</h2>
                    <span>“Musicfy cambió cómo descubro música.”</span>
                </div>

            </div>

        </div>
    </div>
    <?php 
        include ("layouts/footer.php");
    ?> 
    <script src="js/header.js"></script>
</body>
</html>