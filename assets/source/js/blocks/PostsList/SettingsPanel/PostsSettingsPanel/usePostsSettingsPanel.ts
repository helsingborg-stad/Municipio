import type { Taxonomy } from "@wordpress/core-data";
import { store } from "@wordpress/core-data";
import { useSelect } from "@wordpress/data";

export const usePostsSettingsPanel = (
	selectedPostType: string,
): { taxonomies: Taxonomy[] } => {
	const taxonomies =
		useSelect(
			(select) =>
				selectedPostType
					? select(store).getTaxonomies({ type: selectedPostType })
					: [],
			[store, selectedPostType],
		) || [];

	return {
		taxonomies,
	};
};
