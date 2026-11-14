import ChatUiManager from "./chatUiManager";

describe("ChatUiManager", () => {
	const createActionFactory = () => ({
		addMinimize: jest.fn(),
		addClear: jest.fn(),
		addClose: jest.fn(),
	});

	const createAvatarTemplate = (): HTMLTemplateElement => {
		const template = document.createElement("template");
		template.innerHTML = `
			<div data-js-chat-avatar="false"><span>icon</span></div>
			<div data-js-chat-avatar="true"><img src="#" alt="avatar" /></div>
		`;

		return template;
	};

	it("shows image avatar when url exists", () => {
		// Arrange
		const actionFactory = createActionFactory();
		const actionsContainer = document.createElement("div");
		const headingArea = document.createElement("div");
		const title = document.createElement("h2");
		headingArea.appendChild(title);
		const chatTemplate = createAvatarTemplate();
		const uiManager = new ChatUiManager(
			actionFactory as any,
			actionsContainer,
			title,
			chatTemplate,
			headingArea,
		);

		// Act
		uiManager.setAvatar("https://example.com/avatar.png");

		// Assert
		const avatars = headingArea.querySelectorAll("[data-js-chat-avatar]");
		expect(avatars).toHaveLength(2);

		const iconAvatar = avatars[0] as HTMLElement;
		const imageAvatar = avatars[1] as HTMLElement;
		const image = imageAvatar.querySelector("img") as HTMLImageElement;

		expect(iconAvatar.style.display).toBe("none");
		expect(imageAvatar.style.display).toBe("");
		expect(image.src).toContain("https://example.com/avatar.png");
	});

	it("falls back to icon avatar when url is empty", () => {
		// Arrange
		const actionFactory = createActionFactory();
		const actionsContainer = document.createElement("div");
		const headingArea = document.createElement("div");
		const title = document.createElement("h2");
		headingArea.appendChild(title);
		const chatTemplate = createAvatarTemplate();
		const uiManager = new ChatUiManager(
			actionFactory as any,
			actionsContainer,
			title,
			chatTemplate,
			headingArea,
		);

		// Act
		uiManager.setAvatar("https://example.com/avatar.png");
		uiManager.setAvatar("");

		// Assert
		const avatars = headingArea.querySelectorAll("[data-js-chat-avatar]");
		const iconAvatar = avatars[0] as HTMLElement;
		const imageAvatar = avatars[1] as HTMLElement;
		const image = imageAvatar.querySelector("img") as HTMLImageElement;

		expect(iconAvatar.style.display).toBe("");
		expect(imageAvatar.style.display).toBe("none");
		expect(image.getAttribute("src")).toBeNull();
	});
});
