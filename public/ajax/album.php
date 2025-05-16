<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';

$id_album = $_GET['id'] ?? null;

if (!$id_album || !is_numeric($id_album)) {
    echo "Álbum no especificado.";
    exit;
}

// Obtener info del álbum
$stmt = $pdo->prepare("
    SELECT albums.nombre AS nombre_album, usuario.nombre AS nombre_artista
    FROM albums
    JOIN usuario ON albums.id_usuario = usuario.id_usuario
    WHERE albums.id_album = ?
");
$stmt->execute([$id_album]);
$album = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$album) {
    echo "Álbum no encontrado.";
    exit;
}

// Obtener canciones
$stmt = $pdo->prepare("SELECT id_cancion, nombre_c FROM canciones WHERE id_album = ?");
$stmt->execute([$id_album]);
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$foto_album = "/uploads/foto-album/{$id_album}.jpg";
?>

<h2><?= htmlspecialchars($album['nombre_album']) ?></h2>
<p><strong>Artista:</strong> <?= htmlspecialchars($album['nombre_artista']) ?></p>

<h3>Canciones:</h3>
<?php foreach ($canciones as $c): ?>
    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <img src="<?= $foto_album ?>" alt="Carátula del álbum" style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px; border-radius: 8px;">
        <div>
            <strong><?= htmlspecialchars($c['nombre_c']) ?></strong><br>
            <audio controls data-id="<?= $c['id_cancion'] ?>">
                <source src="/uploads/canciones/<?= htmlspecialchars($c['id_cancion']) ?>.mp3" type="audio/mpeg">
                Tu navegador no soporta audio.
            </audio>
        </div>
    </div>
<?php endforeach; ?>

<script>
// Solo permitir un reproductor activo
document.addEventListener('play', function (e) {
    const audios = document.querySelectorAll('audio');
    audios.forEach(audio => {
        if (audio !== e.target) {
            audio.pause();
        }
    });
}, true);

// Registrar reproducción
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
</script>
