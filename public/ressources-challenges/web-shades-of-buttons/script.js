document.addEventListener('DOMContentLoaded', () => {
    const main = document.querySelector('main');
    const originalButton = main.querySelector('button');
    if (originalButton) {
        originalButton.remove();
    }

    function startGame() {
        main.innerHTML = '';
        const redirectIndex = Math.floor(Math.random() * 200);
        console.log(`Le bouton gagnant est le numéro : ${redirectIndex + 1}`);
        createButtons(redirectIndex);
    }

    function createButtons(redirectIndex) {
        for (let i = 0; i < 200; i++) {
            const btn = document.createElement('button');
            btn.textContent = `Bouton ${i + 1}`;
            btn.style.backgroundColor = `hsl(${Math.floor(Math.random() * 360)}, 70%, 60%)`;
            btn.style.color = '#fff';
            btn.style.margin = '4px';

            if (i === redirectIndex) {

                btn.addEventListener('click', () => {
                    const flag = (function() {
                        const a = [
                            [75, 82, 75, 123, 99, 108, 49, 51, 110, 116, 95],
                            [115, 49, 100, 51, 95],
                            [115, 51, 99, 114, 51, 116, 125]
                        ];
                        let b = '';
                        for (let i = 0; i < a.length; i++) {
                            b += a[i].map(c => String.fromCharCode(c)).join('');
                        }
                        return b;
                    })();

                    console.log(`Congratulations! You found the flag: ${flag}`);
                    alert('Flag found! Check the console (F12).');
                });


            } else {
                btn.addEventListener('click', () => {
                    alert('Erreur : bouton magique réinitialisé !');
                    startGame();
                });
            }
            main.appendChild(btn);
        }
    }

    startGame();
});