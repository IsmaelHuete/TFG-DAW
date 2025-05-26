<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
session_start();

if (!isset($_SESSION['email']) || empty($_POST['nombre_playlist'])) {
    header('Location: /index');
    exit;
}

$nombre = trim($_POST['nombre_playlist']);
$email = $_SESSION['email'];

// Obtener ID del usuario
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$email]);
$id_usuario = $stmt->fetchColumn();

if (!$id_usuario) {
    die("Usuario no válido.");
}

$nombre_foto = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (in_array($extension, $permitidas)) {
        $nombre_foto = uniqid('playlist_') . '.' . $extension;

        // ✅ Ruta correcta desde public/ajax hasta public/uploads/foto-playlist
        $carpeta = __DIR__ . '/../uploads/foto-playlist';
        $destino = $carpeta . '/' . $nombre_foto;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            die("❌ Error al guardar la imagen.");
        }
    } else {
        die("❌ Formato no permitido.");
    }
}

// Insertar en la base de datos
if ($nombre_foto) {
    $stmt = $pdo->prepare("INSERT INTO playlists (nombre, id_usuario, foto) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $id_usuario, $nombre_foto]);
} else {
    $stmt = $pdo->prepare("INSERT INTO playlists (nombre, id_usuario) VALUES (?, ?)");
    $stmt->execute([$nombre, $id_usuario]);
}

header("Location: /index");
exit;
