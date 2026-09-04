import { renderHighlightedText, renderHit, type SearchHit } from "./index";

const createHitTemplate = (): HTMLTemplateElement => {
	const template = document.createElement("template");
	template.innerHTML = `
		<article>
			<a class="search-index-page__hit" data-hit-link>
				<figure data-hit-media><img data-hit-image alt=""></figure>
				<span data-hit-title></span>
				<span data-hit-meta></span>
				<p data-hit-summary></p>
			</a>
		</article>
	`;
	return template;
};

const hit: SearchHit = {
	title: "Result title",
	summary: "Result summary",
	subtitle: "Result site",
	ariaLabel: "Open result",
	altText: "Result thumbnail",
	url: "https://example.com/result",
};

describe("renderHighlightedText", () => {
	it("renders decoded text and provider mark elements", () => {
		const element = document.createElement("span");

		renderHighlightedText(
			element,
			"E-tj&auml;nst f&ouml;r <mark>e-</mark>post",
		);

		expect(element.textContent).toBe("E-tjänst för e-post");
		expect(element.innerHTML).toBe("E-tjänst för <mark>e-</mark>post");
	});

	it("treats unsupported markup and mark attributes as inert content", () => {
		const element = document.createElement("span");

		renderHighlightedText(
			element,
			'<img src=x onerror=alert(1)>Safe <mark class="unsafe">match</mark>',
		);

		expect(element.querySelector("img")).toBeNull();
		expect(element.innerHTML).toBe("Safe <mark>match</mark>");
	});
});

describe("renderHit", () => {
	it("renders the image and image layout when the hit has an image", () => {
		const template = createHitTemplate();

		const fragment = renderHit(template, {
			...hit,
			image: "https://example.com/thumbnail.jpg",
		});

		const image = fragment.querySelector<HTMLImageElement>("[data-hit-image]");
		expect(image?.src).toBe("https://example.com/thumbnail.jpg");
		expect(image?.alt).toBe("Result thumbnail");
		expect(
			fragment
				.querySelector("[data-hit-link]")
				?.classList.contains("search-index-page__hit--with-image"),
		).toBe(true);
	});

	it("removes the media region when the hit has no image", () => {
		const template = createHitTemplate();

		const fragment = renderHit(template, hit);

		expect(fragment.querySelector("[data-hit-media]")).toBeNull();
		expect(
			fragment
				.querySelector("[data-hit-link]")
				?.classList.contains("search-index-page__hit--with-image"),
		).toBe(false);
	});
});
