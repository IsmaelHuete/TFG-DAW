<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/playlist.php';
$id_playlist = $_POST['id_playlist'] ?? null;

if (!$id_playlist || !is_numeric($id_playlist)) {
    http_response_code(400);
    echo "error";
    exit;
}

$playlistModel = new Playlist($pdo);
$ok = $playlistModel->eliminar($id_playlist);

echo $ok ? 'ok' : 'error';
