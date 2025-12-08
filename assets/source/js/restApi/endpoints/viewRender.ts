import {newEndpoint, NameSpace, ApiCallArgs } from "../newEndpoint";

export interface ViewRenderArgs extends ApiCallArgs {
    routeParams: string
    data?: {}
}

export const viewRender = newEndpoint<string, ViewRenderArgs>({
    nameSpace: NameSpace.MUNICIPIO_V1,
    route: 'view/render',
    method: 'GET'
});
