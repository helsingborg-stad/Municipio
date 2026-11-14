import Feedback from "./feedback";

describe("Feedback", () => {
	const FILLED = "material-symbols--filled";

	const createMessage = (initialData: any = null) => {
		const container = document.createElement("div");
		let data = initialData;
		return {
			container,
			getData: jest.fn(() => data),
			setData: jest.fn((newData: any) => {
				data = newData;
			}),
			getMessageContainer: jest.fn(() => container),
		};
	};

	const setup = (initialData: any = null) => {
		const message = createMessage(initialData);
		const chat = { updateMessage: jest.fn() };
		const feedbackApi = { postStat: jest.fn() };
		const likeButton = document.createElement("span");
		const dislikeButton = document.createElement("span");
		new Feedback(chat as any, message as any, likeButton, dislikeButton, feedbackApi as any);
		return { message, chat, feedbackApi, likeButton, dislikeButton };
	};

	const postedStats = (feedbackApi: { postStat: jest.Mock }) =>
		feedbackApi.postStat.mock.calls.map(call => call[0]);

	it("renders no feedback state when the message has no data", () => {
		// Arrange & Act
		const { message, likeButton, dislikeButton } = setup(null);

		// Assert
		expect(message.container.classList.contains("is-liked")).toBe(false);
		expect(message.container.classList.contains("is-disliked")).toBe(false);
		expect(likeButton.classList.contains(FILLED)).toBe(false);
		expect(dislikeButton.classList.contains(FILLED)).toBe(false);
	});

	it("restores a previously liked state from message data", () => {
		// Arrange & Act
		const { message, likeButton, dislikeButton } = setup({ liked: true });

		// Assert
		expect(message.container.classList.contains("is-liked")).toBe(true);
		expect(likeButton.classList.contains(FILLED)).toBe(true);
		expect(dislikeButton.classList.contains(FILLED)).toBe(false);
	});

	it("restores a previously disliked state from message data", () => {
		// Arrange & Act
		const { message, likeButton, dislikeButton } = setup({ liked: false });

		// Assert
		expect(message.container.classList.contains("is-disliked")).toBe(true);
		expect(dislikeButton.classList.contains(FILLED)).toBe(true);
		expect(likeButton.classList.contains(FILLED)).toBe(false);
	});

	it("likes a message, persists it and posts a like stat", () => {
		// Arrange
		const { message, chat, feedbackApi, likeButton } = setup({ foo: "bar" });

		// Act
		likeButton.click();

		// Assert
		expect(message.setData).toHaveBeenCalledWith({ foo: "bar", liked: true });
		expect(chat.updateMessage).toHaveBeenCalledWith(message);
		expect(message.container.classList.contains("is-liked")).toBe(true);
		expect(likeButton.classList.contains(FILLED)).toBe(true);
		expect(postedStats(feedbackApi)).toEqual(["like"]);
	});

	it("toggles a like off on second click and posts unlike", () => {
		// Arrange
		const { message, feedbackApi, likeButton } = setup();

		// Act
		likeButton.click();
		likeButton.click();

		// Assert
		expect(message.getData()).toEqual({ liked: null });
		expect(message.container.classList.contains("is-liked")).toBe(false);
		expect(likeButton.classList.contains(FILLED)).toBe(false);
		expect(postedStats(feedbackApi)).toEqual(["like", "unlike"]);
	});

	it("switches from like to dislike and posts dislike and unlike", () => {
		// Arrange
		const { message, feedbackApi, likeButton, dislikeButton } = setup({ liked: true });

		// Act
		dislikeButton.click();

		// Assert
		expect(message.getData()).toEqual({ liked: false });
		expect(message.container.classList.contains("is-liked")).toBe(false);
		expect(message.container.classList.contains("is-disliked")).toBe(true);
		expect(likeButton.classList.contains(FILLED)).toBe(false);
		expect(dislikeButton.classList.contains(FILLED)).toBe(true);
		expect(postedStats(feedbackApi)).toEqual(["dislike", "unlike"]);
	});

	it("switches from dislike to like and posts like and undislike", () => {
		// Arrange
		const { feedbackApi, likeButton } = setup({ liked: false });

		// Act
		likeButton.click();

		// Assert
		expect(postedStats(feedbackApi)).toEqual(["like", "undislike"]);
	});

	it("toggles a dislike off on second click and posts undislike", () => {
		// Arrange
		const { message, feedbackApi, dislikeButton } = setup();

		// Act
		dislikeButton.click();
		dislikeButton.click();

		// Assert
		expect(message.container.classList.contains("is-disliked")).toBe(false);
		expect(postedStats(feedbackApi)).toEqual(["dislike", "undislike"]);
	});

	it("fills the hovered button on mouseenter and restores state on mouseleave", () => {
		// Arrange
		const { likeButton, dislikeButton } = setup();

		// Act
		likeButton.dispatchEvent(new MouseEvent("mouseenter"));
		dislikeButton.dispatchEvent(new MouseEvent("mouseenter"));
		const filledWhileHovering = [
			likeButton.classList.contains(FILLED),
			dislikeButton.classList.contains(FILLED),
		];
		likeButton.dispatchEvent(new MouseEvent("mouseleave"));
		dislikeButton.dispatchEvent(new MouseEvent("mouseleave"));

		// Assert
		expect(filledWhileHovering).toEqual([true, true]);
		expect(likeButton.classList.contains(FILLED)).toBe(false);
		expect(dislikeButton.classList.contains(FILLED)).toBe(false);
	});
});
