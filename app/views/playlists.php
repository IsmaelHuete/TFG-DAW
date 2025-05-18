
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/playlist.css">
    <link rel="stylesheet" href="css/header1.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <?php 
        include ("layouts/header1.php");
    ?>
    <main>
        <div class="container">
            <input type="text" id="buscador" placeholder="Buscar canción, artista o álbum..." autocomplete="off">
            <div class="reproductor">
                <div id="resultados"></div>
                <div id="contenido-principal"></div>
            </div>
        </div>
    </main>
    
<?php 
        include ("layouts/footer.php");
    ?>
</div>
<script src="js/header.js"></script>
    <script src="js/home.js"></script>
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

            // Canciones
            /* if (data.canciones.length > 0) {
                html += '<h3>Canciones</h3><ul style="list-style: none; padding: 0;">';
                data.canciones.forEach(c => {
                    html += `
                        <li style="display: flex; align-items: center; margin-bottom: 15px;">
                            <img src="${c.foto_album}" alt="Carátula" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 8px;">
                            <div>
                                <strong>${c.nombre_c}</strong> (Álbum: ${c.album})<br>
                                <audio controls>
                                    <source src="/uploads/canciones/${c.id_cancion}.mp3" type="audio/mpeg">
                                    Tu navegador no soporta audio.
                                </audio>
                            </div>
                        </li>
                    `;
                });
                html += '</ul>';
            } */

            // Artistas
           /*  if (data.artistas.length > 0) {
                html += '<h3>Artistas</h3><ul>';
                data.artistas.forEach(a => {
                html += `<li><a href="#" class="enlace-artista" data-id="${a.id_usuario}">${a.nombre}</a></li>`;
            });
                html += '</ul>';
            }
 */
            // Álbumes
            // Álbumes
            if (data.albums.length > 0) {
                html += '<h3>Álbumes</h3><ul style="display:flex; flex-wrap: wrap; gap: 10px; list-style: none;">';
                data.albums.forEach(a => {
                    html += `
                        <li class="card-album" style="width: 120px; cursor: pointer;" data-id="${a.id_album}">
                            <img src="/uploads/foto-album/${a.id_album}.jpg" alt="${a.nombre}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                            <p>${a.nombre}</p>
                        </li>
                    `;
                });
                html += '</ul>';
            }


            if (!html) html = '<p>No se encontraron resultados.</p>';

            document.getElementById('resultados').innerHTML = html;

            // Delegar eventos para navegación AJAX
            document.querySelectorAll('.card-album').forEach(card => {
            card.addEventListener('click', function () {
                const id = this.dataset.id;

                fetch('/ajax/album.php?id=' + id)
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('contenido-principal').innerHTML = html;
                        activarEventosAudio();
                    });
            });
        });

            // Y para artistas:
            document.querySelectorAll('.enlace-artista').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.dataset.id;

                    fetch('/ajax/artista.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('contenido-principal').innerHTML = html;
                            activarEventosAudio();
                        });
                });
            });
        });
});
// Asegura que solo se reproduzca una canción a la vez
document.addEventListener('play', function (e) {
    const audios = document.querySelectorAll('audio');
    audios.forEach(audio => {
        if (audio !== e.target) {
            audio.pause();
        }
    });
}, true);
// Función que activa el seguimiento de reproducción (se llamará tras cada carga AJAX)
function activarEventosAudio() {
    document.querySelectorAll('audio').forEach(audio => {
        let reproducido = false;

        audio.addEventListener('timeupdate', () => {
            if (reproducido) return;

            const segundos = audio.currentTime;
            const porcentaje = segundos / audio.duration;

            if (segundos >= 10 || porcentaje >= 0.3) {
                reproducido = true;

                const idCancion = audio.dataset.id;

                fetch('/repro.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id_cancion: idCancion })
                });
            }
        });
    });
}
</script>
