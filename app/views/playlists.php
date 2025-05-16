<input type="text" id="buscador" placeholder="Buscar canción, artista o álbum..." autocomplete="off">

<div id="resultados"></div>

<div id="contenido-principal">

</div>

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
            if (data.canciones.length > 0) {
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
            }

            // Artistas
            if (data.artistas.length > 0) {
                html += '<h3>Artistas</h3><ul>';
                data.artistas.forEach(a => {
                    html += `<li><a href="/artista?id=${a.id_usuario}">${a.nombre}</a></li>`;
                });
                html += '</ul>';
            }

            // Álbumes
            if (data.albums.length > 0) {
                html += '<h3>Álbumes</h3><ul>';
                data.albums.forEach(a => {
                    html += `<li><a href="#" class="enlace-album" data-id="${a.id_album}">${a.nombre}</a> (Artista: ${a.artista})</li>`;
                });
                html += '</ul>';
            }


            if (!html) html = '<p>No se encontraron resultados.</p>';

            document.getElementById('resultados').innerHTML = html;

            // Delegar eventos para navegación AJAX
            document.querySelectorAll('.enlace-album').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.dataset.id;

                    fetch('/ajax/album.php?id=' + id)
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('contenido-principal').innerHTML = html;
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
                        });
                });
            });
        });
});
</script>
