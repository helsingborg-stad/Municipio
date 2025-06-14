declare const modularityAdminLanguage: {
    close: string;
};

class Modal {
    modalContainer: HTMLDivElement | null;
    body: HTMLElement;
    isOpen: boolean;

    constructor() {
        this.modalContainer = null;
        this.body = document.body;
        this.isOpen = false;
        this.handleEvents();
    }

    private createModalContent(url: string) {
        return `
            <div class="modularity-modal-wrapper">
                <button class="modularity-modal-close" data-modularity-modal-action="close">&times; ${modularityAdminLanguage.close}</button>
                <div class="modularity-modal-spinner-container" id="modularity-iframe-loader"><span class="modularity-modal-spinner"></span></div>
                <iframe class="modularity-modal-iframe" src="${url}" frameborder="0" allowtransparency></iframe>
            </div>
        `;
    }

    public open(url: string) {
        this.modalContainer = document.createElement('div');
        this.modalContainer.id = 'modularity-modal';
        this.modalContainer.innerHTML = this.createModalContent(url);

        this.body.appendChild(this.modalContainer);
        this.body.classList.add('modularity-modal-open');
        this.isOpen = true;
    }

    private close() {
        if (this.modalContainer) {
            this.body.removeChild(this.modalContainer);
            this.body.classList.remove('modularity-modal-open');
            this.isOpen = false;
        }
    }

    private handleEvents() {
        this.body.addEventListener('click', (e) => {
            const target = e.target as HTMLElement;
            if (target && target.getAttribute('data-modularity-modal-action') === 'close') {
                e.preventDefault();
                this.close();
            }
        });
    }
}

export default Modal;