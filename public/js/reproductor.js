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
        audio.src = `/uploads/stream.php?file=${cancion.id}.mp3`;
        titulo.textContent = cancion.titulo || "Sin título";
        artista.textContent = cancion.artista || "Desconocido";
        portada.src = cancion.cover || `/uploads/foto-album/${cancion.id_album}.jpg`;
        reproductor.style.display = "flex";
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
            cargarCancion({ src, titulo: title, artista: artist, cover: img });
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

audio.addEventListener('ended', () => {
    spinning = false;
    cancelAnimationFrame(animationFrameId);
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
    
