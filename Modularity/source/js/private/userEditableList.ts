import { ValuesInterface, ItemsObject } from "../interface/privateModules";

class UserEditableList {
    private savingLang: string = 'Saving';
    private buttonText: string;

    constructor(
        private submitButton: HTMLButtonElement,
        private closeButton: HTMLButtonElement,
        private errorNotice: HTMLElement,
        private itemsObject: ItemsObject,
        private checkboxes: NodeListOf<HTMLInputElement>,
        private readonly userId: string, 
        private readonly moduleId: string,
        private metaKey: string
    ) {
        this.buttonText = this.submitButton.textContent ?? 'Save';
        this.savingLang = this.submitButton.getAttribute('data-js-saving-lang') ?? this.savingLang;

        if (wpApiSettings) {
            this.submitListener();
        }
    }

    private submitListener() {
        this.submitButton.addEventListener('click', (event) => {
            event.preventDefault();

            let values: ValuesInterface = {};

            this.checkboxes.forEach((checkbox) => {
                values[checkbox.value] = checkbox.checked;
            });

            this.patchUser(values);
        });
    }

    private patchUser(values: ValuesInterface) {
        this.handleBeforeSave();
    
        const endpoint = `${wpApiSettings?.root}wp/v2/users/${this.userId}`;
        fetch(endpoint, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-NONCE': wpApiSettings?.nonce ?? '',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch existing user data');
            }
            return response.json();
        })
        .then(data => {
            let metaData = data.meta ? data.meta[this.metaKey] : null;

            if (!metaData || typeof metaData !== 'object' || Array.isArray(metaData)) {
                metaData = {};
            }

            metaData[this.moduleId] = values;

            return fetch(endpoint, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-NONCE': wpApiSettings?.nonce ?? '',
                },
                body: JSON.stringify({
                    meta: {
                        [this.metaKey]: metaData,
                    }
                }),
            });
        })
        .then(() => {
            this.handleSuccessfullSave(values);
        })
        .catch(error => {
            console.error('Error:', error);
            this.handleFailedSave();
        });
    }

    private handleBeforeSave() {
        this.submitButton.disabled = true;
        this.closeButton.disabled = true;

        this.submitButton.textContent = this.savingLang;
    }
    
    private handleFailedSave() {
        this.errorNotice.classList.remove('u-display--none');
        this.submitButton.disabled = false;
        this.closeButton.disabled = false;
        this.submitButton.textContent = this.buttonText;
    }

    private handleSuccessfullSave(values: ValuesInterface) {
        this.submitButton.disabled = false;
        this.closeButton.disabled = false;
        this.submitButton.textContent = this.buttonText;
        this.showOrHideItemsBasedOnSaved(values);
        this.closeButton.click();
    }

    private showOrHideItemsBasedOnSaved(values: ValuesInterface) {
        for (const [key, element] of Object.entries(this.itemsObject)) { 
            if (!(key in values)) {
                continue;
            }

            if (values[key]) {
                element.classList.remove('u-display--none');
            } else {
                element.classList.add('u-display--none');
            }
        }
    }
}

export default function initUserEditableList(userEditable: HTMLElement) {
    const metaKey = userEditable.getAttribute('data-js-user-editable');

    if (!metaKey) {
        return;
    }

    const userId = userEditable.getAttribute('data-js-user-editable-user');
    const moduleId = userEditable.getAttribute('data-js-user-editable-id');
    const submitButton = userEditable.querySelector('button[type="submit"]');
    const checkboxes = userEditable.querySelectorAll('input[type="checkbox"]') as NodeListOf<HTMLInputElement>;
    const errorNotice = userEditable.querySelector('[data-js-user-editable-error]');
    let itemsObject: ItemsObject = {};

    userEditable.querySelectorAll('[data-js-item-id]').forEach(item => {
        const itemId = item.getAttribute('data-js-item-id');
        if (itemId) {
            itemsObject[itemId] = item as HTMLElement;
        }
    });

    const closeButton = userEditable.querySelector('button[data-js-cancel-save]');

    if (submitButton && closeButton && userId && moduleId && checkboxes.length) {
        new UserEditableList(
            submitButton as HTMLButtonElement, 
            closeButton as HTMLButtonElement, 
            errorNotice as HTMLElement,
            itemsObject,
            checkboxes, 
            userId, 
            moduleId,
            metaKey
        );
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-js-user-editable]').forEach(userEditable => {
        initUserEditableList(userEditable as HTMLElement);
    });
});