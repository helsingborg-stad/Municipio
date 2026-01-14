import blockConfig from '../block.json';
export {};
declare global {
	export type TaxonomyFilter = {
		taxonomy: string;
		type: string;
	};

	type AttributeDefinition = typeof blockConfig.attributes;
	export type PostsListAttributes = {
		[K in keyof typeof blockConfig.attributes]: typeof blockConfig.attributes[K]['default'];
	};
}
