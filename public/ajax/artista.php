<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "Artista no válido.";
    exit;
}

// Obtener datos del artista
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

// Obtener álbumes
$stmt = $pdo->prepare("SELECT id_album, nombre FROM albums WHERE id_usuario = ?");
$stmt->execute([$id]);
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<script>
document.querySelectorAll('.enlace-album').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const id = this.dataset.id;

        fetch('/ajax/album.php?id=' + id)
            .then(res => res.text())
            .then(html => {
                document.getElementById('contenido-principal').innerHTML = html;
            });
    });
});
</script>
