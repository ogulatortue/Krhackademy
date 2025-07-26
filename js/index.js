const textGlitcher = (function() {

    let data = {
        isChanging: false,
        target: null,
        letters: '*+-/@_$[%£!XO1&>',
        originalString: '',
        singleLetters: []
    };

    Array.prototype.shuffle = function() {
        let input = this;
        for (let i = input.length - 1; i >= 0; i--) {
            let randomIndex = Math.floor(Math.random() * (i + 1));
            let itemAtIndex = input[randomIndex];
            input[randomIndex] = input[i];
            input[i] = itemAtIndex;
        }
        return input;
    }

    function changeLetter(letter) {
        if (letter.textContent != ' ') {
            letter.classList.add('is-changing');
            letter.style.animationDuration = Math.random().toFixed(2) + 's';
            let newChar = data.letters.substr(Math.random() * data.letters.length, 1);
            letter.textContent = newChar;
            letter.setAttribute('data-txt', newChar);
        }
    }

    function resetLetter(letter, value) {
        letter.classList.remove('is-changing');
        letter.textContent = value;
        letter.setAttribute('data-txt', value);
    }

    function divideLetters() {
        if (!data.target) return;
        const text = data.target.textContent;
        let textDivided = '';
        data.originalString = text;
        for (let i = 0; i < text.length; i++) {
            textDivided += `<span class="el-sp" data-txt="${text.substr(i, 1)}">${text.substr(i, 1)}</span>`;
        }
        data.target.innerHTML = textDivided;
        data.singleLetters = data.target.querySelectorAll('.el-sp');
    }

    function animateChange() {
        if (!data.isChanging) return;
        data.singleLetters.forEach(changeLetter);
        setTimeout(animateChange, 50);
    }

    function animateReset() {
        const randomArray = [];
        for (let i = 0; i < data.singleLetters.length; i++) {
            randomArray.push(i);
        }
        randomArray.shuffle();
        randomArray.forEach((el, index) => {
            setTimeout(() => {
                resetLetter(data.singleLetters[el], data.originalString.substring(el, el + 1));
            }, index * 35);
        });
    }

    return {
        init: function(selector) {
            data.target = document.querySelector(selector);
            if (data.target) {
                divideLetters();
            }
        },
        scramble: function(newText, duration = 1000) {
            if (!data.target || data.isChanging) return;
            data.isChanging = true;
            animateChange();
            setTimeout(() => {
                data.isChanging = false;
                data.target.textContent = newText;
                divideLetters();
                animateReset();
            }, duration);
        }
    };
})();

document.addEventListener('DOMContentLoaded', function() {

    const dynamicTextSpan = document.getElementById('dynamic-text');
    const presentationBoxes = document.querySelectorAll('.box-pres');

    function setupDynamicText() {
        if (!dynamicTextSpan) return;

        const words = ["gratuitement", "facilement", "en ligne"];
        let currentIndex = -1; 

        textGlitcher.init('#dynamic-text');

        const changeWord = () => {
            currentIndex = (currentIndex + 1) % words.length;
            const newWord = words[currentIndex];
            textGlitcher.scramble(newWord, 1000);
        };
        
        setInterval(changeWord, 4000);
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