import PuzzelConfig from "./puzzelConfig";

describe("PuzzelConfig", () => {
	it("exposes constructor values and default Puzzel urls", () => {
		// Arrange
		const config = new PuzzelConfig("customer", "queue", "client", "visual", "time", "Name", "avatar.png");

		// Act / Assert
		expect(config.getCustomerId()).toBe("customer");
		expect(config.getChatQueueKey()).toBe("queue");
		expect(config.getClientId()).toBe("client");
		expect(config.getVisualQueueId()).toBe("visual");
		expect(config.getTimeId()).toBe("time");
		expect(config.getName()).toBe("Name");
		expect(config.getAvatar()).toBe("avatar.png");
		expect(config.getAuthenticationUrl()).toBe("https://app-consumeridp.puzzel.com");
		expect(config.getCommunicationUrl()).toBe("https://app-commsrv.puzzel.com");
	});

	it("allows overriding the service urls", () => {
		// Arrange
		const config = new PuzzelConfig("c", "q", "cl", "v", "t", "n", "a", "https://auth", "https://comm");

		// Act / Assert
		expect(config.getAuthenticationUrl()).toBe("https://auth");
		expect(config.getCommunicationUrl()).toBe("https://comm");
	});
});
