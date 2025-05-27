<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';

$nombreArchivo = basename($_GET['file']);
$id_cancion = (int) pathinfo($nombreArchivo, PATHINFO_FILENAME);
$file = __DIR__ . '/canciones/' . $nombreArchivo;

if (!file_exists($file)) {
    http_response_code(404);
    exit;
}

// Registrar reproducción diaria
$fechaHoy = date('Y-m-d');
$insert = $pdo->prepare("
    INSERT INTO reproducciones_diarias (id_cancion, fecha, cantidad)
    VALUES (?, ?, 1)
    ON CONFLICT (id_cancion, fecha)
    DO UPDATE SET cantidad = reproducciones_diarias.cantidad + 1
");
$insert->execute([$id_cancion, $fechaHoy]);

$size = filesize($file);
$start = 0;
$end = $size - 1;

header('Content-Type: audio/mpeg');
header('Accept-Ranges: bytes');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if (isset($_SERVER['HTTP_RANGE'])) {
    if (preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches)) {
        $start = intval($matches[1]);
        if (isset($matches[2]) && is_numeric($matches[2])) {
            $end = intval($matches[2]);
        }
        if ($end > $size - 1) {
            $end = $size - 1;
        }
        $length = $end - $start + 1;

        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: $length");

        $fp = fopen($file, 'rb');
        fseek($fp, $start);
        $buffer = 8192;

        while (!feof($fp) && ($pos = ftell($fp)) <= $end) {
            if ($pos + $buffer > $end) {
                $buffer = $end - $pos + 1;
            }
            echo fread($fp, $buffer);
            flush();
        }
        fclose($fp);
        exit;
    }
}

// No range request: enviar todo el archivo
header("Content-Length: $size");
readfile($file);
exit;
