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
        <link rel="icon" type="image/png" href="img/image-brand.png">
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
                    <div id="bloque-default">
                        <div id="contenido-default">
                            <section id="playlists-destacadas">
                                <h2>Álbumes Destacados</h2>
                                <div class="grid">
                                    <div class="card card-album" data-id="2"><img src="uploads/foto-album/2.jpg"><div>Un Verano Sin Ti</div></div>
                                    <div class="card card-album" data-id="4"><img src="uploads/foto-album/4.jpg"><div>Easy Money Baby</div></div>
                                    <div class="card card-album" data-id="3"><img src="uploads/foto-album/3.jpg"><div>Emmanuel</div></div>
                                    <div class="card card-album" data-id="5"><img src="uploads/foto-album/5.jpg"><div>Timelezz</div></div>
                                </div>
                            </section>
                            <section id="tendencias">
                                <h2>Tendencias</h2>
                                <div class="grid">
                                    <div class="card card-album" data-id="1"><img src="uploads/foto-album/1.jpg"><div>Donde Quiero Estar</div></div>
                                    <div class="card card-album" data-id="11"><img src="uploads/foto-album/11.jpg"><div>Yonaguni</div></div>
                                    <div class="card card-album" data-id="25"><img src="uploads/foto-album/25.jpg"><div>Pareja del año</div></div>
                                    <div class="card card-album" data-id="27"><img src="uploads/foto-album/27.jpg"><div>Fiel Remix</div></div>
                                </div>
                            </section>
                            <section id="recomendaciones">
                                <h2>Recomendaciones</h2>
                                <div class="grid">
                                    <div class="card card-artista" data-id="3"><img src="img/AnueDobleA.jpg"><div>Anuel AA</div></div>
                                    <div class="card card-artista" data-id="2"><img src="img/ConejoMalo.jpg"><div>Bad Bunny</div></div>
                                    <div class="card card-artista" data-id="4"><img src="img/myke.jpg"><div>Myke Towers</div></div>
                                </div>
                            </section>
                        </div> 
                    </div>
                    <div id="resultados"></div>
                    <div id="contenido-principal"></div>
                </div>
                <div id="resultados-dinamicos"></div>
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
                    <button id="btn-prev">⏮</button>
                    <button id="btn-play"></button>
                    <button id="btn-next">⏭</button>
                </div>
            </div>
            <div class="reproductor-derecha">
                <input type="range" id="volumen" min="0" max="1" step="0.01" value="1">
            </div>
            <audio id="audio-player" src=""></audio>
            <input type="range" id="barra-progreso" value="0" step="0.01">
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
function ocultarContenidoDefault() {
    document.getElementById('bloque-default').style.display = 'none';
    document.getElementById('contenido-principal').style.display = 'block';
}

function mostrarContenidoDefault() {
    document.getElementById('bloque-default').style.display = 'block';
    document.getElementById('contenido-principal').style.display = 'none';
    document.getElementById('resultados-dinamicos').innerHTML = '';
    document.getElementById('resultados-dinamicos').style.display = 'none';
}

function ejecutarScriptsDinamicos(contenedor) {
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
}

function mostrarResultados(html) {
    document.getElementById('bloque-default').style.display = 'none';
    document.getElementById('contenido-principal').style.display = 'block';
    document.getElementById('resultados').innerHTML = html;
}

