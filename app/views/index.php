
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

<div id="reproductor-persistente" class="reproductor-persistente">
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
    <input type="range" id="barra-progreso" value="0" step="1">
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

            // Canciones
            /* if (data.canciones.length > 0) {
                html += '<h3>Canciones</h3><ul style="list-style: none; padding: 0;">';
                data.canciones.forEach(c => {
                    html += `
                        <li style="display: flex; align-items: center; margin-bottom: 15px;">
                            <img src="${c.foto_album}" alt="Carátula" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 8px;">
                            <div>
                                <strong>${c.nombre_c}</strong> (Álbum: ${c.album})<br>
                                <audio controls>
                                    <source src="/uploads/canciones/${c.id_cancion}.mp3" type="audio/mpeg">
                                    Tu navegador no soporta audio.
                                </audio>
                            </div>
                        </li>
                    `;
                });
                html += '</ul>';
            } */

            // Artistas
           /*  if (data.artistas.length > 0) {
                html += '<h3>Artistas</h3><ul>';
                data.artistas.forEach(a => {
                html += `<li><a href="#" class="enlace-artista" data-id="${a.id_usuario}">${a.nombre}</a></li>`;
            });
                html += '</ul>';
            }
 */
            // Álbumes
            // Álbumes
            if (data.albums.length > 0) {
                html += '<h3>Álbumes</h3><ul style="display:flex; flex-wrap: wrap; gap: 10px; list-style: none;">';
                data.albums.forEach(a => {
                    html += `
                        <li class="card-album" style="width: 120px; cursor: pointer;" data-id="${a.id_album}">
                            <img src="/uploads/foto-album/${a.id_album}.jpg" alt="${a.nombre}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                            <p>${a.nombre}</p>
                        </li>
                    `;
                });
                html += '</ul>';
            }


            if (!html) html = '<p>No se encontraron resultados.</p>';

            document.getElementById('resultados').innerHTML = html;

            // Delegar eventos para navegación AJAX
            document.querySelectorAll('.card-album').forEach(card => {
            card.addEventListener('click', function () {
                const id = this.dataset.id;

                fetch('/ajax/album.php?id=' + id)
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('contenido-principal').innerHTML = html;
                        activarEventosAudio();

                        // ✅ Después de insertar el HTML, marcar corazones
                        fetch('/ajax/canciones_en_playlist.php')
                            .then(res => res.json())
                            .then(ids => {
                                document.querySelectorAll('.add-playlist').forEach(div => {
                                    const id = div.dataset.id;
                                    if (ids.includes(parseInt(id))) {
                                        div.querySelector('.corazon-blanco').style.display = 'none';
                                        div.querySelector('.corazon-gradient').style.display = 'inline';
                                    }
                                });
                            });
                    });

            });
        });

            // Y para artistas:
            document.querySelectorAll('.enlace-artista').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.dataset.id;

                    fetch('/ajax/artista.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('contenido-principal').innerHTML = html;
                            activarEventosAudio();
                        });
                });
            });
        });
});
// Asegura que solo se reproduzca una canción a la vez
document.addEventListener('play', function (e) {
    const audios = document.querySelectorAll('audio');
    audios.forEach(audio => {
        if (audio !== e.target) {
            audio.pause();
        }
    });
}, true);
// Función que activa el seguimiento de reproducción (se llamará tras cada carga AJAX)
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
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id_cancion: idCancion })
                });
            }
        });
    });
}
</script>


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
?>

<div class="mis-playlists" style="margin-top: 30px;">
    <h2>Mis playlists</h2>
    <?php if (count($playlists) === 0): ?>
        <p>¡Crea tu primera playlist!</p>
            <?php endif; ?>

     
        
        <form method="POST" style="margin-top: 15px;">
            <input type="text" name="nombre_playlist" placeholder="Nombre de la playlist" required>
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
    
</div>