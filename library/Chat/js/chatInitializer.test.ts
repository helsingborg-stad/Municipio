import ChatInitializer from "./chatInitializer";
import ActionFactory from "./chat/feature/actions/actionFactory";
import NullChatUiManager from "./chat/feature/ui/nullChatUiManager";

const mockClearActions = jest.fn();
const mockAddMinimize = jest.fn();
const mockAddClear = jest.fn();

jest.mock("./chat/feature/actions/actionFactory", () => {
	return jest.fn().mockImplementation(() => ({
		clearActions: mockClearActions,
		addMinimize: mockAddMinimize,
		addClear: mockAddClear,
	}));
});

describe("ChatInitializer", () => {
	beforeEach(() => {
		document.body.innerHTML = "";
		jest.clearAllMocks();
	});

	const createChat = (assistants: Record<string, string>) => {
		const chatElement = document.createElement("div");
		chatElement.dataset.jsChatAssistants = JSON.stringify(assistants);

		const chatTemplate = document.createElement("template");
		chatTemplate.setAttribute("data-js-municipio-chat-template", "");
		chatElement.appendChild(chatTemplate);

		const actionsContainer = document.createElement("div");
		actionsContainer.setAttribute("data-js-chat-actions", "");
		chatElement.appendChild(actionsContainer);

		document.body.appendChild(chatElement);

		return {
			getElement: jest.fn().mockReturnValue(chatElement),
		};
	};

	it("does not call action factory clear while falling back", async () => {
		// Arrange
		const chat = createChat({
			broken: "does-not-exist",
			fallback: "null",
		});
		const initializer = new ChatInitializer(chat as any);

		// Act
		const initialized = await initializer.init();

		// Assert
		expect(initialized).toBe(true);
		expect(mockClearActions).toHaveBeenCalledTimes(0);
		expect(ActionFactory).toHaveBeenCalledTimes(1);
	});

	it("does not clear actions when a named integration fails to create", async () => {
		// Arrange
		const chat = createChat({ broken: "does-not-exist" });
		const initializer = new ChatInitializer(chat as any);

		// Act
		const initialized = await initializer.init("broken");

		// Assert
		expect(initialized).toBe(false);
		expect(mockClearActions).toHaveBeenCalledTimes(0);
	});

	it("does not call action factory clear when assistant is already active", async () => {
		// Arrange
		const chat = createChat({ fallback: "null" });
		const initializer = new ChatInitializer(chat as any);

		// Act
		const firstInitialization = await initializer.init("fallback");
		const secondInitialization = await initializer.init("fallback");

		// Assert
		expect(firstInitialization).toBe(true);
		expect(secondInitialization).toBe(true);
		expect(mockClearActions).toHaveBeenCalledTimes(0);
	});

	describe("chat without header (e.g. the chat block)", () => {
		const createHeadlessChat = (assistants: Record<string, string>) => {
			const chatElement = document.createElement("div");
			chatElement.dataset.jsChatAssistants = JSON.stringify(assistants);
			document.body.appendChild(chatElement);

			return { getElement: jest.fn().mockReturnValue(chatElement) };
		};

		it("still initializes an integration instead of giving up", async () => {
			// Arrange
			const initializer = new ChatInitializer(createHeadlessChat({ fallback: "null" }) as any);

			// Act
			const initialized = await initializer.init();

			// Assert
			expect(initialized).toBe(true);
			expect(ActionFactory).not.toHaveBeenCalled();
		});

		it("gives the integration a no-op UI manager", async () => {
			// Arrange
			const chat = createHeadlessChat({ myassistant: "null" });
			const initializer = new ChatInitializer(chat as any);
			const createSpy = jest.spyOn(
				(await import("./chat/integration/null/nullFactory")).default.prototype,
				"create",
			);

			// Act
			await initializer.init();

			// Assert
			expect(createSpy).toHaveBeenCalledWith(chat, expect.any(NullChatUiManager), "myassistant");
		});
	});
});
