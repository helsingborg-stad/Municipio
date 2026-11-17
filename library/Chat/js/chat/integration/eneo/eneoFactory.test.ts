import EneoFactory from "./eneoFactory";
import { createEneo } from "./createEneo";

jest.mock("./createEneo", () => ({
	createEneo: jest.fn(),
}));

describe("EneoFactory", () => {
	const createEneoMock = createEneo as jest.Mock;

	beforeEach(() => {
		createEneoMock.mockReset();
	});

	it("reads name-prefixed data attributes and passes them to createEneo", async () => {
		// Arrange
		const chatElement = document.createElement("div");
		chatElement.setAttribute("data-js-chat-eneo-name", "Ava");
		chatElement.setAttribute("data-js-chat-eneo-greeting-phrase", "Hello there");
		chatElement.setAttribute("data-js-chat-eneo-avatar", "https://example.com/avatar.png");
		const chat = { getElement: jest.fn().mockReturnValue(chatElement) };
		const uiManager = {};
		const integration = { init: jest.fn() };
		createEneoMock.mockReturnValue(integration);

		// Act
		const result = await new EneoFactory().create(chat, uiManager as any, "eneo");

		// Assert
		expect(result).toBe(integration);
		expect(createEneoMock).toHaveBeenCalledWith(
			chat,
			uiManager,
			"Ava",
			"Hello there",
			"https://example.com/avatar.png",
		);
	});

	it("passes null for attributes that are not present", async () => {
		// Arrange
		const chatElement = document.createElement("div");
		chatElement.setAttribute("data-js-chat-other-name", "Other");
		const chat = { getElement: jest.fn().mockReturnValue(chatElement) };

		// Act
		await new EneoFactory().create(chat, {} as any, "eneo");

		// Assert
		expect(createEneoMock).toHaveBeenCalledWith(chat, {}, null, null, null);
	});
});
