document.addEventListener('DOMContentLoaded', () => {
    const flagForm = document.querySelector('#flag-form');
    const messageElement = document.querySelector('#form-message');
    const flagInput = document.querySelector('.flag-input');
    const submitButton = document.querySelector('.btn-submit');
    
    const handleSuccess = (message) => {
        if (flagForm) {
            flagInput.disabled = true;
            submitButton.disabled = true;
            submitButton.innerHTML = 'Validé <i class="fas fa-check"></i>';
            submitButton.classList.add('validated');
            messageElement.textContent = message;
            messageElement.className = 'form-message success';
        }
    };
    
    if (submitButton.disabled) {
        submitButton.innerHTML = 'Validé <i class="fas fa-check"></i>';
        submitButton.classList.add('validated');
        flagInput.disabled = true;
        messageElement.textContent = 'Ce challenge est déjà validé !';
        messageElement.className = 'form-message success';
    }

    if (flagForm) {
        flagForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            
            messageElement.textContent = 'Vérification en cours...';
            messageElement.className = 'form-message';
            submitButton.disabled = true;

            const formData = new FormData(flagForm);
            const challenge_id = formData.get('challenge_id');
            const flag = formData.get('flag');

            try {
                const response = await fetch('/api/verify-flag', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ challenge_id, flag: flag }) // Correction ici
                });

                const result = await response.json();

                if (result.status === 'success') {
                    handleSuccess(result.message);
                } else {
                    messageElement.textContent = result.message || 'Flag incorrect. Veuillez réessayer.';
                    messageElement.className = 'form-message error';
                    submitButton.disabled = false;
                }

            } catch (error) {
                console.error("Erreur lors de la soumission du flag:", error);
                messageElement.textContent = "Une erreur réseau est survenue.";
                messageElement.className = 'form-message error';
                submitButton.disabled = false;
            }
        });
    }
});