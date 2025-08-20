const setupModal = () => {
    const modalContainer = document.getElementById('modal-container');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const modalCloseIcon = document.querySelector('.close-button');

    const showModal = (type, title, message) => {
        modalContainer.classList.remove('modal-success', 'modal-error');
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        modalContainer.classList.add(`modal-${type}`, 'is-visible');

        setTimeout(hideModal, 3000);
    };

    const hideModal = () => {
        modalContainer.classList.remove('is-visible');
    };

    if (modalCloseIcon) modalCloseIcon.addEventListener('click', hideModal);

    window.addEventListener('click', (event) => {
        if (event.target === modalContainer) {
            hideModal();
        }
    });

    // Nouvel événement pour masquer la pop-up lors du défilement
    window.addEventListener('scroll', hideModal);

    return { showModal, hideModal };
};

const modal = setupModal();