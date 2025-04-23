// On attend que le DOM soit entièrement chargé
document.addEventListener('DOMContentLoaded', function () {
    // Récupère le bouton hamburger et le menu mobile
    const mobileMenuButton = document.getElementById('mobile-menu');
    const mobileNav = document.getElementById('mobile-nav');

    // Ajoute un écouteur d'événement au clic sur le bouton hamburger
    mobileMenuButton.addEventListener('click', function () {
        // Basculer la classe 'active' pour afficher ou cacher le menu
        mobileNav.classList.toggle('active');
    });
});