<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json'); // Esto antes de cualquier salida
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/usuario.php';



if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

try {
    $usuarioModel = new Usuario($pdo);
    $id_usuario = $usuarioModel->getIdByEmail($_SESSION['email']);

    $stmt = $pdo->prepare("SELECT id_playlist, nombre, foto FROM playlists WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($playlists);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en el servidor"]);
}
