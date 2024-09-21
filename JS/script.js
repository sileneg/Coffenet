let currentIndex = 0;
const intervalTime = 3000; // Tiempo en milisegundos para el cambio automático de imágenes

function showSlide(index) {
    const slides = document.querySelectorAll('.carousel-slide');
    if (index >= slides.length) {
        currentIndex = 0;
    } else if (index < 0) {
        currentIndex = slides.length - 1;
    } else {
        currentIndex = index;
    }
    const offset = -currentIndex * 100;
    document.querySelector('.carousel').style.transform = `translateX(${offset}%)`;
}

function moveSlide(step) {
    showSlide(currentIndex + step);
    resetInterval(); // Reinicia el intervalo cuando se usan las flechas
}

function resetInterval() {
    clearInterval(autoSlideInterval);
    autoSlideInterval = setInterval(() => {
        showSlide(currentIndex + 1);
    }, intervalTime);
}

document.addEventListener('DOMContentLoaded', () => {
    showSlide(currentIndex);
    autoSlideInterval = setInterval(() => {
        showSlide(currentIndex + 1);
    }, intervalTime);
});

