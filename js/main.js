document.addEventListener('DOMContentLoaded', function () {
    const menuIcon = document.querySelector('.fa-bars');
    const navBar = document.querySelector('.nav_bar');
    const header = document.querySelector('header');
    const projects = document.getElementById("Projets");

    menuIcon.addEventListener('click', function () {
        navBar.classList.toggle('open');
        menuIcon.classList.toggle('fa-times');
    });

    projects.addEventListener('click', function () {
        navBar.classList.remove('open');
        menuIcon.classList.toggle('fa-times');
    });

    window.addEventListener('scroll', handleScroll);
    window.addEventListener('load', handleScroll);

    function handleScroll() {
        navBar.classList.remove('open');
        menuIcon.classList.remove('fa-times');

        if (window.scrollY > 20) {
            header.classList.add('header-active');
        } else {
            header.classList.remove('header-active');
        }
    }
});
