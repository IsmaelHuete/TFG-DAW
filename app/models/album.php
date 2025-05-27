<?php
class Album {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function crear($nombre, $genero, $id_usuario) {
        $stmt = $this->db->prepare("INSERT INTO albums (nombre, genero, id_usuario) VALUES (?, ?, ?) RETURNING id_album");
        $stmt->execute([$nombre, $genero, $id_usuario]);
        return $stmt->fetchColumn();
    }

    public function getAlbumsByUsuario($id_usuario) {
        $stmt = $this->db->prepare("SELECT id_album, nombre, genero FROM albums WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCanciones($id_album) {
        $stmt = $this->db->prepare("SELECT * FROM canciones WHERE id_album = ?");
        $stmt->execute([$id_album]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAlbumCompleto($id_album) {
        $stmt = $this->db->prepare("
            SELECT albums.id_album, albums.nombre AS nombre_album, usuario.nombre AS nombre_artista
            FROM albums
            JOIN usuario ON albums.id_usuario = usuario.id_usuario
            WHERE albums.id_album = ?
        ");
        $stmt->execute([$id_album]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>