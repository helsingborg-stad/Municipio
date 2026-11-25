import InfoDataManager from "./infoDataManager";

describe("InfoDataManager", () => {
	const setLocale = (value: unknown) => {
		Object.defineProperty(globalThis, "municipioChatLocale", { value, configurable: true, writable: true });
	};

	afterEach(() => {
		delete (globalThis as any).municipioChatLocale;
	});

	it("uses fallback strings when no locale is defined", () => {
		// Arrange
		const manager = new InfoDataManager();

		// Act / Assert
		expect(manager.getAgentJoinedInfoContent()).toBe("Agent has joined the chat");
		expect(manager.getAgentLeftInfoContent()).toBe("Agent has left the chat");
		expect(manager.getUserLeftInfoContent()).toBe("User has left the chat");
	});

	it("uses localized strings when available", () => {
		// Arrange
		setLocale({ agentJoinedChat: "Anslöt", agentLeftChat: "Lämnade", userLeftChat: "Användaren lämnade chatten" });
		const manager = new InfoDataManager();

		// Act / Assert
		expect(manager.getAgentJoinedInfoContent()).toBe("Anslöt");
		expect(manager.getAgentLeftInfoContent()).toBe("Lämnade");
		expect(manager.getUserLeftInfoContent()).toBe("Användaren lämnade chatten");
	});

	it("falls back when a localized string is not a string", () => {
		// Arrange
		setLocale({ agentJoinedChat: 42 });
		const manager = new InfoDataManager();

		// Act / Assert
		expect(manager.getAgentJoinedInfoContent()).toBe("Agent has joined the chat");
	});

	it.each([
		["participantJoinEvent", "agent", "Agent has joined the chat"],
		["participantLeaveEvent", "agent", "Agent has left the chat"],
		["participantLeaveEvent", "visitor", "User has left the chat"],
		["participantLeaveEvent", undefined, "Agent has left the chat"],
		["participantJoinEvent", "visitor", null],
		["typingEvent", "agent", null],
	])("returns info for %s from %s", (type, role, expected) => {
		// Arrange
		const manager = new InfoDataManager();

		// Act / Assert
		expect(manager.getInfoContentFromType(type, role)).toBe(expected);
	});
});
