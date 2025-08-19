document.addEventListener('DOMContentLoaded', () => {
    const flagForm = document.querySelector('#flag-form');
    const messageElement = document.querySelector('#form-message');

    if (flagForm) {
        flagForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(flagForm);
            const challenge_id = formData.get('challenge_id');
            const flag = formData.get('flag');

            try {
                const response = await fetch('/api/verify-flag', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ challenge_id, flag: `KRHACK{${flag}}` })
                });

                const result = await response.json();

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