const section1 = document.querySelector('.section1');  
const headings = document.querySelectorAll('.animacion-lenta');
const options = document.querySelector('.options');

function trigger(entries) {
    entries.forEach(entry => {
        entry.target.classList.toggle('unset', entry.isIntersecting);  
    });
}

const option = {
    root: null,
    rootMargin: "0px",
    threshold: 0.25
};

const observer = new IntersectionObserver(trigger, option);

headings.forEach(head => observer.observe(head));  
observer.observe(options);  

observer.observe(section1); 
document.getElementById('foto').addEventListener('change', function(){
    const fileName = this.files[0]?.name || 'Ningún archivo seleccionado';
    document.getElementById('archivo-seleccionado').textContent = fileName;
});