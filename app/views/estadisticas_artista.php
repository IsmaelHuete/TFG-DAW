<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
session_start();
// Validar sesión
if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'artista') {
    header('Location: /404'); 
    exit;
}

$id_artista = $_GET['id'] ?? null;

// Solo el propio artista puede ver su página de estadísticas
if (!$id_artista || !is_numeric($id_artista) || $_SESSION['id_usuario'] != $id_artista) {
    echo "Acceso denegado.";
    exit;
}


// Datos generales
// Datos generales SIN duplicaciones
$stmt = $pdo->prepare("
    SELECT 
        u.nombre AS nombre_artista,
        u.foto_perfil,
        (SELECT COUNT(*) FROM canciones WHERE id_usuario = u.id_usuario) AS total_canciones,
        (SELECT COUNT(*) FROM albums WHERE id_usuario = u.id_usuario) AS total_albums,
        (
            SELECT COALESCE(SUM(rd.cantidad), 0)
            FROM reproducciones_diarias rd
            JOIN canciones c2 ON c2.id_cancion = rd.id_cancion
            WHERE c2.id_usuario = u.id_usuario
        ) AS total_reproducciones
    FROM usuario u
    JOIN artista ar ON u.id_usuario = ar.id_usuario
    WHERE u.id_usuario = ?
");

$stmt->execute([$id_artista]);
$datos = $stmt->fetch();

if (!$datos) {
    echo "Artista no encontrado.";
    exit;
}

// Canciones
$stmtCanciones = $pdo->prepare("
    SELECT 
        c.id_cancion,
        c.nombre_c AS nombre_cancion,
        c.duracion,
        (
            SELECT SUM(cantidad)
            FROM reproducciones_diarias rd
            WHERE rd.id_cancion = c.id_cancion
        ) AS reproducciones,
        a.nombre AS nombre_album
    FROM canciones c
    LEFT JOIN albums a ON c.id_album = a.id_album
    WHERE c.id_usuario = ?
    ORDER BY reproducciones DESC NULLS LAST
");
$stmtCanciones->execute([$id_artista]);
$canciones = $stmtCanciones->fetchAll();

// Álbumes
$stmtAlbums = $pdo->prepare("
    SELECT 
        a.id_album,
        a.nombre AS nombre_album,
        a.genero,
        COUNT(c.id_cancion) AS total_canciones,
        COALESCE(SUM(c.reproducciones), 0) AS total_reproducciones
    FROM albums a
    LEFT JOIN canciones c ON a.id_album = c.id_album
    WHERE a.id_usuario = ?
    GROUP BY a.id_album
");
$stmtAlbums->execute([$id_artista]);
$albums = $stmtAlbums->fetchAll();

$ids_canciones = array_column($canciones, 'id_cancion');

if (empty($ids_canciones)) {
    $historial_crudo = [];
} else {
    // Construir placeholders (?, ?, ?, ...)
    $placeholders = implode(',', array_fill(0, count($ids_canciones), '?'));

    $stmtHistorial = $pdo->prepare("
        SELECT id_cancion, fecha, cantidad
        FROM reproducciones_diarias
        WHERE id_cancion IN ($placeholders)
        AND fecha >= CURRENT_DATE - INTERVAL '6 days'
        ORDER BY fecha
    ");
    $stmtHistorial->execute($ids_canciones);
    $historial_crudo = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);
}
$historialPorCancion = [];

// Crear array de fechas de los últimos 7 días
$fechas_ultimos_7 = [];
for ($i = 6; $i >= 0; $i--) {
    $fechas_ultimos_7[] = date('Y-m-d', strtotime("-$i days"));
}

// Inicializar con ceros para cada canción
foreach ($ids_canciones as $id) {
    foreach ($fechas_ultimos_7 as $fecha) {
        $historialPorCancion[$id][$fecha] = 0;
    }
}

// Rellenar los datos reales
foreach ($historial_crudo as $row) {
    $id = $row['id_cancion'];
    $fecha = $row['fecha'];
    if (in_array($fecha, $fechas_ultimos_7)) {
        $historialPorCancion[$id][$fecha] = (int) $row['cantidad'];
    }
}

// Convertir a formato adecuado para JavaScript
foreach ($historialPorCancion as $id => $valores) {
    $historialPorCancion[$id] = array_map(function($fecha, $valor) {
        return ['fecha' => $fecha, 'valor' => $valor];
    }, array_keys($valores), array_values($valores));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stats</title>
    <link rel="stylesheet" href="css/comun.css">                            
    <link rel="stylesheet" href="css/header1.css">                                                                                  
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/estadisticas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php 
        include ("layouts/header-playlist.php");
    ?>
    <main>
        <h1><?= htmlspecialchars($datos['nombre_artista']) ?> - Estadísticas</h1>
        <p>Total canciones: <?= $datos['total_canciones'] ?></p>
        <p>Total álbumes: <?= $datos['total_albums'] ?></p>
        <p>Reproducciones totales: <?= number_format($datos['total_reproducciones']) ?></p>

        <h2>Canciones</h2>
        <h3>Visualización de reproducciones</h3>
        <select id="selector-cancion">
            <option disabled selected>Selecciona una canción</option>
            <?php foreach ($canciones as $c): ?>
                <option value="<?= $c['id_cancion'] ?>"><?= htmlspecialchars($c['nombre_cancion']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="chart-container">
            <canvas id="chartReproducciones" width="600" height="250"></canvas>
        </div>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Nombre</th>
                <th>Álbum</th>
                <th>Duración</th>
                <th>Reproducciones</th>
            </tr>
            <?php foreach ($canciones as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nombre_cancion']) ?></td>
                    <td><?= htmlspecialchars($c['nombre_album'] ?? 'Single') ?></td>
                    <td><?= $c['duracion'] ?></td>
                    <td><?= number_format($c['reproducciones']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
    <?php include ("layouts/footer.php"); ?>

    <script src="js/header.js"></script>
    <script src="js/home.js"></script>
    <script>
        const datosCanciones = <?= json_encode(array_map(function($c) use ($historialPorCancion) {
            $id = $c['id_cancion'];
                return [
                    'id' => $id,
                    'nombre_cancion' => $c['nombre_cancion'],
                    'historial' => $historialPorCancion[$id] ?? []
                ];
            }, $canciones)) ?>;

        const ctx = document.getElementById('chartReproducciones').getContext('2d');
        let chart;

        document.getElementById('selector-cancion').addEventListener('change', function () {
            const idSeleccionado = parseInt(this.value);
            const cancion = datosCanciones.find(c => c.id === idSeleccionado);

            if (!cancion || !cancion.historial.length) return;

            const labels = cancion.historial.map(item => item.fecha);
            const data = cancion.historial.map(item => item.valor);

            const chartData = {
                labels: labels,
                datasets: [{
                    label: `Reproducciones reales de "${cancion.nombre_cancion}"`,
                    data: data,
                    fill: true,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.3
                }]
            };

            if (chart) chart.destroy();
            chart = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        });

    </script>


</body>
</html>
