<?php
    //incluye el archivo de configuración de la base de datos
    require_once '../config/Conexion_BBDD.php';
    require_once '../app/models/usuario.php';
    session_start();

    //indica que la respuesta será en formato JSON
    header('Content-Type: application/json');

    // Verificar que el usuario esté autenticado mientras la sesión esté activa
    if (!isset($_SESSION['email'])) {
        header('location: /home');
        exit;
    }

    // Obtener el ID del usuario por su email
    $usuarioModel = new Usuario($pdo);
    $id_usuario = $usuarioModel->getIdByEmail($_SESSION['email']);

    if (!$id_usuario) {
        http_response_code(403);
        echo json_encode(['error' => 'Usuario no válido']);
        exit;
    }

    // Obtener ID de canción del body
    $input = json_decode(file_get_contents('php://input'), true);
    $id_cancion = isset($input['id_cancion']) ? (int) $input['id_cancion'] : null;

    if (!$id_cancion || $id_cancion <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de canción no recibido o inválido']);
        exit;
    }

    // Insertar reproducción en tabla escuchan con la fdecha actual
    $stmt = $pdo->prepare("
        INSERT INTO escuchan (id_usuario, id_cancion, fecha)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([$id_usuario, $id_cancion]);


//devuelve una respuesta de éxito
echo json_encode(['status' => 'ok']);

/*
Ejemplo de uso desde el frontend
Cuando el usuario reproduce una canción, el reproductor llama a este endpoint para registrar la reproducción:

fetch('/repro.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_cancion: 123 })
})


Esto permite llevar un historial de reproducciones por usuario y canción.
*/