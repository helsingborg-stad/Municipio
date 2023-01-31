export enum NameSpace {
    MUNICIPIO_V1 = 'municipio/v1',
}

type RequestMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

interface EndpointOptions {
    nameSpace: NameSpace;
    route: string;
    method: RequestMethod;
}

export const newEndpoint = <T, A>(options: EndpointOptions) => ({
    call: (callArgs?: A): Promise<T> => {
        return fetch(`/wp-json/${options.nameSpace}/${options.route}`, {
            method: options.method,
            body: JSON.stringify(callArgs),
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': wpApiSettings.nonce ?? '',
            },
        }).then((response) => {
            if (200 == response.status) {
                const contentType = response.headers.get('content-type');
                const contentTypeIsJson = contentType && contentType.indexOf('application/json') !== -1;

                if (contentTypeIsJson) {
                    return response.json();
                }

                return response.text();
            } else {
                return response.statusText;
            }
        });
    },
});
