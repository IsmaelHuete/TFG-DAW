<?php
$file = __DIR__ . '/canciones/' . basename($_GET['file']);

if (!file_exists($file)) {
    http_response_code(404);
    exit;
}

$fp = fopen($file, 'rb');
$size = filesize($file);
$length = $size;
$start = 0;
$end = $size - 1;
header('Content-Type: audio/mpeg');
header('Accept-Ranges: bytes');

if (isset($_SERVER['HTTP_RANGE'])) {
    preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);

    $start = intval($matches[1]);
    if (isset($matches[2])) {
        $end = intval($matches[2]);
    }

    $length = $end - $start + 1;
    fseek($fp, $start);
    header('HTTP/1.1 206 Partial Content');
    header("Content-Range: bytes $start-$end/$size");
}

header("Content-Length: $length");

$buffer = 1024 * 8;
while (!feof($fp) && ($pos = ftell($fp)) <= $end) {
    if ($pos + $buffer > $end) {
        $buffer = $end - $pos + 1;
    }
    echo fread($fp, $buffer);
    flush();
}
fclose($fp);
