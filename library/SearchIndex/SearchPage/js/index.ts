import { liteClient } from "algoliasearch/lite";
import * as Typesense from "typesense";

interface FacetConfig {
	attribute: string;
	label: string;
	enabled: boolean;
}

interface SearchProviderConfig {
	type: "algolia" | "typesense";
	applicationId?: string;
	apiKey: string;
	indexName?: string;
	collectionName?: string;
	host?: string;
	port?: number;
	protocol?: string;
	searchAsYouType?: boolean;
	facetingEnabled?: boolean;
	facets?: FacetConfig[];
}

interface SearchParams {
	query?: string;
	query_by?: string;
	page?: number;
	page_size?: number;
	highlight_full_fields?: string;
	facetFilters?: string[][];
}

interface SearchHit {
	title: string;
	summary: string;
	subtitle: string;
	ariaLabel: string;
	image?: string;
	altText: string;
	url: string;
}

interface FacetResult {
	attribute: string;
	label: string;
	values: Array<{ value: string; count: number }>;
}

interface SearchResult {
	query: string;
	totalHits: number;
	currentPage: number;
	totalPages: number;
	hits: SearchHit[];
	facets: FacetResult[];
}

interface SearchPageConfig {
	provider: SearchProviderConfig;
	params: SearchParams;
}

interface IndexedPost {
	post_title?: string;
	post_excerpt?: string;
	origin_site?: string;
	thumbnail?: string;
	thumbnail_alt?: string;
	permalink?: string;
}

declare const searchIndexSearchPageConfig: SearchPageConfig;

/**
 * Render provider highlighting while treating all markup except mark as text.
 *
 * @param element Element that receives the highlighted text.
 * @param value Provider value containing encoded text and optional mark elements.
 * @returns void
 */
export const renderHighlightedText = (
	element: HTMLElement,
	value: string,
): void => {
	const template = document.createElement("template");
	template.innerHTML = value;
	const output = document.createDocumentFragment();

	const appendNodes = (source: ParentNode, target: Node): void => {
		source.childNodes.forEach((node) => {
			if (node.nodeType === Node.TEXT_NODE) {
				target.appendChild(document.createTextNode(node.textContent ?? ""));
				return;
			}

			if (node instanceof HTMLElement && node.tagName === "MARK") {
				const mark = document.createElement("mark");
				appendNodes(node, mark);
				target.appendChild(mark);
				return;
			}

			target.appendChild(document.createTextNode(node.textContent ?? ""));
		});
	};

	appendNodes(template.content, output);
	element.replaceChildren(output);
};

const getEnabledFacets = (config: SearchProviderConfig): FacetConfig[] =>
	config.facetingEnabled
		? (config.facets ?? []).filter(({ enabled }) => enabled)
		: [];

const transformPost = (
	post: IndexedPost,
	title = post.post_title ?? "",
	summary = post.post_excerpt ?? "",
): SearchHit => ({
	title,
	summary,
	subtitle: post.origin_site ?? "",
	ariaLabel: post.post_excerpt ?? "",
	image: post.thumbnail?.replaceAll("/wp/", "/"),
	altText: post.thumbnail_alt ?? "",
	url: post.permalink ?? "",
});

const createAlgoliaSearch = (config: SearchProviderConfig) => {
	const client = liteClient(config.applicationId ?? "", config.apiKey);
	return async (params: SearchParams): Promise<SearchResult> => {
		const enabledFacets = getEnabledFacets(config);
		const { results } = await client.searchForHits<IndexedPost>({
			requests: [
				{
					indexName: config.indexName ?? "",
					query: params.query,
					page: params.page ? params.page - 1 : 0,
					hitsPerPage: params.page_size ?? 20,
					facets: enabledFacets.map(({ attribute }) => attribute),
					facetFilters: params.facetFilters,
				},
			],
		});
		const result = results[0];
		const facets = enabledFacets.map(({ attribute, label }) => ({
			attribute,
			label,
			values: Object.entries(result?.facets?.[attribute] ?? {}).map(
				([value, count]) => ({ value, count }),
			),
		}));

		return {
			query: params.query ?? "",
			totalHits: result?.nbHits ?? 0,
			currentPage: (result?.page ?? 0) + 1,
			totalPages: result?.nbPages ?? 1,
			hits: (result?.hits ?? []).map((post) => transformPost(post)),
			facets,
		};
	};
};

