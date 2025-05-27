<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/usuario.php';
require_once __DIR__ . '/../../app/models/playlist.php';


if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

try {
    $usuarioModel = new Usuario($pdo);
    $playlistModel = new Playlist($pdo);

    $id_usuario = $usuarioModel->getIdByEmail($_SESSION['email']);
    $playlists = $playlistModel->getPlaylistsByUsuario($id_usuario);

    echo json_encode($playlists);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en el servidor"]);
}
