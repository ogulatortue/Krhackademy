const setupModal = () => {
    const modalContainer = document.getElementById('modal-container');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const modalCloseIcon = document.querySelector('.close-button');

    let modalTimeoutId;
    let initialScrollY = 0;
    const SCROLL_THRESHOLD = 50;

    const showModal = (type, title, message) => {
        modalContainer.classList.remove('modal-success', 'modal-error');
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        modalContainer.classList.add(`modal-${type}`, 'is-visible');
        initialScrollY = window.scrollY;
        modalTimeoutId = setTimeout(hideModal, 2500);
    };

    const hideModal = () => {
        modalContainer.classList.remove('is-visible');
        clearTimeout(modalTimeoutId);
    };

    if (modalCloseIcon) {
        modalCloseIcon.addEventListener('click', hideModal);
    }

    modalContainer.addEventListener('click', (event) => {
        if (event.target !== modalContainer) {
            return;
        }

        const clickX = event.clientX;
        const clickY = event.clientY;

        hideModal();

        // On attend que le navigateur ait bien masqué la modale
        setTimeout(() => {
            const underlyingElement = document.elementFromPoint(clickX, clickY);
            if (underlyingElement) {
                underlyingElement.click();
            }
        }, 0); // Le délai de 0ms suffit à décaler l'exécution après le rendu
    });

    window.addEventListener('scroll', () => {
        if (Math.abs(window.scrollY - initialScrollY) > SCROLL_THRESHOLD) {
            hideModal();
        }
    });

    return { showModal, hideModal };
};

const modal = setupModal();