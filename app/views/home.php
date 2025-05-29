<?php
    session_start();
?>
<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<body>
    <div class="oferta">
        <p>🔥 Musicfy Premium te lleva más lejos. Escucha sin conexión, sin cortes y con calidad HD. Suscríbete y transforma tu forma de escuchar música.</p>
    </div>
    <?php 
        include ("layouts/header.php");
    ?>
    <main>
        <div class="wrap">
            <div class="container">
                <input type="radio" name="slide" id="c1" checked>
                <label for="c1" class="card">
                    <div class="row">
                        <div class="description">
                            <h4>Quevedo</h4>
                            <p>25.563.510</p>
                        </div>
                    </div>
                </label>
                <input type="radio" name="slide" id="c2" >
                <label for="c2" class="card">
                    <div class="row">
                        <div class="description">
                            <h4>Anuel</h4>
                            <p>34.282.946</p>
                        </div>
                    </div>
                </label>
                <input type="radio" name="slide" id="c3" >
                <label for="c3" class="card">
                    <div class="row">
                        <div class="description">
                            <h4>Arcangel</h4>
                            <p>24.776.694</p>
                        </div>
                    </div>
                </label>
                <input type="radio" name="slide" id="c4" >
                <label for="c4" class="card">
                    <div class="row">
                        <div class="description">
                            <h4>Bad Bunny</h4>
                            <p>83.710.525</p>
                        </div>
                    </div>
                </label>
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