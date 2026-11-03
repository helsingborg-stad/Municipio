import { initializeWpApiSettingsNonceRefresh } from './wpApiSettings';

describe('REST nonce startup refresh', () => {
    const originalFetch = global.fetch;
    const originalReadyState = Object.getOwnPropertyDescriptor(document, 'readyState');
    let fetchMock: jest.Mock;

    beforeEach(() => {
        (global as any).wpApiSettings = {
            root: 'http://localhost/wp-json/',
            nonce: 'cached-nonce',
            nonceRefreshCacheKey: 'logged-out',
        };
        fetchMock = jest.fn().mockResolvedValue({
            headers: { get: () => 'fresh-nonce' },
            text: async () => '',
        });
        global.fetch = fetchMock;
    });

    afterEach(() => {
        global.fetch = originalFetch;
        if (originalReadyState) {
            Object.defineProperty(document, 'readyState', originalReadyState);
        } else {
            delete (document as any).readyState;
        }
        delete (global as any).wpApiSettings;
    });

    it('refreshes a cached nonce once the document is ready', async () => {
        Object.defineProperty(document, 'readyState', { configurable: true, value: 'loading' });
        initializeWpApiSettingsNonceRefresh();
        expect(fetchMock).not.toHaveBeenCalled();
        document.dispatchEvent(new Event('DOMContentLoaded'));
        await new Promise(resolve => setTimeout(resolve, 0));
        expect(fetchMock).toHaveBeenCalledTimes(1);
        expect((global as any).wpApiSettings.nonce).toBe('fresh-nonce');
        document.dispatchEvent(new Event('DOMContentLoaded'));
        expect(fetchMock).toHaveBeenCalledTimes(1);
    });

    it('refreshes immediately when initialized after DOMContentLoaded', async () => {
        Object.defineProperty(document, 'readyState', { configurable: true, value: 'interactive' });
        initializeWpApiSettingsNonceRefresh();
        await new Promise(resolve => setTimeout(resolve, 0));
        expect(fetchMock).toHaveBeenCalledWith(
            expect.stringContaining('/municipio/v1/nonce/refresh?'),
            expect.objectContaining({ credentials: 'include' }),
        );
        expect((global as any).wpApiSettings.nonce).toBe('fresh-nonce');
        expect(typeof (global as any).wpApiSettings.refreshNonce).toBe('function');
    });
});
