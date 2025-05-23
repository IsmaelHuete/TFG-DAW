<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "Artista no válido.";
    exit;
}

$stmt = $pdo->prepare("
    SELECT nombre, foto_perfil 
    FROM usuario 
    WHERE id_usuario = ? 
    AND id_usuario IN (SELECT id_usuario FROM artista)
");
$stmt->execute([$id]);
$artista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artista) {
    echo "Artista no encontrado.";
    exit;
}

$ruta_foto = $artista['foto_perfil'] ? "/uploads/perfiles/" . $artista['foto_perfil'] : "/uploads/perfiles/default.jpg";

// Obtener canciones del artista (con duración y reproducciones)
$stmt = $pdo->prepare("
    SELECT c.id_cancion, c.nombre_c, c.id_album, c.duracion, c.reproducciones, a.nombre AS nombre_album
    FROM canciones c
    JOIN albums a ON c.id_album = a.id_album
    WHERE a.id_usuario = ?
");
$stmt->execute([$id]);
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h2><?= htmlspecialchars($artista['nombre']) ?></h2>

<?php foreach ($canciones as $c): ?>
    <?php
        $foto_album = "/uploads/foto-album/{$c['id_album']}.jpg";
        $ruta_mp3 = "/uploads/stream.php?file={$c['id_cancion']}.mp3";
    ?>
    <div class="container-cancion">
        <div class="add-playlist" data-id="<?= $c['id_cancion'] ?>">
            <svg class="corazon-blanco" width="20px" height="20px" viewBox="-2.08 -2.08 20.16 20.16" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer;">
                <path d="M1.24264 8.24264L8 15L14.7574 8.24264C15.553 7.44699 16 6.36786 16 5.24264V5.05234C16 2.8143 14.1857 1 11.9477 1C10.7166 1 9.55233 1.55959 8.78331 2.52086L8 3.5L7.21669 2.52086C6.44767 1.55959 5.28338 1 4.05234 1C1.8143 1 0 2.8143 0 5.05234V5.24264C0 6.36786 0.44699 7.44699 1.24264 8.24264Z" fill="#ffffff"></path>
            </svg>
            <svg class="corazon-gradient" width="20px" height="20px" viewBox="-2.08 -2.08 20.16 20.16" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <defs>
                    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#481B9A; stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#FF4EC4; stop-opacity:1" />
                    </linearGradient>
                </defs>
                <path d="M1.24264 8.24264L8 15L14.7574 8.24264C15.553 7.44699 16 6.36786 16 5.24264V5.05234C16 2.8143 14.1857 1 11.9477 1C10.7166 1 9.55233 1.55959 8.78331 2.52086L8 3.5L7.21669 2.52086C6.44767 1.55959 5.28338 1 4.05234 1C1.8143 1 0 2.8143 0 5.05234V5.24264C0 6.36786 0.44699 7.44699 1.24264 8.24264Z" fill="url(#grad)" />
            </svg>
        </div>

        <div class="img-wrapper">
            <img src="<?= $foto_album ?>" alt="Carátula del álbum">
            <div class="hover-overlay"
                data-src="<?= $ruta_mp3 ?>"
                data-title="<?= htmlspecialchars($c['nombre_c']) ?>"
                data-artist="<?= htmlspecialchars($artista['nombre']) ?>"
                data-cover="<?= $foto_album ?>">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px">
                    <defs>
                        <linearGradient id="grad-play" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#481B9A; stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#FF4EC4; stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <path fill="url(#grad-play)" fill-rule="evenodd" clip-rule="evenodd"
                        d="M5.46484 3.92349C4.79896 3.5739 4 4.05683 4 4.80888V19.1911C4 19.9432 4.79896 20.4261 5.46483 20.0765L19.1622 12.8854C19.8758 12.5108 19.8758 11.4892 19.1622 11.1146L5.46484 3.92349Z"/>
                </svg>
            </div>
        </div>

        <div class="cancion">
            <div class="info-cancion">
                <strong><?= htmlspecialchars($c['nombre_c']) ?></strong>
                <span><?= htmlspecialchars($artista['nombre']) ?></span>
            </div>

            <div class="stat-cancion">
                <span><?= htmlspecialchars($c['reproducciones']) ?></span>
                <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 47 47" xml:space="preserve" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M24.104,41.577c-0.025,0-0.053-0.001-0.078-0.001c-1.093-0.035-2.025-0.802-2.271-1.867l-5.46-23.659l-3.199,8.316 c-0.357,0.93-1.252,1.544-2.249,1.544H2.41c-1.331,0-2.41-1.079-2.41-2.41c0-1.331,1.079-2.41,2.41-2.41h6.78l5.433-14.122 c0.38-0.989,1.351-1.612,2.418-1.54c1.057,0.074,1.941,0.831,2.18,1.863l5.186,22.474l4.618-15.394 c0.276-0.923,1.078-1.592,2.035-1.702c0.953-0.107,1.889,0.36,2.365,1.198l4.127,7.222h7.037c1.331,0,2.41,1.079,2.41,2.41 c0,1.331-1.079,2.41-2.41,2.41h-8.436c-0.865,0-1.666-0.463-2.094-1.214l-2.033-3.559l-5.616,18.722 C26.104,40.88,25.164,41.577,24.104,41.577z"></path> </g> </g> </g></svg>
                <span><?= htmlspecialchars($c['duracion']) ?></span>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
function activarResaltadoCancion() {
    document.querySelectorAll('.hover-overlay').forEach(overlay => {
        overlay.addEventListener('click', function () {
            document.querySelectorAll('.container-cancion').forEach(div => {
                div.classList.remove('cancion-activa');
            });
            const cancionDiv = this.closest('.container-cancion');
            if (cancionDiv) {
                cancionDiv.classList.add('cancion-activa');
            }
        });
    });
}

activarEventosAudio();
activarResaltadoCancion();
</script>
