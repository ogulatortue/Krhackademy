document.addEventListener('DOMContentLoaded', () => {
    const flagForm = document.querySelector('#flag-form');

    if (flagForm) {
        flagForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const flagInput = flagForm.querySelector('.flag-input');
            const submitButton = flagForm.querySelector('button[type="submit"]');
            
            submitButton.disabled = true;

            const formData = new FormData(flagForm);
            const challenge_id = formData.get('challenge_id');
            const flag = formData.get('flag');

            try {
                const response = await fetch('/api/verify-flag', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ challenge_id, flag: flag })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    flagInput.disabled = true;
                    submitButton.innerHTML = 'Challenge validé <i class="fas fa-check"></i>';
                    submitButton.classList.add('btn-glass', 'validated');
                    modal.showModal('success', 'Challenge validé !', 'Félicitations! Vous avez validé ce challenge.');
                } else {
                    submitButton.disabled = false;
                    modal.showModal('error', 'Flag incorrect', result.message || 'Le flag soumis est incorrect. Veuillez vérifier et réessayer.');
                }
            } catch (error) {
                console.error("Erreur lors de la soumission du flag:", error);
                submitButton.disabled = false;
                modal.showModal('error', 'Erreur de connexion', 'Impossible de se connecter au serveur. Veuillez vérifier votre connexion internet.');
            }
        });
    }
});