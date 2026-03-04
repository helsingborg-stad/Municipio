const getHeaders = ():Record<string, string> => {
    const headers:Record<string, string> = {
        'Content-Type': 'application/json'
    }

    if( wpApiSettings?.nonce ) {
        headers['X-WP-Nonce'] = wpApiSettings.nonce
    }

    return headers
}

const nonceRefreshRoute = 'municipio/v1/nonce/refresh'

const getHourlyCacheBustKey = ():string => {
    return String(Math.floor(Date.now() / (60 * 60 * 1000)))
}

const getNonceRefreshUrl = (root:string):string => {
    const cacheBustKey = getHourlyCacheBustKey()

    try {
        const apiRootUrl = new URL(root, window.location.origin)
        const url = new URL(nonceRefreshRoute, apiRootUrl)
        url.searchParams.set('_nonceCacheBust', cacheBustKey)
        return url.toString()
    } catch {
        const baseUrl = root.endsWith('/') ? root : `${root}/`
        return `${baseUrl}${nonceRefreshRoute}?_nonceCacheBust=${cacheBustKey}`
    }
}

const ensureRefreshNonceMethod = ():void => {
    if( typeof wpApiSettings === 'undefined' || !wpApiSettings ) {
        return
    }

    if( typeof wpApiSettings.refreshNonce === 'function' ) {
        return
    }

    wpApiSettings.refreshNonce = async (nonce?:string):Promise<string | null> => {
        if( typeof nonce === 'string' && nonce.length > 0 ) {
            wpApiSettings.nonce = nonce
            return nonce
        }

        if( !wpApiSettings.root ) {
            return wpApiSettings.nonce ?? null
        }

        try {
            const response = await fetch(getNonceRefreshUrl(wpApiSettings.root), {
                method: 'GET',
                credentials: 'include',
                headers: getHeaders()
            })

            const refreshedNonce = response.headers.get('X-WP-Nonce')

            if( refreshedNonce ) {
                wpApiSettings.nonce = refreshedNonce
            }

            return wpApiSettings.nonce ?? null
        } catch {
            return wpApiSettings.nonce ?? null
        }
    }
}

export const initializeWpApiSettingsNonceRefresh = ():void => {
    ensureRefreshNonceMethod()

    const refresh = ():void => {
        void wpApiSettings?.refreshNonce?.()
    }

    if( document.readyState === 'loading' ) {
        document.addEventListener('DOMContentLoaded', refresh, { once: true })
        return
    }

    refresh()
}
