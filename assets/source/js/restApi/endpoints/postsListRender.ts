import { newEndpoint, NameSpace, ApiCallArgs } from "../newEndpoint";

/**
 * Arguments for the PostsList render API endpoint.
 */
export interface PostsListRenderArgs extends ApiCallArgs {
    /** Block attributes for PostsList configuration. */
    attributes: Record<string, unknown>;
    /** Additional filter/pagination params. */
    [key: string]: unknown;
}

/**
 * REST API endpoint for rendering PostsList asynchronously.
 * Returns rendered HTML for the posts list based on provided attributes and filters.
 */
export const postsListRender = newEndpoint<string, PostsListRenderArgs>({
    nameSpace: NameSpace.MUNICIPIO_V1,
    route: 'posts-list/render',
    method: 'GET'
});
