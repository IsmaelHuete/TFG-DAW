<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: /login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Musicfy</title>
        <link rel="stylesheet" href="css/comun.css">
        <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="css/header1.css">
        <link rel="stylesheet" href="css/footer.css">
    </head>
    <body>
        <?php 
            include ("layouts/header-playlist.php");
        ?>
        <main>
            <div class="container">
                <div class="buscador-con-boton">
                    <input type="text" id="buscador" placeholder="Buscar canción, artista o álbum..." autocomplete="off">
                    <button id="btn-nueva-playlist" class="boton-mas" title="Crear nueva playlist">+</button>
                </div>            
                <div class="reproductor">
                    <div id="resultados"></div>
                    <div id="contenido-principal"></div>
                </div>
            </div>
        </main>


        <div id="reproductor-persistente" class="reproductor-persistente" style="display: none;">
            <div class="reproductor-izquierda">
                <img id="cover-img" src="/img/image-brand.png" alt="Portada" style="width:50px;height:50px;" />
                <div>
                    <div id="titulo-cancion">Sin canción</div>
                    <div id="nombre-artista">Desconocido</div>
                </div>
            </div>
            <div class="reproductor-centro">
            <div class="controles">
                <button id="btn-prev">⏮️</button>
                <button id="btn-play">▶️</button>
                <button id="btn-next">⏭️</button>
            </div>
            <input type="range" id="barra-progreso" value="0" step="0.01">
        </div>
            <div class="reproductor-derecha">
                <input type="range" id="volumen" min="0" max="1" step="0.01" value="1">
            </div>
            <audio id="audio-player" src=""></audio>
        </div>


        <!-- Modal para seleccionar playlist -->
        <div id="modal-playlists" class="modal" style="display:none;">
            <div class="modal-content1">
                <span class="cerrar-modal">&times;</span>
                <h3>Elige una playlist</h3>
                <div id="lista-playlists"></div>
            </div>
        </div>

        <!-- Modal para crear nueva playlist -->
        <div id="modal-nueva-playlist" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="cerrar-modal" id="cerrar-nueva-playlist">&times;</span>
                <h3>Crear nueva playlist</h3>
                <form action="/ajax/crear_playlist.php" method="POST" enctype="multipart/form-data">
                    <input type="text" name="nombre_playlist" placeholder="Nombre de la playlist" required>
                    <input type="file" name="foto" accept="image/*" required>
                    <button type="submit" name="subir_playlist">Crear Playlist</button>
                </form>
            </div>
        </div>
    
    
    




        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $isGratis = false;

        if (isset($_SESSION['email'])) {
            require_once __DIR__ . '/../../config/Conexion_BBDD.php';


            $stmt = $pdo->prepare("SELECT plan FROM usuario WHERE email = ?");
            $stmt->execute([$_SESSION['email']]);
            $tipo = $stmt->fetchColumn();

            $isGratis = ($tipo === 'gratuito');
        }
        ?>
        <script>
            window.usuarioGratis = <?= $isGratis ? 'true' : 'false' ?>;
        </script>

        <?php include ("layouts/footer.php"); ?>
        <script src="js/header.js"></script>
        <script src="js/home.js"></script>
        <script src="js/playlist-modal.js"></script>
        <script src="js/reproductor.js"></script>
    </body>

</html>