// Buscador
document.getElementById('buscador').addEventListener('keyup', function () {
    const query = this.value.trim();

    if (query.length < 2) {
        mostrarContenidoDefault();
        document.getElementById('resultados').innerHTML = '';
        return;
    }

    fetch('/buscar.php?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            let html = '';

            if (data.artistas.length > 0) {
                html += '<h2>Artistas</h2><ul style="display: flex; gap: 10px;">';
                data.artistas.forEach(a => {
                    const rutaFoto = a.foto_perfil ? `/uploads/perfiles/${a.foto_perfil}` : '/uploads/perfiles/default.jpg';
                    html += `<li class="card-artista" data-id="${a.id_usuario}">
                                <img src="${rutaFoto}" style="width: 100px; height: 100px; border-radius: 50%;">
                                <p>${a.nombre}</p>
                             </li>`;
                });
                html += '</ul>';
            }

            if (data.albums.length > 0) {
                html += '<h2>Álbumes</h2><ul style="display: flex; gap: 10px;">';
                data.albums.forEach(a => {
                    html += `<li class="card-album" data-id="${a.id_album}">
                                <img src="/uploads/foto-album/${a.id_album}.jpg" style="width: 100px; height: 100px;border-radius: 10%;">
                                <p>${a.nombre}</p>
                             </li>`;
                });
                html += '</ul>';
            }

            if (data.canciones.length > 0) {
                html += '<h2>Canciones</h2>';
                data.canciones.forEach(c => {
                    html += `<div class="container-cancion">
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
                                <div class="info-cancion">
                                    <strong>${c.nombre_c}</strong>
                                    <span>${c.artista || 'Desconocido'}</span>
                                </div>
                            </div>`;
                });
            }

            if (!html) html = '<p>No se encontraron resultados.</p>';
            mostrarResultados(html);

            document.getElementById('resultados').innerHTML = html;
            window.albumActual = undefined;
            window.artistaActual = undefined;

            document.querySelectorAll('.hover-overlay').forEach(el => {
                el.addEventListener('click', function (e) {
                    const id = this.dataset.id;
                    fetch('/ajax/cancion.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            const contenedor = document.getElementById('contenido-principal');
                            contenedor.innerHTML = html;
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

            document.querySelectorAll('.card-album').forEach(card => {
                card.addEventListener('click', function () {
                    if (!this.closest('#resultados')) return;
                    const id = this.dataset.id;
                    fetch('/ajax/album.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            // Limpiar y mostrar solo el contenido principal
                            document.getElementById('resultados-dinamicos').innerHTML = '';
                            document.getElementById('resultados-dinamicos').style.display = 'none';
                            document.getElementById('bloque-default').style.display = 'none';
                            document.getElementById('contenido-principal').style.display = 'block';
                            const contenedor = document.getElementById('contenido-principal');
                            contenedor.innerHTML = html;
                            ejecutarScriptsDinamicos(contenedor);
                            activarEventosAudio();
                            activarResaltadoCancion();
                            marcarCorazones();
                        });
                });
            });

            document.querySelectorAll('.card-artista').forEach(card => {
                card.addEventListener('click', function () {
                if (!this.closest('#resultados')) return;
                    const id = this.dataset.id;
                    fetch('/ajax/artista.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            // Limpiar y mostrar solo el contenido principal
                            document.getElementById('resultados-dinamicos').innerHTML = '';
                            document.getElementById('resultados-dinamicos').style.display = 'none';
                            document.getElementById('bloque-default').style.display = 'none';
                            document.getElementById('contenido-principal').style.display = 'block';
                            const contenedor = document.getElementById('contenido-principal');
                            contenedor.innerHTML = html;
                            ejecutarScriptsDinamicos(contenedor);
                            activarEventosAudio();
                            activarResaltadoCancion();
                            marcarCorazones();
                        });
                });
            });
        });
});

// Delegación de eventos para destacados/recomendaciones
document.addEventListener('click', function(e) {
    // Si el clic es dentro de #resultados, no hacer nada aquí
    if (e.target.closest('#resultados')) return;

    // ARTISTA
    const cardArtista = e.target.closest('.card-artista');
    if (cardArtista && !cardArtista.closest('#resultados')) {
        const id = cardArtista.dataset.id;
        fetch('/ajax/artista.php?id=' + id)
            .then(res => res.text())
            .then(html => {
                // Limpiar y mostrar solo resultados dinámicos
                document.getElementById('contenido-principal').innerHTML = '';
                document.getElementById('contenido-principal').style.display = 'none';
                document.getElementById('resultados-dinamicos').style.display = 'block';
                const resultadosDinamicos = document.getElementById('resultados-dinamicos');
                resultadosDinamicos.innerHTML = html;
                resultadosDinamicos.scrollIntoView({ behavior: 'smooth' });
                ejecutarScriptsDinamicos(resultadosDinamicos);
                activarEventosAudio();
                activarResaltadoCancion();
                marcarCorazones();
            });
        return;
    }

    // ÁLBUM
    const cardAlbum = e.target.closest('.card-album');
    if (cardAlbum && !cardAlbum.closest('#resultados')) {
        const id = cardAlbum.dataset.id;
        fetch('/ajax/album.php?id=' + id)
            .then(res => res.text())
            .then(html => {
                // Limpiar y mostrar solo resultados dinámicos
                document.getElementById('contenido-principal').innerHTML = '';
                document.getElementById('contenido-principal').style.display = 'none';
                document.getElementById('resultados-dinamicos').style.display = 'block';
                const resultadosDinamicos = document.getElementById('resultados-dinamicos');
                resultadosDinamicos.innerHTML = html;
                resultadosDinamicos.scrollIntoView({ behavior: 'smooth' });
                ejecutarScriptsDinamicos(resultadosDinamicos);
                activarEventosAudio();
                activarResaltadoCancion();
                marcarCorazones();
            });
        return;
    }

    // PLAYLIST
    const cardPlaylist = e.target.closest('.card-playlist');
    if (cardPlaylist && !cardPlaylist.closest('#resultados')) {
        const id = cardPlaylist.dataset.id;
        fetch('/ajax/playlist.php?id=' + id)
            .then(res => res.text())
            .then(html => {
                // Limpiar y mostrar solo resultados dinámicos
                document.getElementById('contenido-principal').innerHTML = '';
                document.getElementById('contenido-principal').style.display = 'none';
                document.getElementById('resultados-dinamicos').style.display = 'block';
                const resultadosDinamicos = document.getElementById('resultados-dinamicos');
                resultadosDinamicos.innerHTML = html;
                resultadosDinamicos.scrollIntoView({ behavior: 'smooth' });
                ejecutarScriptsDinamicos(resultadosDinamicos);
                activarEventosAudio();
                activarResaltadoCancion();
                marcarCorazones();
            });
        return;
    }
});

// Solo permite que una canción suene a la vez en la aplicación.
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
    audio.addEventListener('_custom_timeupdate', audio._custom_timeupdate_handler);
}

// Corazones activos si ya están en playlist
function marcarCorazones() {
    fetch('/ajax/canciones_en_playlist.php')
        .then(res => res.json())
        .then(ids => {
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
            }, 50);
        });
}

// Resalta la canción que está sonando actualmente en la interfaz
function activarResaltadoCancion() {
    document.querySelectorAll('.hover-overlay.activa').forEach(el => {
        el.classList.remove('activa');
    });

    const audio = document.getElementById("audio-player");
    if (!audio || !audio.dataset.id) return;
    const idActual = String(audio.dataset.id);

    let encontrado = false;
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

// Barra de volumen personalizada
const volumen = document.getElementById("volumen");
function actualizarBarraVolumen() {
    const valor = volumen.value;
    const porcentaje = valor * 100;
    volumen.style.backgroundImage = `linear-gradient(to right, #3A157F 0%, #DB3DAC ${porcentaje}%, #ddd ${porcentaje}%, #ddd 100%)`;
}
actualizarBarraVolumen();
volumen.addEventListener('input', actualizarBarraVolumen);
</script>


