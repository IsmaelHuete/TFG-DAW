<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/playlist.php';

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo "no-auth";
    exit;
}

$id_cancion = $_POST['id_cancion'] ?? null;
$id_playlist = $_POST['id_playlist'] ?? null;

if (!$id_cancion || !$id_playlist) {
    http_response_code(400);
    echo "missing-data";
    exit;
}

$playlistModel = new Playlist($pdo);

// Verificar si ya existe
$canciones = $playlistModel->getCanciones($id_playlist);
$yaExiste = false;

foreach ($canciones as $cancion) {
    if ((int)$cancion['id_cancion'] === (int)$id_cancion) {
        $yaExiste = true;
        break;
    }
}

if ($yaExiste) {
    echo "exists";
    exit;
}

if ($playlistModel->addCancion($id_playlist, $id_cancion)) {
    echo "ok";
} else {
    http_response_code(500);
    echo "error";
}
