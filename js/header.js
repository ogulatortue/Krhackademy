document.addEventListener('DOMContentLoaded', function () {

    const header = document.querySelector('header');
    const menuIcon = document.querySelector('.fa-bars');
    const navBar = document.querySelector('.nav-bar');

    function setupMobileNavigation() {
        if (!menuIcon || !navBar) return;

        const closeMenu = () => {
            navBar.classList.remove('open');
            menuIcon.classList.remove('fa-times');
        };

        menuIcon.addEventListener('click', (event) => {
            event.stopPropagation();
            navBar.classList.toggle('open');
            menuIcon.classList.toggle('fa-times');
        });

        document.addEventListener('click', (event) => {
            if (navBar.classList.contains('open') && !navBar.contains(event.target) && !menuIcon.contains(event.target)) {
                closeMenu();
            }
        });
        
        window.addEventListener('scroll', closeMenu);
    }

    function setupScrollEffects() {
        if (!header) return;

        const handleHeaderStyle = () => {
            if (window.scrollY > 20) {
                header.classList.add('header-active');
            } else {
                header.classList.remove('header-active');
            }
        };

        window.addEventListener('scroll', handleHeaderStyle);
        window.addEventListener('load', handleHeaderStyle);
    }

    setupMobileNavigation();
    setupScrollEffects();
});