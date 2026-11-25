import BannerDataManager from "./bannerDataManager";

describe("BannerDataManager", () => {
	const setLocale = (value: unknown) => {
		Object.defineProperty(globalThis, "municipioChatLocale", { value, configurable: true, writable: true });
	};

	afterEach(() => {
		delete (globalThis as any).municipioChatLocale;
	});

	it("uses fallback strings when no locale is defined", () => {
		// Arrange
		const manager = new BannerDataManager();

		// Act / Assert
		expect(manager.getQueueBannerContent(3)).toBe("Queue position: 3");
		expect(manager.getChatInactiveBannerContent()).toBe("The agent has left and the chat is now inactive");
		expect(manager.getErrorBannerContent()).toBe("Something went wrong, please try again later");
	});

	it("uses localized strings when available", () => {
		// Arrange
		setLocale({ queuePosition: "Köplats", agentLeftChatInactive: "Inaktiv", errorMessage: "Något gick fel" });
		const manager = new BannerDataManager();

		// Act / Assert
		expect(manager.getQueueBannerContent(2)).toBe("Köplats: 2");
		expect(manager.getChatInactiveBannerContent()).toBe("Inaktiv");
		expect(manager.getErrorBannerContent()).toBe("Något gick fel");
	});

	it("falls back when a localized string is empty", () => {
		// Arrange
		setLocale({ queuePosition: "" });
		const manager = new BannerDataManager();

		// Act / Assert
		expect(manager.getQueueBannerContent(1)).toBe("Queue position: 1");
	});
});
