import Chat from "./chat";

const flushMicrotasks = async (): Promise<void> => {
	await new Promise((resolve) => setTimeout(resolve, 0));
	await Promise.resolve();
	await Promise.resolve();
};

describe("Chat", () => {
	it("uses the provider session contract for user messages", async () => {
		const ask = jest.fn(async function* (_message: string) {
			yield { type: "text", content: "Hello from provider" } as const;
			yield { type: "done" } as const;
		});

		const clearSessionForAssistant = jest.fn();
		const session: ConversationSession = {
			ask,
			clearSessionForAssistant,
		};

		const create = jest.fn(() => session);
		const sessionFactory: ConversationProviderFactory = {
			create,
		};

		const chatElement = document.createElement("div");
		const pendingMessage = { id: "pending-1" };

		const chatWidget = {
			getElement: jest.fn(() => chatElement),
			addPendingMessage: jest.fn(() => pendingMessage),
			disableSend: jest.fn(),
			enableSend: jest.fn(),
			editMessage: jest.fn(),
			deleteMessage: jest.fn(),
		};

		const markdownParser = {
			render: jest.fn((content: string) => `<p>${content}</p>`),
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

		const chatInstance = new Chat(
			sessionFactory,
			chatWidget,
			markdownParser as any,
			feedbackFactory as any,
			feedbackApi as any,
			"Ava",
			true,
		);

		chatInstance.init();

		const userMessage = {
			getIsReply: () => false,
			getContent: () => "Hi",
		};

		chatElement.dispatchEvent(
			new CustomEvent("chat:message-added", {
				detail: userMessage,
			}),
		);

		await flushMicrotasks();

		expect(create).toHaveBeenCalledWith("Ava", true);
		expect(ask).toHaveBeenCalledWith("Hi");
		expect(chatWidget.disableSend).toHaveBeenCalledTimes(1);
		expect(chatWidget.enableSend).toHaveBeenCalledTimes(1);
		expect(chatWidget.editMessage).toHaveBeenCalledWith(
			"<p>Hello from provider</p>",
			pendingMessage,
		);
		expect(feedbackFactory.create).toHaveBeenCalledWith(pendingMessage);
		expect(feedbackApi.postStat).toHaveBeenCalledWith("message");
		expect(chatWidget.deleteMessage).not.toHaveBeenCalled();
		expect(clearSessionForAssistant).not.toHaveBeenCalled();
	});

	it("resets conversation via provider session contract", () => {
		const ask = jest.fn(async function* (_message: string) {
			yield { type: "done" } as const;
		});
		const clearSessionForAssistant = jest.fn();

		const session: ConversationSession = {
			ask,
			clearSessionForAssistant,
		};

		const create = jest
			.fn<ConversationSession, [string | null, boolean?]>()
			.mockReturnValue(session);

		const sessionFactory: ConversationProviderFactory = { create };

		const chatWidget = {
			getElement: jest.fn(() => document.createElement("div")),
			addPendingMessage: jest.fn(),
			disableSend: jest.fn(),
			enableSend: jest.fn(),
			editMessage: jest.fn(),
			deleteMessage: jest.fn(),
		};

		const markdownParser = {
			render: jest.fn((content: string) => content),
			utils: { escapeHtml: jest.fn((content: string) => content) },
		};

		const chatInstance = new Chat(
			sessionFactory,
			chatWidget,
			markdownParser as any,
			{ create: jest.fn() } as any,
			{ postStat: jest.fn() } as any,
			"Ava",
			true,
		);

		chatInstance.init();
		chatInstance.createNewChatSession();

		expect(create).toHaveBeenNthCalledWith(1, "Ava", true);
		expect(create).toHaveBeenNthCalledWith(2, "Ava", true);
		expect(clearSessionForAssistant).toHaveBeenCalledWith("Ava");
	});
});
