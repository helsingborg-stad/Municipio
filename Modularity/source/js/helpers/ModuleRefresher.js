import { Module } from './Module.js';

/**
 * Ensure that the wpApiSettings object is present on the window object.
 * 
 * @throws {Error} If wpApiSettings or wpApiSettings.root is not present on the window object.
 */
export function ensureWPApiSettings() {
    const { wpApiSettings } = window;
    if (!wpApiSettings?.root) {
        throw new Error('wpApiSettings or wpApiSettings.root is not present on the window object.');
    }
}

export class ModuleRefresher {

    /**
     * 
     * @param ModulesRestAPI restAPI 
     */
    constructor(restAPI) {
        this.restAPI = restAPI;
    }

    refreshModules() {
        const moduleElements = document.querySelectorAll(Module.selector);
        const modules = Array.from(moduleElements).map((element) => new Module(
            element.getAttribute('data-module-id'), element, this.restAPI
        ));

        for (const module of modules) {
            const refreshInterval = module.getRefreshInterval();
            module.refresh(refreshInterval);
        }
    }
}