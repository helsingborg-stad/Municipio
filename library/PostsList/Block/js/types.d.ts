export { };
declare global {

    interface ServerSideRenderProps {
        block: string;
        attributes?: Record<string, any>;
        urlQueryArgs?: Record<string, any>;
        httpMethod?: 'GET' | 'POST';
        LoadingResponsePlaceholder?: () => JSX.Element;
    }

    interface Window {
        wp: {
            blocks: typeof import('@wordpress/blocks'),
            blockEditor: typeof import('@wordpress/block-editor'),
            components: typeof import('@wordpress/components'),
            data: typeof import('@wordpress/data'),
            coreData: typeof import('@wordpress/core-data'),
            i18n: typeof import('@wordpress/i18n'),
            serverSideRender: React.ComponentType<ServerSideRenderProps>
        }
    }

    export type TaxonomyFilter = {
        taxonomy: string;
        type: string;
    }

    export interface PostsListAttributes {
        postType: string;
        numberOfColumns: number;
        design: string;
        postsPerPage: number;
        enableFilters: boolean;
        textSearchEnabled: boolean;
        dateFilterEnabled: boolean;
        taxonomiesEnabledForFiltering: TaxonomyFilter[];
        terms: Array<{
            taxonomy: string;
            terms: number[];
        }>
    }
}