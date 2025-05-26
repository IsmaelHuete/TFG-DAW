<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once  '../config/Conexion_BBDD.php';
    require_once '../app/models/usuario.php';

    $usuarioModel = new Usuario($pdo);
    $foto = null;
    $ruta_foto = '/img/image-brand.png';

    if (isset($_SESSION['email'])) {
        $foto = $usuarioModel->obtenerFotoPerfil($_SESSION['email']);
        if ($foto) {
            $ruta_foto = '/uploads/perfiles/' . $foto;
        }
    }
?>
<nav>
  <div class="nav">
    <div class="nav-section">
      <a href="/"><img src="/img/image-brand.png"></a>   
      <div class="menu">
        <ul>
          <li><a href="/myMusic">My Music</a></li>
          <li><a href="/index">Reproductor</a></li>
          <li><a href="/premium">Premium</a></li>
          <li>
            <?php if (isset($_SESSION['email'])): ?>
              <a href="/perfil">
                <img src="<?= htmlspecialchars($ruta_foto) ?>" alt="Foto de perfil" class="foto-icono-perfil">
              </a>
            <?php else: ?>
              <button><a href="/login">Sign in</a></button>
              <button><a href="/register">Register</a></button>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="difuminado"></div>

  <div class="nav-subheader">
    <div class="subheader">
      <img src="/img/img-subheader-removebg-preview.png"> 
      <div class="info-subheader">
        <h1>Escucha sin límites</h1>
        <button>EMPIEZA A ESCUCHAR</button>
      </div>
    </div>
  </div>
</nav>