const createTypesenseSearch = (config: SearchProviderConfig) => {
	const client = new Typesense.Client({
		nodes: [
			{
				host: config.host ?? "",
				port: config.port ?? 443,
				protocol: config.protocol ?? "https",
			},
		],
		apiKey: config.apiKey,
		connectionTimeoutSeconds: 2,
	});

	return async (params: SearchParams): Promise<SearchResult> => {
		const enabledFacets = getEnabledFacets(config);
		const filterBy = params.facetFilters
			?.map((group) =>
				group
					.map((filter) => {
						const separator = filter.indexOf(":");
						const attribute = filter.slice(0, separator);
						const value = filter.slice(separator + 1).replaceAll('"', '\\"');
						return `${attribute}:=["${value}"]`;
					})
					.join(" || "),
			)
			.join(" && ");
		const response = await client
			.collections<IndexedPost>(config.collectionName ?? "")
			.documents()
			.search({
				q: params.query ?? "",
				query_by: params.query_by ?? "post_title,post_excerpt,content",
				page: params.page ?? 1,
				per_page: params.page_size ?? 20,
				highlight_full_fields:
					params.highlight_full_fields ?? "post_title,post_excerpt",
				facet_by:
					enabledFacets.map(({ attribute }) => attribute).join(",") ||
					undefined,
				filter_by: filterBy || undefined,
			});
		const facetCounts = Object.fromEntries(
			(response.facet_counts ?? []).map(({ field_name, counts }) => [
				field_name,
				counts,
			]),
		);

		return {
			query: params.query ?? "",
			totalHits: response.found,
			currentPage: response.page ?? 1,
			totalPages: Math.max(
				1,
				Math.ceil(response.found / (params.page_size ?? 20)),
			),
			hits: (response.hits ?? []).map(({ document, highlight }) =>
				transformPost(
					document,
					highlight?.post_title?.value,
					highlight?.post_excerpt?.value,
				),
			),
			facets: enabledFacets.map(({ attribute, label }) => ({
				attribute,
				label,
				values: (facetCounts[attribute] ?? []).map(
					({ value, count }: { value: string; count: number }) => ({
						value,
						count,
					}),
				),
			})),
		};
	};
};

const createSearch = (config: SearchProviderConfig) =>
	config.type === "typesense"
		? createTypesenseSearch(config)
		: createAlgoliaSearch(config);

const selectedFacetFilters = (container: HTMLElement): string[][] => {
	const filters = new Map<string, string[]>();
	container
		.querySelectorAll<HTMLInputElement>("input:checked[data-facet-attribute]")
		.forEach((input) => {
			const attribute = input.dataset.facetAttribute ?? "";
			filters.set(attribute, [
				...(filters.get(attribute) ?? []),
				`${attribute}:${input.value}`,
			]);
		});
	return [...filters.values()];
};

const renderHit = (
	template: HTMLTemplateElement,
	hit: SearchHit,
): DocumentFragment => {
	const fragment = template.content.cloneNode(true) as DocumentFragment;
	const image = fragment.querySelector<HTMLImageElement>("[data-hit-image]");
	const link = fragment.querySelector<HTMLAnchorElement>("[data-hit-link]");
	const title = fragment.querySelector<HTMLElement>("[data-hit-title]");
	const summary = fragment.querySelector<HTMLElement>("[data-hit-summary]");
	const meta = fragment.querySelector<HTMLElement>("[data-hit-meta]");
	if (title) renderHighlightedText(title, hit.title);
	if (summary) renderHighlightedText(summary, hit.summary);
	if (meta) meta.textContent = hit.subtitle;
	if (link) {
		link.href = hit.url;
		link.setAttribute("aria-label", hit.ariaLabel);
	}
	if (image && hit.image) {
		image.src = hit.image;
		image.alt = hit.altText;
	} else {
		image?.remove();
	}
	return fragment;
};

