import CloseConfirmationController from "./closeConfirmationController";

describe("CloseConfirmationController", () => {
	const createTemplateWithConfirmation = (): HTMLTemplateElement => {
		const template = document.createElement("template");
		template.innerHTML = `
			<div data-js-chat-close-confirmation>
				<button data-js-chat-close-confirm>Confirm</button>
				<button data-js-chat-close-cancel>Cancel</button>
			</div>
		`;

		return template;
	};

	it("calls onConfirm when confirm button is clicked", () => {
		// Arrange
		const template = createTemplateWithConfirmation();
		const chatElement = document.createElement("div");
		const chat = { getElement: jest.fn().mockReturnValue(chatElement) };
		const controller = new CloseConfirmationController(template, chat);
		const onConfirm = jest.fn();
		const onCancel = jest.fn();

		// Act
		controller.initialize();
		controller.requestConfirmation(onConfirm, onCancel);
		const confirmButton = chatElement.querySelector("[data-js-chat-close-confirm]") as HTMLElement;
		confirmButton.click();

		// Assert
		expect(onConfirm).toHaveBeenCalledTimes(1);
		expect(onCancel).toHaveBeenCalledTimes(0);
	});

	it("calls onCancel when cancel button is clicked", () => {
		// Arrange
		const template = createTemplateWithConfirmation();
		const chatElement = document.createElement("div");
		const chat = { getElement: jest.fn().mockReturnValue(chatElement) };
		const controller = new CloseConfirmationController(template, chat);
		const onConfirm = jest.fn();
		const onCancel = jest.fn();

		// Act
		controller.initialize();
		controller.requestConfirmation(onConfirm, onCancel);
		const cancelButton = chatElement.querySelector("[data-js-chat-close-cancel]") as HTMLElement;
		cancelButton.click();

		// Assert
		expect(onConfirm).toHaveBeenCalledTimes(0);
		expect(onCancel).toHaveBeenCalledTimes(1);
	});

	it("falls back to onConfirm when confirmation markup is missing", () => {
		// Arrange
		const template = document.createElement("template");
		template.innerHTML = `<div></div>`;
		const chatElement = document.createElement("div");
		const chat = { getElement: jest.fn().mockReturnValue(chatElement) };
		const controller = new CloseConfirmationController(template, chat);
		const onConfirm = jest.fn();
		const onCancel = jest.fn();

		// Act
		controller.requestConfirmation(onConfirm, onCancel);

		// Assert
		expect(onConfirm).toHaveBeenCalledTimes(1);
		expect(onCancel).toHaveBeenCalledTimes(0);
	});

	it("calls onCancel when Escape key is pressed", () => {
		// Arrange
		const template = createTemplateWithConfirmation();
		const chatElement = document.createElement("div");
		const chat = { getElement: jest.fn().mockReturnValue(chatElement) };
		const controller = new CloseConfirmationController(template, chat);
		const onConfirm = jest.fn();
		const onCancel = jest.fn();

		// Act
		controller.initialize();
		controller.requestConfirmation(onConfirm, onCancel);
		document.dispatchEvent(new KeyboardEvent("keydown", { key: "Escape" }));

		// Assert
		expect(onConfirm).toHaveBeenCalledTimes(0);
		expect(onCancel).toHaveBeenCalledTimes(1);
	});
});
