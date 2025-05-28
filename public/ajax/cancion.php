<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '/../../app/models/cancion.php';

$id_cancion = $_GET['id'] ?? null;
if (!$id_cancion || !is_numeric($id_cancion)) {
    echo "Canción no especificada.";
    exit;
}

$cancionModel = new Cancion($pdo);
$c = $cancionModel->getPorId($id_cancion);

if (!$c) {
    echo "Canción no encontrada.";
    exit;
}

// Añadir campos necesarios para el reproductor
$c['ruta_mp3'] = "/uploads/stream.php?file={$c['id_cancion']}.mp3";
$c['foto_album'] = $c['id_album'] ? "/uploads/foto-album/{$c['id_album']}.jpg" : "/uploads/foto-album/default.jpg";
$c['artista'] = $c['id_usuario'] ? $cancionModel->getArtistaPorId($c['id_usuario']) : 'Desconocido';
?>

<div class="container-cancion" style="display: flex;">
    <div class="add-playlist" data-id="<?= $c['id_cancion'] ?>">
        <img src="/img/heart-blanco.png" class="corazon-blanco" style="width: 15px; height: 15px; cursor:pointer;">
        <img class="corazon-gradient oculto" src="/img/heart-rosa.png" width="15px" height="15px">
    </div>
    <div class="img-wrapper">
        <img src="<?= $c['foto_album'] ?>" alt="Carátula">
        <div class="hover-overlay"
            data-src="<?= $c['ruta_mp3'] ?>"
            data-id="<?= $c['id_cancion'] ?>"
            data-title="<?= htmlspecialchars($c['nombre_c']) ?>"
            data-artist="<?= htmlspecialchars($c['artista'] ?? 'Desconocido') ?>"
            data-cover="<?= $c['foto_album'] ?>">
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
    <div class="cancion" style="flex-grow: 1;">
        <div class="info-cancion">
            <strong><?= htmlspecialchars($c['nombre_c']) ?></strong>
            <span><?= htmlspecialchars($c['artista'] ?? 'Desconocido') ?></span>
        </div>
        <div class="stat-cancion">
            <span><?= $c['reproducciones'] ?? 0 ?></span>
            <span><?= $c['duracion'] ?? '00:00' ?></span>
        </div>
    </div>
</div>

<script>
    window.cancionActual = [<?= json_encode($c, JSON_UNESCAPED_UNICODE) ?>];
    window.albumActual = undefined;
    window.artistaActual = undefined;
    if (typeof activarEventosAudio === "function") activarEventosAudio();
    if (typeof activarResaltadoCancion === "function") activarResaltadoCancion();
</script>