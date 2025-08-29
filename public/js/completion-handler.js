document.addEventListener('DOMContentLoaded', () => {
    const pageContainer = document.querySelector('.page-lesson, .page-challenge');
    if (!pageContainer) return;

    const config = {
        lesson: {
            containerSelector: '.content-navigation',
            idInputName: 'lesson_id',
            apiComplete: '/api/mark-lesson-complete',
            apiIncomplete: '/api/mark-lesson-incomplete',
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
            apiComplete: '/api/verify-flag',
            apiIncomplete: '/api/mark-challenge-incomplete',
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
            button.disabled = false;
            button.innerHTML = originalButtonHTML;
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
        const flag = formData.get('flag');
        const body = { [settings.idInputName]: id };
        if (type === 'challenge') body.flag = flag;

        // ## CORRECTION ICI ##
        // On utilise event.submitter pour obtenir le bouton qui a déclenché l'événement.
        const submitButton = event.submitter;
        const result = await handleApiRequest(settings.apiComplete, body, submitButton);

        if (result.status === 'success') {
            container.innerHTML = settings.incompleteButtonHTML.replaceAll('{id}', id);
            if (type === 'challenge') form.querySelector('.flag-input').disabled = true;
            modal.showModal('success', `Challenge validé !`, 'Félicitations! Vous avez validé ce challenge.');
            setupListeners();
        } else {
            modal.showModal('error', 'Erreur', result.message || 'Une erreur est survenue.');
        }
    };

    const handleInvalidation = async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const id = new FormData(form).get(settings.idInputName);
        const result = await handleApiRequest(settings.apiIncomplete, { [settings.idInputName]: id }, event.submitter);

        if (result.status === 'success') {
            container.innerHTML = settings.completeButtonHTML.replaceAll('{id}', id);
            if (type === 'challenge') {
                const flagInput = document.querySelector('.flag-input');
                if(flagInput) {
                    flagInput.disabled = false;
                    flagInput.value = '';
                }
            }
            modal.showModal('success', 'Réinitialisation réussie !', `Vous pouvez maintenant refaire ce ${settings.singular}.`);
            setupListeners();
        } else {
            modal.showModal('error', 'Erreur', result.message || 'Une erreur est survenue.');
        }
    };
    
    setupListeners();
});