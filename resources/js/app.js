import 'animate.css';

document.addEventListener('DOMContentLoaded', () => {
    const openButtons = document.querySelectorAll('[data-public-modal-open]');
    const modals = document.querySelectorAll('dialog.sp-terms-modal');

    if (!openButtons.length || !modals.length) {
        return;
    }

    const closeModal = (modal) => {
        if (modal.open) {
            modal.close();
        }
    };

    const openModal = (modal) => {
        if (!modal.open) {
            modal.showModal();
        }
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-public-modal-open');
            const modal = targetId ? document.getElementById(targetId) : null;

            if (modal instanceof HTMLDialogElement) {
                openModal(modal);
            }
        });
    });

    modals.forEach((modal) => {
        modal.querySelectorAll('[data-public-modal-close]').forEach((button) => {
            button.addEventListener('click', () => closeModal(modal));
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });
});
