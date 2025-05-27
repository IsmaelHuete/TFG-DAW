<?php
require_once '../config/Conexion_BBDD.php';
require_once '../app/models/usuario.php';

session_start();

if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'artista') {
    header("Location: /login");
    exit;
}

$email = $_SESSION['email'];
$usuarioModel = new Usuario($pdo);
$id_usuario = $usuarioModel->getIdByEmail($email);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipoSubida = $_POST['tipo_subida'] ?? '';

    if ($tipoSubida === 'cancion') {
        // Subida individual
        $nombre = trim($_POST['nombre'] ?? '');
        $archivo_mp3 = $_FILES['audio'] ?? null;
        $archivo_img = $_FILES['portada'] ?? null;

        if ($nombre !== '' && $archivo_mp3 && $archivo_img) {
            // ... (lo que ya tenías para una sola canción)
        } else {
            $mensaje = "❌ Todos los campos son obligatorios.";
        }

    } elseif ($tipoSubida === 'album') {
        // Subida de álbum
        $nombre_album = trim($_POST['nombre_album'] ?? '');
        $portada_album = $_FILES['portada_album'] ?? null;
        $canciones_mp3 = $_FILES['canciones_mp3'] ?? null;

        if ($nombre_album !== '' && $portada_album && $canciones_mp3 && count($canciones_mp3['name']) > 0) {
            // Crear álbum
            $stmt = $pdo->prepare("INSERT INTO albums (nombre, id_usuario) VALUES (?, ?)");
            $stmt->execute([$nombre_album, $id_usuario]);
            $id_album = $pdo->lastInsertId();

            // Guardar imagen portada
            $ext_portada = strtolower(pathinfo($portada_album['name'], PATHINFO_EXTENSION));
            $nombre_img = $id_album . '.' . $ext_portada;
            move_uploaded_file($portada_album['tmp_name'], "uploads/foto-album/" . $nombre_img);

            // Guardar cada canción
            for ($i = 0; $i < count($canciones_mp3['name']); $i++) {
                $nombreArchivo = $canciones_mp3['name'][$i];
                $tmp = $canciones_mp3['tmp_name'][$i];
                $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

                if ($ext !== 'mp3') continue;

                $nombreCancion = pathinfo($nombreArchivo, PATHINFO_FILENAME);

                $stmt = $pdo->prepare("INSERT INTO canciones (nombre_c, id_album, id_usuario) VALUES (?, ?, ?)");
                $stmt->execute([$nombreCancion, $id_album, $id_usuario]);
                $id_cancion = $pdo->lastInsertId();

                move_uploaded_file($tmp, "uploads/canciones/" . $id_cancion . ".mp3");
            }

            $mensaje = "✅ Álbum subido correctamente.";
        } else {
            $mensaje = "❌ Todos los campos del álbum son obligatorios.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir canción</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="css/header1.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/subirCancion.css">
</head>
<body>
<?php include("layouts/header1.php"); ?>

<main class="container">
    <div style="display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-between;">

        <!-- Formulario para subir una sola canción -->
        <div style="flex: 1 1 45%; border: 2px solid #8839ef; padding: 20px; border-radius: 10px; background: #1a1a1a;">
            <h2 style="color: #e94baf;">Subir una canción suelta</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_subida" value="cancion">

                <label>🎵 Nombre de la canción:</label><br>
                <input type="text" name="nombre" required><br><br>

                <label>📁 Archivo MP3:</label><br>
                <input type="file" name="audio" accept=".mp3" required><br><br>

                <label>🖼️ Imagen de portada:</label><br>
                <input type="file" name="portada" accept="image/*" required><br><br>

                <button type="submit">Subir canción</button>
            </form>
        </div>

        <!-- Formulario para subir álbum completo -->
        <div style="border: 2px dashed #888; padding: 20px; margin-top: 40px;">
            <h3>Subir Álbum completo</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_subida" value="album">

                <label>Nombre del álbum:</label>
                <input type="text" name="nombre_album" required>

                <label>Imagen de portada del álbum:</label>
                <input type="file" name="portada_album" accept="image/*" required>

                <label>Selecciona canciones (.mp3):</label>
                <input type="file" name="audios[]" accept=".mp3" multiple required>

                <div id="nombres-canciones-container"></div>

                <button type="submit">Subir álbum</button>
            </form>
        </div>

        <script>
            document.querySelector('input[name="audios[]"]').addEventListener('change', function () {
                const container = document.getElementById('nombres-canciones-container');
                container.innerHTML = '';

                Array.from(this.files).forEach((file, i) => {
                    const label = document.createElement('label');
                    label.textContent = `Nombre para: ${file.name}`;

                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'nombres_canciones[]';
                    input.required = true;

                    container.appendChild(label);
                    container.appendChild(input);
                });
            });
        </script>


    </div>

    <!-- Mensaje de confirmación -->
    <?php if (!empty($mensaje)) : ?>
        <p style="margin-top: 20px; background: #222; padding: 10px; color: lightgreen; border-left: 5px solid #00c853;">
            <?= $mensaje ?>
        </p>
    <?php endif; ?>

</main>

<?php include("layouts/footer.php"); ?>

<script>
function agregarCancion() {
    const div = document.createElement('div');
    div.classList.add('bloque-cancion');
    div.innerHTML = `
        <input type="text" name="titulos[]" placeholder="Nombre de la canción" required>
        <input type="file" name="audios[]" accept=".mp3" required>
    `;
    document.getElementById('contenedor-canciones').appendChild(div);
}
</script>

</body>
</html>
