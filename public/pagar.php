<?php
session_start();
require_once __DIR__ . '/../config/Conexion_BBDD.php';

if (!isset($_SESSION['email'])) {
    header('Location: /login');
    exit;
}

$plan = $_POST['plan'] ?? 'mensual';

$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$id_usuario = $stmt->fetchColumn();

if (!$id_usuario) {
    die("Usuario no encontrado");
}

$stmt = $pdo->prepare("
    UPDATE usuario
    SET tipo_plan = 'premium'
    WHERE id_usuario = ?
");
$stmt->execute([$id_usuario]);

header("Location: /pago_exito");
exit;
