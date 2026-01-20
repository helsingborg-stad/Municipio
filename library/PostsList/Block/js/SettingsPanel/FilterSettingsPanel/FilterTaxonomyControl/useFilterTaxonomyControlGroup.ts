import { store, type Taxonomy } from "@wordpress/core-data";
import { select } from "@wordpress/data";

type UseFilterTaxonomyControlGroup = (postType: string) => {
	taxonomies: Taxonomy[];
};

export const useFilterTaxonomyControlGroup: UseFilterTaxonomyControlGroup = (
	postType: string,
) => {
	const taxonomies: Taxonomy[] =
		(select(store)
			.getTaxonomies({
				per_page: -1,
			})
			?.filter((taxonomy) =>
				taxonomy.types.includes(postType),
			) as Taxonomy[]) || [];

	return {
		taxonomies,
	};
};
