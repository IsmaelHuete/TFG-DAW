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
        $stmt->execute([$id_usuario]);
        
    }
}
?>
