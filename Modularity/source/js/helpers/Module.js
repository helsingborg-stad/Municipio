/**
 * Module class
 * Represents a module on the page.
 * 
 * @property {string} id
 * @property {HTMLElement} element
 * @property {ModulesRestAPI} restAPI
 */
export class Module {

    static selector = '[data-module-refresh-interval]';

    constructor(id, element, restAPI) {
        this.id = id;
        this.element = element;
        this.restAPI = restAPI;
        this.setupEventListeners();
    }

    setupEventListeners() {

        if( !this.element ) {
            throw new Error('Element is not set.');
        }

        this.element.addEventListener('refresh', this.handleRefresh.bind(this));

        this.element.addEventListener('DOMNodeRemoved', () => {
            this.element.removeEventListener('refresh', this.handleRefresh);
        });
    }

    refresh(interval = null) {
        const refreshEvent = new CustomEvent('refresh');
        this.element.dispatchEvent(refreshEvent);

        if (interval) {
            setTimeout(() => {
                this.refresh(interval);
            }, interval);
        }
    }

    async handleRefresh() {
        const freshModule = await this.restAPI.getModule(this.id);
        this.replace(freshModule);
    }


    replace(freshModule) {
        this.element.parentNode.replaceChild(freshModule, this.element);
        this.element = freshModule;
        this.setupEventListeners();
    }

    getRefreshInterval() {
        const intervalAttributeValue = this.element.getAttribute('data-module-refresh-interval');
        if (!intervalAttributeValue || isNaN(intervalAttributeValue)) return null;
        return parseInt(intervalAttributeValue) * 1000;
    }
}