document.addEventListener("DOMContentLoaded", () => {
    const modalPlaylists = document.getElementById("modal-playlists");
    const lista = document.getElementById("lista-playlists");
    const cerrarPlaylists = document.querySelector("#modal-playlists .cerrar-modal");

    let idCancionSeleccionada = null;

    // CORAZÓN: abrir selección de playlist
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.corazon-blanco, .corazon-gradient');
        if (!btn) return;

        const parent = btn.closest('.add-playlist');
        idCancionSeleccionada = parent.dataset.id;

        fetch('/ajax/obtener_playlists_usuario.php')
            .then(res => res.json())
            .then(playlists => {
                lista.innerHTML = '';
                playlists.forEach(p => {
                    const li = document.createElement('li');
                    li.classList.add('playlist-item-modal'); // Para que puedas aplicar estilos

                    const rutaFoto = (p.foto && p.foto.trim() !== "")
                        ? `/uploads/foto-playlist/${p.foto}`
                        : '/uploads/foto-playlist/default.jpg';

                    li.innerHTML = `
                        <img src="${rutaFoto}" alt="${p.nombre}" class="img-playlist-modal">
                        <span>${p.nombre}</span>
                    `;

        li.style.cursor = 'pointer';
                    li.onclick = () => {
                        fetch('/ajax/insertar_en_playlist.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `id_cancion=${idCancionSeleccionada}&id_playlist=${p.id_playlist}`
                        })
                        .then(res => res.text())
                        .then(response => {
                           if (response === 'ok') {
                            parent.querySelector('.corazon-blanco').style.display = 'none';
                            parent.querySelector('.corazon-gradient').style.display = 'inline';

                            // Mostrar mensaje de éxito dentro del modal
                            const msg = document.createElement('p');
                            msg.textContent = "🎉 Añadida correctamente";
                            msg.style.color = 'lightgreen';
                            msg.style.textAlign = 'center';
                            msg.style.marginTop = '10px';

                            lista.appendChild(msg);

                            setTimeout(() => {
                                msg.remove(); // Ocultar mensaje tras 2.5s
                            }, 2500);

                        } else if (response === 'exists') {
                            const msg = document.createElement('p');
                            msg.textContent = "⚠️ Ya estaba en la playlist";
                            msg.style.color = 'gold';
                            msg.style.textAlign = 'center';
                            msg.style.marginTop = '10px';

                            lista.appendChild(msg);

                            setTimeout(() => {
                                msg.remove();
                            }, 2500);
                        } else {
                            alert("❌ Error al añadir a la playlist.");
                        }
                        });
                    };
                    lista.appendChild(li);
                });

                modalPlaylists.style.display = 'block';
            })
            .catch(err => {
                console.error("Error obteniendo playlists:", err);
                alert("❌ Error al obtener las playlists.");
            });
    });

    // Cerrar modal de selección de playlist
    cerrarPlaylists.onclick = () => modalPlaylists.style.display = 'none';
    window.addEventListener('click', e => {
        if (e.target === modalPlaylists) modalPlaylists.style.display = "none";
        if (e.target === modalNuevaPlaylist) modalNuevaPlaylist.style.display = "none";
    });

    // MARCAR corazones activos al cargar
    fetch('/ajax/canciones_en_playlist.php')
        .then(res => res.json())
        .then(ids => {
            document.querySelectorAll('.add-playlist').forEach(div => {
                const id = div.dataset.id;
                if (ids.includes(parseInt(id))) {
                    div.querySelector('.corazon-blanco')?.classList.add('oculto');
                    div.querySelector('.corazon-gradient')?.classList.remove('oculto');
                }
            });
        })
        .catch(err => console.error("Error al cargar canciones añadidas:", err));

    // ----------------------------
    // NUEVO: Modal para crear playlist
    // ----------------------------
    const btnAbrirNuevaPlaylist = document.getElementById('btn-nueva-playlist');
    const modalNuevaPlaylist = document.getElementById('modal-nueva-playlist');
    const cerrarNuevaPlaylist = document.getElementById('cerrar-nueva-playlist');

    if (btnAbrirNuevaPlaylist && modalNuevaPlaylist && cerrarNuevaPlaylist) {
        btnAbrirNuevaPlaylist.addEventListener('click', () => {
            modalNuevaPlaylist.style.display = 'flex';
        });

        cerrarNuevaPlaylist.addEventListener('click', () => {
            modalNuevaPlaylist.style.display = 'none';
        });
    }
});
