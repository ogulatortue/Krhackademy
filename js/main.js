document.addEventListener('DOMContentLoaded', function () {
    const menuIcon = document.querySelector('.fa-bars');
    const navBar = document.querySelector('.nav_bar');
    const header = document.querySelector('header');
    const contentArea = document.querySelector(".lessons-main-content");

    const dynamicTextSpan = document.getElementById('dynamic-text');
    const words = ["en ligne", "gratuitement", "facilement"];
    let currentIndex = 0;

    menuIcon.addEventListener('click', function () {
        navBar.classList.toggle('open');
        menuIcon.classList.toggle('fa-times');
    });

    if (contentArea) {
        contentArea.addEventListener('click', function () {
            navBar.classList.remove('open');
            menuIcon.classList.remove('fa-times');
        });
    } else {
        document.body.addEventListener('click', function() {
            if (navBar.classList.contains('open')) {
                navBar.classList.remove('open');
                menuIcon.classList.remove('fa-times');
            }
        });
    }

    window.addEventListener('scroll', handleScroll);
    window.addEventListener('load', handleScroll);

    function handleScroll() {
        if (navBar.classList.contains('open')) {
            navBar.classList.remove('open');
            menuIcon.classList.remove('fa-times');
        }

        if (window.scrollY > 20) {
            header.classList.add('header-active');
        } else {
            header.classList.remove('header-active');
        }
    }

    if (dynamicTextSpan) {
        function changeText() {
            dynamicTextSpan.classList.remove('glitch');

            currentIndex = (currentIndex + 1) % words.length;
            const newText = words[currentIndex];

            void dynamicTextSpan.offsetWidth;
            dynamicTextSpan.textContent = newText;

            dynamicTextSpan.setAttribute('data-text', newText);

            requestAnimationFrame(() => {
                dynamicTextSpan.classList.add('glitch');
            });
        }

        changeText();
        setInterval(changeText, 4000);
    }
});