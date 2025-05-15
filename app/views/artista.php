<?php
require_once  '../config/Conexion_BBDD.php';

$id_artista = $_GET['id'] ?? null;

if (!$id_artista) {
    echo "Artista no encontrado.";
    exit;
}

// Obtener datos del artista
$stmt = $pdo->prepare("SELECT nombre, foto_perfil FROM usuario WHERE id_usuario = ? AND id_usuario IN (SELECT id_usuario FROM artista)");
$stmt->execute([$id_artista]);
$artista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artista) {
    echo "Artista no válido.";
    exit;
}

// Obtener canciones del artista
$stmt = $pdo->prepare("
    SELECT id_cancion, nombre_c
    FROM canciones
    WHERE id_usuario = ?
    ORDER BY id_cancion ASC
");
$stmt->execute([$id_artista]);
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><?= htmlspecialchars($artista['nombre']) ?></h2>

<?php if ($artista['foto_perfil']): ?>
    <img src="/uploads/perfiles/<?= htmlspecialchars($artista['foto_perfil']) ?>" alt="Foto de perfil" width="200">
<?php else: ?>
    <p>Este artista no tiene foto de perfil.</p>
<?php endif; ?>

<h3>Canciones:</h3>
<ul>
    <?php foreach ($canciones as $c): ?>
        <li>
            <strong><?= htmlspecialchars($c['nombre_c']) ?></strong><br>
            <audio controls data-id="<?= $c['id_cancion'] ?>">
                <source src="/uploads/canciones/<?= htmlspecialchars($c['id_cancion']) ?>.mp3" type="audio/mpeg">
                Tu navegador no soporta el reproductor de audio.
            </audio>
        </li>
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
</script>