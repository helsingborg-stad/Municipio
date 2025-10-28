import { Module } from './Module.js';

describe('Module', () => {

    describe('setupEventListeners', () => {
        it('sets up the refresh event', () => {
            const element = document.createElement('div');
            const module = new Module('id', element, null);
            const spy = jest.spyOn(element, 'addEventListener');

            module.setupEventListeners();

            expect(spy).toHaveBeenCalledWith('refresh', expect.any(Function));
        })

        it('throws if element is not set', () => {
            const action = () => new Module('id', null, null);
            expect(() => action()).toThrow();
        })

        it('removes the refresh event when the element is removed', () => {
            const element = document.createElement('div');
            const module = new Module('id', element, null);
            const spy = jest.spyOn(element, 'removeEventListener');
            module.setupEventListeners();

            element.dispatchEvent(new CustomEvent('DOMNodeRemoved'));

            expect(spy).toHaveBeenCalledWith('refresh', expect.any(Function));
        })
    })

    describe('handleRefresh', () => {
        it('calls the api for a fresh instance, and replaces the element', async () => {
            const element = document.createElement('div');
            const freshModule = document.createElement('div');
            document.body.appendChild(element);
            const restAPI = { getModule: jest.fn(() => Promise.resolve(freshModule)) };
            const module = new Module('id', element, restAPI);

            await module.handleRefresh();

            expect(restAPI.getModule).toHaveBeenCalledWith('id');
            expect(element.parentNode).toBe(null);
            expect(freshModule.parentNode).not.toBe(null);
        })
    })

    describe('refresh', () => {
        it('dispatches a refresh event', () => {
            const element = document.createElement('div');
            document.body.appendChild(element);
            const freshModule = document.createElement('div');
            const restAPI = { getModule: jest.fn(() => Promise.resolve(freshModule)) };
            const module = new Module('id', element, restAPI);
            const spy = jest.spyOn(element, 'dispatchEvent');

            module.refresh();

            expect(spy).toHaveBeenCalledWith(expect.any(CustomEvent));
        });
    });

    describe('getRefreshInterval', () => {
        it('returns the interval in milliseconds if the attribute is set', () => {
            const element = document.createElement('div');
            element.setAttribute('data-module-refresh-interval', '10');
            const module = new Module('id', element, null);

            const result = module.getRefreshInterval();

            expect(result).toBe(10000);
        })

        it('returns null if the attribute is not set', () => {
            const element = document.createElement('div');
            const module = new Module('id', element, null);

            const result = module.getRefreshInterval();

            expect(result).toBe(null);
        })
    });

});