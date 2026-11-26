import { updateTaxonomyFilters } from "./updateTaxonomyFilters";

const createForm = (filtersHtml: string | null): HTMLFormElement => {
	document.body.innerHTML = `
		<form>
			<input type="search" name="archive_search" value="keep me">
			${filtersHtml === null ? "" : `<div data-js-posts-list-taxonomy-filters>${filtersHtml}</div>`}
			<button type="submit">Filter</button>
		</form>`;
	return document.querySelector("form") as HTMLFormElement;
};

const createRendered = (filtersHtml: string | null): HTMLElement => {
	const temp = document.createElement("div");
	temp.innerHTML =
		filtersHtml === null
			? "<form></form>"
			: `<form><div data-js-posts-list-taxonomy-filters>${filtersHtml}</div></form>`;
	return temp;
};

describe("updateTaxonomyFilters", () => {
	it("replaces the taxonomy filters with the rendered ones", () => {
		const form = createForm('<select name="archive_category[]"><option value="a">A (10)</option></select>');

		const updated = updateTaxonomyFilters(
			form,
			createRendered('<select name="archive_category[]"><option value="a" selected>A (4)</option><option value="b">B (2)</option></select>'),
		);

		expect(updated).toBe(true);
		const options = form.querySelectorAll("option");
		expect(Array.from(options).map((o) => o.textContent)).toEqual(["A (4)", "B (2)"]);
		expect(options[0].selected).toBe(true);
	});

	it("leaves the rest of the form untouched", () => {
		const form = createForm("<span>old</span>");
		const searchField = form.querySelector('input[name="archive_search"]');

		updateTaxonomyFilters(form, createRendered("<span>new</span>"));

		expect(form.querySelector('input[name="archive_search"]')).toBe(searchField);
		expect(form.querySelector("button")).not.toBeNull();
	});

	it("empties the filters when the rendered list has none", () => {
		const form = createForm("<span>old</span>");

		updateTaxonomyFilters(form, createRendered(""));

		expect(form.querySelector("[data-js-posts-list-taxonomy-filters]")?.childElementCount).toBe(0);
	});

	it("does nothing when either side lacks taxonomy filters", () => {
		const form = createForm("<span>old</span>");

		expect(updateTaxonomyFilters(form, createRendered(null))).toBe(false);
		expect(updateTaxonomyFilters(null, createRendered("<span>new</span>"))).toBe(false);
		expect(form.querySelector("span")?.textContent).toBe("old");
	});
});
