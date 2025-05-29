<?php
class Playlist {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function crear($nombre, $id_usuario, $foto = null) {
        $stmt = $this->db->prepare("INSERT INTO playlists (nombre, id_usuario, foto) VALUES (?, ?, ?) RETURNING id_playlist");
        $stmt->execute([$nombre, $id_usuario, $foto]);
        return $stmt->fetchColumn();
    }

    public function eliminar($id_playlist) {
        // Primero eliminamos las canciones asociadas
        $stmt = $this->db->prepare("DELETE FROM cancion_playlist WHERE id_playlist = ?");
        $stmt->execute([$id_playlist]);

        // Luego eliminamos la playlist
        $stmt = $this->db->prepare("DELETE FROM playlists WHERE id_playlist = ?");
        return $stmt->execute([$id_playlist]);
    }

    public function addCancion($id_playlist, $id_cancion) {
        $stmt = $this->db->prepare("INSERT INTO cancion_playlist (id_playlist, id_cancion) VALUES (?, ?)");
        return $stmt->execute([$id_playlist, $id_cancion]);
    }

    public function getCanciones($id_playlist) {
        $stmt = $this->db->prepare("SELECT c.* FROM canciones c INNER JOIN cancion_playlist cp ON c.id_cancion = cp.id_cancion WHERE cp.id_playlist = ?");
        $stmt->execute([$id_playlist]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarCancion($id_playlist, $id_cancion) {
        $stmt = $this->db->prepare("DELETE FROM cancion_playlist WHERE id_playlist = ? AND id_cancion = ?");
        return $stmt->execute([$id_playlist, $id_cancion]);
    }

    public function getPlaylistsByUsuario($id_usuario) {
        $stmt = $this->db->prepare("SELECT id_playlist, nombre, foto FROM playlists WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCancionesPorUsuario($id_usuario) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT cp.id_cancion
            FROM cancion_playlist cp
            JOIN playlists p ON cp.id_playlist = p.id_playlist
            WHERE p.id_usuario = ?
        ");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>