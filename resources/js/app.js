import 'animate.css';

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('[data-terms-modal]');

    if (!(modal instanceof HTMLDialogElement)) {
        return;
    }

    const openButtons = document.querySelectorAll('[data-terms-modal-open]');
    const closeButtons = modal.querySelectorAll('[data-terms-modal-close]');

    const openModal = () => {
        if (!modal.open) {
            modal.showModal();
        }
    };

    const closeModal = () => {
        if (modal.open) {
            modal.close();
        }
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', openModal);
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
});
