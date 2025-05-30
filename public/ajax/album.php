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
    <div class="fila-encabezados" style="display: flex; font-weight: bold; margin-bottom: 10px;">
        <div style="flex: 2; text-align: left; margin-left: 25px;">Canción</div>
        <div style="flex: 2; text-align: center; margin-right: 42px;">Álbum</div>
        <div style="flex: 1; text-align: right;">Duración</div>
    </div>
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
                    <span><?= htmlspecialchars($album['nombre_album']) ?></span>
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