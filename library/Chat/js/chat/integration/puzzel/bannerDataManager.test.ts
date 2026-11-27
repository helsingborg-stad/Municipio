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

	it("adds the new chat button from the template to the inactive and error banners", () => {
		// Arrange
		const template = document.createElement("template");
		template.innerHTML = `<button data-js-new-chat>New chat</button>`;
		const manager = new BannerDataManager(template);

		// Act
		const banners = [manager.getChatInactiveBannerContent(), manager.getErrorBannerContent()] as DocumentFragment[];

		// Assert
		expect(banners[0].textContent).toBe("The agent has left and the chat is now inactiveNew chat");
		expect(banners[1].textContent).toBe("Something went wrong, please try again laterNew chat");
		banners.forEach((banner) => expect(banner.querySelector("[data-js-new-chat]")).not.toBeNull());
		expect(template.content.querySelectorAll("[data-js-new-chat]")).toHaveLength(1);
	});
});
