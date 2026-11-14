import ChatUiManager from "./chatUiManager";

describe("ChatUiManager", () => {
	const createActionFactory = () => ({
		addMinimize: jest.fn().mockReturnValue("minimize"),
		addClear: jest.fn().mockReturnValue("clear"),
		addClose: jest.fn().mockReturnValue("close"),
	});

	const createAvatarTemplate = (): HTMLTemplateElement => {
		const template = document.createElement("template");
		template.innerHTML = `
			<div data-js-chat-avatar="0"><span>icon</span></div>
			<div data-js-chat-avatar="1"><img src="#" alt="avatar" /></div>
		`;

		return template;
	};

	const createUiManager = (chatTemplate: HTMLTemplateElement = createAvatarTemplate()) => {
		const actionFactory = createActionFactory();
		const actionsContainer = document.createElement("div");
		const headingArea = document.createElement("div");
		const title = document.createElement("h2");
		headingArea.appendChild(title);
		const uiManager = new ChatUiManager(
			actionFactory as any,
			actionsContainer,
			title,
			chatTemplate,
			headingArea,
		);

		return { uiManager, actionFactory, actionsContainer, headingArea, title, chatTemplate };
	};

	it("prepends the image avatar with the given url", () => {
		// Arrange
		const { uiManager, headingArea } = createUiManager();

		// Act
		uiManager.setAvatar("https://example.com/avatar.png");

		// Assert
		const avatars = headingArea.querySelectorAll("[data-js-chat-avatar]");
		expect(avatars).toHaveLength(1);
		expect(headingArea.firstElementChild).toBe(avatars[0]);
		expect(avatars[0].getAttribute("data-js-chat-avatar")).toBe("1");
		expect((avatars[0].querySelector("img") as HTMLImageElement).src).toBe("https://example.com/avatar.png");
	});

	it("prepends the icon avatar when url is empty or null", () => {
		// Arrange
		const { uiManager, headingArea } = createUiManager();

		// Act
		uiManager.setAvatar("https://example.com/avatar.png");
		uiManager.setAvatar("");

		// Assert
		let avatars = headingArea.querySelectorAll("[data-js-chat-avatar]");
		expect(avatars).toHaveLength(1);
		expect(avatars[0].getAttribute("data-js-chat-avatar")).toBe("0");

		// Act
		uiManager.setAvatar(null);

		// Assert
		avatars = headingArea.querySelectorAll("[data-js-chat-avatar]");
		expect(avatars).toHaveLength(1);
		expect(avatars[0].getAttribute("data-js-chat-avatar")).toBe("0");
	});

	it("keeps showing an avatar when called repeatedly with the same kind of avatar", () => {
		// Arrange
		const { uiManager, headingArea, chatTemplate } = createUiManager();

		// Act
		uiManager.setAvatar("https://example.com/first.png");
		uiManager.setAvatar("https://example.com/second.png");

		// Assert
		const avatars = headingArea.querySelectorAll("[data-js-chat-avatar]");
		expect(avatars).toHaveLength(1);
		expect((avatars[0].querySelector("img") as HTMLImageElement).src).toBe("https://example.com/second.png");
		expect(chatTemplate.content.querySelectorAll("[data-js-chat-avatar]")).toHaveLength(2);
	});

	it("does not render an avatar when the template has none", () => {
		// Arrange
		const { uiManager, headingArea } = createUiManager(document.createElement("template"));

		// Act
		uiManager.setAvatar("https://example.com/avatar.png");

		// Assert
		expect(headingArea.querySelectorAll("[data-js-chat-avatar]")).toHaveLength(0);
	});

	it("falls back to the icon avatar when the image avatar has no img element", () => {
		// Arrange
		const template = document.createElement("template");
		template.innerHTML = `
			<div data-js-chat-avatar="0"><span>icon</span></div>
			<div data-js-chat-avatar="1"></div>
		`;
		const { uiManager, headingArea } = createUiManager(template);

		// Act
		uiManager.setAvatar("https://example.com/avatar.png");

		// Assert
		const avatars = headingArea.querySelectorAll("[data-js-chat-avatar]");
		expect(avatars).toHaveLength(1);
		expect(avatars[0].getAttribute("data-js-chat-avatar")).toBe("0");
	});

	it("sets the title text", () => {
		// Arrange
		const { uiManager, title } = createUiManager();

		// Act
		uiManager.setTitle("Ava");

		// Assert
		expect(title.textContent).toBe("Ava");
	});

	it("clears the actions container", () => {
		// Arrange
		const { uiManager, actionsContainer } = createUiManager();
		actionsContainer.innerHTML = "<button>old</button>";

		// Act
		uiManager.clearActions();

		// Assert
		expect(actionsContainer.innerHTML).toBe("");
	});

	it("delegates adding actions to the action factory", () => {
		// Arrange
		const { uiManager, actionFactory } = createUiManager();

		// Act & Assert
		expect(uiManager.addMinimizeAction()).toBe("minimize");
		expect(uiManager.addClearAction()).toBe("clear");
		expect(uiManager.addCloseAction()).toBe("close");
		expect(actionFactory.addMinimize).toHaveBeenCalledTimes(1);
		expect(actionFactory.addClear).toHaveBeenCalledTimes(1);
		expect(actionFactory.addClose).toHaveBeenCalledTimes(1);
	});
});
