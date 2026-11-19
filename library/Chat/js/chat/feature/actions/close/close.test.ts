import Close from "./close";

describe("Close", () => {
	it("requests confirmation before closing and closes on confirm callback", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const template = document.createElement("template");
		template.innerHTML = `
			<button data-js-chat-close popovertarget="chat-global-root" popovertargetaction="hide">Close</button>
		`;

		const popoverElement = document.createElement("div") as HTMLElement & { hidePopover?: () => void };
		popoverElement.id = "chat-global-root";
		popoverElement.hidePopover = jest.fn();
		document.body.appendChild(popoverElement);

		let confirmCallback: (() => void) | null = null;
		const confirmationController = {
			initialize: jest.fn(),
			requestConfirmation: jest.fn((onConfirm: () => void) => {
				confirmCallback = onConfirm;
			}),
			remove: jest.fn(),
		};

		const closeAction = new Close(actionsContainer, template, confirmationController as any);
		const listener = jest.fn();
		closeAction.addListener(listener);
		closeAction.add();

		const closeButton = actionsContainer.querySelector("[data-js-chat-close]") as HTMLElement;

		// Act
		closeButton.click();
		confirmCallback?.();

		// Assert
		expect(confirmationController.initialize).toHaveBeenCalledTimes(1);
		expect(confirmationController.requestConfirmation).toHaveBeenCalledTimes(1);
		expect(listener).toHaveBeenCalledTimes(1);
		expect(popoverElement.hidePopover).toHaveBeenCalledTimes(1);
	});

	it("cleans up confirmation controller on remove", () => {
		// Arrange
		const actionsContainer = document.createElement("div");
		const template = document.createElement("template");
		template.innerHTML = `<button data-js-chat-close>Close</button>`;
		const confirmationController = {
			initialize: jest.fn(),
			requestConfirmation: jest.fn(),
			remove: jest.fn(),
		};

		const closeAction = new Close(actionsContainer, template, confirmationController as any);
		closeAction.add();

		// Act
		closeAction.remove();

		// Assert
		expect(confirmationController.remove).toHaveBeenCalledTimes(1);
	});
});
