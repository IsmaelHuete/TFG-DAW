<?php
require_once __DIR__ . '/../../config/Conexion_BBDD.php';

class Artista {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }
    public function registrar($id_usuario) {
        $stmt = $this->db->prepare("INSERT INTO normal (id_usuario, fecha_registro) VALUES (?, NOW())");
        return $stmt->execute([$id_usuario]);
    }
}
?>
