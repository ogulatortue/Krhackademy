document.addEventListener('DOMContentLoaded', () => {
    const submitButton = document.getElementById('demo-submit');
    const userInput = document.getElementById('demo-user');
    const resultBox = document.getElementById('demo-result');
    const successPayload = "' OR '1'='1' --";

    if (submitButton && userInput && resultBox) {
        submitButton.addEventListener('click', () => {
            const username = userInput.value.trim();
            resultBox.textContent = '';
            const p = document.createElement('p');
            const i = document.createElement('i');
            
            if (username === successPayload) {
                resultBox.className = 'demo-result success';
                i.className = 'fas fa-check-circle';
                p.append(i, " Succès ! Authentification contournée. Vous êtes connecté en tant que 'admin'.");
            } else {
                const code = document.createElement('code');
                resultBox.className = 'demo-result error';
                i.className = 'fas fa-times-circle';
                code.textContent = "' OR '1'='1' --";
                p.append(i, " Échec. Identifiants incorrects. Essayez avec ", code, " comme nom d'utilisateur.");
            }
            resultBox.append(p);
        });
    }
});