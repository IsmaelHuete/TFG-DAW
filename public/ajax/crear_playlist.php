<?php
require_once '../config/Conexion_BBDD.php';
session_start();

if (!isset($_SESSION['email']) || !isset($_POST['nombre'])) {
    header('Location: /index');
    exit;
}

$nombre = $_POST['nombre'];
$email = $_SESSION['email'];

// Obtener id_usuario
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$email]);
$id_usuario = $stmt->fetchColumn();

// Insertar nueva playlist
$stmt = $pdo->prepare("INSERT INTO playlists (nombre, id_usuario) VALUES (?, ?)");
$stmt->execute([$nombre, $id_usuario]);

header('Location: /index');
