import Popover from "./popover";

describe("Popover", () => {
	let changeListener: ((event: { matches: boolean }) => void) | null;
	let initialMatches: boolean;
	const originalMatchMedia = window.matchMedia;

	beforeEach(() => {
		changeListener = null;
		initialMatches = false;
		Object.defineProperty(window, "matchMedia", {
			configurable: true,
			writable: true,
			value: jest.fn((query: string) => ({
				matches: initialMatches,
				media: query,
				addEventListener: jest.fn((_type: string, listener: any) => {
					changeListener = listener;
				}),
				removeEventListener: jest.fn(),
			})),
		});
	});

	afterEach(() => {
		Object.defineProperty(window, "matchMedia", {
			configurable: true,
			writable: true,
			value: originalMatchMedia,
		});
	});

	const setup = () => {
		const popoverDetail = { popover: { setCover: jest.fn() } };
		const chatContainer = document.createElement("div");
		const messageArea = document.createElement("div");
		new Popover(popoverDetail, chatContainer, messageArea);
		return { popoverDetail, chatContainer, messageArea };
	};

	it("listens to the mobile breakpoint media query", () => {
		// Arrange & Act
		setup();

		// Assert
		expect(window.matchMedia).toHaveBeenCalledWith("(max-width: 768px)");
		expect(changeListener).not.toBeNull();
	});

	it("does not touch styles or cover on desktop initialisation", () => {
		// Arrange
		initialMatches = false;

		// Act
		const { popoverDetail, chatContainer, messageArea } = setup();

		// Assert
		expect(popoverDetail.popover.setCover).not.toHaveBeenCalled();
		expect(chatContainer.style.width).toBe("");
		expect(messageArea.style.height).toBe("");
	});

	it("covers the viewport and stretches the chat on mobile initialisation", () => {
		// Arrange
		initialMatches = true;

		// Act
		const { popoverDetail, chatContainer, messageArea } = setup();

		// Assert
		expect(popoverDetail.popover.setCover).toHaveBeenCalledWith(true);
		expect(chatContainer.style.width).toBe("100%");
		expect(chatContainer.style.height).toBe("100%");
		expect(messageArea.style.height).toBe("100%");
	});

	it("resets cover and styles when going from mobile to desktop", () => {
		// Arrange
		initialMatches = true;
		const { popoverDetail, chatContainer, messageArea } = setup();

		// Act
		changeListener!({ matches: false });

		// Assert
		expect(popoverDetail.popover.setCover).toHaveBeenLastCalledWith(false);
		expect(chatContainer.style.width).toBe("");
		expect(chatContainer.style.height).toBe("");
		expect(messageArea.style.height).toBe("");
	});

	it("applies cover when going from desktop to mobile", () => {
		// Arrange
		const { popoverDetail, chatContainer } = setup();

		// Act
		changeListener!({ matches: true });

		// Assert
		expect(popoverDetail.popover.setCover).toHaveBeenCalledTimes(1);
		expect(popoverDetail.popover.setCover).toHaveBeenCalledWith(true);
		expect(chatContainer.style.width).toBe("100%");
	});

	it("ignores change events that do not change the breakpoint state", () => {
		// Arrange
		initialMatches = true;
		const { popoverDetail } = setup();

		// Act
		changeListener!({ matches: true });

		// Assert
		expect(popoverDetail.popover.setCover).toHaveBeenCalledTimes(1);
	});
});
