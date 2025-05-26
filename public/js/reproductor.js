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

    let playlist = []; // Se puede cargar dinámicamente
    let indiceActual = 0;

    function cargarCancion(cancion) {

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
    }

    


    function reproducir() {
        audio.play();
        playBtn.textContent = "⏸️";
    }

    function pausar() {
        audio.pause();
        playBtn.textContent = "▶️";
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
        if (indiceActual > 0) {
            indiceActual--;
            cargarCancion(playlist[indiceActual]);
            reproducir();
        }
    });

    nextBtn.addEventListener("click", () => {
        if (indiceActual < playlist.length - 1) {
            indiceActual++;
            cargarCancion(playlist[indiceActual]);
            reproducir();
        }
    });

    // Clic en .hover-overlay para reproducir
    document.body.addEventListener("click", function (e) {
        const overlay = e.target.closest(".hover-overlay");
        if (!overlay) return;

        const src = overlay.dataset.src;
        const title = overlay.dataset.title;
        const artist = overlay.dataset.artist;
        const img = overlay.dataset.cover;

        if (src && title && artist && img) {
            const idSeleccionada = overlay.dataset.id;

            // Asegúrate de tener window.albumActual disponible
            if (window.albumActual && Array.isArray(window.albumActual)) {
                const indiceInicio = window.albumActual.findIndex(c => c.id_cancion == idSeleccionada);
                

                if (indiceInicio !== -1) {
                    const basePlaylist = window.albumActual.slice(indiceInicio).map(c => ({
                        id: c.id_cancion,
                        titulo: c.nombre_c,
                        artista: c.artista,
                        cover: c.foto_album,
                        id_album: c.id_album
                    }));

                    playlist = [];
                    let anuncioCount = 1;

                    for (let i = 0; i < basePlaylist.length; i++) {
                        playlist.push(basePlaylist[i]);

                        if ((i + 1) % 3 === 0 && i !== basePlaylist.length - 1) {
                            playlist.push({
                                id: `anuncio-${anuncioCount++}`,
                                titulo: "Anuncio",
                                artista: "",
                                cover: "../img/image-brand.png",
                                id_album: null,
                                src: "/uploads/canciones/anuncio.mp3",
                                esAnuncio: true
                            });
                        }
                    }

                    indiceActual = 0;
                    cargarCancion(playlist[indiceActual]);
                    reproducir();
                }

                
            } else {
                // Si no hay albumActual, reproducir solo esa
                cargarCancion({ id: idSeleccionada, titulo: title, artista: artist, cover: img });
                reproducir();
            }
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

audio.addEventListener('ended', () => {
    spinning = false;
    cancelAnimationFrame(animationFrameId);
    if (indiceActual < playlist.length - 1) {
        indiceActual++;
        cargarCancion(playlist[indiceActual]);
        reproducir();
    } else {
        console.log("Fin de la playlist.");
        // Opcional: repetir desde el principio
        // indiceActual = 0;
        // cargarCancion(playlist[indiceActual]);
        // reproducir();
    }

});
});


document.addEventListener("DOMContentLoaded", () => {
    const audio = document.getElementById("audio-player");
    const titulo = document.getElementById("titulo-cancion");
    const artista = document.getElementById("nombre-artista");
    const cover = document.getElementById("cover-img");

    document.body.addEventListener("click", function (e) {
        const overlay = e.target.closest(".hover-overlay");
        if (!overlay) return;

        const src = overlay.dataset.src;
        const title = overlay.dataset.title;
        const artist = overlay.dataset.artist;
        const img = overlay.dataset.cover;

        if (src && title && artist && img) {
            audio.src = src;
            audio.play();

            titulo.textContent = title;
            artista.textContent = artist;
            cover.src = img;
        }
    });
});
    
