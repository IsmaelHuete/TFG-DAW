const audio = document.getElementById('audio-player');
const playBtn = document.getElementById('play-btn');
const pauseBtn = document.getElementById('pause-btn');
const barra = document.getElementById('barra-progreso');
const tiempoActual = document.getElementById('tiempo-actual');
const duracionTotal = document.getElementById('duracion-total');
const albumCover = document.getElementById('cover-img');

const retrocederBtn = document.getElementById('retroceder');
const adelantarBtn = document.getElementById('adelantar');

let isDragging = false; // Variable para controlar si el usuario está interactuando con la barra

// Función para formatear el tiempo en minutos y segundos
function formatearTiempo(segundos) {
    const min = Math.floor(segundos / 60).toString().padStart(2, '0');
    const sec = Math.floor(segundos % 60).toString().padStart(2, '0');
    return `${min}:${sec}`;
}

// Actualizar la duración total cuando se carga el audio
audio.addEventListener('loadedmetadata', () => {
    if (!isNaN(audio.duration)) {
        barra.max = audio.duration;
        duracionTotal.textContent = formatearTiempo(audio.duration);
    } else {
        console.warn("No se pudo obtener duración del audio.");
    }
});

// Actualizar la barra de progreso y el tiempo actual mientras se reproduce el audio
audio.addEventListener('timeupdate', () => {
    if (!isDragging) { // Solo actualiza la barra si el usuario no está interactuando con ella
        barra.value = audio.currentTime;
        tiempoActual.textContent = formatearTiempo(audio.currentTime);
    }
});

// Adelantar 15 segundos
retrocederBtn.addEventListener('click', () => {
    audio.currentTime = Math.max(0, audio.currentTime - 15);
});

// Retroceder 15 segundos
adelantarBtn.addEventListener('click', () => {
    audio.currentTime = Math.min(audio.duration, audio.currentTime + 15);
});

// Cambiar el tiempo del audio al mover la barra de progreso
barra.addEventListener('input', () => {
    isDragging = true; // Indica que el usuario está interactuando con la barra
    audio.currentTime = barra.value;
    tiempoActual.textContent = formatearTiempo(audio.currentTime);
});

barra.addEventListener('change', () => {
    isDragging = false; // El usuario ha terminado de interactuar con la barra
    if (!audio.paused) {
        audio.play(); // Reanuda la reproducción si estaba en curso
    }
});

// Reproducir el audio
playBtn.addEventListener('click', () => {
    audio.play();
    playBtn.style.display = 'none';
    pauseBtn.style.display = 'inline';
});

// Pausar el audio
pauseBtn.addEventListener('click', () => {
    audio.pause();
    pauseBtn.style.display = 'none';
    playBtn.style.display = 'inline';
});

// Rotación de la imagen del álbum
let rotation = 0;
let spinning = false;
let animationFrameId = null;

function rotate() {
    if (!spinning) return;
    rotation += 0.1; // Velocidad de rotación
    albumCover.style.transform = `rotate(${rotation}deg)`;
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
    pauseBtn.style.display = 'none';
    playBtn.style.display = 'inline';
});

