import AiConversationProvider from "./aiConversationProvider";

describe("AiConversationProvider", () => {
	it("maps ChatSession events to normalized provider events", async () => {
		const session = {
			ask: jest.fn(async function* () {
				yield { type: "text", content: "Hello" } as ChatEvent;
				yield { type: "tool_call" } as ChatEvent;
				yield { type: "done" } as ChatEvent;
			}),
			clearSessionForAssistant: jest.fn(),
		} as unknown as ChatSession;

		const provider = new AiConversationProvider(session, "Ava");
		const events = [] as Array<{ type: string; content?: string; name?: string }>;

		for await (const event of provider.sendMessage("Hi")) {
			events.push(event);
		}

		expect(events).toEqual([
			{ type: "text", content: "Hello" },
			{ type: "activity", name: "tool_call" },
			{ type: "done" },
		]);
	});

	it("resets conversation using assistant key", () => {
		const session = {
			ask: jest.fn(),
			clearSessionForAssistant: jest.fn(),
		} as unknown as ChatSession;

		const provider = new AiConversationProvider(session, "Ava");
		provider.resetConversation();

		expect(session.clearSessionForAssistant).toHaveBeenCalledWith("Ava");
	});

	it("exposes streaming and feedback capabilities", () => {
		const session = {
			ask: jest.fn(),
			clearSessionForAssistant: jest.fn(),
		} as unknown as ChatSession;

		const provider = new AiConversationProvider(session, "Ava");

		expect(provider.getCapabilities()).toEqual({
			supportsStreaming: true,
			supportsFeedback: true,
			supportsSessionReset: true,
		});
	});
});
