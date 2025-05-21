<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/usuario.php';
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

// Verificar si ya existe la relación
$stmt = $pdo->prepare("SELECT 1 FROM cancion_playlist WHERE id_cancion = ? AND id_playlist = ?");
$stmt->execute([$id_cancion, $id_playlist]);
$existe = $stmt->fetch();

if ($existe) {
    echo "exists";
    exit;
}

// Insertar
$stmt = $pdo->prepare("INSERT INTO cancion_playlist (id_cancion, id_playlist) VALUES (?, ?)");
if ($stmt->execute([$id_cancion, $id_playlist])) {
    echo "ok";
} else {
    http_response_code(500);
    echo "error";
}
