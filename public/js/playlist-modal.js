document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal-playlists");
    const lista = document.getElementById("lista-playlists");
    const cerrar = document.querySelector(".cerrar-modal");

    let idCancionSeleccionada = null;

    // DELEGACIÓN para ambos corazones
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.corazon-blanco, .corazon-gradient');
        if (!btn) return;

        const parent = btn.closest('.add-playlist');
        idCancionSeleccionada = parent.dataset.id;

        // Obtener playlists disponibles
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
                                // Mostrar siempre el degradado (aunque ya lo esté)
                                parent.querySelector('.corazon-blanco').style.display = 'none';
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

    // ✅ Marcar corazones al cargar
    fetch('/ajax/canciones_en_playlist.php')
        .then(res => res.json())
        .then(ids => {
            console.log("🎯 IDs de canciones ya en playlist:", ids);
            document.querySelectorAll('.add-playlist').forEach(div => {
                const id = div.dataset.id;
                if (ids.includes(parseInt(id))) {
                    div.querySelector('.corazon-blanco').style.display = 'none';
                    div.querySelector('.corazon-gradient').style.display = 'inline';
                }
            });
        })
        .catch(err => console.error("Error al cargar canciones añadidas:", err));
});
