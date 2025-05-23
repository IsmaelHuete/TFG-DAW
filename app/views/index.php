
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/header1.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <?php 
        include ("layouts/header1.php");
    ?>
    <main>
        <div class="container">
            <input type="text" id="buscador" placeholder="Buscar canción, artista o álbum..." autocomplete="off">
            <div class="reproductor">
                <div id="resultados"></div>
                <div id="contenido-principal"></div>
            </div>
        </div>
    </main>
    
<?php 
        include ("layouts/footer.php");
    ?>
</div>

<div id="reproductor-persistente" class="reproductor-persistente" style="display: none;">
    <div class="reproductor-izquierda">
        <img id="cover-img" src="/img/image-brand.png" alt="Portada" style="width:50px;height:50px;" />
        <div>
            <div id="titulo-cancion">Sin canción</div>
            <div id="nombre-artista">Desconocido</div>
        </div>
    </div>
    <div class="reproductor-centro">
    <div class="controles">
        <button id="btn-prev">⏮️</button>
        <button id="btn-play">▶️</button>
        <button id="btn-next">⏭️</button>
    </div>
    <input type="range" id="barra-progreso" value="0" step="0.01">
</div>
    <div class="reproductor-derecha">
        <input type="range" id="volumen" min="0" max="1" step="0.01" value="1">
    </div>
    <audio id="audio-player" src=""></audio>
</div>


<!-- Modal para seleccionar playlist -->
<div id="modal-playlists" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="cerrar-modal" style="float:right;cursor:pointer;">&times;</span>
        <h3>Elige una playlist</h3>
        <ul id="lista-playlists"></ul>
    </div>
</div>


<script src="js/header.js"></script>
<script src="js/home.js"></script>
<script src="js/playlist-modal.js"></script>
<script src="js/reproductor.js"></script>
</body>
</html>


