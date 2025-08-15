// Mettez ce code dans un fichier JS chargé par vos pages de challenge
document.addEventListener('DOMContentLoaded', () => {
    const flagForm = document.querySelector('#flag-form');
    const messageElement = document.querySelector('#form-message');

    if (flagForm) {
        flagForm.addEventListener('submit', async (event) => {
            // 1. On empêche le formulaire de se soumettre de manière classique
            event.preventDefault(); 

            const formData = new FormData(flagForm);
            const challenge_id = formData.get('challenge_id');
            const flag = formData.get('flag');

            try {
                // 2. On envoie les données avec fetch vers la nouvelle API
                const response = await fetch('/api/verify-flag', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ challenge_id, flag })
                });

                const result = await response.json();

                // 3. On affiche la réponse à l'utilisateur sans recharger la page
                if (result.status === 'success') {
                    messageElement.textContent = result.message;
                    messageElement.className = 'form-message success';
                } else {
                    messageElement.textContent = result.message;
                    messageElement.className = 'form-message error';
                }

            } catch (error) {
                console.error("Erreur lors de la soumission du flag:", error);
                messageElement.textContent = "Une erreur réseau est survenue.";
                messageElement.className = 'form-message error';
            }
        });
    }
});