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
                    li.classList.add('playlist-item-modal');

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
                                parent.querySelector('.corazon-blanco')?.classList.add('oculto');
                                const gradiente = parent.querySelector('.corazon-gradient');
                                gradiente?.classList.remove('oculto');
                                gradiente?.style.removeProperty('display');
                                // Mostrar mensaje de éxito dentro del modal
                                const msg = document.createElement('p');
                                msg.textContent = "🎉 Añadida correctamente";
                                msg.style.color = 'lightgreen';
                                msg.style.textAlign = 'center';
                                msg.style.marginTop = '10px';
                                lista.appendChild(msg);
                                setTimeout(() => {
                                    msg.remove();
                                }, 2500);

                                // Recargar la playlist completa tras añadir la canción
                                fetch(`/ajax/playlist.php?id=${p.id_playlist}`)
                                    .then(res => res.text())
                                    .then(html => {
                                        const contenedor = document.getElementById('contenido-principal');
                                        contenedor.innerHTML = html;
                                        if (typeof ejecutarScriptsDinamicos === "function") {
                                            ejecutarScriptsDinamicos(contenedor);
                                        }
                                    });
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
                    const gradiente = div.querySelector('.corazon-gradient');
                    gradiente?.classList.remove('oculto');
                    gradiente?.style.removeProperty('display');
                } else {
                    div.querySelector('.corazon-blanco')?.classList.remove('oculto');
                    const gradiente = div.querySelector('.corazon-gradient');
                    gradiente?.classList.add('oculto');
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

        window.addEventListener('click', (e) => {
            if (e.target === modalNuevaPlaylist) {
                modalNuevaPlaylist.style.display = 'none';
            }
        });
    }

    // Delegación de eventos para borrar canciones de cualquier playlist
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-eliminar');
        if (!btn) return;

        const form = btn.closest('.form-eliminar-cancion');
        if (!form) return;

        // Mostrar modal de confirmación
        const modal = document.getElementById('modal-confirmacion');
        if (!modal) return;
        modal.classList.remove('oculto');

        window.formPendienteEliminar = form;
    });

    document.body.addEventListener('click', function(e) {
        // Cancelar
        if (e.target && e.target.id === 'btn-cancelar') {
            const modal = document.getElementById('modal-confirmacion');
            if (modal) modal.classList.add('oculto');
            window.formPendienteEliminar = null;
        }

        // Confirmar
        if (e.target && e.target.id === 'btn-confirmar') {
            const form = window.formPendienteEliminar;
            if (!form) return;

            const idCancion = form.dataset.idCancion;
            const idPlaylist = form.dataset.idPlaylist;

            fetch('ajax/eliminar_cancion_playlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_cancion=${idCancion}&id_playlist=${idPlaylist}`
            })
            .then(res => res.text())
            .then(response => {
                if (response.trim() === 'ok') {
                    form.closest('.container-cancion')?.remove();
                } else {
                    alert('❌ Error inesperado al eliminar la canción.');
                }
            })
            .catch(err => {
                alert('❌ Error de red: ' + err.message);
            })
            .finally(() => {
                const modal = document.getElementById('modal-confirmacion');
                if (modal) modal.classList.add('oculto');
                window.formPendienteEliminar = null;
            });
        }
    });

    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-eliminar-playlist');
        if (!btn) return;

        e.stopPropagation();
        const playlistId = btn.dataset.id;
        const playlistNombre = btn.dataset.nombre;

        document.getElementById('nombre-playlist').textContent = playlistNombre;
        document.getElementById('modalEliminarPlaylist').style.display = 'block';
        document.getElementById('confirmarEliminarPlaylist').dataset.id = playlistId;
    });

    document.body.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'confirmarEliminarPlaylist') {
            const id = document.getElementById('confirmarEliminarPlaylist').dataset.id;
            fetch('/ajax/eliminar_playlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_playlist=${id}`
            })
            .then(res => res.text())
            .then(response => {
                if (response.trim() === 'ok') {
                    document.querySelectorAll(`.playlist-item[data-id="${id}"]`).forEach(el => el.remove());
                    document.getElementById('modalEliminarPlaylist').style.display = 'none';

                    const dropdown = document.getElementById("dropdown-playlists");
                    if (dropdown) {
                        fetch('/ajax/obtener_playlists_usuario.php')
                            .then(res => res.json())
                            .then(data => {
                                dropdown.innerHTML = '';
                                if (!Array.isArray(data) || data.length === 0) {
                                    dropdown.innerHTML = '<p style="color: gray; padding: 10px;">No tienes playlists.</p>';
                                    return;
                                }
                                data.forEach(p => {
                                    const div = document.createElement("div");
                                    div.className = "playlist-item";
                                    div.dataset.id = p.id_playlist;
                                    div.innerHTML = `
                                    <img src="/uploads/foto-playlist/${p.foto || 'default.jpg'}" alt="Playlist">
                                    <span>${p.nombre}</span>
                                    <button class="btn-eliminar-playlist" data-id="${p.id_playlist}" data-nombre="${p.nombre}">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                            <defs>
                                                <linearGradient id="degradado-x" x1="0" y1="0" x2="18" y2="18" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#481B9A"/>
                                                <stop offset="1" stop-color="#FF4EC4"/>
                                                </linearGradient>
                                            </defs>
                                            <path d="M4 4L14 14M14 4L4 14" stroke="url(#degradado-x)" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                    `;
                                    dropdown.appendChild(div);
                                });
                            });
                    }
                    const resultados = document.getElementById('resultados-dinamicos');
                    if (resultados && resultados.innerHTML.includes(`data-id="${id}"`)) {
                        resultados.innerHTML = '';
                        resultados.style.display = 'none';
                        const contenidoPrincipal = document.getElementById('contenido-principal');
                        if (contenidoPrincipal) {
                            contenidoPrincipal.innerHTML = '<p style="color: gray; padding: 30px; text-align:center;">Esta playlist ha sido eliminada.</p>';
                            contenidoPrincipal.style.display = 'block';
                        }
                    }
                } else {
                    alert('Error al eliminar playlist');
                }
            })
            .catch(() => {
                alert('Error al eliminar playlist');
            });
        }
    });

    document.body.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'cancelarEliminarPlaylist') {
            document.getElementById('modalEliminarPlaylist').style.display = 'none';
        }
    });
});
