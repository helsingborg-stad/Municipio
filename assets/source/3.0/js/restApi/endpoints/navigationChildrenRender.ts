import { newEndpoint, NameSpace } from "../newEndpoint";

export const navigationChildrenRender = newEndpoint<string[],{}>({
    nameSpace: NameSpace.MUNICIPIO_V1,
    route: 'navigation/children/render',
    method: 'GET'
});
