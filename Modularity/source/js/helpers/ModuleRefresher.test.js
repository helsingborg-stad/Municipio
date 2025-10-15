import { ensureWPApiSettings, ModuleRefresher } from './ModuleRefresher.js';

describe('ensureWPApiSettings', () => {
    it('throws if wpApiSettings is not present on the window object', () => {
        expect(() => { ensureWPApiSettings(); }).toThrow();
    })

    it('throws if wpApiSettings.root is not present on the window object', () => {
        window.wpApiSettings = {};
        expect(() => { ensureWPApiSettings(); }).toThrow();
    })
})

describe('ModuleRefresher', () => {
    it('calls refresh on available modules', () => {
        const restAPI = { getModule: jest.fn(() => Promise.resolve(document.createElement('div'))) };
        const moduleRefresher = new ModuleRefresher(restAPI);
        const module = document.createElement('div');
        module.setAttribute('data-module-id', 'id');
        module.setAttribute('data-module-refresh-interval', '10');
        document.body.appendChild(module);

        moduleRefresher.refreshModules();

        expect(restAPI.getModule).toHaveBeenCalledWith('id');
    })
})