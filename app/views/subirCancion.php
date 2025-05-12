<?php
require_once '../config/Conexion_BBDD.php';
require_once '../vendor/autoload.php'; 
session_start();

if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'artista') {
    header("Location: /login");
    exit;
}

$email = $_SESSION['email'];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_c = $_POST['nombre'] ?? '';
    $archivo = $_FILES['archivo'] ?? null;

    if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
        $mensaje = "Error al subir el archivo.";
    } elseif (pathinfo($archivo['name'], PATHINFO_EXTENSION) !== 'mp3') {
        $mensaje = "Solo se permiten archivos .mp3.";
    } elseif ($archivo['size'] > 10 * 1024 * 1024) {
        $mensaje = "El archivo es demasiado grande.";
    } else {
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $id_usuario = $stmt->fetchColumn();

        if (!$id_usuario) {
            $mensaje = "No se encontró el artista.";
        } else {
            // Crear ruta temporal para analizar duración
            $ruta_temporal = $archivo['tmp_name'];
            $getID3 = new getID3;
            $info = $getID3->analyze($ruta_temporal);

            // Calcular duración
            if (isset($info['playtime_seconds'])) {
                $duracion_segundos = (int)$info['playtime_seconds'];
                $duracion_formateada = gmdate("H:i:s", $duracion_segundos);
            } else {
                $mensaje = "No se pudo obtener la duración del archivo.";
                $duracion_formateada = null;
            }

            if ($duracion_formateada) {
                // Insertar canción con duración
                $stmt = $pdo->prepare("INSERT INTO canciones (nombre_c, duracion, id_usuario) VALUES (?, ?, ?) RETURNING id_cancion");
                $stmt->execute([$nombre_c, $duracion_formateada, $id_usuario]);
                $id_cancion = $stmt->fetchColumn();

                // Guardar archivo
                $nombre_archivo = $id_cancion . ".mp3";
                $ruta_final = "uploads/canciones/" . $nombre_archivo;

                if (!move_uploaded_file($archivo['tmp_name'], $ruta_final)) {
                    $mensaje = "Error al guardar el archivo.";
                } else {
                    $mensaje = "✅ Canción subida con éxito.";
                }
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$email]);
$id_usuario = $stmt->fetchColumn();

if (!$id_usuario) {
    echo "Usuario no encontrado.";
    exit;
}

// Obtener canciones del artista
$stmt = $pdo->prepare("SELECT id_cancion, nombre_c, duracion FROM canciones WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir canción</title>
</head>
<body>
    <h2>Subir nueva canción</h2>

    <?php if ($mensaje): ?>
        <p><strong><?php echo htmlspecialchars($mensaje); ?></strong></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nombre de la canción:</label>
        <input type="text" name="nombre" required><br><br>

        <label>Archivo MP3:</label>
        <input type="file" name="archivo" accept=".mp3" required><br><br>

        <input type="submit" value="Subir canción">
    </form>

    <div class="lista-canciones">
        <h3>Tus canciones subidas</h3>
        <?php if (count($canciones) === 0): ?>
            <p>No has subido ninguna canción todavía.</p>
        <?php else: ?>
            <ul>
            <?php foreach ($canciones as $cancion): ?>
                <li style="margin-bottom: 20px;">
                    <strong><?php echo htmlspecialchars($cancion['nombre_c']); ?></strong> (<?php echo $cancion['duracion']; ?>)<br>
                    <audio controls>
                        <source src="uploads/canciones/<?php echo $cancion['id_cancion']; ?>.mp3" type="audio/mpeg">
                        Tu navegador no soporta el reproductor de audio.
                    </audio>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>

