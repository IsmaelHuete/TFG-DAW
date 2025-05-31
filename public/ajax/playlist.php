<?php
    require_once __DIR__ . '/../../config/Conexion_BBDD.php';

    $id_playlist = $_GET['id'] ?? null;

    if (!$id_playlist || !is_numeric($id_playlist)) {
        echo "Playlist no válida.";
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT p.nombre AS nombre_playlist, p.foto, u.nombre AS creador
        FROM playlists p
        JOIN usuario u ON p.id_usuario = u.id_usuario
        WHERE p.id_playlist = ?
    ");
    $stmt->execute([$id_playlist]);
    $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$playlist) {
        echo "Playlist no encontrada.";
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT DISTINCT c.id_cancion, c.nombre_c, c.duracion, c.id_album, u.nombre AS artista
        FROM cancion_playlist cp
        JOIN canciones c ON cp.id_cancion = c.id_cancion
        JOIN usuario u ON c.id_usuario = u.id_usuario
        WHERE cp.id_playlist = ?
    ");
    $stmt->execute([$id_playlist]);
    $canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Eliminar duplicados por id_cancion
    $ids = [];
    $cancionesUnicas = [];
    foreach ($canciones as $c) {
        if (!in_array($c['id_cancion'], $ids)) {
            $cancionesUnicas[] = $c;
            $ids[] = $c['id_cancion'];
        }
    }
    $canciones = array_values($cancionesUnicas);

    // Añadir datos útiles para el reproductor
    foreach ($canciones as &$c) {
        $c['ruta_mp3'] = "/uploads/stream.php?file={$c['id_cancion']}.mp3";
        $c['foto_album'] = $c['id_album'] ? "/uploads/foto-album/{$c['id_album']}.jpg" : "/uploads/foto-album/default.jpg";
    }
?>

<h2><?= htmlspecialchars($playlist['nombre_playlist']) ?> - <?= htmlspecialchars($playlist['creador']) ?></h2>

<?php foreach ($canciones as $c): ?>
<div class="container-cancion">
    <div class="add-playlist" data-id="<?= $c['id_cancion'] ?>">
        <img src="/img/heart-blanco.png" class="corazon-blanco" style="width: 15px; height: 15px; cursor:pointer;">
        <img class="corazon-gradient oculto" src="/img/heart-rosa.png" width="15px" height="15px">
    </div>

    <div class="img-wrapper">
        <img src="<?= $c['foto_album'] ?>" alt="Portada álbum">
        <div class="hover-overlay"
            data-id="<?= $c['id_cancion'] ?>"
            data-src="<?= $c['ruta_mp3'] ?>"
            data-title="<?= htmlspecialchars($c['nombre_c']) ?>"
            data-artist="<?= htmlspecialchars($c['artista']) ?>"
            data-cover="<?= $c['foto_album'] ?>">
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
            <span><?= htmlspecialchars($c['artista']) ?></span>
        </div>
        <div class="stat-cancion">
            <span><?= $c['reproducciones'] ?? 0 ?></span>
            <span><?= $c['duracion'] ?? '00:00' ?></span>
            <form class="form-eliminar-cancion" data-id-playlist="<?= $id_playlist ?>" data-id-cancion="<?= $c['id_cancion'] ?>">
                <button type="button" class="btn-eliminar" title="Eliminar de la playlist"><svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <defs>
                        <linearGradient id="degradado-x" x1="0" y1="0" x2="18" y2="18" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#481B9A"/>
                        <stop offset="1" stop-color="#FF4EC4"/>
                        </linearGradient>
                    </defs>
                    <path d="M4 4L14 14M14 4L4 14" stroke="url(#degradado-x)" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<div id="modal-confirmacion" class="modal1 oculto">
    <div class="modal-contenido">
        <span class="cerrar-modal">&times;</span>
        <p>¿Quieres eliminar esta canción de la playlist?</p>
        <div class="botones">
            <button id="btn-confirmar" class="btn btn-rojo">Eliminar</button>
            <button id="btn-cancelar" class="btn">Cancelar</button>
        </div>
    </div>
</div>

<script>
    activarEventosAudio();
    activarResaltadoCancion();

    // Usa var para evitar errores de redeclaración
    var modal = document.getElementById('modal-confirmacion');
    var btnCancelar = document.getElementById('btn-cancelar');
    var btnConfirmar = document.getElementById('btn-confirmar');
    var formPendiente = null;

    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-eliminar');
        if (!btn) return;

        formPendiente = btn.closest('.form-eliminar-cancion');
        if (!formPendiente) return;

        modal.classList.remove('oculto');
    });

    btnCancelar.addEventListener('click', () => {
        modal.classList.add('oculto');
        formPendiente = null;
    });

    btnConfirmar.addEventListener('click', () => {
        if (!formPendiente) return;

        const idCancion = formPendiente.dataset.idCancion;
        const idPlaylist = formPendiente.dataset.idPlaylist;

        fetch('ajax/eliminar_cancion_playlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id_cancion=${idCancion}&id_playlist=${idPlaylist}`
        })
        .then(res => {
            if (!res.ok) throw new Error("Error HTTP: " + res.status);
            return res.text();
        })
        .then(response => {
            if (response.trim() === 'ok') {
                formPendiente.closest('.container-cancion')?.remove();
            } else {
                alert('❌ Error inesperado al eliminar la canción.');
            }
        })
        .catch(err => {
            console.error("Error en la petición:", err);
            alert('❌ Error de red real: ' + err.message);
        })
        .finally(() => {
            modal.classList.add('oculto');
            formPendiente = null;
        });
    });

    // Variables globales para el reproductor
    window.albumActual = undefined;
    window.artistaActual = undefined;
    window.playlistActual = <?= json_encode($canciones, JSON_UNESCAPED_UNICODE) ?>;
</script>