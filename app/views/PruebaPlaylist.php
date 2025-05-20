<?php
require_once '../config/Conexion_BBDD.php';
require_once '../app/models/usuario.php';

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: /login");
    exit;
}

$usuarioModel = new Usuario($pdo);
$id_usuario = $usuarioModel->getIdByEmail($_SESSION['email']);

// Obtener playlists del usuario
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Mis Playlists</h2>
<?php if (empty($playlists)): ?>
    <p>No tienes playlists aún.</p>
    <form method="POST" action="/ajax/crear_playlist.php">
        <input type="text" name="nombre" placeholder="Nombre de la playlist" required>
        <button type="submit">Crear Playlist</button>
    </form>
<?php else: ?>
    <ul>
        <?php foreach ($playlists as $p): ?>
            <li><a href="#" class="enlace-playlist" data-id="<?= $p['id_playlist'] ?>"><?= htmlspecialchars($p['nombre']) ?></a></li>
        <?php endforeach; ?>
    </ul>
    <button onclick="mostrarCrearPlaylist()">+ Crear nueva playlist</button>
    <div id="form-crear" style="display: none;">
        <form method="POST" action="/ajax/crear_playlist.php">
            <input type="text" name="nombre" placeholder="Nombre de la playlist" required>
            <button type="submit">Crear</button>
        </form>
    </div>
<?php endif; ?>

<div id="contenido-playlist"></div>

<script>
function mostrarCrearPlaylist() {
    document.getElementById('form-crear').style.display = 'block';
}

// AJAX para ver canciones de la playlist
document.querySelectorAll('.enlace-playlist').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const id = this.dataset.id;

        fetch('/ajax/ver_playlist.php?id=' + id)
            .then(res => res.text())
            .then(html => {
                document.getElementById('contenido-playlist').innerHTML = html;
            });
    });
});
</script>
