document.addEventListener('DOMContentLoaded', function () {

    const dynamicTextSpan = document.getElementById('dynamic-text');
    const presentationBoxes = document.querySelectorAll('.box-pres');

    function setupDynamicText() {
        if (!dynamicTextSpan) return;

        const words = ["en ligne", "gratuitement", "facilement"];
        let currentIndex = 0;

        const changeText = () => {
            currentIndex = (currentIndex + 1) % words.length;
            dynamicTextSpan.textContent = words[currentIndex];
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

    setupDynamicText();
    setupCardHoverEffect();
});