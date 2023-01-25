import {newEndpoint, NameSpace } from "../newEndpoint";

export interface MediaSideloadArgs {
    url: string,
    return?: 'html'|'src'|'id',
    description?: string
}

export const mediaSideload = newEndpoint<string, MediaSideloadArgs>({
    nameSpace: NameSpace.MUNICIPIO_V1,
    route: 'media/sideload',
    method: 'POST'
});
