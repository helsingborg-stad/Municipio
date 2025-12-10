export {};
declare global {
	export type TaxonomyFilter = {
		taxonomy: string;
		type: string;
	};

	export interface PostsListAttributes {
		dateFilterEnabled: boolean;
		dateFormat: string;
		dateSource: string;
		design: string;
		numberOfColumns: number;
		orderBy: string;
		order: "asc" | "desc";
		paginationEnabled: boolean;
		postsPerPage: number;
		postType: string;
		taxonomiesEnabledForFiltering: TaxonomyFilter[];
		textSearchEnabled: boolean;
		terms: Array<{
			taxonomy: string;
			terms: number[];
		}>;
	}
}
