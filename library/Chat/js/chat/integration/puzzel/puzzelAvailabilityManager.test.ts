import PuzzelAvailabilityManager from "./puzzelAvailabilityManager";

describe("PuzzelAvailabilityManager", () => {
	it.each([
		[true, true, true],
		[true, false, false],
		[false, true, false],
		[false, false, false],
	])("chat open %p and agents available %p gives %p", async (isOpen, hasAgents, expected) => {
		// Arrange
		const getChatOpen = { execute: jest.fn().mockResolvedValue(isOpen) };
		const getQueueInfo = { execute: jest.fn().mockResolvedValue(hasAgents) };
		const manager = new PuzzelAvailabilityManager(getChatOpen as any, getQueueInfo as any);

		// Act
		const result = await manager.isAvailable();

		// Assert
		expect(result).toBe(expected);
		expect(getChatOpen.execute).toHaveBeenCalledTimes(1);
		expect(getQueueInfo.execute).toHaveBeenCalledTimes(1);
	});

	it("rejects when either check fails", async () => {
		// Arrange
		const getChatOpen = { execute: jest.fn().mockResolvedValue(true) };
		const getQueueInfo = { execute: jest.fn().mockRejectedValue(new Error("queue down")) };
		const manager = new PuzzelAvailabilityManager(getChatOpen as any, getQueueInfo as any);

		// Act
		const result = manager.isAvailable();

		// Assert
		await expect(result).rejects.toThrow("queue down");
	});
});
