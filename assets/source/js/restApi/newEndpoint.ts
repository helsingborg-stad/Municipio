export enum NameSpace {
    MUNICIPIO_V1 = 'municipio/v1',
}

type RequestMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

interface EndpointOptions {
    nameSpace: NameSpace;
    route: string;
    method: RequestMethod;
}

export interface ApiCallArgs extends Record<string,any> {
    routeParams?: string
}

const getNonce = ():string|null => {
    if( typeof wpApiSettings === 'undefined' || !wpApiSettings.nonce ) {
        return null
    }
    
    return wpApiSettings.nonce
}

const getApiRoot = ():string|null => {
    if( typeof wpApiSettings === 'undefined' || !wpApiSettings.root ) {
        return null
    }
    
    return wpApiSettings.root
}

const buildUrl = (nameSpace:string, route:string, routeParams?:string, params?:{}):string => {

    const apiRoot = getApiRoot()

    if( !apiRoot ) {
        return ''
    }

    let url = `${apiRoot}${nameSpace}/${route}`
    url += routeParams ? `/${routeParams}` : ''

    if (params) {
        const searchParams = new URLSearchParams();
        Object.entries(params).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(v => searchParams.append(key, String(v)));
            } else if (value !== null && value !== undefined) {
                searchParams.append(key, String(value));
            }
        });
        const queryString = searchParams.toString();
        url += queryString ? '?' + queryString : '';
    }

    return url
}

export const newEndpoint = <T, A extends ApiCallArgs>(options:EndpointOptions) => ({
    call: (callArgs?:A):Promise<T> => {
        
        const {routeParams, ...data} = callArgs ?? {}
        const url = buildUrl(options.nameSpace,options.route,routeParams,options.method === 'GET' && data)
        const nonce = getNonce()
        
        const headers:HeadersInit = {
            'Content-Type': 'application/json',
        }
        
        if( nonce ) {
            headers['X-WP-Nonce'] = nonce
        }

        const fetchOptions:RequestInit = {
            method: options.method,
            headers
        }

        if( options.method !== 'GET' ) {
            fetchOptions.body = JSON.stringify(callArgs)
        }
        
        return fetch(
            url,
            {
                method: options.method,
                body: options.method !== 'GET' ? JSON.stringify(callArgs) : undefined,
                headers
            }
            ).then(response => {
                if(response.status === 200) {
                    const contentType = response.headers.get("content-type");
                    const contentTypeIsJson = (contentType && contentType.indexOf("application/json") !== -1)
                    
                    if(contentTypeIsJson) {
                        return response.json()
                    }
                    
                    return response.text();
                } else {
                    return response.statusText;
                }
            });
        },
    });
    