<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/Conexion_BBDD.php';
require_once '../app/models/usuario.php';

$usuarioModel = new Usuario($pdo);
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
      <a href="/"><img src="/img/image-brand.png" alt="Logo"></a>

      <!-- Botón hamburguesa -->
      <button id="menu-toggle-btn" class="menu-icon">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <!-- Menú desktop -->
      <div class="menu menu-desktop">
        <ul>
          <li><a href="/index">Musicfy</a></li>
          <li><a href="/premium">Premium</a></li>
          <li>
            <?php if (isset($_SESSION['email'])): ?>
              <a href="/perfil">
                <img src="<?= htmlspecialchars($ruta_foto) ?>" alt="Perfil" class="foto-icono-perfil">
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
</nav>

<!-- Menú móvil lateral -->
<div id="menu-mobile" class="menu-mobile">
  <a href="/"><img src="/img/image-brand.png" alt="Logo"></a>
  <ul>
    <li><a href="/index">Musicfy</a></li>
    <li><a href="/premium">Premium</a></li>
    <li>
        <?php if (isset($_SESSION['email'])): ?>
          <a href="/perfil">
            <img src="<?= htmlspecialchars($ruta_foto) ?>" alt="Perfil" class="foto-icono-perfil">
          </a>
        <?php else: ?>
          <button><a href="/login">Sign in</a></button>
          <button><a href="/register">Register</a></button>
        <?php endif; ?>
      </li>
  </ul>
</div>

<!-- Overlay para cerrar -->
<div id="overlay"></div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("menu-toggle-btn");
  const overlay = document.getElementById("overlay");
  const links = document.querySelectorAll(".menu-mobile a");

  toggleBtn.addEventListener("click", () => {
    document.body.classList.toggle("menu-open");
  });

  overlay.addEventListener("click", () => {
    document.body.classList.remove("menu-open");
  });

  // Cierra el menú al hacer clic en un enlace
  links.forEach(link => {
    link.addEventListener("click", () => {
      document.body.classList.remove("menu-open");
    });
  });
});
</script>