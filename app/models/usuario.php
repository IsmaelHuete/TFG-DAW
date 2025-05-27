<?php
    require_once __DIR__ . '/../../config/Conexion_BBDD.php';

    class Usuario {
        private $db;

        public function __construct($pdo) {
            $this->db = $pdo;
        }

        public function registrar($email, $nombre, $f_nacimiento, $contraseña) {
            $stmt = $this->db->prepare("INSERT INTO usuario (email, nombre, f_nacimiento, contraseña) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$email, $nombre, $f_nacimiento, $contraseña]);
        }

        public function login($email, $contraseña) {
            $stmt = $this->db->prepare("SELECT * FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
                return $usuario;
            }
            return false;
        }
        public function getNombreByEmail($email) {
            $stmt = $this->db->prepare("SELECT nombre FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn();
        }
        public function getIdByEmail($email) {
            $stmt = $this->db->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn();
        }

        public function getUsuarioById($id_usuario) {
            $stmt = $this->db->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function actualizarFotoPerfil($email, $rutaRelativa) {
            $stmt = $this->db->prepare("UPDATE usuario SET foto_perfil = ? WHERE email = ?");
            return $stmt->execute([$rutaRelativa, $email]);
        }

        public function obtenerFotoPerfil($email) {
            $stmt = $this->db->prepare("SELECT foto_perfil FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn();
        }

        public function obtenerTipo($email) {
            // Obtener ID del usuario
            $stmt = $this->db->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $id_usuario = $stmt->fetchColumn();

            if (!$id_usuario) {
                return null; // o lanzar excepción si prefieres
            }

            // Verificar si es artista
            $stmt = $this->db->prepare("SELECT 1 FROM artista WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);

            if ($stmt->fetch()) {
                return 'artista';
            }

            return 'normal';
        }

        public function getPlanByEmail($email) {
            $stmt = $this->db->prepare("SELECT plan FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn();
        }

        public function actualizarPlan($email, $nuevoPlan) {
            $stmt = $this->db->prepare("UPDATE usuario SET plan = ? WHERE email = ?");
            return $stmt->execute([$nuevoPlan, $email]);
        }

    }
?>
