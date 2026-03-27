var sidenav = document.getElementById("burger-menu");
var closebtn = document.getElementById("closebtn");
var openbtn = document.getElementById("openbtn");
var overlay = document.getElementById("menu-overlay");

// Active les actions du menu seulement si tous les elements existent.
if (openbtn && closebtn && sidenav && overlay) {
    openbtn.addEventListener("click", function (event) {
        event.preventDefault();
        openNav();
    });

    closebtn.addEventListener("click", function (event) {
        event.preventDefault();
        closeNav();
    });

    overlay.addEventListener("click", closeNav);

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeNav();
        }
    });
}

// Ouvre le menu burger, affiche le fond sombre et bloque le scroll.
function openNav() {
    sidenav.classList.add("active");
    overlay.classList.add("active");
    sidenav.setAttribute("aria-hidden", "false");
    openbtn.setAttribute("aria-expanded", "true");
    document.body.classList.add("menu-open");
}

// Ferme le menu burger et remet la page dans son etat normal.
function closeNav() {
    sidenav.classList.remove("active");
    overlay.classList.remove("active");
    sidenav.setAttribute("aria-hidden", "true");
    openbtn.setAttribute("aria-expanded", "false");
    document.body.classList.remove("menu-open");
}