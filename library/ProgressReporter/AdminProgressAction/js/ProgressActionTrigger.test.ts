/**
 * @jest-environment jsdom
 */

import EventSourceStreamSource from "./EventSourceStreamSource";
import FetchStreamSource from "./FetchStreamSource";
import ProgressActionTrigger from "./ProgressActionTrigger";
import ProgressStreamController from "./ProgressStreamController";

jest.mock("./EventSourceStreamSource");
jest.mock("./FetchStreamSource");
jest.mock("./ProgressStreamController");

describe("ProgressActionTrigger", () => {
	beforeEach(() => {
		jest.clearAllMocks();
	});

	it("uses EventSource for triggers without a method", () => {
		const button = createButton();

		new ProgressActionTrigger(button);

		expect(EventSourceStreamSource).toHaveBeenCalledWith(
			button.dataset.jsProgressUrl,
		);
		expect(FetchStreamSource).not.toHaveBeenCalled();
	});

	it("uses fetch streaming for POST triggers", () => {
		const button = createButton();
		button.dataset.jsProgressMethod = "post";
		button.dataset.jsProgressNonce = "nonce-value";

		new ProgressActionTrigger(button);

		expect(FetchStreamSource).toHaveBeenCalledWith(
			button.dataset.jsProgressUrl,
			"nonce-value",
		);
	});

	it("prevents navigation and starts progress on click", () => {
		const button = createButton();
		new ProgressActionTrigger(button);
		const controller = jest.mocked(ProgressStreamController).mock.instances[0];
		const event = new MouseEvent("click", { bubbles: true, cancelable: true });
		const preventDefault = jest.spyOn(event, "preventDefault");

		button.dispatchEvent(event);

		expect(preventDefault).toHaveBeenCalled();
		expect(controller.start).toHaveBeenCalled();
	});

	it("does not start a disabled anchor trigger", () => {
		const anchor = document.createElement("a");
		anchor.dataset.jsProgressUrl =
			"https://example.test/admin-ajax.php?action=build";
		anchor.setAttribute("disabled", "disabled");
		new ProgressActionTrigger(anchor);
		const controller = jest.mocked(ProgressStreamController).mock.instances[0];

		anchor.dispatchEvent(
			new MouseEvent("click", { bubbles: true, cancelable: true }),
		);

		expect(controller.start).not.toHaveBeenCalled();
	});
});

function createButton(): HTMLButtonElement {
	const button = document.createElement("button");
	button.dataset.jsProgressUrl =
		"https://example.test/admin-ajax.php?action=build";
	return button;
}
