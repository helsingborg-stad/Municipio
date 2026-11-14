import PuzzelChatStatusHandler from "./puzzelChatStatusHandler";
import { PUZZEL_CHAT_STATUS } from "../enums";

describe("PuzzelChatStatusHandler", () => {
	const createChatHandler = () => ({
		setChatInactiveData: jest.fn(),
		displayBanner: jest.fn(),
		disable: jest.fn(),
		enable: jest.fn(),
		setQueueBannerData: jest.fn(),
	});

	const createHandler = () => {
		const chatHandler = createChatHandler();
		const handler = new PuzzelChatStatusHandler(chatHandler as any, {} as any);
		return { chatHandler, handler };
	};

	it("returns null status for empty or missing status lists", () => {
		// Arrange
		const { handler } = createHandler();

		// Act / Assert
		expect(handler.getStatus([])).toBeNull();
		expect(handler.getStatus(null as any)).toBeNull();
		expect(handler.getCurrentStatus([])).toBeNull();
	});

	it("returns the last status in the list", () => {
		// Arrange
		const { handler } = createHandler();
		const statuses = [{ status: "InQueue", queuePosition: 3 }, { status: "Connected" }];

		// Act
		const status = handler.getStatus(statuses as any);
		const currentStatus = handler.getCurrentStatus(statuses as any);

		// Assert
		expect(status).toBe(statuses[1]);
		expect(currentStatus).toBe("Connected");
	});

	it.each([
		["in queue with position", [{ status: "InQueue", queuePosition: 4 }], 4],
		["in queue without position", [{ status: "InQueue", queuePosition: 0 }], null],
		["connected", [{ status: "Connected", queuePosition: 4 }], null],
		["empty", [], null],
	])("getQueuePosition returns the position only when %s", (_label, statuses, expected) => {
		// Arrange
		const { handler } = createHandler();

		// Act
		const result = handler.getQueuePosition(statuses as any);

		// Assert
		expect(result).toBe(expected);
	});

	it.each([PUZZEL_CHAT_STATUS.DISCONNECTED, PUZZEL_CHAT_STATUS.FAILURE])(
		"shows inactive banner and disables chat on %s",
		(status) => {
			// Arrange
			const { chatHandler, handler } = createHandler();

			// Act
			handler.handleStatusEvent({ status } as any);

			// Assert
			expect(chatHandler.setChatInactiveData).toHaveBeenCalledTimes(1);
			expect(chatHandler.displayBanner).toHaveBeenCalledWith(true);
			expect(chatHandler.disable).toHaveBeenCalledTimes(1);
			expect(chatHandler.enable).not.toHaveBeenCalled();
		},
	);

	it("shows queue position banner and enables chat when in queue", () => {
		// Arrange
		const { chatHandler, handler } = createHandler();

		// Act
		handler.handleStatusEvent({ status: PUZZEL_CHAT_STATUS.IN_QUEUE, queuePosition: 5 } as any);

		// Assert
		expect(chatHandler.setQueueBannerData).toHaveBeenCalledWith(5);
		expect(chatHandler.displayBanner).toHaveBeenCalledWith(true);
		expect(chatHandler.enable).toHaveBeenCalledTimes(1);
	});

	it("shows queue position 1 when allocated", () => {
		// Arrange
		const { chatHandler, handler } = createHandler();

		// Act
		handler.handleStatusEvent({ status: PUZZEL_CHAT_STATUS.ALLOCATED, queuePosition: 9 } as any);

		// Assert
		expect(chatHandler.setQueueBannerData).toHaveBeenCalledWith(1);
		expect(chatHandler.displayBanner).toHaveBeenCalledWith(true);
		expect(chatHandler.enable).toHaveBeenCalledTimes(1);
	});

	it("hides the banner and enables chat when connected", () => {
		// Arrange
		const { chatHandler, handler } = createHandler();

		// Act
		handler.handleStatusEvent({ status: PUZZEL_CHAT_STATUS.CONNECTED } as any);

		// Assert
		expect(chatHandler.displayBanner).toHaveBeenCalledWith(false);
		expect(chatHandler.enable).toHaveBeenCalledTimes(1);
		expect(chatHandler.setQueueBannerData).not.toHaveBeenCalled();
	});

	it.each([[null], [{ status: "Unknown" }]])("does nothing for %p", (status) => {
		// Arrange
		const { chatHandler, handler } = createHandler();

		// Act
		handler.handleStatusEvent(status as any);

		// Assert
		Object.values(chatHandler).forEach((fn) => expect(fn).not.toHaveBeenCalled());
	});
});
