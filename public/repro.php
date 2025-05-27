<?php
require_once '../config/Conexion_BBDD.php';
session_start();

header('Content-Type: application/json');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

// Obtener el ID del usuario por su email
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$id_usuario = $stmt->fetchColumn();

if (!$id_usuario) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuario no válido']);
    exit;
}

// Obtener ID de canción del body
$input = json_decode(file_get_contents('php://input'), true);
$id_cancion = isset($input['id_cancion']) ? (int) $input['id_cancion'] : null;

if (!$id_cancion || $id_cancion <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de canción no recibido o inválido']);
    exit;
}

// Insertar reproducción en tabla escuchan
$stmt = $pdo->prepare("
    INSERT INTO escuchan (id_usuario, id_cancion, fecha)
    VALUES (?, ?, NOW())
");
$stmt->execute([$id_usuario, $id_cancion]);

echo json_encode(['status' => 'ok']);
