import { renderHighlightedText } from "./index";

describe("renderHighlightedText", () => {
	it("renders decoded text and provider mark elements", () => {
		const element = document.createElement("span");

		renderHighlightedText(element, "E-tj&auml;nst f&ouml;r <mark>e-</mark>post");

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