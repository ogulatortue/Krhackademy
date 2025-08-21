document.addEventListener('DOMContentLoaded', () => {
    const flagForm = document.querySelector('#flag-form');
    const flagInput = document.querySelector('.flag-input');
    const submitButton = document.querySelector('.btn-submit');
    
    const handleSuccess = () => {
        if (flagForm) {
            flagInput.disabled = true;
            submitButton.disabled = true;
            submitButton.innerHTML = 'Validé <i class="fas fa-check"></i>';
            submitButton.classList.add('validated');
        }
    };
    
    if (submitButton.disabled) {
        submitButton.innerHTML = 'Validé <i class="fas fa-check"></i>';
        submitButton.classList.add('validated');
        flagInput.disabled = true;
    }

    if (flagForm) {
        flagForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            
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
                    handleSuccess();
                    modal.showModal('success', 'Challenge validé !', result.message);
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