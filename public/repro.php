<?php
require_once '../config/Conexion_BBDD.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

// Obtener ID del usuario desde el email
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$id_usuario = $stmt->fetchColumn();

if (!$id_usuario) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuario no válido']);
    exit;
}

// Obtener datos del body JSON
$input = json_decode(file_get_contents('php://input'), true);
$id_cancion = $input['id_cancion'] ?? null;

if (!$id_cancion) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de canción no recibido']);
    exit;
}

// Insertar reproducción
$stmt = $pdo->prepare("
    INSERT INTO escuchan (id_usuario, id_cancion, fecha)
    VALUES (?, ?, NOW())
");
$stmt->execute([$id_usuario, $id_cancion]);
$stmt = $pdo->prepare("UPDATE canciones SET reproducciones = reproducciones + 1 WHERE id_cancion = ?");
$stmt->execute([$id_cancion]);
echo json_encode(['status' => 'ok']);
