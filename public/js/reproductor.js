document.addEventListener("DOMContentLoaded", () => {
    const audio = document.getElementById("audio-player");
    const playBtn = document.getElementById("btn-play");
    const prevBtn = document.getElementById("btn-prev");
    const nextBtn = document.getElementById("btn-next");
    const barra = document.getElementById("barra-progreso");
    const volumen = document.getElementById("volumen");
    const titulo = document.getElementById("titulo-cancion");
    const artista = document.getElementById("nombre-artista");
    const portada = document.getElementById("cover-img");
    const reproductor = document.getElementById("reproductor-persistente");

    let playlist = window.albumActual || window.artistaActual || []; // Se puede cargar dinámicamente
    let indiceActual = 0;

    function cargarCancion(cancion) {
        audio.dataset.id = cancion.id;
        audio.src = cancion.src
            ? cancion.src
            : `/uploads/stream.php?file=${cancion.id}.mp3`;

        titulo.textContent = cancion.titulo || "Sin título";
        artista.textContent = cancion.artista || "Desconocido";
        if (cancion.cover && cancion.cover.trim() !== '') {
            portada.src = cancion.cover;
        } else if (cancion.id_album) {
            portada.src = `/uploads/foto-album/${cancion.id_album}.jpg`;
        } else {
            portada.src = '/img/default-cover.jpg'; // o cualquier imagen por defecto
        }


        reproductor.style.display = "flex";
        window.reproducido = false;
        if (cancion.esAnuncio) {
            nextBtn.disabled = true;
            nextBtn.style.opacity = 0.5;
            nextBtn.style.cursor = 'not-allowed';
        } else {
            nextBtn.disabled = false;
            nextBtn.style.opacity = 1;
            nextBtn.style.cursor = 'pointer';
        }

        prevBtn.disabled = cancion.esAnuncio;
        prevBtn.style.opacity = cancion.esAnuncio ? 0.5 : 1;
        prevBtn.style.cursor = cancion.esAnuncio ? 'not-allowed' : 'pointer';
        if (typeof activarEventosAudio === "function") {
            activarEventosAudio();
        }
        if (typeof activarResaltadoCancion === "function") {
            activarResaltadoCancion();
        }
    }

    


    function reproducir() {
        audio.play();
        playBtn.textContent = "⏸";
    }

    function pausar() {
        audio.pause();
        playBtn.textContent = "▶";
    }

    // Botón play/pausa
    playBtn.addEventListener("click", () => {
        if (audio.paused) {
            reproducir();
        } else {
            pausar();
        }
    });

    // Barra de progreso
    barra.addEventListener("input", () => {
        audio.currentTime = barra.value;
    });

    audio.addEventListener("loadedmetadata", () => {
        barra.max = Math.floor(audio.duration);
    });

    audio.addEventListener("timeupdate", () => {
        barra.value = Math.floor(audio.currentTime);
    });

    // Control de volumen
    volumen.addEventListener("input", () => {
        audio.volume = volumen.value;
    });

    // Botones prev/next
    prevBtn.addEventListener("click", () => {
        if (playlist.length === 0) return;
        if (indiceActual > 0) {
            indiceActual--;
        } else {
            // Si es la primera, ve a la última
            indiceActual = playlist.length - 1;
        }
        cargarCancion(playlist[indiceActual]);
        reproducir();
    });

    nextBtn.addEventListener("click", () => {
        if (playlist.length === 0) return;
        if (indiceActual < playlist.length - 1) {
            indiceActual++;
        } else {
            // Si es la última, vuelve a la primera
            indiceActual = 0;
        }
        cargarCancion(playlist[indiceActual]);
        reproducir();
    });

    // Clic en .hover-overlay para reproducir
    document.body.addEventListener("click", function (e) {
        const overlay = e.target.closest(".hover-overlay");
        if (!overlay) return;

        const src = overlay.dataset.src;
        const title = overlay.dataset.title;
        const artist = overlay.dataset.artist;
        const img = overlay.dataset.cover;
        const idSeleccionada = overlay.dataset.id;

        // NUEVO: Detecta si es canción suelta por data-suelta
        if (overlay.dataset.suelta === "1") {
            window.albumActual = undefined;
            window.artistaActual = undefined;
            playlist = [{
                id: idSeleccionada,
                titulo: title,
                artista: artist,
                cover: img,
                id_album: null,
                src: src
            }];
            indiceActual = 0;
            cargarCancion(playlist[0]);
            reproducir();
            return;
        }

        // Lógica normal de playlist
        let baseArray = null;
        if (window.albumActual && Array.isArray(window.albumActual)) {
            baseArray = window.albumActual;
        } else if (window.artistaActual && Array.isArray(window.artistaActual)) {
            baseArray = window.artistaActual;
        } else if (window.playlistActual && Array.isArray(window.playlistActual)) {
            baseArray = window.playlistActual;
        } else if (window.cancionActual && Array.isArray(window.cancionActual)) {
            baseArray = window.cancionActual;
        }

        if (baseArray) {
            // Construye la playlist con anuncios
            const basePlaylist = baseArray.map(c => ({
                id: c.id_cancion,
                titulo: c.nombre_c,
                artista: c.artista,
                cover: c.foto_album,
                id_album: c.id_album,
                src: c.ruta_mp3 // si lo tienes
            }));

            playlist = [];
            let anuncioCount = 1;
            let indicesCanciones = []; // Para mapear id_cancion -> índice real en playlist

            for (let i = 0; i < basePlaylist.length; i++) {
                // Guarda el índice real de cada canción
                indicesCanciones.push(playlist.length);
                playlist.push(basePlaylist[i]);
                if (window.usuarioGratis && (i + 1) % 3 === 0 && i !== basePlaylist.length - 1) {
                    playlist.push({
                        id: `anuncio-${anuncioCount++}`,
                        titulo: "Anuncio",
                        artista: "",
                        cover: "/img/image-brand.png",
                        id_album: null,
                        src: "/uploads/canciones/anuncio.mp3",
                        esAnuncio: true
                    });
                }
            }

            // Busca el índice REAL de la canción seleccionada en la playlist con anuncios
            const indiceBase = baseArray.findIndex(c => c.id_cancion == idSeleccionada);
            let indiceActualTmp = 0;
            if (indiceBase !== -1) {
                indiceActualTmp = indicesCanciones[indiceBase];
            }

            indiceActual = indiceActualTmp;
            cargarCancion(playlist[indiceActual]);
            reproducir();
            return;
        } else {
            // Fallback por si acaso
            cargarCancion({
                id: idSeleccionada,
                titulo: title,
                artista: artist,
                cover: img,
                id_album: null,
                src: `/uploads/stream.php?file=${idSeleccionada}.mp3`
            });
            reproducir();
        }
    });

    
    // Rotación de la imagen del álbum
    let rotation = 0;
    let spinning = false;
    let animationFrameId = null;

    function rotate() {
        if (!spinning) return;
        rotation += 0.1; // Velocidad de rotación
        portada.style.transform = `rotate(${rotation}deg)`;
        animationFrameId = requestAnimationFrame(rotate);
    }

    audio.addEventListener('play', () => {
        if (!spinning) {
            spinning = true;
            animationFrameId = requestAnimationFrame(rotate);
        }
    });

    audio.addEventListener('pause', () => {
        spinning = false;
        cancelAnimationFrame(animationFrameId);
    });

    audio.addEventListener("ended", () => {
            console.log("Canción terminada. Indice actual:", indiceActual, "Playlist length:", playlist.length);

        if (indiceActual < playlist.length - 1) {
            indiceActual++;
            cargarCancion(playlist[indiceActual]);
            reproducir();
        } else {
            // Si es la última, vuelve a la primera
            indiceActual = 0;
            cargarCancion(playlist[indiceActual]);
            reproducir();
        }
    });
        function actualizarBarraProgreso() {
    const porcentaje = (barra.value / barra.max) * 100;
    barra.style.background = `linear-gradient(to right, #3A157F 0%, #DB3DAC ${porcentaje}%, #fff ${porcentaje}%, #fff 100%)`;
    }
    barra.addEventListener("input", actualizarBarraProgreso);
    barra.addEventListener("change", actualizarBarraProgreso);
    audio.addEventListener("timeupdate", actualizarBarraProgreso);
    audio.addEventListener("loadedmetadata", actualizarBarraProgreso);
});


