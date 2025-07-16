document.addEventListener('DOMContentLoaded', () => {

    const submitButton = document.getElementById('demo-submit');
    const userInput = document.getElementById('demo-user');
    const resultBox = document.getElementById('demo-result');
    const successPayload = "' OR '1'='1' --";

    if (submitButton && userInput && resultBox) {
        submitButton.addEventListener('click', () => {
            const username = userInput.value.trim();

            if (username === successPayload) {
                resultBox.innerHTML = `<p style="color: var(--easy); font-weight: bold;"><i class="fas fa-check-circle"></i> Succès ! Authentification contournée. Vous êtes connecté en tant que 'admin'.</p>`;
                resultBox.style.borderColor = 'var(--easy)';
            } else {
                resultBox.innerHTML = `<p style="color: var(--danger);"><i class="fas fa-times-circle"></i> Échec. Identifiants incorrects. Essayez avec <code>' OR '1'='1' --</code> comme nom d'utilisateur.</p>`;
                resultBox.style.borderColor = 'var(--danger)';
            }
        });
    } else {
        console.error("Un ou plusieurs éléments de la démo sont introuvables sur la page.");
    }
});