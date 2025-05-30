<?php
class Cancion {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }
    public function getPorId($id_cancion) {
        $stmt = $this->db->prepare("SELECT * FROM canciones WHERE id_cancion = ?");
        $stmt->execute([$id_cancion]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getArtistaPorId($id_usuario) {
        $stmt = $this->db->prepare("SELECT nombre FROM usuario WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchColumn();
    }
    public function getPorAlbum($id_album) {
        $stmt = $this->db->prepare("SELECT * FROM canciones WHERE id_album = ?");
        $stmt->execute([$id_album]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPorArtista($id_usuario) {
        $stmt = $this->db->prepare("SELECT * FROM canciones WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarEscucha($id_usuario, $id_cancion) {
        $stmt = $this->db->prepare("INSERT INTO escuchan (id_usuario, id_cancion, fecha) VALUES (?, ?, NOW())");
        return $stmt->execute([$id_usuario, $id_cancion]);
    }

    public function getReproduccionesTotales($id_cancion) {
        $stmt = $this->db->prepare("SELECT SUM(cantidad) FROM reproducciones_diarias WHERE id_cancion = ?");
        $stmt->execute([$id_cancion]);
        return $stmt->fetchColumn();
    }
    public function getNombreAlbumPorId($id_album) {
        $stmt = $this->db->prepare("SELECT nombre FROM albums WHERE id_album = ?");
        $stmt->execute([$id_album]);
        return $stmt->fetchColumn() ?: 'Desconocido';
    }
}
?>