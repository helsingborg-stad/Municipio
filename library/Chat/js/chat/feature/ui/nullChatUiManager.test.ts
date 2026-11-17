import NullChatUiManager from "./nullChatUiManager";

describe("NullChatUiManager", () => {
	it("accepts every UI call without throwing", () => {
		const uiManager = new NullChatUiManager();

		expect(() => {
			uiManager.clearActions();
			uiManager.setTitle("Title");
			uiManager.setAvatar("https://example.com/avatar.png");
			uiManager.setAvatar(null);
		}).not.toThrow();
	});

	it("returns actions whose methods can be called without effect", () => {
		const uiManager = new NullChatUiManager();
		const listener = jest.fn();

		[uiManager.addMinimizeAction(), uiManager.addClearAction(), uiManager.addCloseAction()].forEach((action) => {
			expect(() => {
				action.add();
				action.addListener(listener);
				action.remove();
			}).not.toThrow();
		});

		expect(listener).not.toHaveBeenCalled();
	});
});
