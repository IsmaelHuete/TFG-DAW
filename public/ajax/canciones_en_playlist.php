<?php
session_start();

require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/usuario.php';
require_once __DIR__ . '/../../app/models/playlist.php';

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

$usuarioModel = new Usuario($pdo);
$playlistModel = new Playlist($pdo);
$id_usuario = $usuarioModel->getIdByEmail($_SESSION['email']);

$canciones = $playlistModel->getCancionesPorUsuario($id_usuario);

echo json_encode($canciones);
