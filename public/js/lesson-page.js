document.addEventListener('DOMContentLoaded', () => {
    const completionForm = document.querySelector('.completion-form');

    if (completionForm) {
        completionForm.addEventListener('submit', async (event) => {
            // On empêche le formulaire de recharger la page
            event.preventDefault();

            const button = event.target.querySelector('button');
            const lessonId = event.target.querySelector('input[name="lesson_id"]').value;

            // On désactive le bouton pour éviter les double-clics
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
                    button.textContent = 'Terminée !';
                    button.classList.add('completed'); // Pour un style visuel de succès
                } else {
                    button.textContent = 'Erreur';
                    alert(result.message || 'Une erreur est survenue.');
                }

            } catch (error) {
                console.error("Erreur lors de la sauvegarde:", error);
                button.textContent = 'Erreur Réseau';
                button.disabled = false;
            }
        });
    }
});