<?php
require_once '../config/Conexion_BBDD.php';
require_once '../vendor/autoload.php';
require_once '../app/models/usuario.php';
require_once '../app/models/artista.php';

session_start();

if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'artista') {
    header("Location: /login");
    exit;
}

$email = $_SESSION['email'];
$mensaje = '';

$usuarioModel = new Usuario($pdo);
$artistaModel = new Artista($pdo);

$id_usuario = $usuarioModel->getIdByEmail($email);
if (!$id_usuario) {
    echo "Usuario no encontrado.";
    exit;
}

// Obtener álbumes del artista
$albums = $artistaModel->getAlbumsByUsuario($id_usuario);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_c = $_POST['nombre'] ?? '';
    $archivo = $_FILES['archivo'] ?? null;
    $album_existente = $_POST['id_album'] ?? '';
    $nuevo_album = trim($_POST['nombre_album'] ?? '');
    $genero_album = trim($_POST['genero_album'] ?? '');

    // Obtener o crear álbum
    if (!empty($album_existente)) {
        $id_album = $album_existente;
    } elseif (!empty($nuevo_album) && !empty($genero_album)) {
        $id_album = $artistaModel->crearAlbum($nuevo_album, $genero_album, $id_usuario);
    } else {
        $mensaje = "Debes seleccionar o crear un álbum con su género.";
    }

    if (isset($id_album) && $archivo && $archivo['error'] === UPLOAD_ERR_OK) {
        if (pathinfo($archivo['name'], PATHINFO_EXTENSION) !== 'mp3') {
            $mensaje = "Solo se permiten archivos .mp3.";
        } elseif ($archivo['size'] > 10 * 1024 * 1024) {
            $mensaje = "El archivo es demasiado grande.";
        } else {
            $ruta_temporal = $archivo['tmp_name'];
            $getID3 = new getID3;
            $info = $getID3->analyze($ruta_temporal);

            if (isset($info['playtime_seconds'])) {
                $duracion_segundos = (int)$info['playtime_seconds'];
                $duracion_formateada = gmdate("H:i:s", $duracion_segundos);
                $id_cancion = $artistaModel->subirCancionConAlbum($nombre_c, $duracion_formateada, $id_usuario, $id_album);
                $nombre_archivo = $id_cancion . ".mp3";
                $ruta_final = "uploads/canciones/" . $nombre_archivo;

                if (!is_dir("uploads/canciones")) {
                    mkdir("uploads/canciones", 0777, true);
                }

                if (!move_uploaded_file($archivo['tmp_name'], $ruta_final)) {
                    $mensaje = "Error al guardar el archivo.";
                } else {
                    $mensaje = "✅ Canción subida con éxito.";
                }
            } else {
                $mensaje = "No se pudo obtener la duración del archivo.";
            }
        }
    }

    $albums = $artistaModel->getAlbumsByUsuario($id_usuario); // actualizar lista
}

$canciones = $artistaModel->getCancionesByUsuario($id_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi música</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="css/header1.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/miMusica.css">
</head>
<body>
    <?php include("layouts/header1.php"); ?>
    <main>
        <div class="container">
            <h2>Subir canción</h2>
            <?php if ($mensaje): ?>
                <p><strong><?= htmlspecialchars($mensaje) ?></strong></p>
            <?php endif; ?>

                 <form action="" method="POST" enctype="multipart/form-data">
                <label>Selecciona un álbum existente:</label>
                <select name="id_album">
                    <option value="">-- Ninguno --</option>
                    <?php foreach ($albums as $album): ?>
                        <option value="<?= $album['id_album'] ?>"><?= htmlspecialchars($album['nombre']) ?></option>
                    <?php endforeach; ?>
                </select><br><br>

                <label>O crea un nuevo álbum:</label>
                <input type="text" name="nombre_album" placeholder="Nombre del álbum"><br><br>

                <label>Género del álbum:</label>
                <input type="text" name="genero_album" maxlength="50" placeholder="Ej: Pop, Rock..."><br><br>

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
                                <strong><?= htmlspecialchars($cancion['nombre_c']); ?></strong> (<?= $cancion['duracion']; ?>)<br>
                                <audio controls>
                                    <source src="uploads/canciones/<?= $cancion['id_cancion']; ?>.mp3" type="audio/mpeg">
                                    Tu navegador no soporta el reproductor de audio.
                                </audio>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div> 
        </div>
    </main>
    <div class="cancion-play">
        <img src="img/albumConejoMalo1.jpg" class="cancion-play-portada" id="album-cover">

        <div class="controles-audio">
                <div class="botones">
                    <button id="retroceder">⏪ 15s</button>
                    <button id="play-btn">&#x25B6;</button>
                    <button id="pause-btn" style="display: none;">&#9208;</button>
                    <button id="adelantar">15s ⏩</button>
                </div>
                <div class="barra-tiempo">
                    <span id="tiempo-actual">00:00</span>
                    <input type="range" id="barra-progreso" value="0" min="0" step="0.01">
                    <span id="duracion-total">00:00</span>
                </div>
            </div>
            <audio id="audio-player" >
                <source src="uploads/canciones/<?= $cancion['id_cancion']; ?>.mp3" type="audio/mpeg">
                Tu navegador no soporta el audio.
            </audio>
    </div>

    <?php include("layouts/footer.php"); ?>
    <script src="js/header.js"></script>
    <script src="js/home.js"></script>
    <script src="js/cancion-play.js"></script>
</body>
</html>
