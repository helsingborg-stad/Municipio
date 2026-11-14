import PuzzelFactory from "./puzzelFactory";
import PuzzelConfig from "./puzzelConfig";
import { createPuzzel } from "./createPuzzel";

jest.mock("./createPuzzel", () => ({
	createPuzzel: jest.fn().mockReturnValue({ integration: true }),
}));

describe("PuzzelFactory", () => {
	const createChat = (attributes: Record<string, string>) => {
		const element = document.createElement("div");
		Object.entries(attributes).forEach(([key, value]) => element.setAttribute(key, value));
		return { getElement: jest.fn().mockReturnValue(element) };
	};

	const validAttributes = {
		"data-js-chat-puzzel-customer-id": "customer",
		"data-js-chat-puzzel-queue-key": "queue",
		"data-js-chat-puzzel-visual-queue-id": "visual",
		"data-js-chat-puzzel-time-id": "time",
		"data-js-chat-puzzel-client-id": "client",
		"data-js-chat-puzzel-name": "Assistant",
		"data-js-chat-puzzel-avatar": "avatar.png",
	};

	afterEach(() => {
		jest.restoreAllMocks();
		(createPuzzel as jest.Mock).mockClear();
	});

	it("builds a config from prefixed data attributes and creates the integration", async () => {
		// Arrange
		const chat = createChat(validAttributes);
		const uiManager = {};
		const factory = new PuzzelFactory();

		// Act
		const result = await factory.create(chat, uiManager as any, "puzzel");

		// Assert
		expect(result).toEqual({ integration: true });
		const [chatArg, uiManagerArg, config, name] = (createPuzzel as jest.Mock).mock.calls[0];
		expect(chatArg).toBe(chat);
		expect(uiManagerArg).toBe(uiManager);
		expect(name).toBe("puzzel");
		expect(config).toBeInstanceOf(PuzzelConfig);
		expect(config.getCustomerId()).toBe("customer");
		expect(config.getChatQueueKey()).toBe("queue");
		expect(config.getVisualQueueId()).toBe("visual");
		expect(config.getTimeId()).toBe("time");
		expect(config.getClientId()).toBe("client");
		expect(config.getName()).toBe("Assistant");
		expect(config.getAvatar()).toBe("avatar.png");
	});

	it("uses a null avatar when the avatar attribute is missing", async () => {
		// Arrange
		const { "data-js-chat-puzzel-avatar": _avatar, ...attributes } = validAttributes;
		const chat = createChat(attributes);
		const factory = new PuzzelFactory();

		// Act
		await factory.create(chat, {} as any, "puzzel");

		// Assert
		const config = (createPuzzel as jest.Mock).mock.calls[0][2];
		expect(config.getAvatar()).toBeNull();
	});

	it.each(Object.keys(validAttributes).filter((key) => !key.endsWith("-avatar")))(
		"returns false and logs when %s is missing",
		async (missingAttribute) => {
			// Arrange
			const consoleErrorSpy = jest.spyOn(console, "error").mockImplementation(() => {});
			const attributes: Record<string, string> = { ...validAttributes };
			delete attributes[missingAttribute];
			const chat = createChat(attributes);
			const factory = new PuzzelFactory();

			// Act
			const result = await factory.create(chat, {} as any, "puzzel");

			// Assert
			expect(result).toBe(false);
			expect(consoleErrorSpy).toHaveBeenCalledWith("Missing required Puzzel configuration attributes");
			expect(createPuzzel).not.toHaveBeenCalled();
		},
	);

	it("reads attributes using the given integration name as prefix", async () => {
		// Arrange
		jest.spyOn(console, "error").mockImplementation(() => {});
		const chat = createChat(validAttributes);
		const factory = new PuzzelFactory();

		// Act
		const result = await factory.create(chat, {} as any, "other");

		// Assert
		expect(result).toBe(false);
	});
});
