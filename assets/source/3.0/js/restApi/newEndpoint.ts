export enum NameSpace {
    MUNICIPIO_V1 = 'municipio/v1'
}

type RequestMethod = 'GET'|'POST'|'PUT'|'PATCH'|'DELETE'

interface EndpointOptions {
    nameSpace:NameSpace,
    route:string, 
    method:RequestMethod
}

export interface ApiCallArgs extends Record<string,any> {
    routeParams?: string
}

export const newEndpoint = <T, A extends ApiCallArgs>(options:EndpointOptions) => ({
    call: (callArgs?:A):Promise<T> => {

        const {routeParams, ...data} = callArgs ?? {}
        
        let url = `/wp-json/${options.nameSpace}/${options.route}`
        url += routeParams ? `/${routeParams}` : ''
        url += options.method === 'GET' ? '?' + new URLSearchParams(data) : ''

        const nonce = typeof wpApiSettings !== 'undefined' && wpApiSettings.nonce ? wpApiSettings.nonce : null
        
        const headers:HeadersInit = {
            'Content-Type': 'application/json',
        }

        if( nonce ) {
            headers['X-WP-Nonce'] = nonce
        }


        return fetch(
            url,
            {
                method: options.method,
                body: options.method !== 'GET' ? JSON.stringify(callArgs) : undefined,
                headers
            }
            ).then(response => {
                const contentType = response.headers.get("content-type");
                const contentTypeIsJson = (contentType && contentType.indexOf("application/json") !== -1)
                
                if(contentTypeIsJson) {
                    return response.json()
                }

                return response.text()
            })
    }
})
