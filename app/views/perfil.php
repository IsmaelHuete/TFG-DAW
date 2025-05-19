<?php
    // Incluir los archivos necesarios
        require_once '../config/Conexion_BBDD.php';
        require_once '../app/models/usuario.php';
        require_once '../app/models/artista.php';

    // Comprobar si la sesión está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    // Comprobar si el usuario está autenticado
        if (!isset($_SESSION['email'])) {
            header("Location: /login");
            exit;
        }


    // Función para formatear el número de reproducciones
        function formatReproductions($number) {
        if ($number >= 1000000) {
            return number_format($number / 1000000, ) . 'm';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, ) . 'k'; 
        }
        return $number; 
        }

    // Crear una instancia de la clase PDO
        $usuarioModel = new Usuario($pdo);
        $artistaModel = new Artista($pdo);

    // Obtener el email del usuario de la sesión
        $email = $_SESSION['email'];
        $tipo = $_SESSION['tipo'] ?? 'normal';

    // Obtener el nombre del usuario
        $nombre_usuario = $usuarioModel->getNombreByEmail($email);

    // Si el usuario es artista
        if ($tipo === 'artista') {
            $id_usuario = $usuarioModel->getIdByEmail($email);
            $num_canciones = $artistaModel->getNumeroCanciones($id_usuario);
            $num_albums = $artistaModel->getNumeroAlbums($id_usuario);
            $total_reproducciones = $artistaModel->getTotalReproducciones($id_usuario);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subir_foto'])) {
            $archivo = $_FILES['foto'] ?? null;

            if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $permitidas = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($extension, $permitidas)) {
                    $mensaje = "❌ Formato no permitido.";
                } elseif ($archivo['size'] > 5 * 1024 * 1024) {
                    
                    $mensaje = "❌ Imagen demasiado grande (máx 5MB).";
                } else {
                    $nombre_archivo = uniqid('perfil_') . '.' . $extension;
                    $ruta_relativa = "uploads/perfiles/" . $nombre_archivo;
                    $ruta_destino = $ruta_relativa; 

                    if (!is_dir("public/uploads/perfiles")) {
                    mkdir("public/uploads/perfiles", 0777, true);
                    }

                    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                    $usuarioModel->actualizarFotoPerfil($email, "/" . $ruta_relativa); 
                    $mensaje = "✅ Foto subida correctamente.";
                    } else {
                    $mensaje = "❌ Error al guardar la imagen.";
                    }
                }
            } else {
                $mensaje = "❌ No se subió ninguna imagen.";
            }
        }
?>
<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/header1.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <?php 
        include ("layouts/header1.php");
    ?>
    <main>
    <div class="contenedor-perfil">
            <div class="encabezado-perfil">
                <!-- Foto de perfil y datos del usuario -->
                <div class="info-perfil">
                    <h1>
                        <?php echo $nombre_usuario ?>
                    </h1>
                    <h2>Datos de la cuenta</h2>
                    <div class="item-info">
                        <span class="etiqueta">Usuario:</span>
                        <!-- Para acortar el email -->
                        <span class="dato"><?php echo preg_replace('/^(.{6}).*(@.*)$/', '$1...$2', $email); ?></span>

                    </div>
                    <div class="item-info">
                        <span class="etiqueta">Tipo de cuenta:</span>
                        <span class="dato"><?php echo htmlspecialchars(ucfirst($tipo)); ?></span>
                    </div>
                    <div class="item-info">
                        <span class="etiqueta">Plan actual:</span>
                        <span class="dato insignia-premium">
                            <?php if ($_SESSION['plan'] === "premium"): ?>
                                Premium
                            <?php else: ?>
                                Gratuito
                            <?php endif; ?>
                        </span>

                    </div>
                </div>
                <div class="foto-perfil">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <label for="foto" class="boton-subir-foto"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg></label>
                        <input type="file" name="foto" id="foto" accept="image/*" required>
                        <button type="submit" name="subir_foto" class="boton-foto">Subir foto</button>
                    </form>
                    <?php
                        $foto = $usuarioModel->obtenerFotoPerfil($email);
                        $ruta_foto = $foto ? $foto : '/img/image-brand.png';
                        /* echo $mensaje ?? ''; // Mostrar mensaje de subida */
                    ?>
                    <img src="<?= htmlspecialchars($ruta_foto) ?>" alt="Foto de perfil del usuario" >
                </div>
            </div>
 
            <div class="contenido-perfil">
                <!-- Si el usuario no es artista, se le muestra la opción de hacerse premium -->
                <?php if ($tipo !== 'artista'): ?>
                <div class="seccion-perfil">
                    <h2>Mejora tu cuenta</h2>
                    <div class="tarjeta-mejora">
                        <div class="info-mejora">
                            <h3>Musicfy Premium</h3>
                            <p>Escucha sin anuncios, descarga canciones y calidad HD.</p>
                        </div>
                        <a href="/premium" class="boton-mejora">Hazte Premium</a>
                    </div>
                </div>
                <!-- Si el usuario es artista, se le muestra la zona de artista -->
                <?php else: ?>
                    <div class="seccion-perfil">
                        <h2>Zona de Artista</h2>
                        <div class="estadisticas-artista">
                            <div class="tarjeta-dato">
                                <div class="valor-dato"><?= $num_canciones ?></div>
                                <div class="texto-dato">Canciones</div>
                            </div>
                            <div class="tarjeta-dato">
                                <div class="valor-dato"><?= $num_albums ?></div>
                                <div class="texto-dato">Álbumes</div>
                            </div>
                            <div class="tarjeta-dato">
                               <div class="valor-dato"><?= formatReproductions($total_reproducciones) ?></div>
                                <div class="texto-dato">Reproducciones</div>
                            </div>
                        </div>
                        <div class="acciones-artista">
                            <a href="/miMusica" class="boton-subir">Subir música</a>
                            <a href="/estadisticas" class="boton-stat">Ver estadísticas</a>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- Sección de opciones de cuenta -->
                <div class="seccion-perfil">
                    <h2>Opciones de cuenta</h2>
                    <div class="acciones-cuenta">
                        <a href="/logout" class="boton-salir">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            Cerrar Sesión
                        </a>
                        <a href="#" class="boton-config">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            Configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <?php 
        include ("layouts/footer.php");
    ?> 
    <script src="js/header.js"></script>
    <script src="js/home.js"></script>
</body>
</html>