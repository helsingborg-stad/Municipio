import Chat from "./chat";
import type { ConversationProvider } from "./conversationProvider";

type ChatMessageMock = {
	getIsReply: () => boolean;
	getContent: () => string;
};

type ChatElementMock = {
	addEventListener: jest.Mock;
	triggerMessage: (message: ChatMessageMock) => Promise<void>;
};

function createChatElementMock(): ChatElementMock {
	const listeners = new Map<string, (event: any) => void>();

	return {
		addEventListener: jest.fn((eventName: string, callback: (event: any) => void) => {
			listeners.set(eventName, callback);
		}),
		triggerMessage: async (message: ChatMessageMock) => {
			const callback = listeners.get("chat:message-added");
			if (!callback) {
				throw new Error("chat:message-added listener missing");
			}

			callback({ detail: message });
			await Promise.resolve();
			await Promise.resolve();
		},
	};
}

function createProvider(
	options: {
		capabilities?: {
			supportsStreaming: boolean;
			supportsFeedback: boolean;
			supportsSessionReset: boolean;
		};
		events?: Array<{ type: "text"; content: string } | { type: "activity"; name: string } | { type: "done" }>;
		throwOnSend?: boolean;
	} = {},
): ConversationProvider {
	const capabilities =
		options.capabilities ??
		({
			supportsStreaming: true,
			supportsFeedback: true,
			supportsSessionReset: true,
		} as const);

	return {
		sendMessage: jest.fn(async function* () {
			if (options.throwOnSend) {
				throw new Error("provider-failure");
			}

			for (const event of options.events ?? [{ type: "done" }]) {
				yield event;
			}
		}),
		resetConversation: jest.fn(),
		getCapabilities: jest.fn(() => capabilities),
	};
}

describe("Chat runtime", () => {
	beforeEach(() => {
		jest.restoreAllMocks();
		(global as any).municipioChatLocale = {
			errorMessage: "Something went wrong",
		};
		jest.spyOn(console, "error").mockImplementation(() => undefined);
	});

	it("resets provider session only when provider supports session reset", () => {
		const providerWithoutReset = createProvider({
			capabilities: {
				supportsStreaming: true,
				supportsFeedback: true,
				supportsSessionReset: false,
			},
		});
		const providerWithReset = createProvider({
			capabilities: {
				supportsStreaming: true,
				supportsFeedback: true,
				supportsSessionReset: true,
			},
		});

		const providerFactory = {
			create: jest
				.fn()
				.mockReturnValueOnce(providerWithoutReset)
				.mockReturnValueOnce(providerWithoutReset)
				.mockReturnValueOnce(providerWithReset),
		};

		const chatElement = createChatElementMock();
		const pendingMessage = { id: "pending" };
		const chatUi = {
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
		const feedbackFactory = { create: jest.fn() };
		const feedbackApi = { postStat: jest.fn() };

		const chat = new Chat(
			providerFactory as any,
			chatUi,
			markdownParser as any,
			feedbackFactory as any,
			feedbackApi as any,
			"Ava",
			true,
		);

		chat.init();
		chat.createNewChatSession();
		expect(providerWithoutReset.resetConversation).not.toHaveBeenCalled();

		chat.createNewChatSession();
		expect(providerWithReset.resetConversation).toHaveBeenCalledTimes(1);
	});

	it("creates feedback only when provider supports feedback", async () => {
		const provider = createProvider({
			capabilities: {
				supportsStreaming: true,
				supportsFeedback: false,
				supportsSessionReset: true,
			},
			events: [
				{ type: "text", content: "Hello" },
				{ type: "done" },
			],
		});
		const providerFactory = { create: jest.fn(() => provider) };

		const chatElement = createChatElementMock();
		const pendingMessage = { id: "pending" };
		const chatUi = {
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
		const feedbackFactory = { create: jest.fn() };
		const feedbackApi = { postStat: jest.fn() };

		const chat = new Chat(
			providerFactory as any,
			chatUi,
			markdownParser as any,
			feedbackFactory as any,
			feedbackApi as any,
			"Ava",
			true,
		);

		chat.init();
		await (chat as any).sendMessage("Hi");

		expect(chatUi.disableSend).toHaveBeenCalledTimes(1);
		expect(chatUi.enableSend).toHaveBeenCalledTimes(1);
		expect(feedbackFactory.create).not.toHaveBeenCalled();
		expect(feedbackApi.postStat).toHaveBeenCalledWith("message");
	});

	it("deletes pending message when provider returns done without content or activity", async () => {
		const provider = createProvider({
			events: [{ type: "done" }],
		});
		const providerFactory = { create: jest.fn(() => provider) };

		const chatElement = createChatElementMock();
		const pendingMessage = { id: "pending" };
		const chatUi = {
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
		const feedbackFactory = { create: jest.fn() };
		const feedbackApi = { postStat: jest.fn() };

		const chat = new Chat(
			providerFactory as any,
			chatUi,
			markdownParser as any,
			feedbackFactory as any,
			feedbackApi as any,
			"Ava",
			true,
		);

		chat.init();
		await (chat as any).sendMessage("Hi");

		expect(chatUi.deleteMessage).toHaveBeenCalledWith(pendingMessage);
		expect(feedbackFactory.create).not.toHaveBeenCalled();
		expect(feedbackApi.postStat).not.toHaveBeenCalled();
	});

	it("shows localized fallback error when provider send fails", async () => {
		const provider = createProvider({ throwOnSend: true });
		const providerFactory = { create: jest.fn(() => provider) };

		const chatElement = createChatElementMock();
		const pendingMessage = { id: "pending" };
		const chatUi = {
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
		const feedbackFactory = { create: jest.fn() };
		const feedbackApi = { postStat: jest.fn() };

		const chat = new Chat(
			providerFactory as any,
			chatUi,
			markdownParser as any,
			feedbackFactory as any,
			feedbackApi as any,
			"Ava",
			true,
		);

		chat.init();
		await (chat as any).sendMessage("Hi");

		expect(chatUi.enableSend).toHaveBeenCalledTimes(1);
		expect(chatUi.editMessage).toHaveBeenLastCalledWith(
			"Something went wrong",
			pendingMessage,
		);
	});
});
