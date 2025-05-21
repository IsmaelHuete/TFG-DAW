<?php
require_once '../config/Conexion_BBDD.php';

$id_playlist = $_GET['id'] ?? null;
if (!$id_playlist || !is_numeric($id_playlist)) {
    echo "ID de playlist inválido.";
    exit;
}

// Obtener canciones de la playlist
$stmt = $pdo->prepare("
    SELECT canciones.id_cancion, canciones.nombre_c
    FROM canciones
    JOIN cancion_playlist ON canciones.id_cancion = cancion_playlist.id_cancion
    WHERE cancion_playlist.id_playlist = ?
");
$stmt->execute([$id_playlist]);
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($canciones)) {
    echo "<p>Playlist vacía.</p>";
    echo '<button onclick="abrirBuscador()">Añadir canciones</button>';
    echo '<div id="popup-buscador" style="display:none;"></div>';
    exit;
}

// Mostrar canciones
foreach ($canciones as $c) {
    echo "<div style='margin-bottom: 10px'>";
    echo "<strong>" . htmlspecialchars($c['nombre_c']) . "</strong>";
    echo "<button class='boton-opciones' data-id='" . $c['id_cancion'] . "'>⋮</button>";
    echo "</div>";
}
?>

<script>
function abrirBuscador() {
    fetch('/buscar.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('popup-buscador').style.display = 'block';
            document.getElementById('popup-buscador').innerHTML = html;
        });
}

// Botón de 3 puntitos (puede ampliarse)
document.querySelectorAll('.boton-opciones').forEach(btn => {
    btn.addEventListener('click', function () {
        const idCancion = this.dataset.id;
        alert("Opciones para canción ID: " + idCancion); // Aquí puedes desplegar un menú real
    });
});
</script>
