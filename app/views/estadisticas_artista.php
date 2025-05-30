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


// ----------------------
// CONSULTA DE DATOS GENERALES DEL ARTISTA
// ----------------------
// Esta consulta obtiene información básica y agregada del artista:
// - Nombre y foto de perfil
// - Número total de canciones y álbumes
// - Suma total de reproducciones de todas sus canciones
$stmt = $pdo->prepare("
    SELECT 
        u.nombre AS nombre_artista,     -- Nombre del artista
        u.foto_perfil,          -- Foto de perfil del artista
        (SELECT COUNT(*) FROM canciones WHERE id_usuario = u.id_usuario) AS total_canciones, -- Total de canciones del artista
        (SELECT COUNT(*) FROM albums WHERE id_usuario = u.id_usuario) AS total_albums, -- Total de álbumes del artista
        (
            SELECT COALESCE(SUM(rd.cantidad), 0)    -- Total de reproducciones del artista
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

/*
Ejemplo de $datos:
[
    'nombre_artista' => 'Shakira',
    'foto_perfil' => 'shakira.jpg',
    'total_canciones' => 12,
    'total_albums' => 3,
    'total_reproducciones' => 15432
]
*/

if (!$datos) {
    echo "Artista no encontrado.";
    exit;
}

// ----------------------
// CONSULTA DE CANCIONES DEL ARTISTA
// ----------------------
// Esta consulta obtiene todas las canciones del artista, con:
// - ID, nombre y duración de la canción
// - Suma de reproducciones de cada canción
// - Nombre del álbum al que pertenece (si tiene)
$stmtCanciones = $pdo->prepare("
    SELECT 
        c.id_cancion,                               -- ID de la canción
        c.nombre_c AS nombre_cancion,               -- Nombre de la canción
        c.duracion,                                 -- Duración de la canción       
        (
            SELECT SUM(cantidad)
            FROM reproducciones_diarias rd
            WHERE rd.id_cancion = c.id_cancion
        ) AS reproducciones,                        -- Total de reproducciones de la canción
        a.nombre AS nombre_album                    -- Nombre del álbum al que pertenece (si tiene)
    FROM canciones c
    LEFT JOIN albums a ON c.id_album = a.id_album
    WHERE c.id_usuario = ?
    ORDER BY reproducciones DESC NULLS LAST
");
$stmtCanciones->execute([$id_artista]);
$canciones = $stmtCanciones->fetchAll();

/*
Ejemplo de $canciones:
[
    [
        'id_cancion' => 1,
        'nombre_cancion' => 'Chantaje',
        'duracion' => '03:16',
        'reproducciones' => 5000,
        'nombre_album' => 'El Dorado'
    ],
    [
        'id_cancion' => 2,
        'nombre_cancion' => 'Me Enamoré',
        'duracion' => '03:10',
        'reproducciones' => 3200,
        'nombre_album' => 'El Dorado'
    ],
    // ...
]
*/

// ----------------------
// CONSULTA DE ÁLBUMES DEL ARTISTA
// ----------------------
// Esta consulta obtiene todos los álbumes del artista, con:
// - ID, nombre y género del álbum
// - Número de canciones en el álbum
// - Suma de reproducciones de todas las canciones del álbum
$stmtAlbums = $pdo->prepare("
    SELECT 
        a.id_album,                                 -- ID del álbum     
        a.nombre AS nombre_album,                   -- Nombre del álbum 
        a.genero,                                   -- Género del álbum    
        COUNT(c.id_cancion) AS total_canciones,     -- Número de canciones en el álbum
        COALESCE(SUM(c.reproducciones), 0) AS total_reproducciones  -- Suma de reproducciones de todas las canciones del álbum
    FROM albums a
    LEFT JOIN canciones c ON a.id_album = c.id_album
    WHERE a.id_usuario = ?
    GROUP BY a.id_album
");
$stmtAlbums->execute([$id_artista]);
$albums = $stmtAlbums->fetchAll();

/*
Ejemplo de $albums:
[
    [
        'id_album' => 1,
        'nombre_album' => 'El Dorado',
        'genero' => 'Pop',
        'total_canciones' => 10,
        'total_reproducciones' => 12000
    ],
    [
        'id_album' => 2,
        'nombre_album' => 'Sale el Sol',
        'genero' => 'Pop Latino',
        'total_canciones' => 8,
        'total_reproducciones' => 5432
    ],
    // ...
]
*/

// ----------------------
// CONSULTA DE REPRODUCCIONES DIARIAS POR CANCIÓN (últimos 7 días)
// ----------------------
// Prepara un array con los IDs de todas las canciones del artista

$ids_canciones = array_column($canciones, 'id_cancion');

if (empty($ids_canciones)) {
    $historial_crudo = [];
} else {
    // Construir placeholders (?, ?, ?, ...) para la consulta
    $placeholders = implode(',', array_fill(0, count($ids_canciones), '?'));

    // Consulta las reproducciones diarias de cada canción en los últimos 7 días
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

/*
Ejemplo de $historial_crudo:
[
    ['id_cancion' => 1, 'fecha' => '2025-05-23', 'cantidad' => 120],
    ['id_cancion' => 1, 'fecha' => '2025-05-24', 'cantidad' => 150],
    ['id_cancion' => 2, 'fecha' => '2025-05-23', 'cantidad' => 80],
    ['id_cancion' => 2, 'fecha' => '2025-05-24', 'cantidad' => 95],
    // ...
]
*/

$historialPorCancion = [];

// Crear array de fechas de los últimos 7 días
// Esto genera un array como ['2025-05-23', '2025-05-24', ..., '2025-05-29']
$fechas_ultimos_7 = [];
for ($i = 6; $i >= 0; $i--) {
    $fechas_ultimos_7[] = date('Y-m-d', strtotime("-$i days"));
}

// Inicializar con ceros para cada canción y cada día
// Así, aunque una canción no tenga reproducciones un día, aparecerá con valor 0 en la gráfica
foreach ($ids_canciones as $id) {
    foreach ($fechas_ultimos_7 as $fecha) {
        $historialPorCancion[$id][$fecha] = 0;
    }
}

// Rellenar los datos reales de reproducciones diarias
// Si hay datos en $historial_crudo para una canción y fecha, se asigna el valor real
foreach ($historial_crudo as $row) {
    $id = $row['id_cancion'];
    $fecha = $row['fecha'];
    if (in_array($fecha, $fechas_ultimos_7)) {
        $historialPorCancion[$id][$fecha] = (int) $row['cantidad'];
    }
}

// Convertir el historial a un formato adecuado para JavaScript (array de objetos con fecha y valor)
// Ejemplo para una canción:
// [
//   ['fecha' => '2025-05-23', 'valor' => 120],
//   ['fecha' => '2025-05-24', 'valor' => 150],
//   ...
// ]
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
    <link rel="icon" type="image/png" href="img/image-brand.png">
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
        // Prepara los datos de canciones y su historial de reproducciones para JavaScript
        // Cada elemento tiene: id, nombre_cancion, historial (array de {fecha, valor})
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

        // Cuando el usuario selecciona una canción, muestra la gráfica de reproducciones de los últimos 7 días
        document.getElementById('selector-cancion').addEventListener('change', function () {
            const idSeleccionado = parseInt(this.value);
            const cancion = datosCanciones.find(c => c.id === idSeleccionado);

            if (!cancion || !cancion.historial.length) return;

            // Extrae las fechas y valores de reproducciones para la gráfica
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

            // Si ya existe una gráfica, la destruye antes de crear una nueva (evita superposiciones)
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
