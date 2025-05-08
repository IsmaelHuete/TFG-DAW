<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['email'])) {
        header("Location: /login");
        exit;
    }
    
    $email = $_SESSION['email'];
    $tipo = $_SESSION['tipo'] ?? 'normal';
?>
<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<body>
    <?php 
        include ("layouts/header1.php");
    ?>
    <main>
    <div class="contenedor-perfil">
            <div class="encabezado-perfil">
                <div class="avatar-perfil">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" width="80" height="80">
                        <defs>
                            <linearGradient id="grad-avatar" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="20%" stop-color="#481B9A" />
                                <stop offset="100%" stop-color="#FF4EC4" />
                            </linearGradient>
                        </defs>
                        <path stroke="url(#grad-avatar)" fill="none" stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </div>
                <div class="info-perfil">
                    <h1>Mi Perfil</h1>
                </div>
            </div>

            <div class="contenido-perfil">
                <div class="seccion-perfil">
                    <h2>Datos de la cuenta</h2>
                    <div class="tarjeta-info">
                        <div class="item-info">
                            <span class="etiqueta">Correo:</span>
                            <span class="dato"><?php echo htmlspecialchars($email); ?></span>
                        </div>
                        <div class="item-info">
                            <span class="etiqueta">Tipo de cuenta:</span>
                            <span class="dato"><?php echo htmlspecialchars(ucfirst($tipo)); ?></span>
                        </div>
                        <div class="item-info">
                            <span class="etiqueta">Plan actual:</span>
                            <span class="dato insignia-premium">
                                <?php if ($tipo === 'artista' || isset($_SESSION['premium'])): ?>
                                    Premium
                                <?php else: ?>
                                    Gratuito
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <?php if ($tipo !== 'artista'): ?>
                <div class="seccion-perfil">
                    <h2>Mejora tu cuenta</h2>
                    <div class="tarjeta-mejora">
                        <div class="info-mejora">
                            <h3>Musicfy Premium</h3>
                            <p>Escucha sin anuncios, descarga canciones y calidad HD.</p>
                        </div>
                        <a href="/premium" class="boton-mejora">Hazte Premium</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="seccion-perfil">
                    <h2>Zona de Artista</h2>
                    <div class="estadisticas-artista">
                        <div class="tarjeta-dato">
                            <div class="valor-dato">0</div>
                            <div class="texto-dato">Canciones</div>
                        </div>
                        <div class="tarjeta-dato">
                            <div class="valor-dato">0</div>
                            <div class="texto-dato">Álbumes</div>
                        </div>
                        <div class="tarjeta-dato">
                            <div class="valor-dato">0</div>
                            <div class="texto-dato">Reproducciones</div>
                        </div>
                    </div>
                    <div class="acciones-artista">
                        <a href="#" class="boton-accion">Subir música</a>
                        <a href="#" class="boton-accion">Ver estadísticas</a>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="seccion-perfil">
                    <h2>Opciones de cuenta</h2>
                    <div class="acciones-cuenta">
                        <a href="/logout" class="boton-salir">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            Cerrar Sesión
                        </a>
                        <a href="#" class="boton-config">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            Configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <?php 
        include ("layouts/footer.php");
    ?> 
    <script src="js/header.js"></script>
    <script src="js/home.js"></script>
</body>
</html>