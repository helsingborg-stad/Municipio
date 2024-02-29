import {LitElement, html, css} from 'https://cdn.jsdelivr.net/npm/lit@3.1.2/+esm';

const getBemClassName = (block, element, modifier) => {
    const prefix = 'c-';
    let className = prefix + block;

    if (element && modifier) {
        className = `${className}__${element}--${modifier}`;
    } else if (element) {
        className = `${className}__${element}`;
    }

    return className;
};

class Card extends LitElement {
    static bemBlockName = 'card';
    static defaultImageUrl = 'https://live.staticflickr.com/3761/9118418251_22959c2880_b.jpg';
    static styles = css`
        hbg-card {
            display: block;
        }
    `;

    static properties = {
        imageUrl: {},
        heading: {},
        subHeading: {},
        body: {}
    };

    constructor() {
        super();
        this.imageUrl = Card.defaultImageUrl;
        this.heading = 'Default Heading';
        this.subHeading = 'Default Sub Heading';
        this.body = 'Default Body Text';
    }
    
    createRenderRoot() {
        return this; // Inherit styles
    }
    
    getBem(element, modifier) {
        return getBemClassName(Card.bemBlockName, element, modifier);
    }

    getImageMarkup() {
        return this.imageUrl
            ? html`
                <div class="${this.getBem('image')}">
                    <div class="${this.getBem('image-background')}" style="background-image:url('${this.imageUrl}');"></div>
                </div>`
            : '';
    }

    getHeadingMarkup() {
        return this.heading
            ? html`
                <div class="c-group c-group--horizontal c-group--justify-content-space-between c-group--align-items-start">
                    <div class="c-group c-group--vertical">
                        <h3>${this.heading}</h3>
                    </div>
                </div>`
            : '';
    }

    getSubHeadingMarkup() {
        return this.subHeading ? html`<p>${this.subHeading}</p>` : '';
    }

    getBodyMarkup() {
        return this.body ? html`<p>${this.body}</p>` : '';
    }

    render() {

        return html`
            <div class="${this.getBem()}">
                ${this.getImageMarkup()}
                <div class="${this.getBem('body')}">
                    ${this.getHeadingMarkup()}
                    ${this.getSubHeadingMarkup()}
                    ${this.getBodyMarkup()}
                </div>
            </div>
        `;
    }
}

customElements.define('hbg-card', Card);