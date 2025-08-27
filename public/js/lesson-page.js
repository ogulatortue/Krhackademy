document.addEventListener('DOMContentLoaded', () => {
    const navigation = document.querySelector('.content-navigation');

    const addCompletionListener = () => {
        const completionForm = document.querySelector('.completion-form');
        if (completionForm) {
            completionForm.addEventListener('submit', handleCompletion);
        }
    };

    const addInvalidationListener = () => {
        const invalidationForm = document.querySelector('.invalidation-form');
        if (invalidationForm) {
            invalidationForm.addEventListener('submit', handleInvalidation);
        }
    };

    const handleCompletion = async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const button = form.querySelector('button');
        const lessonId = form.querySelector('input[name="lesson_id"]').value;

        button.disabled = true;
        button.innerHTML = 'Sauvegarde...';

        try {
            const response = await fetch('/api/mark-lesson-complete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ lesson_id: lessonId })
            });
            const result = await response.json();

            if (result.status === 'success') {
                navigation.innerHTML = `
                    <span class="btn-glass validated">Leçon validée <i class="fas fa-check"></i></span>
                    <form class="invalidation-form" style="margin: 0;">
                        <input type="hidden" name="lesson_id" value="${lessonId}">
                        <button type="submit" class="btn-glass" title="Recommencer la leçon">
                            <i class="fas fa-arrow-rotate-left"></i>
                        </button>
                    </form>
                `;
                addInvalidationListener();
                modal.showModal('success', 'Leçon validée !', 'Félicitations! Vous avez validé cette leçon.');
            } else {
                button.disabled = false;
                button.innerHTML = 'Valider la leçon <i class="fas fa-arrow-right" aria-hidden="true"></i>';
                modal.showModal('error', 'Erreur de validation', result.message || 'Une erreur est survenue.');
            }
        } catch (error) {
            button.disabled = false;
            button.innerHTML = 'Valider la leçon <i class="fas fa-arrow-right" aria-hidden="true"></i>';
            modal.showModal('error', 'Erreur de connexion', 'Impossible de joindre le serveur.');
        }
    };

    const handleInvalidation = async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const button = form.querySelector('button');
        const lessonId = form.querySelector('input[name="lesson_id"]').value;

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch('/api/mark-lesson-incomplete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ lesson_id: lessonId })
            });
            const result = await response.json();

            if (result.status === 'success') {
                navigation.innerHTML = `
                    <form class="completion-form">
                        <input type="hidden" name="lesson_id" value="${lessonId}">
                        <button type="submit" class="btn-glass">
                            Valider la leçon <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>
                `;
                addCompletionListener();
                modal.showModal('success', 'Leçon réinitialisée !', 'Vous pouvez maintenant refaire cette leçon.');
            } else {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-arrow-rotate-left"></i>';
                modal.showModal('error', 'Erreur', result.message || 'Une erreur est survenue.');
            }
        } catch (error) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-arrow-rotate-left"></i>';
            modal.showModal('error', 'Erreur de connexion', 'Impossible de joindre le serveur.');
        }
    };

    addCompletionListener();
    addInvalidationListener();
});