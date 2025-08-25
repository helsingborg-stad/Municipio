import { newEndpoint, NameSpace } from "../newEndpoint";

export const navigationChildren = newEndpoint<string[], {}>({
    nameSpace: NameSpace.MUNICIPIO_V1,
    route: 'navigation/children',
    method: 'GET'
});
