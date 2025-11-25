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
        },
        React: typeof import('react')
    }

    export type TaxonomyFilter = {
        taxonomy: string;
        type: string;
    }

    export interface PostsListAttributes {
        dateFilterEnabled: boolean;
        design: string;
        enableFilters: boolean;
        numberOfColumns: number;
        orderBy: string;
        order: 'asc' | 'desc';
        postsPerPage: number;
        postType: string;
        taxonomiesEnabledForFiltering: TaxonomyFilter[];
        textSearchEnabled: boolean;
        terms: Array<{
            taxonomy: string;
            terms: number[];
        }>
    }
}