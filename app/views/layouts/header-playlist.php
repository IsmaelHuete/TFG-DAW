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
          <li class="menu-playlists">
            <a href="#" id="toggle-playlists">Playlists</a>
            <div id="dropdown-playlists" class="dropdown-playlists">
              <!-- Se cargan dinámicamente -->
            </div>
          </li>
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


  const toggle = document.getElementById("toggle-playlists");
  const container = document.querySelector(".menu-playlists");

  toggle.addEventListener("click", (e) => {
    e.preventDefault();
    container.classList.toggle("open");

    const dropdown = document.getElementById("dropdown-playlists");

    // Solo carga si no se ha cargado aún
    if (dropdown.childElementCount === 0) {
      fetch('/ajax/obtener_playlists_usuario.php')
        .then(res => res.json())
        .then(data => {
          if (!Array.isArray(data) || data.length === 0) {
            dropdown.innerHTML = '<p style="color: gray; padding: 10px;">No tienes playlists.</p>';
            return;
          }

          data.forEach(p => {
            const div = document.createElement("div");
            div.className = "playlist-item";
            div.innerHTML = `
              <img src="/uploads/foto-playlist/${p.foto || 'default.jpg'}" alt="Playlist">
              <span>${p.nombre}</span>
              <button class="btn-eliminar-playlist" data-id="<?= $playlist['id_playlist'] ?>" data-nombre="<?= $playlist['nombre'] ?>">🗑</button>

            `;



            div.addEventListener('click', () => {
              const playlistId = p.id_playlist;

              fetch(`/ajax/playlist.php?id=${playlistId}`)
                .then(res => res.text())
                .then(html => {
                  const contenedor = document.getElementById('resultados-dinamicos');
                  document.getElementById('contenido-principal').innerHTML = '';
                  document.getElementById('contenido-principal').style.display = 'none';
                  contenedor.style.display = 'block';
                  contenedor.innerHTML = html;
                  contenedor.scrollIntoView({ behavior: 'smooth' });


                  // Ejecutar scripts dentro del HTML cargado (si los hay)
                  contenedor.querySelectorAll('script').forEach(oldScript => {
                    const nuevoScript = document.createElement('script');
                    if (oldScript.src) {
                      nuevoScript.src = oldScript.src;
                    } else {
                      nuevoScript.textContent = oldScript.textContent;
                    }
                    document.body.appendChild(nuevoScript);
                    oldScript.remove();
                  });

                  activarEventosAudio();
                  activarResaltadoCancion();
                  marcarCorazones();
                });
            });




            dropdown.appendChild(div);
          });
        })
        .catch(() => {
          dropdown.innerHTML = '<p style="color: red; padding: 10px;">Error al cargar playlists.</p>';
        });
    }
  });

  document.querySelectorAll('.btn-eliminar-playlist').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation(); // 🚫 Detiene que el clic se propague al contenedor
                const playlistId = btn.dataset.id;
                const playlistNombre = btn.dataset.nombre;

                document.getElementById('nombre-playlist').textContent = playlistNombre;
                document.getElementById('modalEliminarPlaylist').style.display = 'block';
                document.getElementById('confirmarEliminarPlaylist').dataset.id = playlistId;
            });
        });


        // Cerrar modal
        document.getElementById('cancelarEliminarPlaylist').addEventListener('click', () => {
            document.getElementById('modalEliminarPlaylist').style.display = 'none';
        });

        // Confirmar eliminación
        document.getElementById('confirmarEliminarPlaylist').addEventListener('click', () => {
            const id = document.getElementById('confirmarEliminarPlaylist').dataset.id;

            fetch('/ajax/eliminar_playlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_playlist: id })
            })
            .then(response => response.json())
            .then(data => {
            if (data.success) {
                document.getElementById('modalEliminarPlaylist').style.display = 'none';
                location.reload(); // o eliminar el div de la playlist del DOM
            } else {
                alert('Error al eliminar playlist');
            }
            });
        });
});
</script>