<script>
document.getElementById('buscador').addEventListener('keyup', function () {
    const query = this.value.trim();

    if (query.length < 2) {
        document.getElementById('resultados').innerHTML = '';
        return;
    }

    fetch('/buscar.php?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            let html = '';
 

                if (data.canciones.length > 0) {

// ARTISTAS
            if (data.artistas.length > 0) {
                html += '<h2>Artistas</h2><ul style="display: flex; flex-wrap: wrap; gap: 10px; list-style: none;">';
                data.artistas.forEach(a => {
                    const rutaFoto = a.foto_perfil ? `/uploads/perfiles/${a.foto_perfil}` : '/uploads/perfiles/default.jpg';

                    html += `
                        <li class="card-artista" style="width: 120px; cursor: pointer;" data-id="${a.id_usuario}">
                            <img src="${rutaFoto}" alt="${a.nombre}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                            <p style="text-align: center;">${a.nombre}</p>
                        </li>
                    `;
                });
                html += '</ul>';
            }

            // ÁLBUMES
            if (data.albums.length > 0) {
                html += '<h2>Álbumes</h2><ul style="display: flex; flex-wrap: wrap; gap: 10px; list-style: none;">';
                data.albums.forEach(a => {
                    html += `
                        <li class="card-album" style="width: 120px; cursor: pointer;" data-id="${a.id_album}">
                            <img src="/uploads/foto-album/${a.id_album}.jpg" alt="${a.nombre}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                            <p style="text-align: center;">${a.nombre}</p>
                        </li>
                    `;
                });
                html += '</ul>';
            }

            // Canciones
            // CANCIONES como cards visuales
                    html += '<h2>Canciones</h2>';
                    data.canciones.forEach(c => {
                        html += `
                                <div class="container-cancion" style="display: flex;">
                                    <div class="add-playlist" data-id="${c.id_cancion}">
                                        <svg class="corazon-blanco" width="20px" height="20px" viewBox="-2.08 -2.08 20.16 20.16" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer;">
                                            <path d="M1.24264 8.24264L8 15L14.7574 8.24264C15.553 7.44699 16 6.36786 16 5.24264V5.05234C16 2.8143 14.1857 1 11.9477 1C10.7166 1 9.55233 1.55959 8.78331 2.52086L8 3.5L7.21669 2.52086C6.44767 1.55959 5.28338 1 4.05234 1C1.8143 1 0 2.8143 0 5.05234V5.24264C0 6.36786 0.44699 7.44699 1.24264 8.24264Z" fill="#ffffff"></path>
                                        </svg>
                                        <svg class="corazon-gradient" width="20px" height="20px" viewBox="-2.08 -2.08 20.16 20.16" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                            <defs>
                                                <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                                                    <stop offset="0%" style="stop-color:#481B9A; stop-opacity:1" />
                                                    <stop offset="100%" style="stop-color:#FF4EC4; stop-opacity:1" />
                                                </linearGradient>
                                            </defs>
                                            <path d="M1.24264 8.24264L8 15L14.7574 8.24264C15.553 7.44699 16 6.36786 16 5.24264V5.05234C16 2.8143 14.1857 1 11.9477 1C10.7166 1 9.55233 1.55959 8.78331 2.52086L8 3.5L7.21669 2.52086C6.44767 1.55959 5.28338 1 4.05234 1C1.8143 1 0 2.8143 0 5.05234V5.24264C0 6.36786 0.44699 7.44699 1.24264 8.24264Z" fill="url(#grad)" />
                                        </svg>
                                    </div>
                                    <div class="img-wrapper">
                                        <img src="${c.foto_album}" alt="Carátula">
                                        <div class="hover-overlay"
                                            data-src="/uploads/canciones/${c.id_cancion}.mp3"
                                            data-title="${c.nombre_c}"
                                            data-artist="${c.artista || 'Desconocido'}"
                                            data-cover="${c.foto_album}">
                                            <svg ...>▶</svg>
                                        </div>
                                    </div>
                                    <div class="cancion" style="flex-grow: 1;">
                                        <div class="info-cancion">
                                            <strong>${c.nombre_c}</strong>
                                            <span>${c.artista || 'Desconocido'}</span>
                                        </div>
                                        <div class="stat-cancion">
                                            <span>${c.reproducciones ?? 0}</span>
                                            <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 47 47" xml:space="preserve" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M24.104,41.577c-0.025,0-0.053-0.001-0.078-0.001c-1.093-0.035-2.025-0.802-2.271-1.867l-5.46-23.659l-3.199,8.316 c-0.357,0.93-1.252,1.544-2.249,1.544H2.41c-1.331,0-2.41-1.079-2.41-2.41c0-1.331,1.079-2.41,2.41-2.41h6.78l5.433-14.122 c0.38-0.989,1.351-1.612,2.418-1.54c1.057,0.074,1.941,0.831,2.18,1.863l5.186,22.474l4.618-15.394 c0.276-0.923,1.078-1.592,2.035-1.702c0.953-0.107,1.889,0.36,2.365,1.198l4.127,7.222h7.037c1.331,0,2.41,1.079,2.41,2.41 c0,1.331-1.079,2.41-2.41,2.41h-8.436c-0.865,0-1.666-0.463-2.094-1.214l-2.033-3.559l-5.616,18.722 C26.104,40.88,25.164,41.577,24.104,41.577z"></path> </g> </g> </g></svg>
                                            <span>${c.duracion ?? '00:00'}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                    });
                    html += '</ul>';
                }
            

            // Playliost
           /*  if (data.playlist.length > 0) {
                html += '<h3>Mis playlists</h3><ul style="display: flex; flex-wrap: wrap; gap: 10px; list-style: none; padding: 0;">';
                data.playlist.forEach(a => {
                    html += `
                        <li class="card-album" style="width: 120px; cursor: pointer;" data-id="${a.id_album}">
                            <img src="/uploads/foto-album/${a.id_album}.jpg" alt="${a.nombre}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                            <p style="text-align: center;">${a.nombre}</p>
                        </li>
                    `;
                });
                html += '</ul>';
            } */


            if (!html) html = '<p>No se encontraron resultados.</p>';






            document.getElementById('resultados').innerHTML = html;
            

            // EVENTO ÁLBUM
            document.querySelectorAll('.card-album').forEach(card => {
                card.addEventListener('click', function () {
                    const id = this.dataset.id;

                    fetch('/ajax/album.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('contenido-principal').innerHTML = html;
                            activarEventosAudio();
                            activarResaltadoCancion();
                            marcarCorazones();
                        });
                });
            });

            // EVENTO ARTISTA
            document.querySelectorAll('.card-artista').forEach(card => {
                card.addEventListener('click', function () {
                    const id = this.dataset.id;

                    fetch('/ajax/artista.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('contenido-principal').innerHTML = html;
                            activarEventosAudio();
                            activarResaltadoCancion();
                            marcarCorazones();
                        });
                });
            });
        });
});

// FUNCIONES

// Solo una canción sonando a la vez
document.addEventListener('play', function (e) {
    const audios = document.querySelectorAll('audio');
    audios.forEach(audio => {
        if (audio !== e.target) {
            audio.pause();
        }
    });
}, true);

// Seguimiento de reproducción
function activarEventosAudio() {
    document.querySelectorAll('audio').forEach(audio => {
        let reproducido = false;

        audio.addEventListener('timeupdate', () => {
            if (reproducido) return;

            const segundos = audio.currentTime;
            const porcentaje = segundos / audio.duration;

            if (segundos >= 10 || porcentaje >= 0.3) {
                reproducido = true;

                const idCancion = audio.dataset.id;
                fetch('/repro.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_cancion: idCancion })
                });
            }
        });
    });
}

// Corazones activos si ya están en playlist
function marcarCorazones() {
    fetch('/ajax/canciones_en_playlist.php')
        .then(res => res.json())
        .then(ids => {
            // Espera a que el DOM esté listo
            setTimeout(() => {
                document.querySelectorAll('.add-playlist').forEach(div => {
                    const id = div.dataset.id;
                    if (ids.includes(parseInt(id))) {
                        div.querySelector('.corazon-blanco')?.classList.add('oculto');
                        div.querySelector('.corazon-gradient')?.classList.remove('oculto');
                    } else {
                        div.querySelector('.corazon-blanco')?.classList.remove('oculto');
                        div.querySelector('.corazon-gradient')?.classList.add('oculto');
                    }
                });
            }, 50); // espera 50ms tras pintar el HTML
        });
}
document.addEventListener('click', function (e) {
    const overlay = e.target.closest('.hover-overlay');
    if (!overlay) return;

    // Quitar anteriores
    document.querySelectorAll('.container-cancion').forEach(div => {
        div.classList.remove('cancion-activa');
    });

    // Añadir al clicado
    const cancionDiv = overlay.closest('.container-cancion');
    if (cancionDiv) {
        cancionDiv.classList.add('cancion-activa');
    }
});
</script>


<!-- 
<?php
// --- Lógica de playlists al final de index.php ---
require_once '../config/Conexion_BBDD.php';
require_once '../app/models/usuario.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header("Location: /login");
    exit;
}

$usuarioModel = new Usuario($pdo);
$id_usuario = $usuarioModel->getIdByEmail($_SESSION['email']);

// Crear nueva playlist si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_playlist'])) {
    $nombre_playlist = trim($_POST['nombre_playlist']);
    if ($nombre_playlist !== '') {
        $stmt = $pdo->prepare("INSERT INTO playlists (nombre, id_usuario) VALUES (?, ?)");
        $stmt->execute([$nombre_playlist, $id_usuario]);
    }
}

// Obtener playlists del usuario
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> -->

<!-- <div class="mis-playlists" style="margin-top: 30px;">
    <h2>Mis playlists</h2>
    <?php if (count($playlists) === 0): ?>
        <p>¡Crea tu primera playlist!</p>
            <?php endif; ?>

     
        
        <form method="POST" enctype="multipart/form-data" style="margin-top: 15px;">
            <input type="text" name="nombre_playlist" placeholder="Nombre de la playlist" required>
            <input type="file" name="foto" accept="image/*">
            <button type="submit">Crear playlist</button>
        </form>
    
        <ul>
            <?php foreach ($playlists as $playlist): ?>
                <li>
                    <a href="/playlist?id=<?= $playlist['id_playlist'] ?>">
                        <?= htmlspecialchars($playlist['nombre']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    
</div> -->