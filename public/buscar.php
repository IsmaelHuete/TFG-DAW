<?php
require_once '../config/Conexion_BBDD.php';
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$q = trim($q);

$resultado = [
    'canciones' => [],
    'artistas' => [],
    'albums' => []
];

if ($q !== '') {
    $like = '%' . strtolower($q) . '%';

    // Canciones + ID del álbum
    $stmt = $pdo->prepare("
        SELECT canciones.id_cancion, canciones.nombre_c, canciones.id_album, albums.nombre AS album
        FROM canciones
        LEFT JOIN albums ON canciones.id_album = albums.id_album
        WHERE LOWER(canciones.nombre_c) LIKE ?
        LIMIT 10
    ");
    $stmt->execute([$like]);
    $canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Añadir la ruta de imagen basada en id_album
    foreach ($canciones as &$cancion) {
        $id_album = $cancion['id_album'];
        $cancion['foto_album'] = $id_album ? "/uploads/foto-album/{$id_album}.jpg" : "/uploads/foto-album/default.jpg";
    }
    $resultado['canciones'] = $canciones;

    // Artistas
    $stmt = $pdo->prepare("
        SELECT id_usuario, nombre
        FROM usuario
        WHERE id_usuario IN (SELECT id_usuario FROM artista)
        AND LOWER(nombre) LIKE ?
        LIMIT 10
    ");
    $stmt->execute([$like]);
    $resultado['artistas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Álbumes
    $stmt = $pdo->prepare("
        SELECT albums.id_album, albums.nombre, usuario.nombre AS artista
        FROM albums
        JOIN usuario ON albums.id_usuario = usuario.id_usuario
        WHERE LOWER(albums.nombre) LIKE ?
        LIMIT 10
    ");
    $stmt->execute([$like]);
    $resultado['albums'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($resultado);
