import { createChatUtils } from "./ChatUtils";

describe("ChatUtils.renderMarkdown", () => {
	const utils = createChatUtils();

	it("escapes raw HTML open-tag characters", () => {
		const html = utils.renderMarkdown("<script>alert(1)</script>");
		expect(html).not.toContain("<script>");
		expect(html).toContain("&lt;script");
	});

	it("escapes ampersands before applying markdown", () => {
		const html = utils.renderMarkdown("Tom & Jerry");
		expect(html).toContain("Tom &amp; Jerry");
	});

	it("renders bold, italic, and line breaks", () => {
		const html = utils.renderMarkdown("**bold**\n*italic*");
		expect(html).toBe("<strong>bold</strong><br><em>italic</em>");
	});

	it("renders http(s) links with target+rel", () => {
		const html = utils.renderMarkdown("See [docs](https://example.com/a)");
		expect(html).toBe(
			'See <a href="https://example.com/a" target="_blank" rel="noopener">docs</a>',
		);
	});

	it("neutralizes quotes in crafted URLs to prevent attribute injection", () => {
		const html = utils.renderMarkdown(
			'[x](https://example.com/"onmouseover=alert(1)//)',
		);
		// Either the URL is dropped (returning the label) or the quote has
		// been encoded / escaped — the result must never allow attribute break-out.
		expect(html).not.toMatch(/href="[^"]*"[^>]*onmouseover/);
	});

	it("does not emit anchor tags for non-http(s) protocols", () => {
		const html = utils.renderMarkdown("[click](javascript:alert(1))");
		expect(html).not.toMatch(/<a\s/);
	});

	it("preserves escaped ampersands in urls when emitting href", () => {
		const html = utils.renderMarkdown("[x](https://example.com/?a=1&b=2)");
		expect(html).toContain('href="https://example.com/?a=1&amp;b=2"');
	});
});

describe("ChatUtils DOM helpers", () => {
	const utils = createChatUtils();

	beforeAll(() => {
		Element.prototype.scrollIntoView = jest.fn();
	});

	beforeEach(() => {
		document.body.innerHTML = "";
	});

	it("safeGetElement throws when element missing", () => {
		expect(() => utils.safeGetElement("nope")).toThrow();
	});

	it("safeQueryElement throws when selector does not match", () => {
		expect(() => utils.safeQueryElement(".nope")).toThrow();
	});

	it("unsafeGetElement returns null when element missing", () => {
		expect(utils.unsafeGetElement("nope")).toBeNull();
	});

	it("appendMessageElement clones the correct template per role", () => {
		document.body.innerHTML = `
			<div id="root"></div>
			<template id="user"><div class="c-comment__bubble--inner" data-role="user"></div></template>
			<template id="assistant"><div class="c-comment__bubble--inner" data-role="assistant"></div></template>
		`;
		const parent = utils.safeGetElement<HTMLElement>("root");
		const userTpl = utils.safeGetElement<HTMLTemplateElement>("user");
		const asstTpl = utils.safeGetElement<HTMLTemplateElement>("assistant");

		const userMsg = utils.appendMessageElement("user", parent, userTpl, asstTpl);
		const asstMsg = utils.appendMessageElement(
			"assistant",
			parent,
			userTpl,
			asstTpl,
		);

		expect(userMsg.getAttribute("data-role")).toBe("user");
		expect(asstMsg.getAttribute("data-role")).toBe("assistant");
		expect(parent.children.length).toBe(2);
	});
});
