const setupModal = () => {
    const modalContainer = document.getElementById('modal-container');
    if (!modalContainer) {
        return { showModal: () => {}, hideModal: () => {} };
    }

    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const modalCloseIcon = document.querySelector('.close-button');
    let modalTimeoutId;

    const showModal = (type, title, message) => {
        clearTimeout(modalTimeoutId);
        modalContainer.classList.remove('modal-success', 'modal-error');
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        modalContainer.classList.add(`modal-${type}`, 'is-visible');
        modalTimeoutId = setTimeout(hideModal, 4000);
    };

    const hideModal = () => {
        modalContainer.classList.remove('is-visible');
        clearTimeout(modalTimeoutId);
    };

    if (modalCloseIcon) {
        modalCloseIcon.addEventListener('click', hideModal);
    }
    
    window.addEventListener('click', (event) => {
        if (modalContainer.classList.contains('is-visible') && !event.target.closest('.modal-content')) {
            hideModal();
        }
    }, true);

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modalContainer.classList.contains('is-visible')) {
            hideModal();
        }
    });

    return { showModal, hideModal };
};

const modal = setupModal();

document.addEventListener('DOMContentLoaded', () => {
    const modalContainer = document.getElementById('modal-container');
    if (modalContainer && modalContainer.dataset.flashType) {
        const type = modalContainer.dataset.flashType;
        const title = modalContainer.dataset.flashTitle;
        const message = modalContainer.dataset.flashMessage;
        
        modal.showModal(type, title, message);

        delete modalContainer.dataset.flashType;
        delete modalContainer.dataset.flashTitle;
        delete modalContainer.dataset.flashMessage;
    }
    const backButton = document.getElementById('go-back-button');
    if (backButton) {
        backButton.addEventListener('click', function(event) {
            event.preventDefault();
            history.back();
        });
    }
});