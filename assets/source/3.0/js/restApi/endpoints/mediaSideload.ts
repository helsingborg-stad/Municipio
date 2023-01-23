import {newEndpoint, NameSpace } from "../newEndpoint";

interface Args {
    url: string,
    return: 'html'|'src'|'id'
}

export const mediaSideload = newEndpoint<string, Args>({
    nameSpace: NameSpace.MUNICIPIO_V1,
    route: 'media/sideload',
    method: 'POST'
});
