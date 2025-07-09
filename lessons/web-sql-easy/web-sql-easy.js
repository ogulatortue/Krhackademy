// Attend que le contenu de la page soit entièrement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', () => {

    // Sélection des éléments du DOM une seule fois
    const submitButton = document.getElementById('demo-submit');
    const userInput = document.getElementById('demo-user');
    const resultBox = document.getElementById('demo-result');
    const successPayload = "' OR '1'='1' --";

    // Vérifie si tous les éléments nécessaires existent avant d'ajouter l'écouteur
    if (submitButton && userInput && resultBox) {
        submitButton.addEventListener('click', () => {
            const username = userInput.value.trim();

            if (username === successPayload) {
                // Succès
                resultBox.innerHTML = `<p style="color: var(--easy); font-weight: bold;"><i class="fas fa-check-circle"></i> Succès ! Authentification contournée. Vous êtes connecté en tant que 'admin'.</p>`;
                resultBox.style.borderColor = 'var(--easy)';
            } else {
                // Échec
                resultBox.innerHTML = `<p style="color: var(--danger);"><i class="fas fa-times-circle"></i> Échec. Identifiants incorrects. Essayez avec <code>' OR '1'='1' --</code> comme nom d'utilisateur.</p>`;
                resultBox.style.borderColor = 'var(--danger)';
            }
        });
    } else {
        console.error("Un ou plusieurs éléments de la démo sont introuvables sur la page.");
    }
});