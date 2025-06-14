import { ModulesRestAPI } from './ModulesRestAPI.js';

describe('ModulesRestAPI', () => {
    describe('getModule', () => {
        it('throws if the response is not ok', async () => {
            const fetcher = jest.fn(() => Promise.resolve({ ok: false }));
            const api = new ModulesRestAPI(fetcher, {});
            await expect(api.getModule('id')).rejects.toThrow();
        })

        it('returns the fetched module as a DOM node', async () => {
            const fetcher = jest.fn(() => Promise.resolve({ ok: true, json: () => Promise.resolve('<div>test</div>') }));
            const api = new ModulesRestAPI(fetcher, {});
            const module = await api.getModule('id');
            expect(module.nodeName).toBe('DIV');
            expect(module.innerHTML).toBe('test');
        })
    })
});