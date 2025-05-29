<?php
    require_once __DIR__ . '/../../config/Conexion_BBDD.php';
    require_once __DIR__ . '/../../app/models/artista.php';

    // Obtiene el id del artista desde la URL
    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        echo "Artista no válido.";
        exit;
    }

    //obtiene la información del artista
    $artistaModel = new Artista($pdo);
    $artista = $artistaModel->getById($id);
    // Si no existe el artista, muestra un mensaje de error
    if (!$artista) {
        echo "Artista no encontrado.";
        exit;
    }
    //crea la variabe ruta y almacena la ruta de la foto del perfil del artista y con operador ternario si no existe la foto del perfil, se asigna una foto por defecto
    $ruta_foto = $artista['foto_perfil'] ? "/uploads/perfiles/" . $artista['foto_perfil'] : "/uploads/perfiles/default.jpg";

    // Obtiene las canciones del artista usando el modelo
    $canciones = $artistaModel->getCanciones($id);

    // Añade campos necesarios para el reproductor
    foreach ($canciones as &$c) {
        $c['ruta_mp3'] = "/uploads/stream.php?file={$c['id_cancion']}.mp3";
        $c['artista'] = $artista['nombre'];
        $c['foto_album'] = "/uploads/foto-album/{$c['id_album']}.jpg";
    }
    unset($c);
    ?>

    <h2><?= htmlspecialchars($artista['nombre']) ?></h2>

    <?php foreach ($canciones as $c): ?>
        <?php
            $foto_album = "/uploads/foto-album/{$c['id_album']}.jpg";
            $ruta_mp3 = "/uploads/stream.php?file={$c['id_cancion']}.mp3";
        ?>
        <div class="container-cancion">
            <div class="add-playlist" data-id="<?= $c['id_cancion'] ?>">
                <img  src="/img/heart-blanco.png" class="corazon-blanco" style="width: 15px; height: 15px; cursor:pointer;">
                <img class="corazon-gradient oculto" src="/img/heart-rosa.png"  width="15px" height="15px">
            </div>

            <div class="img-wrapper">
                <img src="<?= $foto_album ?>" alt="Carátula del álbum">
                <div class="hover-overlay"
                    data-id="<?= $c['id_cancion'] ?>"
                    data-src="<?= $ruta_mp3 ?>"
                    data-title="<?= htmlspecialchars($c['nombre_c']) ?>"
                    data-artist="<?= htmlspecialchars($artista['nombre']) ?>"
                    data-cover="<?= $foto_album ?>">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px">
                        <defs>
                            <linearGradient id="grad-play" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#481B9A; stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#FF4EC4; stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <path fill="url(#grad-play)" fill-rule="evenodd" clip-rule="evenodd"
                            d="M5.46484 3.92349C4.79896 3.5739 4 4.05683 4 4.80888V19.1911C4 19.9432 4.79896 20.4261 5.46483 20.0765L19.1622 12.8854C19.8758 12.5108 19.8758 11.4892 19.1622 11.1146L5.46484 3.92349ZM2 4.80888C2 2.55271 4.3969 1.10395 6.39451 2.15269L20.0919 9.34382C22.2326 10.4677 22.2325 13.5324 20.0919 14.6562L6.3945 21.8473C4.39689 22.8961 2 21.4473 2 19.1911V4.80888Z"/>
                    </svg>
                </div>
            </div>

            <div class="cancion">
                <div class="info-cancion">
                    <strong><?= htmlspecialchars($c['nombre_c']) ?></strong>
                    <span><?= htmlspecialchars($artista['nombre']) ?></span>
                </div>

                <div class="stat-cancion">
                    <span><?= htmlspecialchars($c['reproducciones']) ?></span>
                    <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 47 47" xml:space="preserve" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M24.104,41.577c-0.025,0-0.053-0.001-0.078-0.001c-1.093-0.035-2.025-0.802-2.271-1.867l-5.46-23.659l-3.199,8.316 c-0.357,0.93-1.252,1.544-2.249,1.544H2.41c-1.331,0-2.41-1.079-2.41-2.41c0-1.331,1.079-2.41,2.41-2.41h6.78l5.433-14.122 c0.38-0.989,1.351-1.612,2.418-1.54c1.057,0.074,1.941,0.831,2.18,1.863l5.186,22.474l4.618-15.394 c0.276-0.923,1.078-1.592,2.035-1.702c0.953-0.107,1.889,0.36,2.365,1.198l4.127,7.222h7.037c1.331,0,2.41,1.079,2.41,2.41 c0,1.331-1.079,2.41-2.41,2.41h-8.436c-0.865,0-1.666-0.463-2.094-1.214l-2.033-3.559l-5.616,18.722 C26.104,40.88,25.164,41.577,24.104,41.577z"></path> </g> </g> </g></svg>
                    <span><?= htmlspecialchars($c['duracion']) ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        //funcion para activar los eventos de reproducción de audio simplemente un hover de backgorund que va un poco regular
        function activarResaltadoCancion() {
            document.querySelectorAll('.hover-overlay').forEach(overlay => {
                overlay.addEventListener('click', function () {
                    document.querySelectorAll('.container-cancion').forEach(div => {
                        div.classList.remove('cancion-activa');
                    });
                    const cancionDiv = this.closest('.container-cancion');
                    if (cancionDiv) {
                        cancionDiv.classList.add('cancion-activa');
                    }
                });
            });
        }
    // llamar los eventos del reproductor y el resaltado de canción
    activarEventosAudio();
    activarResaltadoCancion();
    </script>

    <script>
        //windows.artistaActual expone el array de canciones del artista en una variable global
        //para que el reproductor pueda acceder a ellas y reproducirlas en orden.
        //también borra la variable global de álbum para evitar conflictos si el usuario cambia de vista
        window.artistaActual = <?= json_encode($canciones, JSON_UNESCAPED_UNICODE) ?>;
        window.albumActual = undefined;
/*
Ejemplo de uso:
- Cuando el usuario hace clic en una canción de este artista, el reproductor puede usar window.artistaActual
  para saber qué canciones hay en la lista y permitir avanzar/retroceder entre ellas.
*/
    </script>
