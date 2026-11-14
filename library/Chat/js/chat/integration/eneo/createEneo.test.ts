import { createEneo } from "./createEneo";
import Eneo from "./eneo";

jest.mock("./eneo", () => ({
	__esModule: true,
	default: jest.fn(),
}));

type FakeMessage = {
	container: HTMLElement;
	getIsReply: () => boolean;
	getContent: () => string;
	getData: () => any;
	setData: (data: any) => void;
	getMessageContainer: () => HTMLElement;
};

describe("createEneo", () => {
	const EneoMock = Eneo as unknown as jest.Mock;

	const createMessage = (content: string, isReply: boolean): FakeMessage => {
		const container = document.createElement("div");
		let data: any = null;
		return {
			container,
			getIsReply: () => isReply,
			getContent: () => content,
			getData: () => data,
			setData: (newData: any) => {
				data = newData;
			},
			getMessageContainer: () => container,
		};
	};

	const createChat = (messages: FakeMessage[] = [], persistent: string | null = null) => {
		const element = document.createElement("div");
		element.innerHTML = `
			<template data-js-chat-feedback>
				<div class="municipio-chat__feedback">
					<span data-js-chat-message-like-button></span>
					<span data-js-chat-message-dislike-button></span>
				</div>
			</template>
		`;
		if (persistent !== null) {
			element.setAttribute("data-js-chat-persistent", persistent);
		}

		const chat = {
			getElement: jest.fn(() => element),
			getMessages: jest.fn(() => messages),
			addMessage: jest.fn((content: string, isReply: boolean) => {
				messages.push(createMessage(content, isReply));
			}),
			updateMessage: jest.fn(),
		};

		return chat;
	};

	const hasFeedback = (message: FakeMessage) =>
		message.container.querySelector("[data-js-chat-message-like-button]") !== null;

	const eneoArgs = () => EneoMock.mock.calls[0];

	beforeEach(() => {
		EneoMock.mockReset();
		Object.defineProperty(globalThis, "wpApiSettings", {
			configurable: true,
			value: { root: "https://example.com/wp-json/" },
		});
	});

	const originalFetch = global.fetch;

	afterEach(() => {
		Reflect.deleteProperty(globalThis, "wpApiSettings");
		global.fetch = originalFetch;
	});

	it("constructs Eneo with the chat, ui manager, assistant name and avatar", () => {
		// Arrange
		const chat = createChat();
		const uiManager = {};

		// Act
		const result = createEneo(chat, uiManager as any, "Ava", null, "https://example.com/a.png");

		// Assert
		expect(EneoMock).toHaveBeenCalledTimes(1);
		expect(result).toBe(EneoMock.mock.instances[0]);
		const args = eneoArgs();
		expect(args[1]).toBe(chat);
		expect(args[5]).toBe(uiManager);
		expect(args[6]).toBe("Ava");
		expect(args[7]).toBe("https://example.com/a.png");
	});

	it.each([
		[null, false],
		["false", false],
		["", true],
		["true", true],
	])("derives persistSession from data-js-chat-persistent=%p as %p", (attribute, expected) => {
		// Arrange
		const chat = createChat([], attribute);

		// Act
		createEneo(chat, {} as any, "Ava", null, null);

		// Assert
		expect(eneoArgs()[8]).toBe(expected);
	});

	it("adds the greeting phrase as a reply when there are no messages", () => {
		// Arrange
		const chat = createChat();

		// Act
		createEneo(chat, {} as any, "Ava", "Hi, how can I help?", null);

		// Assert
		expect(chat.addMessage).toHaveBeenCalledTimes(1);
		expect(chat.addMessage).toHaveBeenCalledWith("Hi, how can I help?", true);
	});

	it("does not add the greeting phrase when messages already exist", () => {
		// Arrange
		const chat = createChat([createMessage("Hello", false)]);

		// Act
		createEneo(chat, {} as any, "Ava", "Hi, how can I help?", null);

		// Assert
		expect(chat.addMessage).not.toHaveBeenCalled();
	});

	it("does not add a greeting when no greeting phrase is configured", () => {
		// Arrange
		const chat = createChat();

		// Act
		createEneo(chat, {} as any, "Ava", null, null);

		// Assert
		expect(chat.addMessage).not.toHaveBeenCalled();
	});

	it("attaches feedback only to existing reply messages, skipping the leading greeting", () => {
		// Arrange
		const greeting = createMessage("Hi, how can I help?", true);
		const userMessage = createMessage("What are the opening hours?", false);
		const reply = createMessage("We are open 8-17.", true);
		const chat = createChat([greeting, userMessage, reply]);

		// Act
		createEneo(chat, {} as any, "Ava", "Hi, how can I help?", null);

		// Assert
		expect(hasFeedback(greeting)).toBe(false);
		expect(hasFeedback(userMessage)).toBe(false);
		expect(hasFeedback(reply)).toBe(true);
	});

	it("attaches feedback to the first reply when it does not match the greeting phrase", () => {
		// Arrange
		const firstReply = createMessage("Some other reply", true);
		const chat = createChat([firstReply]);

		// Act
		createEneo(chat, {} as any, "Ava", "Hi, how can I help?", null);

		// Assert
		expect(hasFeedback(firstReply)).toBe(true);
	});

	it("attaches feedback to a later reply even if it has the same text as the greeting", () => {
		// Arrange
		const userMessage = createMessage("Hello", false);
		const reply = createMessage("Hi, how can I help?", true);
		const chat = createChat([userMessage, reply]);

		// Act
		createEneo(chat, {} as any, "Ava", "Hi, how can I help?", null);

		// Assert
		expect(hasFeedback(reply)).toBe(true);
	});

	it("does not attach feedback to a freshly added greeting message", () => {
		// Arrange
		const messages: FakeMessage[] = [];
		const chat = createChat(messages);

		// Act
		createEneo(chat, {} as any, "Ava", "Hi, how can I help?", null);

		// Assert
		expect(messages).toHaveLength(1);
		expect(hasFeedback(messages[0])).toBe(false);
	});

	it("wires feedback to post stats against the wpApiSettings root", () => {
		// Arrange
		const fetchMock = jest.fn().mockResolvedValue({} as Response);
		global.fetch = fetchMock as any;
		const reply = createMessage("Answer", true);
		const chat = createChat([createMessage("Question", false), reply]);
		createEneo(chat, {} as any, "Ava", null, null);

		// Act
		(reply.container.querySelector("[data-js-chat-message-like-button]") as HTMLElement).click();

		// Assert
		expect(fetchMock).toHaveBeenCalledWith(
			"https://example.com/wp-json/municipio/v1/chat/stats",
			expect.objectContaining({ body: JSON.stringify({ type: "like" }) }),
		);
		expect(chat.updateMessage).toHaveBeenCalledWith(reply);
	});

	describe("markdown parser", () => {
		const getParser = () => {
			createEneo(createChat(), {} as any, "Ava", null, null);
			return eneoArgs()[2];
		};

		it.each([
			"https://example.com/page",
			"http://example.com/page",
			"mailto:info@example.com",
			"tel:+4642100000",
			"/relative/path",
			"#section",
		])("renders allowed link %s as an anchor", url => {
			// Arrange
			const parser = getParser();

			// Act
			const html = parser.render(`[link](${url})`);

			// Assert
			expect(html).toContain("<a href=");
		});

		it.each([
			"javascript:alert(1)",
			"data:text/html;base64,PHNjcmlwdD4=",
			"ftp://example.com/file",
			"vbscript:msgbox(1)",
			"relative/path",
		])("does not render disallowed link %s as an anchor", url => {
			// Arrange
			const parser = getParser();

			// Act
			const html = parser.render(`[link](${url})`);

			// Assert
			expect(html).not.toContain("<a ");
		});

		it("escapes raw HTML instead of rendering it", () => {
			// Arrange
			const parser = getParser();

			// Act
			const html = parser.render(`<script>alert(1)</script>`);

			// Assert
			expect(html).not.toContain("<script>");
			expect(html).toContain("&lt;script&gt;");
		});

		it("does not autolink bare URLs", () => {
			// Arrange
			const parser = getParser();

			// Act
			const html = parser.render("Visit https://example.com today");

			// Assert
			expect(html).not.toContain("<a ");
		});
	});
});
