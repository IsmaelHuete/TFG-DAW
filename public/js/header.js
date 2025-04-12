
  window.addEventListener("scroll", function () {
    const nav = document.querySelector("nav");
    if (window.scrollY > 1) {
      nav.classList.add("scroll");
    } else {
      nav.classList.remove("scroll");
    }
  });
