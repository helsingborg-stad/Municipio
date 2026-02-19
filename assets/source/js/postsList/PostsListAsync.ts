import { postsListRender } from "../restApi/endpoints/postsListRender";
import { updateUrlParams } from "./updateUrlParams";

export const postsListAsync = (
	container: HTMLElement,
	postsContainer: HTMLElement,
	form: HTMLFormElement,
) => {
	const fetchHTML = (
		(attributes: Record<string, unknown>) =>
		(params: Record<string, unknown>) => {
			return postsListRender.call({
				attributes: JSON.stringify(attributes) as unknown as Record<
					string,
					unknown
				>,
				...params,
			});
		}
	)(JSON.parse(container.dataset?.postsListAttributes || "{}"));

	const renderHTML = (html: string) => {
		const temp = document.createElement("div");
		temp.innerHTML = html;
		const classNames = [...postsContainer.classList].reduce(
			(acc, className) => {
				return `${acc}.${className}`;
			},
			"",
		);
		const newContent = temp.querySelector(classNames) as HTMLElement;
		if (newContent) postsContainer.innerHTML = newContent.innerHTML;
	};

	const setIsLoading = (isLoading: boolean) => {
		const toggle = (e: Element, className: string) =>
			isLoading ? e.classList.add(className) : e.classList.remove(className);

		container.setAttribute("aria-busy", isLoading ? "true" : "false");
		container.querySelectorAll("[data-js-posts-list-item]").forEach((e) => {
			toggle(e, "u-preloader--inner");
		});

		toggle(container, "is-loading");
	};

	const render = (
		(requestCount: number = 0) =>
		async (
			params: Record<string, unknown>,
			clearUrlParams: boolean = false,
			afterRender: () => void = () => {},
		) => {
			updateUrlParams(params, clearUrlParams);
			const currentRequest = ++requestCount;

			try {
				setIsLoading(true);
				const html = await fetchHTML(params);
				const staleResponse = currentRequest !== requestCount;
				if (staleResponse) return;
				renderHTML(html);
				afterRender();
			} catch (err) {
				console.error(err);
			} finally {
				setIsLoading(false);
			}
		}
	)();

	const setupAccessibility = () => {
		container.setAttribute("aria-live", "polite");
		container.setAttribute("aria-atomic", "true");
	};

	const setupPagination = () => {
		container.addEventListener("click", (e) => {
			const target = e.target as HTMLElement;
			const link = target.closest('a[href*="page"]');

			if (!link) return;

			e.preventDefault();
			const url = new URL(
				link.getAttribute("href") || "",
				window.location.origin,
			);
			const params = Object.fromEntries(url.searchParams.entries());
			render(params, false, () =>
				postsContainer.scrollIntoView({ behavior: "smooth", block: "start" }),
			);
		});
	};

	const setupFilterForm = () => {
		form.addEventListener("submit", (e) => {
			e.preventDefault();
			const formData = new FormData(form);
			const params: Record<string, unknown> = {};

			// Handle multiselect inputs by collecting all values for eachhttps://github.com/helsingborg-stad/ key
			const uniqueKeys = [...new Set(formData.keys())];
			uniqueKeys.forEach((key) => {
				const values = formData.getAll(key);
				// For multiple values, use array notation in parameter name for PHP
				if (values.length > 1) {
					const arrayKey = key.endsWith("[]") ? key : `${key}[]`;
					params[arrayKey] = values;
				} else {
					params[key] = values[0];
				}
			});

			const currentUrl = new URL(window.location.href);
			currentUrl.searchParams.forEach((value, key) => {
				if (key.endsWith("_page") && !isNaN(Number(value))) {
					params[key] = "";
				}
			});
			render(params);
		});
	};

	const setupResetButton = () => {
		container.addEventListener("click", (e: PointerEvent) => {
			const target = e.target as HTMLElement;
			const resetLink = target.closest('a[href]:not([href*="page"])');

			if (!resetLink) return;

			// Check if this is inside the filter form area (reset button)
			const form = container.querySelector("form");
			if (!form || !form.contains(resetLink)) return;

			// Skip if it's a pagination link
			if (resetLink.getAttribute("href")?.includes("page")) return;

			e.preventDefault();

			form //Fix for multi select
				.querySelectorAll<HTMLElement>("[data-js-select-component]")
				.forEach((e) => {
					const selectEl = e.querySelector<HTMLSelectElement>(
						"[data-js-select-element]",
					);
					if (!selectEl || !selectEl.hasAttribute("multiple")) return;

					Array.from(selectEl.options).forEach((option) => {
						option.selected = false;
						option.removeAttribute("selected");
					});

					selectEl.dispatchEvent(new Event("change"));
				});

			form.reset();
			render({}, true);
		});
	};

	setupAccessibility();
	setupPagination();
	setupFilterForm();
	setupResetButton();
};
