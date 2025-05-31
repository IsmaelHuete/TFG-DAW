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
    SELECT c.id_cancion, c.nombre_c, c.duracion, c.id_album, u.nombre AS artista
    FROM cancion_playlist cp
    JOIN canciones c ON cp.id_cancion = c.id_cancion
    JOIN usuario u ON c.id_usuario = u.id_usuario
    WHERE cp.id_playlist = ?
");
$stmt->execute([$id_playlist]);
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <svg viewBox="0 0 24 24" width="20px" height="20px"><path fill="#FF4EC4" d="M8 5v14l11-7z"/></svg>
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
                <button type="button" class="btn-eliminar" title="Eliminar de la playlist">🗑️</button>
            </form>

        </div>
    </div>
</div>
<?php endforeach; ?>

<div id="modal-confirmacion" class="modal1 oculto">
    <div class="modal-contenido">
        <p>¿Quieres eliminar esta canción de la playlist?</p>
        <div class="botones">
            <button id="btn-cancelar" class="btn">Cancelar</button>
            <button id="btn-confirmar" class="btn btn-rojo">Eliminar</button>
        </div>
    </div>
</div>


<script>
    window.playlistActual = <?= json_encode($canciones, JSON_UNESCAPED_UNICODE) ?>;
    window.albumActual = undefined;
    window.artistaActual = undefined;
    activarEventosAudio();
    activarResaltadoCancion();

    const modal = document.getElementById('modal-confirmacion');
    const btnCancelar = document.getElementById('btn-cancelar');
    const btnConfirmar = document.getElementById('btn-confirmar');

    let formPendiente = null;

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


</script>

