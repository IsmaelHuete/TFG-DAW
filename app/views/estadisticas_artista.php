<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
session_start();
// Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo "Acceso no autorizado. Inicia sesión.";
    exit;
}

$id_artista = $_GET['id'] ?? null;

// Solo el propio artista puede ver su página de estadísticas
if (!$id_artista || !is_numeric($id_artista) || $_SESSION['id_usuario'] != $id_artista) {
    echo "Acceso denegado.";
    exit;
}


// Datos generales
// Datos generales SIN duplicaciones
$stmt = $pdo->prepare("
    SELECT 
        u.nombre AS nombre_artista,
        u.foto_perfil,
        (SELECT COUNT(*) FROM canciones WHERE id_usuario = u.id_usuario) AS total_canciones,
        (SELECT COUNT(*) FROM albums WHERE id_usuario = u.id_usuario) AS total_albums,
        (SELECT COALESCE(SUM(reproducciones), 0) FROM canciones WHERE id_usuario = u.id_usuario) AS total_reproducciones
    FROM usuario u
    JOIN artista ar ON u.id_usuario = ar.id_usuario
    WHERE u.id_usuario = ?
");

$stmt->execute([$id_artista]);
$datos = $stmt->fetch();

if (!$datos) {
    echo "Artista no encontrado.";
    exit;
}

// Canciones
$stmtCanciones = $pdo->prepare("
    SELECT 
        c.nombre_c AS nombre_cancion,
        c.duracion,
        c.reproducciones,
        a.nombre AS nombre_album
    FROM canciones c
    LEFT JOIN albums a ON c.id_album = a.id_album
    WHERE c.id_usuario = ?
    ORDER BY c.reproducciones DESC
");
$stmtCanciones->execute([$id_artista]);
$canciones = $stmtCanciones->fetchAll();

// Álbumes
$stmtAlbums = $pdo->prepare("
    SELECT 
        a.id_album,
        a.nombre AS nombre_album,
        a.genero,
        COUNT(c.id_cancion) AS total_canciones,
        COALESCE(SUM(c.reproducciones), 0) AS total_reproducciones
    FROM albums a
    LEFT JOIN canciones c ON a.id_album = c.id_album
    WHERE a.id_usuario = ?
    GROUP BY a.id_album
");
$stmtAlbums->execute([$id_artista]);
$albums = $stmtAlbums->fetchAll();
?>

<!-- Aquí comienza el HTML -->
<h1><?= htmlspecialchars($datos['nombre_artista']) ?> - Estadísticas</h1>
<p>Total canciones: <?= $datos['total_canciones'] ?></p>
<p>Total álbumes: <?= $datos['total_albums'] ?></p>
<p>Reproducciones totales: <?= number_format($datos['total_reproducciones']) ?></p>

<h2>Canciones</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Nombre</th>
        <th>Álbum</th>
        <th>Duración</th>
        <th>Reproducciones</th>
    </tr>
    <?php foreach ($canciones as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nombre_cancion']) ?></td>
            <td><?= htmlspecialchars($c['nombre_album'] ?? 'Single') ?></td>
            <td><?= $c['duracion'] ?></td>
            <td><?= number_format($c['reproducciones']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
