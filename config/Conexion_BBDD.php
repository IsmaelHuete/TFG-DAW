<?php
    $host = 'localhost'; 
    $port = '5432'; 
    $dbname = 'Musicfy';
    $user = 'postgres';
    $password = '';

    try {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
    }
?>