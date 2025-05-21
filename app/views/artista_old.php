<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';

$id_artista = $_GET['id'] ?? null;

if (!$id_artista) {
    echo "Artista no especificado.";
    exit;
}

// Obtener datos del artista
$stmt = $pdo->prepare("
    SELECT nombre FROM usuario WHERE id_usuario = ? AND id_usuario IN (SELECT id_usuario FROM artista)
");
$stmt->execute([$id_artista]);
$artista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artista) {
    echo "Artista no encontrado.";
    exit;
}

// Obtener sus álbumes
$stmt = $pdo->prepare("
    SELECT id_album, nombre FROM albums WHERE id_usuario = ?
");
$stmt->execute([$id_artista]);
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Artista: <?= htmlspecialchars($artista['nombre']) ?></h2>
<h3>Álbumes:</h3>
<ul>
<?php foreach ($albums as $a): ?>
    <li><a href="#" class="enlace-album" data-id="<?= $a['id_album'] ?>"><?= htmlspecialchars($a['nombre']) ?></a></li>
<?php endforeach; ?>
</ul>

<script>
// Al empezar una reproducción, parar todas las demás
document.addEventListener('play', function (e) {
    const audios = document.querySelectorAll('audio');
    audios.forEach(audio => {
        if (audio !== e.target) {
            audio.pause();
        }
    });
}, true);
document.querySelectorAll('audio').forEach(audio => {
    let reproducido = false;

    audio.addEventListener('timeupdate', () => {
        // Solo registrar una vez
        if (reproducido) return;

        const segundos = audio.currentTime;
        const porcentaje = segundos / audio.duration;

        // Se registra si han pasado al menos 10 segundos o el 30%
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

// Hacer que los enlaces de álbum dentro de artista también funcionen con AJAX
document.querySelectorAll('.enlace-album').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const id = this.dataset.id;

        fetch('/views/album.php?id=' + id)
            .then(res => res.text())
            .then(html => {
                document.getElementById('contenido-principal').innerHTML = html;
            });
    });
});
</script>
