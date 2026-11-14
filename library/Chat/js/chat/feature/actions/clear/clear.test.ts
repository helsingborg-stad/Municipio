import Clear from "./clear";

describe("Clear", () => {
	const createTemplate = (markup = `<button data-js-chat-clear>New session</button>`) => {
		const template = document.createElement("template");
		template.innerHTML = markup;
		return template;
	};

	it("appends a clone of the template button to the actions container", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const template = createTemplate();
		const clear = new Clear(actionsContainer, template, { clearMessages: jest.fn() } as any);

		// Act
		clear.add();

		// Assert
		const buttons = actionsContainer.querySelectorAll("[data-js-chat-clear]");
		expect(buttons).toHaveLength(1);
		expect(buttons[0]).not.toBe(template.content.querySelector("[data-js-chat-clear]"));
	});

	it("only appends one button when add is called multiple times", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const chat = { clearMessages: jest.fn() };
		const clear = new Clear(actionsContainer, createTemplate(), chat as any);

		// Act
		clear.add();
		clear.add();
		(actionsContainer.querySelector("[data-js-chat-clear]") as HTMLElement).click();

		// Assert
		expect(actionsContainer.querySelectorAll("[data-js-chat-clear]")).toHaveLength(1);
		expect(chat.clearMessages).toHaveBeenCalledTimes(1);
	});

	it("notifies listeners and clears chat messages on click", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const chat = { clearMessages: jest.fn() };
		const clear = new Clear(actionsContainer, createTemplate(), chat as any);
		const listener = jest.fn();
		clear.addListener(listener);
		clear.add();

		// Act
		(actionsContainer.querySelector("[data-js-chat-clear]") as HTMLElement).click();

		// Assert
		expect(listener).toHaveBeenCalledTimes(1);
		expect(listener.mock.calls[0][0]).toBeInstanceOf(Event);
		expect(chat.clearMessages).toHaveBeenCalledTimes(1);
	});

	it("removes the button and stops reacting to clicks after remove", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const chat = { clearMessages: jest.fn() };
		const clear = new Clear(actionsContainer, createTemplate(), chat as any);
		clear.add();
		const button = actionsContainer.querySelector("[data-js-chat-clear]") as HTMLElement;

		// Act
		clear.remove();
		button.click();

		// Assert
		expect(actionsContainer.querySelector("[data-js-chat-clear]")).toBeNull();
		expect(chat.clearMessages).not.toHaveBeenCalled();
	});

	it("can be re-added after being removed", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const clear = new Clear(actionsContainer, createTemplate(), { clearMessages: jest.fn() } as any);
		clear.add();
		clear.remove();

		// Act
		clear.add();

		// Assert
		expect(actionsContainer.querySelectorAll("[data-js-chat-clear]")).toHaveLength(1);
	});

	it("does nothing when remove is called before add", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const clear = new Clear(actionsContainer, createTemplate(), { clearMessages: jest.fn() } as any);

		// Act & Assert
		expect(() => clear.remove()).not.toThrow();
	});

	it("throws when the template has no clear button", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const clear = new Clear(actionsContainer, createTemplate(`<div></div>`), { clearMessages: jest.fn() } as any);

		// Act & Assert
		expect(() => clear.add()).toThrow("New session button template not found");
	});
});
