export { };
declare global {

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