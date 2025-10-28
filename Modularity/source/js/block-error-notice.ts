export default class BlockErrorNotice {
    fieldGroup: HTMLElement;
    field: HTMLTextAreaElement | null;
    noticeElement: HTMLElement | false;
    regex: RegExp;
    errorMessage: string;
    notice: HTMLElement | null;

    constructor(fieldGroup: HTMLElement, regex: RegExp, errorMessage: string) {
        this.fieldGroup         = fieldGroup;
        this.field              = fieldGroup.querySelector('textarea');
        this.noticeElement      = false;
        this.regex              = regex;
        this.errorMessage       = errorMessage;
        this.notice             = null;
        
        this.field && this.init();
    }

    init() {
        this.createNotice();
        this.checkFieldValue();
        this.setupTextareaListener();
    }
    
    private createNotice() {
        const noticeString = `
            <div class="c-notice" style="margin-bottom: 16px; display: none; background-color: #d73740; color: white; padding: 16px 24px; border-radius: 4px;" data-js-block-error-notice>
            <span class="c-notice__icon" margin-right: 16px;>
            <span class="c-icon c-icon--report c-icon--material c-icon--material-report material-icons c-icon--size-md" role="img" aria-label="Icon: Undefined" alt="Icon: Undefined"><span data-nosnippet="" translate="no" aria-hidden="true">report</span></span></span>
            <span id="notice__text__" for="" class="c-notice__message">${this.errorMessage}</span>
            </div>`;

        const div = document.createElement('div');

        div.innerHTML = noticeString;

        this.fieldGroup.insertBefore(div, this.fieldGroup.firstChild);

        const notice = this.fieldGroup.querySelector('[data-js-block-error-notice]') as HTMLElement | null;
        if (notice) {
            this.notice = notice;
        }
    }

   private checkFieldValue() {
        const faultyScriptElement = this.checkRegex();
        if (faultyScriptElement) {
            this.fieldGroup.setAttribute('data-js-block-field-validation-error', '');
            if (this.notice) {
                this.notice.style.display = 'block';
            }
        } else {
            this.fieldGroup.removeAttribute('data-js-block-field-validation-error');
            this.notice && (this.notice.style.display = 'none');
        }
    }

    public getFaultyScriptModules() {
        return document.querySelectorAll('[data-js-block-field-validation-error]').length > 0;
    }

    private setupTextareaListener() {
        this.field && this.field.addEventListener('input', () => {
            this.checkFieldValue();
        });
        
        this.field && this.field.addEventListener('change', () => {
            this.checkFieldValue();
        });
    }

    private checkRegex() {
        return this.regex.test(this.field?.value || '');
    }
}