const startSearchPage = (root: HTMLElement, config: SearchPageConfig): void => {
	const input = root.querySelector<HTMLInputElement>(
		"[data-search-index-query]",
	);
	const hits = root.querySelector<HTMLElement>("[data-search-index-hits]");
	const stats = root.querySelector<HTMLElement>("[data-search-index-stats]");
	const facets = root.querySelector<HTMLElement>("[data-search-index-facets]");
	const pagination = root.querySelector<HTMLElement>(
		"[data-search-index-pagination]",
	);
	const hitTemplate = root.querySelector<HTMLTemplateElement>(
		"template[data-search-index-hit]",
	);
	const noResults = root.querySelector<HTMLTemplateElement>(
		"template[data-search-index-no-results]",
	);
	const langElement = root.querySelector<HTMLScriptElement>(
		"[data-search-index-lang]",
	);
	if (
		!input ||
		!hits ||
		!stats ||
		!facets ||
		!pagination ||
		!hitTemplate ||
		!noResults ||
		!langElement
	) {
		return;
	}
	const lang = JSON.parse(langElement.textContent ?? "{}");
	const search = createSearch(config.provider);
	let params = { ...config.params };
	let debounceTimer: number | undefined;
	let requestId = 0;
	input.value = params.query ?? "";

	const execute = async (nextParams: SearchParams): Promise<void> => {
		const currentRequestId = ++requestId;
		params = nextParams;
		root.setAttribute("aria-busy", "true");
		try {
			const result = await search(params);
			if (currentRequestId !== requestId) return;
			hits.replaceChildren();
			stats.textContent = lang.stats.replace("%s", String(result.totalHits));
			if (result.hits.length === 0) {
				hits.append(noResults.content.cloneNode(true));
			} else {
				result.hits.forEach((hit) => {
					hits.append(renderHit(hitTemplate, hit));
				});
			}

			const noFacets = root.querySelector<HTMLElement>(
				"[data-search-index-no-facets]",
			);
			facets.replaceChildren();
			if (noFacets) facets.append(noFacets);
			result.facets.forEach((facet) => {
				const fieldset = document.createElement("fieldset");
				fieldset.className = "search-index-page__facet";
				const legend = document.createElement("legend");
				legend.textContent = facet.label;
				fieldset.append(legend);
				facet.values.forEach(({ value, count }) => {
					const label = document.createElement("label");
					const checkbox = document.createElement("input");
					checkbox.type = "checkbox";
					checkbox.value = value;
					checkbox.dataset.facetAttribute = facet.attribute;
					checkbox.checked =
						params.facetFilters
							?.flat()
							.includes(`${facet.attribute}:${value}`) ?? false;
					label.append(
						checkbox,
						document.createTextNode(` ${value} (${count})`),
					);
					fieldset.append(label);
				});
				facets.append(fieldset);
			});
			noFacets?.toggleAttribute("hidden", result.facets.length > 0);

			pagination.replaceChildren();
			for (let page = 1; page <= result.totalPages; page += 1) {
				if (result.totalPages > 7 && Math.abs(page - result.currentPage) > 3)
					continue;
				const button = document.createElement("button");
				button.type = "button";
				button.textContent = String(page);
				button.dataset.page = String(page);
				if (page === result.currentPage) {
					button.setAttribute("aria-current", "true");
				}
				pagination.append(button);
			}

			const url = new URL(location.href);
			url.searchParams.set("s", result.query);
			history.replaceState({}, "", url);
		} catch {
			if (currentRequestId !== requestId) return;
			hits.replaceChildren(noResults.content.cloneNode(true));
			stats.textContent = lang.noresults;
		} finally {
			if (currentRequestId === requestId) root.removeAttribute("aria-busy");
		}
	};

	input.addEventListener(
		config.provider.searchAsYouType === false ? "change" : "input",
		() => {
			window.clearTimeout(debounceTimer);
			debounceTimer = window.setTimeout(
				() => void execute({ ...params, query: input.value, page: 1 }),
				200,
			);
		},
	);
	facets.addEventListener(
		"change",
		() =>
			void execute({
				...params,
				facetFilters: selectedFacetFilters(facets),
				page: 1,
			}),
	);
	pagination.addEventListener("click", (event) => {
		const button = (event.target as HTMLElement).closest<HTMLButtonElement>(
			"button[data-page]",
		);
		if (button) void execute({ ...params, page: Number(button.dataset.page) });
	});
	root
		.querySelector("[data-search-index-filter-toggle]")
		?.addEventListener("click", () => facets.classList.toggle("is-open"));
	void execute(params);
};

document.addEventListener("DOMContentLoaded", () => {
	document
		.querySelectorAll<HTMLElement>("[data-search-index-page]")
		.forEach((root) => {
			startSearchPage(root, searchIndexSearchPageConfig);
		});
});
