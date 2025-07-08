document.addEventListener('DOMContentLoaded', function () {

    const header = document.querySelector('header');
    const menuIcon = document.querySelector('.fa-bars');
    const navBar = document.querySelector('.nav_bar');
    const dynamicTextSpan = document.getElementById('dynamic-text');
    const presentationBoxes = document.querySelectorAll('.box_pres');
    const lessonContainers = document.querySelectorAll('.box_container');

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

    function setupDynamicText() {
        if (!dynamicTextSpan) return;

        const words = ["en ligne", "gratuitement", "facilement"];
        let currentIndex = 0;

        const changeText = () => {
            dynamicTextSpan.classList.remove('glitch');
            
            currentIndex = (currentIndex + 1) % words.length;
            const newText = words[currentIndex];
            
            void dynamicTextSpan.offsetWidth; 

            dynamicTextSpan.textContent = newText;
            dynamicTextSpan.setAttribute('data-text', newText);
            
            requestAnimationFrame(() => {
                dynamicTextSpan.classList.add('glitch');
            });
        };

        changeText();
        setInterval(changeText, 4000);
    }

    function setupCardHoverEffect() {
        if (presentationBoxes.length === 0) return;

        presentationBoxes.forEach(box => {
            box.addEventListener('mousemove', e => {
                const rect = box.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                box.style.setProperty('--x', `${x}px`);
                box.style.setProperty('--y', `${y}px`);
            });
        });
    }

    function setupStaggeredAnimation() {
        if (lessonContainers.length === 0) return;

        lessonContainers.forEach(container => {
            const lessons = container.querySelectorAll('a.lesson-card');
            lessons.forEach((lesson, index) => {
                lesson.style.animationDelay = `${(index + 1) * 0.1}s`;
            });
        });
    }

    setupMobileNavigation();
    setupScrollEffects();
    setupDynamicText();
    setupCardHoverEffect();
    setupStaggeredAnimation();
});