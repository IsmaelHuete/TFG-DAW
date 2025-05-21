document.addEventListener('DOMContentLoaded', () => {
    const audio = document.getElementById('audio-player');
    const playBtn = document.getElementById('btn-play');
    const prevBtn = document.getElementById('btn-prev');
    const nextBtn = document.getElementById('btn-next');
    const barra = document.getElementById('barra-progreso');
    const volumen = document.getElementById('volumen');
    const titulo = document.getElementById('titulo-cancion');
    const artista = document.getElementById('nombre-artista');
    const portada = document.getElementById('cover-img');

    let playlist = []; // Se puede cargar dinámicamente con AJAX
    let indiceActual = 0;

    function cargarCancion(cancion) {
        audio.src = `/uploads/canciones/${cancion.id}.mp3`;
        titulo.textContent = cancion.titulo;
        artista.textContent = cancion.artista;
        portada.src = `/uploads/foto-album/${cancion.id_album}.jpg`;
    }

    function reproducir() {
        audio.play();
        playBtn.textContent = '⏸️';
    }

    function pausar() {
        audio.pause();
        playBtn.textContent = '▶️';
    }

    playBtn.addEventListener('click', () => {
        if (audio.paused) {
            reproducir();
        } else {
            pausar();
        }
    });

    barra.addEventListener('input', () => {
        audio.currentTime = barra.value;
    });

    volumen.addEventListener('input', () => {
        audio.volume = volumen.value;
    });

    audio.addEventListener('loadedmetadata', () => {
        barra.max = Math.floor(audio.duration);
    });

    audio.addEventListener('timeupdate', () => {
        barra.value = Math.floor(audio.currentTime);
    });

    prevBtn.addEventListener('click', () => {
        if (indiceActual > 0) {
            indiceActual--;
            cargarCancion(playlist[indiceActual]);
            reproducir();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (indiceActual < playlist.length - 1) {
            indiceActual++;
            cargarCancion(playlist[indiceActual]);
            reproducir();
        }
    });

    // EJEMPLO: Cargar una canción inicial
    playlist = [
        { id: 21, titulo: "Otro Atardecer", artista: "Bad Bunny", id_album: 3 },
        { id: 22, titulo: "Callaita", artista: "Bad Bunny", id_album: 4 },
    ];
    cargarCancion(playlist[0]);
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
