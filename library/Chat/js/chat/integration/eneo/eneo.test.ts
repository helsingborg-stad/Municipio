import Eneo from "./eneo";

describe("Eneo", () => {
	it("registers one minimize and one clear action during init", async () => {
		// Arrange
		const session = {
			ask: jest.fn(),
			clearSessionForAssistant: jest.fn(),
		};
		const sessionFactory = {
			create: jest.fn().mockReturnValue(session),
		};
		const chatElement = document.createElement("div");
		const chat = {
			getElement: jest.fn().mockReturnValue(chatElement),
		};
		const markdownParser = {
			render: jest.fn((content: string) => content),
			utils: {
				escapeHtml: jest.fn((content: string) => content),
			},
		};
		const feedbackFactory = {
			create: jest.fn(),
		};
		const feedbackApi = {
			postStat: jest.fn(),
		};
		const clearAction = {
			addListener: jest.fn(),
		};
		const uiManager = {
			clearActions: jest.fn(),
			addMinimizeAction: jest.fn(),
			addClearAction: jest.fn().mockReturnValue(clearAction),
		};

		const eneo = new Eneo(
			sessionFactory as any,
			chat,
			markdownParser as any,
			feedbackFactory as any,
			feedbackApi as any,
			"Ava",
			true,
			uiManager as any,
		);
		const createNewEneoSessionSpy = jest.spyOn(eneo, "createNewEneoSession");

		// Act
		await eneo.init();
		const onClear = clearAction.addListener.mock.calls[0][0] as () => void;
		onClear();

		// Assert
		expect(uiManager.clearActions).toHaveBeenCalledTimes(1);
		expect(uiManager.addMinimizeAction).toHaveBeenCalledTimes(1);
		expect(uiManager.addClearAction).toHaveBeenCalledTimes(1);
		expect(clearAction.addListener).toHaveBeenCalledTimes(1);
		expect(createNewEneoSessionSpy).toHaveBeenCalledTimes(1);
	});
});