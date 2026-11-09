import PollingEventListener from "./pollingEventListener";

describe("PollingEventListener", () => {
	beforeEach(() => {
		jest.useFakeTimers();
	});

	afterEach(() => {
		jest.clearAllTimers();
		jest.useRealTimers();
		jest.restoreAllMocks();
	});

	it("does not notify on the initial poll and only notifies new events on later polls", async () => {
		// Arrange
		const listener = new PollingEventListener<string>();
		const callback = jest.fn();
		const execute = jest.fn()
			.mockResolvedValueOnce(["a", "b"])
			.mockResolvedValueOnce(["a", "b", "c", "d"]);

		// Act
		await listener.listen("conv-1", callback, execute, 1000, "poll failed");
		const callsAfterInitialPoll = callback.mock.calls.length;
		await jest.advanceTimersByTimeAsync(1000);

		// Assert
		expect(callsAfterInitialPoll).toBe(0);
		expect(execute).toHaveBeenCalledWith("conv-1");
		expect(callback.mock.calls).toEqual([["c"], ["d"]]);
	});

	it("does not notify when the event list shrinks or stays the same", async () => {
		// Arrange
		const listener = new PollingEventListener<string>();
		const callback = jest.fn();
		const execute = jest.fn()
			.mockResolvedValueOnce(["a", "b"])
			.mockResolvedValueOnce(["a", "b"])
			.mockResolvedValueOnce(["a"])
			.mockResolvedValueOnce(["a", "x"]);

		// Act
		await listener.listen("conv-1", callback, execute, 1000, "poll failed");
		await jest.advanceTimersByTimeAsync(3000);

		// Assert
		expect(execute).toHaveBeenCalledTimes(4);
		expect(callback.mock.calls).toEqual([["x"]]);
	});

	it("notifies the latest event on every poll, including the first, when alwaysNotify is true", async () => {
		// Arrange
		const listener = new PollingEventListener<string>();
		const callback = jest.fn();
		const execute = jest.fn()
			.mockResolvedValueOnce(["a", "b"])
			.mockResolvedValueOnce(["a", "b"])
			.mockResolvedValueOnce([]);

		// Act
		await listener.listen("conv-1", callback, execute, 1000, "poll failed", true);
		await jest.advanceTimersByTimeAsync(2000);

		// Assert
		expect(callback.mock.calls).toEqual([["b"], ["b"]]);
	});

	it("ignores non-array responses", async () => {
		// Arrange
		const listener = new PollingEventListener<string>();
		const callback = jest.fn();
		const execute = jest.fn().mockResolvedValue({ not: "an array" });

		// Act
		await listener.listen("conv-1", callback, execute as any, 1000, "poll failed", true);

		// Assert
		expect(callback).not.toHaveBeenCalled();
	});

	it("polls at the configured interval and stops after stopListening", async () => {
		// Arrange
		const listener = new PollingEventListener<string>();
		const execute = jest.fn().mockResolvedValue([]);
		await listener.listen("conv-1", jest.fn(), execute, 3000, "poll failed");

		// Act
		await jest.advanceTimersByTimeAsync(2999);
		const callsBeforeInterval = execute.mock.calls.length;
		await jest.advanceTimersByTimeAsync(1);
		const callsAfterInterval = execute.mock.calls.length;
		listener.stopListening("conv-1");
		await jest.advanceTimersByTimeAsync(9000);

		// Assert
		expect(callsBeforeInterval).toBe(1);
		expect(callsAfterInterval).toBe(2);
		expect(execute).toHaveBeenCalledTimes(2);
	});

	it("does not start an interval if stopListening is called during the initial poll", async () => {
		// Arrange
		const listener = new PollingEventListener<string>();
		let resolveInitial: (value: string[]) => void = () => {};
		const execute = jest.fn().mockImplementationOnce(
			() => new Promise<string[]>((resolve) => { resolveInitial = resolve; }),
		).mockResolvedValue([]);

		// Act
		const listenPromise = listener.listen("conv-1", jest.fn(), execute, 1000, "poll failed");
		listener.stopListening("conv-1");
		resolveInitial([]);
		await listenPromise;
		await jest.advanceTimersByTimeAsync(5000);

		// Assert
		expect(execute).toHaveBeenCalledTimes(1);
	});

	it("propagates errors from the initial poll", async () => {
		// Arrange
		const listener = new PollingEventListener<string>();
		const execute = jest.fn().mockRejectedValue(new Error("network"));

		// Act
		const result = listener.listen("conv-1", jest.fn(), execute, 1000, "poll failed");

		// Assert
		await expect(result).rejects.toThrow("network");
	});

	it("logs errors from interval polls and keeps polling", async () => {
		// Arrange
		const consoleErrorSpy = jest.spyOn(console, "error").mockImplementation(() => {});
		const listener = new PollingEventListener<string>();
		const error = new Error("network");
		const execute = jest.fn()
			.mockResolvedValueOnce([])
			.mockRejectedValueOnce(error)
			.mockResolvedValue([]);
		await listener.listen("conv-1", jest.fn(), execute, 1000, "poll failed");

		// Act
		await jest.advanceTimersByTimeAsync(2000);

		// Assert
		expect(consoleErrorSpy).toHaveBeenCalledWith("poll failed", error);
		expect(execute).toHaveBeenCalledTimes(3);
	});

	it("replaces the interval and notifies all callbacks when listening again for the same conversation", async () => {
		// Arrange
		const listener = new PollingEventListener<string>();
		const firstCallback = jest.fn();
		const secondCallback = jest.fn();
		const execute = jest.fn()
			.mockResolvedValueOnce(["a"])
			.mockResolvedValueOnce(["a"])
			.mockResolvedValueOnce(["a", "b"])
			.mockResolvedValue(["a", "b"]);
		await listener.listen("conv-1", firstCallback, execute, 1000, "poll failed");
		await listener.listen("conv-1", secondCallback, execute, 1000, "poll failed");

		// Act
		await jest.advanceTimersByTimeAsync(1000);

		// Assert
		expect(execute).toHaveBeenCalledTimes(3);
		expect(firstCallback.mock.calls).toEqual([["b"]]);
		expect(secondCallback.mock.calls).toEqual([["b"]]);
	});

	it("keeps conversations independent", async () => {
		// Arrange
		const listener = new PollingEventListener<string>();
		const executeOne = jest.fn().mockResolvedValue([]);
		const executeTwo = jest.fn().mockResolvedValue([]);
		await listener.listen("conv-1", jest.fn(), executeOne, 1000, "poll failed");
		await listener.listen("conv-2", jest.fn(), executeTwo, 1000, "poll failed");

		// Act
		listener.stopListening("conv-1");
		await jest.advanceTimersByTimeAsync(1000);

		// Assert
		expect(executeOne).toHaveBeenCalledTimes(1);
		expect(executeTwo).toHaveBeenCalledTimes(2);
	});
});
