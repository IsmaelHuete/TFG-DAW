<?php
session_start();
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/usuario.php';


header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

$usuarioModel = new Usuario($pdo);
$id_usuario = $usuarioModel->getIdByEmail($_SESSION['email']);

$stmt = $pdo->prepare("
    SELECT DISTINCT id_cancion
    FROM cancion_playlist cp
    JOIN playlists p ON cp.id_playlist = p.id_playlist
    WHERE p.id_usuario = ?
");
$stmt->execute([$id_usuario]);
$canciones = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($canciones);
