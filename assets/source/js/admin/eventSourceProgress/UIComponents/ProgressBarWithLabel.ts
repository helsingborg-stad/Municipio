export default class ProgressBarWithLabel extends HTMLElement {
    private progressElement: HTMLElement;
    private labelElement: HTMLElement;
    private label: string;
    private progress: number;
    private root: ShadowRoot;
    private uniqueId: string = '';
    public static customElementName: string = 'progress-bar-with-label';

    constructor() {
        super();

        this.uniqueId = this.getUniqueID();
        this.root = this.attachShadow({ mode: 'open' });

        this.root.innerHTML = `
            <style> 
                progress {
                    border: none;
                    border-radius: 3px;
                    background-color: #f3f3f3;
                    margin-right: 8px;
                    min-width: 192px;
                }

                ::-webkit-progress-bar {
                    background-color: rgba(0, 0, 0, 0.15);
                    border-radius: 3px;
                    border: none;
                }

                ::-webkit-progress-value {
                    background-color: #2271b1;
                    border-radius: 3px;
                    transition: width 1s;
                }

                label {
                    font-style: italic;
                } 
            </style>
            <progress part="progress-bar" id="${this.uniqueId}"></progress>
            <label part="label" for="${this.uniqueId}"></label>
        `;

        this.progressElement = this.root.querySelector('progress')!;
        this.labelElement = this.root.querySelector('label')!;
        this.label = "";
        this.progress = 0;

        this.progressElement.setAttribute("value", this.progress.toString());
        this.progressElement.setAttribute("max", "100");
    }

    public getElement(): HTMLElement {
        return this;
    }

    public setProgress(value: number): void {
        this.progress = Math.min(100, Math.max(0, value));
        if (isNaN(this.progress)) {
            this.progress = 0;
        }
        this.progressElement!.setAttribute("value", this.progress.toString());
    }

    public setLabel(label: string): void {
        this.label = label;
        this.labelElement.textContent = this.label;
    }

    private getUniqueID(): string {
        return Math.random().toString(36).substr(2, 9);
    }

    static get observedAttributes() {
        return ['label', 'progress'];
    }

    attributeChangedCallback(name: string, oldValue: string, newValue: string) {
        if (oldValue !== newValue) {
            switch (name) {
                case 'label':
                    this.setLabel(newValue);
                    break;
                case 'progress':
                    this.setProgress(Number(newValue));
                    break;
            }
        }
    }

    connectedCallback() {
        if (this.hasAttribute('label')) {
            this.setLabel(this.getAttribute('label')!);
        }
        if (this.hasAttribute('progress')) {
            this.setProgress(Number(this.getAttribute('progress')));
        }
    }
}