<script>
document.getElementById('buscador').addEventListener('keyup', function () {
    const query = this.value.trim();

    if (query.length < 2) {
        document.getElementById('resultados').innerHTML = '';
        return;
    }

    fetch('/buscar.php?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            let html = '';
 


// ARTISTAS
            if (data.artistas.length > 0) {
                html += '<h2>Artistas</h2><ul style="display: flex; flex-wrap: wrap; gap: 10px; list-style: none;">';
                data.artistas.forEach(a => {
                    const rutaFoto = a.foto_perfil ? `/uploads/perfiles/${a.foto_perfil}` : '/uploads/perfiles/default.jpg';

                    html += `
                        <li class="card-artista" style="width: 120px; cursor: pointer;" data-id="${a.id_usuario}">
                            <img src="${rutaFoto}" alt="${a.nombre}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                            <p style="text-align: center;">${a.nombre}</p>
                        </li>
                    `;
                });
                html += '</ul>';
            }

            // ÁLBUMES
            if (data.albums.length > 0) {
                html += '<h2>Álbumes</h2><ul style="display: flex; flex-wrap: wrap; gap: 10px; list-style: none;">';
                data.albums.forEach(a => {
                    html += `
                        <li class="card-album" style="width: 120px; cursor: pointer;" data-id="${a.id_album}">
                            <img src="/uploads/foto-album/${a.id_album}.jpg" alt="${a.nombre}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                            <p style="text-align: center;">${a.nombre}</p>
                        </li>
                    `;
                });
                html += '</ul>';
            }

            // Canciones
            // CANCIONES como cards visuales
            if (data.canciones.length > 0) {

                    html += '<h2>Canciones</h2>';
                    data.canciones.forEach(c => {
                        html += `
                                <div class="container-cancion" style="display: flex;">
                                    <div class="img-wrapper">
                                        <img src="${c.foto_album}" alt="Carátula">
                                        <div class="hover-overlay"
                                            data-src="/uploads/canciones/${c.id_cancion}.mp3"
                                            data-id="${c.id_cancion}"
                                            data-title="${c.nombre_c}"
                                            data-artist="${c.artista || 'Desconocido'}"
                                            data-cover="${c.foto_album}"
                                            data-suelta="1">
                                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px">
                                                <defs>
                                                    <linearGradient id="grad-play" x1="0%" y1="0%" x2="100%" y2="0%">
                                                    <stop offset="0%" style="stop-color:#481B9A; stop-opacity:1" />
                                                    <stop offset="100%" style="stop-color:#FF4EC4; stop-opacity:1" />
                                                    </linearGradient>
                                                </defs>
                                                <path fill="url(#grad-play)" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5.46484 3.92349C4.79896 3.5739 4 4.05683 4 4.80888V19.1911C4 19.9432 4.79896 20.4261 5.46483 20.0765L19.1622 12.8854C19.8758 12.5108 19.8758 11.4892 19.1622 11.1146L5.46484 3.92349ZM2 4.80888C2 2.55271 4.3969 1.10395 6.39451 2.15269L20.0919 9.34382C22.2326 10.4677 22.2325 13.5324 20.0919 14.6562L6.3945 21.8473C4.39689 22.8961 2 21.4473 2 19.1911V4.80888Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="cancion" style="flex-grow: 1;">
                                        <div class="info-cancion">
                                            <strong>${c.nombre_c}</strong>
                                            <span>${c.artista || 'Desconocido'}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                    });
                    html += '</ul>';
                }
            

            // Playliost
           /*  if (data.playlist.length > 0) {
                html += '<h3>Mis playlists</h3><ul style="display: flex; flex-wrap: wrap; gap: 10px; list-style: none; padding: 0;">';
                data.playlist.forEach(a => {
                    html += `
                        <li class="card-album" style="width: 120px; cursor: pointer;" data-id="${a.id_album}">
                            <img src="/uploads/foto-album/${a.id_album}.jpg" alt="${a.nombre}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                            <p style="text-align: center;">${a.nombre}</p>
                        </li>
                    `;
                });
                html += '</ul>';
            } */


            if (!html) html = '<p>No se encontraron resultados.</p>';






            document.getElementById('resultados').innerHTML = html;
            window.albumActual = undefined;
            window.artistaActual = undefined;

            // Evento para canciones sueltas (solo cuando NO hay playlist global)
            document.querySelectorAll('.hover-overlay').forEach(el => {
                el.addEventListener('click', function (e) {
                    // Si es una canción suelta (por ejemplo, por data-suelta o porque no hay playlist global)
                    const id = this.dataset.id;
                    fetch('/ajax/cancion.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            const contenedor = document.getElementById('contenido-principal');
                            contenedor.innerHTML = html;

                            // Ejecutar scripts embebidos (para definir window.cancionActual)
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

                            if (typeof activarEventosAudio === "function") activarEventosAudio();
                            if (typeof activarResaltadoCancion === "function") activarResaltadoCancion();
                        });
                    e.stopPropagation();
                });
            });


            // EVENTO ÁLBUM
            document.querySelectorAll('.card-album').forEach(card => {
                card.addEventListener('click', function () {
                    const id = this.dataset.id;

                    fetch('/ajax/album.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            const contenedor = document.getElementById('contenido-principal');
                            contenedor.innerHTML = html;

                            // 🔁 Ejecutar manualmente los <script> insertados (como el de albumActual)
                            contenedor.querySelectorAll('script').forEach(oldScript => {
                                const nuevoScript = document.createElement('script');
                                if (oldScript.src) {
                                    nuevoScript.src = oldScript.src;
                                } else {
                                    nuevoScript.textContent = oldScript.textContent;
                                }
                                document.body.appendChild(nuevoScript);
                                oldScript.remove(); // opcional
                            });

                            activarEventosAudio();
                            activarResaltadoCancion();
                            marcarCorazones();
                        });
                });
            });


            // EVENTO ARTISTA
            document.querySelectorAll('.card-artista').forEach(card => {
                card.addEventListener('click', function () {
                    const id = this.dataset.id;

                    fetch('/ajax/artista.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            const contenedor = document.getElementById('contenido-principal');
                            contenedor.innerHTML = html;

                            // Ejecutar scripts embebidos
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
            });
        });
});

// FUNCIONES

// Solo una canción sonando a la vez
document.addEventListener('play', function (e) {
    const audios = document.querySelectorAll('audio');
    audios.forEach(audio => {
        if (audio !== e.target) {
            audio.pause();
        }
    });
}, true);

// Seguimiento de reproducción
function activarEventosAudio() {
    const audio = document.getElementById("audio-player");
    if (!audio) return;

    // Elimina listeners previos
    audio.removeEventListener('_custom_timeupdate', audio._custom_timeupdate_handler);
    audio._custom_timeupdate_handler = function () {
        if (window.reproducido) return;

        const segundos = audio.currentTime;
        const porcentaje = segundos / audio.duration;

        if (segundos >= 10 || porcentaje >= 0.3) {
            window.reproducido = true;

            const idCancion = audio.dataset.id;
            if (!idCancion) return;

            fetch('/repro.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_cancion: idCancion })
            });
        }
    };
    audio.addEventListener('timeupdate', audio._custom_timeupdate_handler);
    // Marca el handler para poder eliminarlo después
    audio.addEventListener('_custom_timeupdate', audio._custom_timeupdate_handler);
}

// Corazones activos si ya están en playlist
function marcarCorazones() {
    fetch('/ajax/canciones_en_playlist.php')
        .then(res => res.json())
        .then(ids => {
            // Espera a que el DOM esté listo
            setTimeout(() => {
                document.querySelectorAll('.add-playlist').forEach(div => {
                    const id = div.dataset.id;
                    if (ids.includes(parseInt(id))) {
                        div.querySelector('.corazon-blanco')?.classList.add('oculto');
                        div.querySelector('.corazon-gradient')?.classList.remove('oculto');
                        div.querySelector('.corazon-gradient').style.display = 'block';
                    } else {
                        div.querySelector('.corazon-blanco')?.classList.remove('oculto');
                        div.querySelector('.corazon-gradient')?.classList.add('oculto');
                        div.querySelector('.corazon-gradient').style.display = 'none';
                    }

                });
            }, 50); // espera 50ms tras pintar el HTML
        });
}


function activarResaltadoCancion() {
    // Quita el resaltado anterior
    document.querySelectorAll('.hover-overlay.activa').forEach(el => {
        el.classList.remove('activa');
    });

    // Busca el overlay de la canción actual y resáltalo
    const audio = document.getElementById("audio-player");
    if (!audio || !audio.dataset.id) return;
    const idActual = String(audio.dataset.id);

    // Busca todos los overlays y compara como string
    document.querySelectorAll('.hover-overlay').forEach(el => {
        if (String(el.dataset.id) === idActual) {
            el.classList.add('activa');
            encontrado = true;
        }
    });
    if (!encontrado) {
        console.log('No se encontró overlay para id', idActual);
    }
}
const volumen = document.getElementById("volumen");

function actualizarBarraVolumen() {
    const valor = volumen.value;
    const porcentaje = valor * 100; // de 0 a 100
    volumen.style.backgroundImage = `linear-gradient(to right, #3A157F 0%, #DB3DAC ${porcentaje}%, #ddd ${porcentaje}%, #ddd 100%)`;
}

// Actualizar al iniciar
actualizarBarraVolumen();

// Actualizar al cambiar el valor
volumen.addEventListener('input', actualizarBarraVolumen);
</script>


