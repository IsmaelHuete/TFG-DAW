<?php

    //conexion a la base de datos
    require_once '../config/Conexion_BBDD.php';

    //indicar que la respuesta es JSON
    header('Content-Type: application/json');

    //obtngo el parametro de la busqueda y le quito los espacios en blanco con trim
    $q = $_GET['q'] ?? '';
    $q = trim($q);

    //inizializo el array de resultados
    $resultado = [
        'canciones' => [],
        'artistas' => [],
        'albums' => []
    ];

    //si la consulta de busqueda no esta vacia 
    if ($q !== '') {
        
        $like = '%' . strtolower($q) . '%';

        // =========================
        // BÚSQUEDA DE CANCIONES
        // =========================
        //buscar la cancion con el nombre que coincida con la consulta
        $stmt = $pdo->prepare("
            SELECT c.id_cancion, c.nombre_c, c.id_album, c.reproducciones, c.duracion, 
                a.nombre AS album, u.nombre AS artista
            FROM canciones c
            LEFT JOIN albums a ON c.id_album = a.id_album
            LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
            WHERE LOWER(c.nombre_c) LIKE ?
            LIMIT 10
        ");

        //ejecutar la consulta con el parámetro de búsqueda
        $stmt->execute([$like]);
        //obtener los resultados con fetchAll
        $canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //añadir la ruta de imagen basada en id_album
        foreach ($canciones as &$cancion) {
            $id_album = $cancion['id_album'];
            // si la canción tiene álbum usa su portada si no, usa una imagen por defecto con el operador ternario
            $cancion['foto_album'] = $id_album ? "/uploads/foto-album/{$id_album}.jpg" : "/uploads/foto-album/default.jpg";
        }
        $resultado['canciones'] = $canciones;

        // Ejemplo de resultado para canciones:
        // [
        //   {
        //     "id_cancion": 5,
        //     "nombre_c": "Waka Waka",
        //     "id_album": 2,
        //     "reproducciones": 12345,
        //     "duracion": "03:22",
        //     "album": "Sale el Sol",
        //     "artista": "Shakira",
        //     "foto_album": "/uploads/foto-album/2.jpg"
        //   }
        // ]




        // =========================
        // BÚSQUEDA DE ARTISTAS
        // =========================
        //buscar el artista con el nombre que coincida con la consulta
        $stmt = $pdo->prepare("
            SELECT id_usuario, nombre,foto_perfil
            FROM usuario
            WHERE id_usuario IN (SELECT id_usuario FROM artista)
            AND LOWER(nombre) LIKE ?
            LIMIT 10
        ");
        $stmt->execute([$like]);
        $resultado['artistas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // =========================
        // BÚSQUEDA DE ÁLBUMES
        // =========================
        // buscar el album con el nombre que coincida con la consulta
        $stmt = $pdo->prepare("
            SELECT albums.id_album, albums.id_usuario, albums.nombre, usuario.nombre AS artista
            FROM albums
            JOIN usuario ON albums.id_usuario = usuario.id_usuario
            WHERE LOWER(albums.nombre) LIKE ?
            LIMIT 10
        ");

        $stmt->execute([$like]);
        $resultado['albums'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


//devuelve el array de resultados como JSON
echo json_encode($resultado);

/*
ejemplo de uso en el frontend con ajax haciendo fetch

fetch('/public/buscar.php?q=anuel')
  .then(res => res.json())
  .then(data => {
    aqui iria el let html con los resultados
  });

Esto permite mostrar los resultados de búsqueda en tiempo real en la web.
*/