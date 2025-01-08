declare const acf: any;

class HiddenPostStatusConditional {
    constructor(
        private hiddenAcfPostStatusField: any, 
        private inputs: NodeListOf<HTMLInputElement>, 
        private saveButton: HTMLElement
    ) {
        this.setCurrentSavedValue();
        this.setupListeners();
    }

    private setCurrentSavedValue(): void {
        this.inputs.forEach((input) => {
            if (input.checked) {
                this.hiddenAcfPostStatusField.val(input.value);
            }
        });
    }

    private setupListeners(): void {
        this.saveButton.addEventListener('click', () => {
            this.setCurrentSavedValue();
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof acf === 'undefined') {
        return;
    }

    const hiddenAcfPostStatusField = acf.getField('field_67124199dcb25');
    const postVisibility = document.getElementById('post-visibility-select');
    const inputs = postVisibility?.querySelectorAll('input[type="radio"]');
    const saveButton = document.querySelector('.save-post-visibility');

    if (hiddenAcfPostStatusField && inputs && saveButton) {
        new HiddenPostStatusConditional(hiddenAcfPostStatusField, inputs as NodeListOf<HTMLInputElement>, saveButton as HTMLElement);
    }
});