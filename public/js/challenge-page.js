document.addEventListener('DOMContentLoaded', () => {
    const submissionSection = document.querySelector('.flag-submission');

    const addCompletionListener = () => {
        const flagForm = document.querySelector('#flag-form');
        if (flagForm) {
            const submitButton = document.querySelector('button[form="flag-form"]');
            if (submitButton) {
                submitButton.addEventListener('click', handleCompletion);
            }
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
        const flagForm = document.querySelector('#flag-form');
        const flagInput = flagForm.querySelector('.flag-input');
        const submitButton = event.currentTarget;
        
        if (!flagForm.checkValidity()) {
            flagForm.reportValidity();
            return;
        }

        submitButton.disabled = true;
        const challenge_id = new FormData(flagForm).get('challenge_id');
        const flag = new FormData(flagForm).get('flag');

        try {
            const response = await fetch('/api/verify-flag', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ challenge_id, flag })
            });

            const result = await response.json();

            if (result.status === 'success') {
                flagInput.disabled = true;
                const controlsWrapper = submissionSection.querySelector('.submission-controls');
                controlsWrapper.innerHTML = `
                    <span class="btn-glass validated">Challenge validé <i class="fas fa-check"></i></span>
                    <form class="invalidation-form" style="margin: 0;">
                        <input type="hidden" name="challenge_id" value="${challenge_id}">
                        <button type="submit" class="btn-glass" title="Réinitialiser le challenge">
                            <i class="fas fa-arrow-rotate-left"></i>
                        </button>
                    </form>
                `;
                addInvalidationListener();
                modal.showModal('success', 'Challenge validé !', 'Félicitations! Vous avez validé ce challenge.');
            } else {
                submitButton.disabled = false;
                modal.showModal('error', 'Flag incorrect', result.message || 'Le flag soumis est incorrect.');
            }
        } catch (error) {
            submitButton.disabled = false;
            modal.showModal('error', 'Erreur de connexion', 'Impossible de joindre le serveur.');
        }
    };

    const handleInvalidation = async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const button = form.querySelector('button');
        const challenge_id = form.querySelector('input[name="challenge_id"]').value;

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch('/api/mark-challenge-incomplete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ challenge_id })
            });
            const result = await response.json();

            if (result.status === 'success') {
                const flagInput = submissionSection.querySelector('.flag-input');
                flagInput.disabled = false;
                flagInput.value = '';
                
                const controlsWrapper = submissionSection.querySelector('.submission-controls');
                controlsWrapper.innerHTML = `<button type="submit" form="flag-form" class="btn-glass">Soumettre le flag <i class="fas fa-arrow-right"></i></button>`;
                addCompletionListener();
                modal.showModal('success', 'Challenge réinitialisé !', 'Vous pouvez maintenant soumettre un nouveau flag.');
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