import { newEndpoint, NameSpace, ApiCallArgs } from "../newEndpoint";

export interface PostsListRenderArgs extends ApiCallArgs {
    attributes: Record<string, unknown>;
    [key: string]: unknown; // Allow additional filter/pagination params
}

export const postsListRender = newEndpoint<string, PostsListRenderArgs>({
    nameSpace: NameSpace.MUNICIPIO_V1,
    route: 'posts-list/render',
    method: 'GET'
});
