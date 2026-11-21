export const TAXONOMY_FILTERS_SELECTOR = "[data-js-posts-list-taxonomy-filters]";

/**
 * Replaces the taxonomy filters in the form with the ones from a freshly rendered posts list,
 * so that term counts and available terms follow the filters just applied.
 *
 * Select components added to the DOM are initialized by the styleguide's select observer.
 *
 * @returns Whether the filters were replaced.
 */
export const updateTaxonomyFilters = (
	form: HTMLFormElement | null,
	renderedHtml: ParentNode,
): boolean => {
	const currentFilters = form?.querySelector<HTMLElement>(
		TAXONOMY_FILTERS_SELECTOR,
	);
	const renderedFilters = renderedHtml.querySelector<HTMLElement>(
		TAXONOMY_FILTERS_SELECTOR,
	);

	if (!currentFilters || !renderedFilters) return false;

	currentFilters.replaceChildren(
		...Array.from(renderedFilters.childNodes).map((node) =>
			document.importNode(node, true),
		),
	);

	return true;
};
