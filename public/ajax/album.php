<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';

$id_album = $_GET['id'] ?? null;

if (!$id_album || !is_numeric($id_album)) {
    echo "Álbum no especificado.";
    exit;
}

// Obtener info del álbum
$stmt = $pdo->prepare("
    SELECT albums.id_album, albums.nombre AS nombre_album, usuario.nombre AS nombre_artista
    FROM albums
    JOIN usuario ON albums.id_usuario = usuario.id_usuario
    WHERE albums.id_album = ?
");
$stmt->execute([$id_album]);
$album = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$album) {
    echo "Álbum no encontrado.";
    exit;
}

// Obtener canciones
$stmt = $pdo->prepare("SELECT id_cancion, nombre_c, reproducciones, duracion FROM canciones WHERE id_album = ?");
$stmt->execute([$id_album]);
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
$foto_album = "/uploads/foto-album/{$id_album}.jpg";
?>

<h2><?= htmlspecialchars($album['nombre_album']) ?></h2>
<?php foreach ($canciones as $c):?>

    <div class="container-cancion">
        <div class="add-playlist" data-id="<?= $c['id_cancion'] ?>">
            <!-- Corazón blanco (visible al inicio) -->
            <svg class="corazon-blanco" width="20px" height="20px" viewBox="-2.08 -2.08 20.16 20.16" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer;">
                <path d="M1.24264 8.24264L8 15L14.7574 8.24264C15.553 7.44699 16 6.36786 16 5.24264V5.05234C16 2.8143 14.1857 1 11.9477 1C10.7166 1 9.55233 1.55959 8.78331 2.52086L8 3.5L7.21669 2.52086C6.44767 1.55959 5.28338 1 4.05234 1C1.8143 1 0 2.8143 0 5.05234V5.24264C0 6.36786 0.44699 7.44699 1.24264 8.24264Z" fill="#ffffff"></path>
            </svg>

            <!-- Corazón degradado (oculto al inicio) -->
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
            <div class="hover-overlay">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px">
                    <defs>
                        <linearGradient id="grad-play" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#481B9A; stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#FF4EC4; stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <path fill="url(#grad-play)" fill-rule="evenodd" clip-rule="evenodd"
                        d="M5.46484 3.92349C4.79896 3.5739 4 4.05683 4 4.80888V19.1911C4 19.9432 4.79896 20.4261 5.46483 20.0765L19.1622 12.8854C19.8758 12.5108 19.8758 11.4892 19.1622 11.1146L5.46484 3.92349ZM2 4.80888C2 2.55271 4.3969 1.10395 6.39451 2.15269L20.0919 9.34382C22.2326 10.4677 22.2325 13.5324 20.0919 14.6562L6.3945 21.8473C4.39689 22.8961 2 21.4473 2 19.1911V4.80888Z"/>
                </svg>

            </div>
        </div>

        <div class="cancion">
            <div class="info-cancion">
                <strong><?= htmlspecialchars($c['nombre_c']) ?></strong>
                <span><?= htmlspecialchars($album['nombre_artista']) ?></span>
            </div>

            
            <div class="stat-cancion">
                <span><?= htmlspecialchars($c['reproducciones']) ?><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M17 13C17 12.4696 17.2107 11.9609 17.5858 11.5858C17.9609 11.2107 18.4696 11 19 11C19.5304 11 20.0392 11.2107 20.4142 11.5858C20.7893 11.9609 21 12.4696 21 13V18.9999C21 19.5303 20.7893 20.0391 20.4142 20.4142C20.0392 20.7893 19.5304 20.9999 19 20.9999C18.4696 20.9999 17.9609 20.7893 17.5858 20.4142C17.2107 20.0391 17 19.5303 17 18.9999V13Z" stroke="#999999" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M3 13C3 12.4696 3.21074 11.9609 3.58582 11.5858C3.96089 11.2107 4.46957 11 5 11C5.53043 11 6.03917 11.2107 6.41425 11.5858C6.78932 11.9609 7 12.4696 7 13V18.9999C7 19.5303 6.78932 20.0391 6.41425 20.4142C6.03917 20.7893 5.53043 20.9999 5 20.9999C4.46957 20.9999 3.96089 20.7893 3.58582 20.4142C3.21074 20.0391 3 19.5303 3 18.9999V13Z" stroke="#999999" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M19 11V10C19 8.14348 18.2625 6.36305 16.9498 5.05029C15.637 3.73754 13.8565 3 12 3C10.1435 3 8.36305 3.73754 7.05029 5.05029C5.73754 6.36305 5 8.14348 5 10V11" stroke="#999999" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg></span>
                <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 47 47" xml:space="preserve" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M24.104,41.577c-0.025,0-0.053-0.001-0.078-0.001c-1.093-0.035-2.025-0.802-2.271-1.867l-5.46-23.659l-3.199,8.316 c-0.357,0.93-1.252,1.544-2.249,1.544H2.41c-1.331,0-2.41-1.079-2.41-2.41c0-1.331,1.079-2.41,2.41-2.41h6.78l5.433-14.122 c0.38-0.989,1.351-1.612,2.418-1.54c1.057,0.074,1.941,0.831,2.18,1.863l5.186,22.474l4.618-15.394 c0.276-0.923,1.078-1.592,2.035-1.702c0.953-0.107,1.889,0.36,2.365,1.198l4.127,7.222h7.037c1.331,0,2.41,1.079,2.41,2.41 c0,1.331-1.079,2.41-2.41,2.41h-8.436c-0.865,0-1.666-0.463-2.094-1.214l-2.033-3.559l-5.616,18.722 C26.104,40.88,25.164,41.577,24.104,41.577z"></path> </g> </g> </g></svg>
                <span><?=htmlspecialchars($c['duracion']) ?></span>
            </div>
        </div>
    </div>

<?php endforeach; ?>

<script>activarEventosAudio();</script>
