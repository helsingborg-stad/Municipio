import Null from "./null";

describe("Null", () => {
	afterEach(() => {
		document.body.innerHTML = "";
	});

	const createUiManager = () => ({ clearActions: jest.fn() });

	it("clears actions and removes the popover and its bubble trigger", async () => {
		// Arrange
		document.body.innerHTML = `
			<button data-js-municipio-chat-bubble-trigger popovertarget="chat-global-root"></button>
			<button data-js-municipio-chat-bubble-trigger popovertarget="other-root" id="other-trigger"></button>
			<div id="chat-global-root" popover>
				<div data-js-municipio-chat-wrapper>
					<div id="chat"></div>
				</div>
			</div>
		`;
		const chatElement = document.getElementById("chat");
		const uiManager = createUiManager();
		const nullIntegration = new Null({ getElement: () => chatElement } as any, uiManager as any);

		// Act
		const result = await nullIntegration.init();

		// Assert
		expect(result).toBe(nullIntegration);
		expect(uiManager.clearActions).toHaveBeenCalledTimes(1);
		expect(document.getElementById("chat-global-root")).toBeNull();
		expect(document.querySelector('[popovertarget="chat-global-root"]')).toBeNull();
		expect(document.getElementById("other-trigger")).not.toBeNull();
	});

	it("uses the closest popover element id to find the bubble trigger", async () => {
		// Arrange
		document.body.innerHTML = `
			<button data-js-municipio-chat-bubble-trigger popovertarget="custom-popover"></button>
			<button data-js-municipio-chat-bubble-trigger popovertarget="chat-global-root" id="global-trigger"></button>
			<div id="custom-popover" popover>
				<div id="chat"></div>
			</div>
		`;
		const chatElement = document.getElementById("chat");
		const nullIntegration = new Null({ getElement: () => chatElement } as any, createUiManager() as any);

		// Act
		await nullIntegration.init();

		// Assert
		expect(document.getElementById("custom-popover")).toBeNull();
		expect(document.querySelector('[popovertarget="custom-popover"]')).toBeNull();
		expect(document.getElementById("global-trigger")).not.toBeNull();
	});

	it("removes the chat wrapper when the chat is not inside a popover", async () => {
		// Arrange
		document.body.innerHTML = `
			<section id="page">
				<div data-js-municipio-chat-wrapper id="wrapper">
					<div id="chat"></div>
				</div>
			</section>
		`;
		const chatElement = document.getElementById("chat");
		const nullIntegration = new Null({ getElement: () => chatElement } as any, createUiManager() as any);

		// Act
		await nullIntegration.init();

		// Assert
		expect(document.getElementById("wrapper")).toBeNull();
		expect(document.getElementById("chat")).toBeNull();
		expect(document.getElementById("page")).not.toBeNull();
	});

	it("removes only the chat element when there is no wrapper or popover", async () => {
		// Arrange
		document.body.innerHTML = `<div id="container"><div id="chat"></div></div>`;
		const chatElement = document.getElementById("chat");
		const nullIntegration = new Null({ getElement: () => chatElement } as any, createUiManager() as any);

		// Act
		await nullIntegration.init();

		// Assert
		expect(document.getElementById("chat")).toBeNull();
		expect(document.getElementById("container")).not.toBeNull();
	});

	it("resolves without throwing when the chat has no element", async () => {
		// Arrange
		const uiManager = createUiManager();
		const nullIntegration = new Null({ getElement: () => null } as any, uiManager as any);

		// Act
		const result = await nullIntegration.init();

		// Assert
		expect(result).toBe(nullIntegration);
		expect(uiManager.clearActions).toHaveBeenCalledTimes(1);
	});
});
