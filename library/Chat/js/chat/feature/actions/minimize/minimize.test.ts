import Minimize from "./minimize";

describe("Minimize", () => {
	const createTemplate = (markup = `<button data-js-chat-minimize>Minimize</button>`) => {
		const template = document.createElement("template");
		template.innerHTML = markup;
		return template;
	};

	const createPopover = (id: string) => {
		const popoverElement = document.createElement("div") as HTMLElement & { hidePopover?: () => void };
		popoverElement.id = id;
		popoverElement.hidePopover = jest.fn();
		document.body.appendChild(popoverElement);
		return popoverElement;
	};

	afterEach(() => {
		document.body.innerHTML = "";
	});

	it("appends a single minimize button even if add is called twice", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const minimize = new Minimize(actionsContainer, createTemplate());

		// Act
		minimize.add();
		minimize.add();

		// Assert
		expect(actionsContainer.querySelectorAll("[data-js-chat-minimize]")).toHaveLength(1);
	});

	it("hides the popover referenced by the popovertarget attribute and notifies listeners", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const minimize = new Minimize(
			actionsContainer,
			createTemplate(`<button data-js-chat-minimize popovertarget="custom-root">Minimize</button>`),
		);
		const customPopover = createPopover("custom-root");
		const defaultPopover = createPopover("chat-global-root");
		const listener = jest.fn();
		minimize.addListener(listener);
		minimize.add();

		// Act
		(actionsContainer.querySelector("[data-js-chat-minimize]") as HTMLElement).click();

		// Assert
		expect(listener).toHaveBeenCalledTimes(1);
		expect(customPopover.hidePopover).toHaveBeenCalledTimes(1);
		expect(defaultPopover.hidePopover).not.toHaveBeenCalled();
	});

	it("falls back to the chat-global-root popover when no popovertarget is set", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const minimize = new Minimize(actionsContainer, createTemplate());
		const defaultPopover = createPopover("chat-global-root");
		minimize.add();

		// Act
		(actionsContainer.querySelector("[data-js-chat-minimize]") as HTMLElement).click();

		// Assert
		expect(defaultPopover.hidePopover).toHaveBeenCalledTimes(1);
	});

	it("still notifies listeners when the popover target does not exist", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const minimize = new Minimize(actionsContainer, createTemplate());
		const listener = jest.fn();
		minimize.addListener(listener);
		minimize.add();

		// Act
		const click = () => (actionsContainer.querySelector("[data-js-chat-minimize]") as HTMLElement).click();

		// Assert
		expect(click).not.toThrow();
		expect(listener).toHaveBeenCalledTimes(1);
	});

	it("removes the button and detaches the click handler", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const minimize = new Minimize(actionsContainer, createTemplate());
		const defaultPopover = createPopover("chat-global-root");
		const listener = jest.fn();
		minimize.addListener(listener);
		minimize.add();
		const button = actionsContainer.querySelector("[data-js-chat-minimize]") as HTMLElement;

		// Act
		minimize.remove();
		button.click();

		// Assert
		expect(actionsContainer.querySelector("[data-js-chat-minimize]")).toBeNull();
		expect(listener).not.toHaveBeenCalled();
		expect(defaultPopover.hidePopover).not.toHaveBeenCalled();
	});

	it("throws when the template has no minimize button", () => {
		// Arrange
		const minimize = new Minimize(document.createElement("div"), createTemplate(`<span></span>`));

		// Act & Assert
		expect(() => minimize.add()).toThrow("Minimize button template not found");
	});
});
