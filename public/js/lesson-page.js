document.addEventListener('DOMContentLoaded', () => {
    const completionForm = document.querySelector('.completion-form');

    if (completionForm) {
        completionForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = event.target.querySelector('button');
            const lessonId = event.target.querySelector('input[name="lesson_id"]').value;

            button.disabled = true;
            button.textContent = 'Sauvegarde...';

            try {
                const response = await fetch('/api/mark-lesson-complete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ lesson_id: lessonId })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    button.innerHTML = 'Leçon validée <i class="fas fa-check"></i>';
                    button.classList.add('validated');
                    
                    modal.showModal('success', 'Leçon validée !', 'Félicitations! Vous avez validé cette leçon.');
                } else {
                    button.textContent = 'Erreur';
                    button.disabled = false;

                    modal.showModal('error', 'Erreur de validation', result.message || 'Une erreur est survenue lors de la validation.');
                }
            } catch (error) {
                console.error("Erreur lors de la sauvegarde:", error);
                button.textContent = 'Erreur Réseau';
                button.disabled = false;
                
                modal.showModal('error', 'Erreur de connexion', 'Impossible de se connecter au serveur. Veuillez vérifier votre connexion internet.');
            }
        });
    }
});