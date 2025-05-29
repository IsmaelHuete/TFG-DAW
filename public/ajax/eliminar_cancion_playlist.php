<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/playlist.php';

$id_cancion = $_POST['id_cancion'] ?? null;
$id_playlist = $_POST['id_playlist'] ?? null;

if (!$id_cancion || !$id_playlist || !is_numeric($id_cancion) || !is_numeric($id_playlist)) {
    http_response_code(400); // Añade esto para respuesta clara
    echo "error";
    exit;
}

$playlistModel = new Playlist($pdo);
$ok = $playlistModel->eliminarCancion($id_playlist, $id_cancion);

if ($ok) {
    echo "ok";
} else {
    http_response_code(500);
    echo "error";
}
