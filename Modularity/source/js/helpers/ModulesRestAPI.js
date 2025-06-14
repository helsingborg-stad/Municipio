export const ModulesRestAPIEndpoints = (basePath) => ({
    getModule: `${basePath}modularity/v1/modules`,
})

export class ModulesRestAPI {

    fetcher = {};
    endpoints = {};
    nonce = '';
    cacheBustKey = 0;

    constructor(fetcher, endpoints, nonce) {
        this.fetcher = fetcher;
        this.endpoints = endpoints;
        this.nonce = nonce;
        this.cacheBustKey = Math.floor(Date.now() / 15000);
    }

    async getModule(moduleID) {
        const url = `${this.endpoints.getModule}/${moduleID}?cacheBust=${this.cacheBustKey}`;
        const response = await this.fetcher(url);

        if (!response.ok) {
            throw new Error(`Could not load module from URL: ${url}`);
        }

        const htmlString = await response.json();
        const template = document.createElement('template');
        template.innerHTML = htmlString;

        return template.content.firstChild;
    }
}