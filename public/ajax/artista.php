<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "Artista no válido.";
    exit;
}

// Obtener nombre del artista
$stmt = $pdo->prepare("
    SELECT nombre FROM usuario 
    WHERE id_usuario = ? AND id_usuario IN (SELECT id_usuario FROM artista)
");
$stmt->execute([$id]);
$artista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artista) {
    echo "Artista no encontrado.";
    exit;
}

// Obtener álbumes del artista
$stmt = $pdo->prepare("SELECT id_album, nombre FROM albums WHERE id_usuario = ?");
$stmt->execute([$id]);
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las canciones del artista (de cualquier álbum)
$stmt = $pdo->prepare("
    SELECT c.id_cancion, c.nombre_c, c.id_album, a.nombre AS nombre_album
    FROM canciones c
    JOIN albums a ON c.id_album = a.id_album
    WHERE a.id_usuario = ?
");
$stmt->execute([$id]);
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Artista: <?= htmlspecialchars($artista['nombre']) ?></h2>

<?php if (count($albums) > 0): ?>
    <h3>Álbumes:</h3>
    <ul>
        <?php foreach ($albums as $a): ?>
            <li>
                <a href="#" class="enlace-album" data-id="<?= $a['id_album'] ?>">
                    <?= htmlspecialchars($a['nombre']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Este artista no tiene álbumes aún.</p>
<?php endif; ?>

<?php if (count($canciones) > 0): ?>
    <h3>Canciones:</h3>
    <ul style="list-style: none; padding: 0;">
        <?php foreach ($canciones as $c): ?>
            <?php $foto_album = "/uploads/foto-album/{$c['id_album']}.jpg"; ?>
            <li style="display: flex; align-items: center; margin-bottom: 15px;">
                <img src="<?= $foto_album ?>" alt="Carátula" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 8px;">
                <div>
                    <strong><?= htmlspecialchars($c['nombre_c']) ?></strong> (Álbum: <?= htmlspecialchars($c['nombre_album']) ?>)<br>
                    <audio controls data-id="<?= $c['id_cancion'] ?>">
                        <source src="/uploads/stream.php?file=<?= $c['id_cancion'] ?>.mp3" type="audio/mpeg">
                        Tu navegador no soporta audio.
                    </audio>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Este artista no tiene canciones aún.</p>
<?php endif; ?>

<!-- Activar AJAX para álbumes y eventos de reproducción -->
<script>
document.querySelectorAll('.enlace-album').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const id = this.dataset.id;

        fetch('/ajax/album.php?id=' + id)
            .then(res => res.text())
            .then(html => {
                document.getElementById('contenido-principal').innerHTML = html;
                activarEventosAudio();
            });
    });
});

// Activar eventos para los audios cargados
activarEventosAudio();
</script>
