<?php
    //incluir archivos necesarios
    require_once __DIR__ . '/../../config/Conexion_BBDD.php';
    require_once __DIR__ . '/../../app/models/album.php';

    //obtiene el id del álbum desde la URL
    $id_album = $_GET['id'] ?? null;
    //mete en la variable la ruta de la foto del álbum con el id del álbum
    $foto_album = "/uploads/foto-album/{$id_album}.jpg";

    // si no se recibe un id válido, muestra un mensaje de error
    if (!$id_album || !is_numeric($id_album)) {
        echo "Álbum no especificado.";
        exit;
    }

    // obtener info del álbum
    $albumModel = new Album($pdo);
    $album = $albumModel->getAlbumCompleto($id_album); 

    //si no existe el álbum, muestra un mensaje de error
    if (!$album) {
        echo "Álbum no encontrado.";
        exit;
    }

    //obtener canciones del album
    $canciones = $albumModel->getCanciones($id_album);
    //añade información extra a cada canción id_album, foto_album, artista
    foreach ($canciones as $i => $c) {
        $c['id_album'] = $album['id_album'];
        $c['foto_album'] = "/uploads/foto-album/{$album['id_album']}.jpg";
        $c['artista'] = $album['nombre_artista'];
        $canciones[$i] = $c;
    }

    ?>

    <h2><?= htmlspecialchars($album['nombre_album']) ?></h2>
    <?php foreach ($canciones as $c):?>
        <?php 
            $ruta_mp3 = "/uploads/stream.php?file={$c['id_cancion']}.mp3"; 
        ?>
        <div class="container-cancion" >
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
                    data-artist="<?= htmlspecialchars($album['nombre_artista']) ?>"
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
                    <span><?= htmlspecialchars($album['nombre_artista']) ?></span>
                </div>

                
                <div class="stat-cancion">
                    <span><?= htmlspecialchars($c['reproducciones']) ?><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M17 13C17 12.4696 17.2107 11.9609 17.5858 11.5858C17.9609 11.2107 18.4696 11 19 11C19.5304 11 20.0392 11.2107 20.4142 11.5858C20.7893 11.9609 21 12.4696 21 13V18.9999C21 19.5303 20.7893 20.0391 20.4142 20.4142C20.0392 20.7893 19.5304 20.9999 19 20.9999C18.4696 20.9999 17.9609 20.7893 17.5858 20.4142C17.2107 20.0391 17 19.5303 17 18.9999V13Z" stroke="#999999" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M3 13C3 12.4696 3.21074 11.9609 3.58582 11.5858C3.96089 11.2107 4.46957 11 5 11C5.53043 11 6.03917 11.2107 6.41425 11.5858C6.78932 11.9609 7 12.4696 7 13V18.9999C7 19.5303 6.78932 20.0391 6.41425 20.4142C6.03917 20.7893 5.53043 20.9999 5 20.9999C4.46957 20.9999 3.96089 20.7893 3.58582 20.4142C3.21074 20.0391 3 19.5303 3 18.9999V13Z" stroke="#999999" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M19 11V10C19 8.14348 18.2625 6.36305 16.9498 5.05029C15.637 3.73754 13.8565 3 12 3C10.1435 3 8.36305 3.73754 7.05029 5.05029C5.73754 6.36305 5 8.14348 5 10V11" stroke="#999999" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg></span>
                    <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 47 47" xml:space="preserve" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M24.104,41.577c-0.025,0-0.053-0.001-0.078-0.001c-1.093-0.035-2.025-0.802-2.271-1.867l-5.46-23.659l-3.199,8.316 c-0.357,0.93-1.252,1.544-2.249,1.544H2.41c-1.331,0-2.41-1.079-2.41-2.41c0-1.331,1.079-2.41,2.41-2.41h6.78l5.433-14.122 c0.38-0.989,1.351-1.612,2.418-1.54c1.057,0.074,1.941,0.831,2.18,1.863l5.186,22.474l4.618-15.394 c0.276-0.923,1.078-1.592,2.035-1.702c0.953-0.107,1.889,0.36,2.365,1.198l4.127,7.222h7.037c1.331,0,2.41,1.079,2.41,2.41 c0,1.331-1.079,2.41-2.41,2.41h-8.436c-0.865,0-1.666-0.463-2.094-1.214l-2.033-3.559l-5.616,18.722 C26.104,40.88,25.164,41.577,24.104,41.577z"></path> </g> </g> </g></svg>
                    <span><?=htmlspecialchars($c['duracion']) ?></span>
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
        //añadir evento al botón de añadir a playlist
        //crear el modal de añadir a playlist
        document.addEventListener('DOMContentLoaded', () => {
            const btnAbrir = document.getElementById('btn-nueva-playlist');
            const modal = document.getElementById('modal-nueva-playlist');
            const cerrar = document.getElementById('cerrar-nueva-playlist');

            btnAbrir.addEventListener('click', () => {
                modal.style.display = 'flex';
            });

            cerrar.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                modal.style.display = 'none';
                }
            });
        });
    </script>
    <script>
        //variable global para almacenar las canciones del álbum
        //se inicializa con las canciones obtenidas del servidor
        window.albumActual = <?= json_encode($canciones, JSON_UNESCAPED_UNICODE) ?>;
    </script>

<!--
Ejemplo de uso AJAX desde el frontend:
Cuando el usuario hace clic en un álbum, el frontend carga este archivo por AJAX y muestra el contenido:

fetch('/ajax/album.php?id=5')
  .then(res => res.text())
  .then(html => {
      document.getElementById('contenido-principal').innerHTML = html;
      
  });

Esto permite mostrar las canciones del álbum dinámicamente sin recargar la página.
-->