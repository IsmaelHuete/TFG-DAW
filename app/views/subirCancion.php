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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_album = trim($_POST['nombre_album'] ?? '');
    $portada_album = $_FILES['portada_album'] ?? null;
    $nombresCanciones = $_POST['nombres_canciones'] ?? [];
    $archivosCanciones = $_FILES['audios'] ?? null;

    if ($nombre_album && $portada_album && $archivosCanciones && count($archivosCanciones['name']) > 0) {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 1. INSERTAR ÁLBUM
            $stmt = $pdo->prepare("INSERT INTO albums (nombre, id_usuario) VALUES (?, ?)");
            $stmt->execute([$nombre_album, $id_usuario]);
            $id_album = $pdo->lastInsertId();

            // 2. GUARDAR PORTADA
            $ext_portada = strtolower(pathinfo($portada_album['name'], PATHINFO_EXTENSION));
            $nombre_img = $id_album . '.' . $ext_portada;
            $ruta_img = 'uploads/foto-album/' . $nombre_img;

            if (!move_uploaded_file($portada_album['tmp_name'], $ruta_img)) {
                throw new Exception("❌ No se pudo guardar la imagen del álbum.");
            }

            // 3. INSERTAR CANCIONES EN BUCLE
            for ($i = 0; $i < count($archivosCanciones['name']); $i++) {
                $nombreCancion = trim($nombresCanciones[$i] ?? '');
                $tmpFile = $archivosCanciones['tmp_name'][$i];
                $nombreArchivo = $archivosCanciones['name'][$i];
                $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

                if ($nombreCancion === '' || $ext !== 'mp3') {
                    echo "<p>⏭️ Canción $i inválida (nombre vacío o no MP3).</p>";
                    continue;
                }

                // Insertar canción
                $stmt = $pdo->prepare("INSERT INTO canciones (nombre_c, id_album, id_usuario) VALUES (?, ?, ?)");
                $stmt->execute([$nombreCancion, $id_album, $id_usuario]);
                $id_cancion = $pdo->lastInsertId();

                // Mover archivo
                $ruta_mp3 =  'uploads/canciones/' . $id_cancion . '.mp3';
                if (!move_uploaded_file($tmpFile, $ruta_mp3)) {
                    echo "<p>❌ No se pudo mover el archivo MP3 para '$nombreCancion'</p>";
                    continue;
                }

            }


        } catch (Exception $e) {
            echo "<p>❌ Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>❌ Faltan campos del álbum o canciones.</p>";
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

    <main>
        <div style="display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-between;">
            <!-- Formulario para subir una sola canción -->
            <div class="cancion" >
                <h2 style="color: #e94baf;">Subir un sencillo</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipo_subida" value="cancion">

                    <label>🎵 Nombre de la canción:</label>
                    <input type="text" name="nombre" required>

                    <label>📁 Archivo MP3:</label>
                    <input type="file" name="audio" accept=".mp3" required>

                    <label>🖼️ Imagen de portada:</label>
                    <input type="file" name="portada" accept="image/*" required>
                    <div ></div>
                    <button type="submit">Subir canción</button>
                </form>
            </div>

            <!-- Formulario para subir álbum completo -->
            <div class="album">
                <h2 style="color: #e94baf;">Subir un álbum</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipo_subida" value="album">


                    <label>🎵 Nombre del álbum:</label>
                    <input type="text" name="nombre_album" required>

                    <label>🖼️ Imagen de portada:</label>
                    <input type="file" name="portada_album" accept="image/*" required>

                    <h3 style="margin-top:20px;">🎶 Canciones del álbum</h3>
                    <div id="contenedor-canciones">
                        <!-- Aquí se insertarán las canciones -->
                    </div>

                    <button type="button" onclick="agregarCancion()">➕ Agregar canción</button>

                    <br><br>
                    <button type="submit">Subir álbum</button>
                </form>
            </div>
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
            const contenedor = document.getElementById('contenedor-canciones');

            const div = document.createElement('div');
            div.classList.add('bloque-cancion');
            div.style.marginBottom = "15px";

            const index = contenedor.children.length;

            div.innerHTML = `
                <label>🎵 Nombre de la canción:</label>
                <input type="text" name="nombres_canciones[]" required>

                <label>📁 Archivo MP3:</label>
                <input type="file" name="audios[]" accept=".mp3" required>
                <hr style="margin-top:10px;">
            `;

            contenedor.appendChild(div);
        }
    </script>


    <script src="js/header.js"></script>

</body>
</html>
