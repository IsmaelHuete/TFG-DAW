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

        public function getArtistaById($id_usuario) {
            $stmt = $this->db->prepare("SELECT * FROM artista WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
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
                SELECT COALESCE(SUM(rd.cantidad), 0)
                FROM reproducciones_diarias rd
                JOIN canciones c ON rd.id_cancion = c.id_cancion
                WHERE c.id_usuario = ?
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
       /*  public function crearAlbum($nombre, $genero, $id_usuario) {
            $stmt = $this->db->prepare("INSERT INTO albums (nombre, genero, id_usuario) VALUES (?, ?, ?) RETURNING id_album");
            $stmt->execute([$nombre, $genero, $id_usuario]);
            return $stmt->fetchColumn();
        } */

        /* public function getAlbumsByUsuario($id_usuario) {
            $stmt = $this->db->prepare("SELECT id_album, nombre FROM albums WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } */

        public function subirCancionConAlbum($nombre_c, $duracion, $id_usuario, $id_album) {
            $stmt = $this->db->prepare("INSERT INTO canciones (nombre_c, duracion, id_usuario, id_album) VALUES (?, ?, ?, ?) RETURNING id_cancion");
            $stmt->execute([$nombre_c, $duracion, $id_usuario, $id_album]);
            return $stmt->fetchColumn();
        }

        // Obtiene los datos básicos del artista
        public function getById($id_usuario) {
            $stmt = $this->db->prepare("SELECT nombre, foto_perfil FROM usuario WHERE id_usuario = ? AND id_usuario IN (SELECT id_usuario FROM artista)");
            $stmt->execute([$id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Obtiene todas las canciones del artista con info de álbum
        public function getCanciones($id_usuario) {
            $stmt = $this->db->prepare("
                SELECT c.id_cancion, c.nombre_c, c.id_album, c.duracion,  a.nombre AS nombre_album
                FROM canciones c
                JOIN albums a ON c.id_album = a.id_album
                WHERE a.id_usuario = ?
            ");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

?>
