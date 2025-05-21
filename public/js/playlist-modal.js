document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal-playlists");
    const lista = document.getElementById("lista-playlists");
    const cerrar = document.querySelector(".cerrar-modal");

    let idCancionSeleccionada = null;

    // DELEGACIÓN GLOBAL en document.body
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.corazon-blanco');
        if (!btn) return;

        const parent = btn.closest('.add-playlist');
        idCancionSeleccionada = parent.dataset.id;

        fetch('/ajax/obtener_playlists_usuario.php')
            .then(res => res.json())
            .then(playlists => {
                lista.innerHTML = '';
                playlists.forEach(p => {
                    const li = document.createElement('li');
                    li.textContent = p.nombre;
                    li.style.cursor = 'pointer';
                    li.onclick = () => {
                        fetch('/ajax/insertar_en_playlist.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `id_cancion=${idCancionSeleccionada}&id_playlist=${p.id_playlist}`
                        })
                        .then(res => res.text())
                        .then(response => {
                            if (response === 'ok' || response === 'exists') {
                                btn.style.display = 'none';
                                parent.querySelector('.corazon-gradient').style.display = 'inline';
                                modal.style.display = 'none';
                            } else {
                                alert("❌ Error al añadir a la playlist.");
                            }
                        });
                    };
                    lista.appendChild(li);
                });

                modal.style.display = 'block';
            })
            .catch(err => {
                console.error("Error obteniendo playlists:", err);
                alert("❌ Error al obtener las playlists.");
            });
    });

    // Cerrar modal
    cerrar.onclick = () => modal.style.display = 'none';
    window.onclick = e => {
        if (e.target === modal) modal.style.display = "none";
    };

    // Al cargar, marcar los corazones de canciones ya añadidas
    fetch('/ajax/canciones_en_playlist.php')
        .then(res => res.json())
        .then(ids => {
            document.querySelectorAll('.add-playlist').forEach(div => {
                const id = div.dataset.id;
                if (ids.includes(id)) {
                    div.querySelector('.corazon-blanco').style.display = 'none';
                    div.querySelector('.corazon-gradient').style.display = 'inline';
                }
            });
        })
        .catch(err => console.error("Error al cargar canciones añadidas:", err));

});
