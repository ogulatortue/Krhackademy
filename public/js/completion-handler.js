document.addEventListener('DOMContentLoaded', () => {
    const pageContainer = document.querySelector('.page-lesson, .page-challenge');
    if (!pageContainer) return;

    const config = {
        lesson: {
            containerSelector: '.content-navigation',
            idInputName: 'lesson_id',
            apiProgress: '/api/progress',
            singular: 'leçon',
            completeButtonHTML: `
                <form class="completion-form">
                    <input type="hidden" name="lesson_id" value="{id}">
                    <button type="submit" class="btn-glass">Valider la leçon <i class="fas fa-arrow-right"></i></button>
                </form>`,
            incompleteButtonHTML: `
                <span class="btn-glass validated">Leçon validée <i class="fas fa-check"></i></span>
                <form class="invalidation-form" style="margin: 0;">
                    <input type="hidden" name="lesson_id" value="{id}">
                    <button type="submit" class="btn-glass" title="Recommencer la leçon"><i class="fas fa-arrow-rotate-left"></i></button>
                </form>`
        },
        challenge: {
            containerSelector: '.submission-controls',
            idInputName: 'challenge_id',
            apiVerify: '/api/verify-flag',
            apiProgress: '/api/progress',
            singular: 'challenge',
            completeButtonHTML: `<button type="submit" form="flag-form" class="btn-glass">Soumettre le flag <i class="fas fa-arrow-right"></i></button>`,
            incompleteButtonHTML: `
                <span class="btn-glass validated">Challenge validé <i class="fas fa-check"></i></span>
                <form class="invalidation-form" style="margin: 0;">
                    <input type="hidden" name="challenge_id" value="{id}">
                    <button type="submit" class="btn-glass" title="Réinitialiser le challenge"><i class="fas fa-arrow-rotate-left"></i></button>
                </form>`
        }
    };

    const type = pageContainer.classList.contains('page-lesson') ? 'lesson' : 'challenge';
    const settings = config[type];
    const container = document.querySelector(settings.containerSelector);

    const handleApiRequest = async (url, body, button) => {
        const originalButtonHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(body)
            });
            return await response.json();
        } catch (error) {
            return { status: 'error', message: 'Impossible de joindre le serveur.' };
        } finally {
            if(button) {
                button.disabled = false;
                button.innerHTML = originalButtonHTML;
            }
        }
    };

    const setupListeners = () => {
        const completionForm = document.querySelector('.completion-form, #flag-form');
        if (completionForm) {
            completionForm.addEventListener('submit', handleCompletion);
        }
        const invalidationForm = document.querySelector('.invalidation-form');
        if (invalidationForm) {
            invalidationForm.addEventListener('submit', handleInvalidation);
        }
    };

    const handleCompletion = async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        if (!form.checkValidity()) return form.reportValidity();
        
        const formData = new FormData(form);
        const id = formData.get(settings.idInputName);
        const submitButton = event.submitter;

        let body;
        let url;

        if (type === 'challenge') {
            const flag = formData.get('flag');
            body = { challenge_id: id, flag: flag };
            url = settings.apiVerify;
        } else {
            body = { id: id, type: 'lesson', status: 'complete' };
            url = settings.apiProgress;
        }

        const result = await handleApiRequest(url, body, submitButton);

        if (result.status === 'success') {
            container.innerHTML = settings.incompleteButtonHTML.replaceAll('{id}', id);
            if (type === 'challenge') form.querySelector('.flag-input').disabled = true;
            const validationMessage = type === 'lesson' ? 'Bravo ! Vous avez validé cette leçon' : 'Bravo ! Vous avez validé ce challenge';
            modal.showModal('success', 'Validé', validationMessage);
            setupListeners();
        } else {
            const fallbackMessage = type === 'challenge' ? 'Flag incorrect, essayez encore !' : 'Une erreur est survenue.';
            modal.showModal('error', 'Erreur', result.message || fallbackMessage);
        }
    };

    const handleInvalidation = async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const id = new FormData(form).get(settings.idInputName);
        const body = { id: id, type: type, status: 'incomplete' };
        
        const result = await handleApiRequest(settings.apiProgress, body, event.submitter);

        if (result.status === 'success') {
            container.innerHTML = settings.completeButtonHTML.replaceAll('{id}', id);
            if (type === 'challenge') {
                const flagInput = document.querySelector('.flag-input');
                if(flagInput) {
                    flagInput.disabled = false;
                    flagInput.value = '';
                }
            }
            const invalidationMessage = type === 'lesson' ? 'Vous pouvez maintenant refaire cette leçon' : 'Vous pouvez maintenant refaire ce challenge';
            modal.showModal('success', 'Réinitialisé', invalidationMessage);
            setupListeners();
        } else {
            modal.showModal('error', 'Erreur', result.message || 'Une erreur est survenue.');
        }
    };
    
    setupListeners();
});