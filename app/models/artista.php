<?php
    require_once __DIR__ . '/../../config/Conexion_BBDD.php';

    class Artista {
        private $db;

        public function __construct($pdo) {
            $this->db = $pdo;
        }

        public function registrar() {
            $stmt = $this->db->prepare("INSERT INTO artista (id_usuario) VALUES (?)");
            $id_usuario = $this->db->lastInsertId();
            return $stmt->execute([$id_usuario]);
        }

        public function getNumeroCanciones($id_usuario) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM canciones WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn();
        }

        public function getNumeroAlbums($id_usuario) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM albums WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn();
        }

        public function getTotalReproducciones($id_usuario) {
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(c.reproducciones), 0)
                FROM canciones c
                JOIN albums a ON c.id_album = a.id_album
                WHERE a.id_usuario = ?
            ");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchColumn();
        }

        public function subirCancion($nombre_c, $duracion, $id_usuario) {
            $stmt = $this->db->prepare("INSERT INTO canciones (nombre_c, duracion, id_usuario) VALUES (?, ?, ?) RETURNING id_cancion");
            $stmt->execute([$nombre_c, $duracion, $id_usuario]);
            return $stmt->fetchColumn();
        }

        public function getCancionesByUsuario($id_usuario) {
            $stmt = $this->db->prepare("SELECT id_cancion, nombre_c, duracion FROM canciones WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function crearAlbum($nombre, $genero, $id_usuario) {
            $stmt = $this->db->prepare("INSERT INTO albums (nombre, genero, id_usuario) VALUES (?, ?, ?) RETURNING id_album");
            $stmt->execute([$nombre, $genero, $id_usuario]);
            return $stmt->fetchColumn();
        }

        public function getAlbumsByUsuario($id_usuario) {
            $stmt = $this->db->prepare("SELECT id_album, nombre FROM albums WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function subirCancionConAlbum($nombre_c, $duracion, $id_usuario, $id_album) {
            $stmt = $this->db->prepare("INSERT INTO canciones (nombre_c, duracion, id_usuario, id_album, reproducciones, guardados) VALUES (?, ?, ?, ?, 0, 0) RETURNING id_cancion");
            $stmt->execute([$nombre_c, $duracion, $id_usuario, $id_album]);
            return $stmt->fetchColumn();
        }

    }

?>
