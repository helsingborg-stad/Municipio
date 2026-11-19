import ActionFactory from "./actionFactory";

describe("ActionFactory", () => {
	const createTemplate = () => {
		const template = document.createElement("template");
		template.innerHTML = `
			<button data-js-chat-minimize>Minimize</button>
			<button data-js-chat-clear>Clear</button>
			<button data-js-chat-close popovertarget="chat-global-root" popovertargetaction="hide">Close</button>
		`;
		return template;
	};

	afterEach(() => {
		document.body.innerHTML = "";
	});

	it("adds a minimize button to the actions container", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const factory = new ActionFactory(actionsContainer, createTemplate(), {} as any);

		// Act
		factory.addMinimize();

		// Assert
		expect(actionsContainer.querySelectorAll("[data-js-chat-minimize]")).toHaveLength(1);
	});

	it("adds a clear button wired to the chat instance", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const chat = { clearMessages: jest.fn() };
		const factory = new ActionFactory(actionsContainer, createTemplate(), chat as any);

		// Act
		factory.addClear();
		(actionsContainer.querySelector("[data-js-chat-clear]") as HTMLElement).click();

		// Assert
		expect(chat.clearMessages).toHaveBeenCalledTimes(1);
	});

	it("adds a close button that closes the popover when no confirmation template exists", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const chatElement = document.createElement("div");
		const chat = { getElement: jest.fn().mockReturnValue(chatElement) };
		const popoverElement = document.createElement("div") as HTMLElement & { hidePopover?: () => void };
		popoverElement.id = "chat-global-root";
		popoverElement.hidePopover = jest.fn();
		document.body.appendChild(popoverElement);
		const factory = new ActionFactory(actionsContainer, createTemplate(), chat as any);

		// Act
		const close = factory.addClose();
		const listener = jest.fn();
		close.addListener(listener);
		const closeButton = actionsContainer.querySelector("[data-js-chat-close]") as HTMLElement;
		closeButton.click();

		// Assert
		expect(closeButton.hasAttribute("popovertargetaction")).toBe(false);
		expect(listener).toHaveBeenCalledTimes(1);
		expect(popoverElement.hidePopover).toHaveBeenCalledTimes(1);
	});
});
