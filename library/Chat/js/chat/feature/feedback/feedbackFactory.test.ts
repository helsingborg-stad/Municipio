import FeedbackFactory from "./feedbackFactory";

describe("FeedbackFactory", () => {
	const createFeedbackTemplate = () => {
		const template = document.createElement("template");
		template.innerHTML = `
			<div class="municipio-chat__feedback">
				<span role="button" data-js-chat-message-like-button>thumb_up</span>
				<span role="button" data-js-chat-message-dislike-button>thumb_down</span>
			</div>
		`;
		return template;
	};

	const createMessage = (data: any = null) => {
		const container = document.createElement("div");
		return {
			container,
			getData: jest.fn(() => data),
			setData: jest.fn((newData: any) => {
				data = newData;
			}),
			getMessageContainer: jest.fn(() => container),
		};
	};

	it("appends the feedback markup to the message container", () => {
		// Arrange
		const template = createFeedbackTemplate();
		const factory = new FeedbackFactory({ updateMessage: jest.fn() } as any, template, { postStat: jest.fn() } as any);
		const message = createMessage();

		// Act
		factory.create(message);

		// Assert
		expect(message.container.querySelector(".municipio-chat__feedback")).not.toBeNull();
		expect(message.container.querySelector("[data-js-chat-message-like-button]")).not.toBeNull();
		expect(message.container.querySelector("[data-js-chat-message-dislike-button]")).not.toBeNull();
		expect(template.content.querySelector("[data-js-chat-message-like-button]")).not.toBeNull();
	});

	it("wires the appended like button to the feedback api and chat", () => {
		// Arrange
		const chat = { updateMessage: jest.fn() };
		const feedbackApi = { postStat: jest.fn() };
		const factory = new FeedbackFactory(chat as any, createFeedbackTemplate(), feedbackApi as any);
		const message = createMessage();
		factory.create(message);

		// Act
		(message.container.querySelector("[data-js-chat-message-like-button]") as HTMLElement).click();

		// Assert
		expect(feedbackApi.postStat).toHaveBeenCalledWith("like");
		expect(chat.updateMessage).toHaveBeenCalledWith(message);
		expect(message.container.classList.contains("is-liked")).toBe(true);
	});

	it("creates independent feedback for each message", () => {
		// Arrange
		const factory = new FeedbackFactory({ updateMessage: jest.fn() } as any, createFeedbackTemplate(), { postStat: jest.fn() } as any);
		const first = createMessage();
		const second = createMessage();
		factory.create(first);
		factory.create(second);

		// Act
		(second.container.querySelector("[data-js-chat-message-dislike-button]") as HTMLElement).click();

		// Assert
		expect(first.container.classList.contains("is-disliked")).toBe(false);
		expect(second.container.classList.contains("is-disliked")).toBe(true);
	});

	it("adds nothing and returns null when the chat has no feedback template", () => {
		const factory = new FeedbackFactory({ updateMessage: jest.fn() } as any, null, { postStat: jest.fn() } as any);
		const message = createMessage();

		expect(factory.create(message)).toBeNull();
		expect(message.container.children).toHaveLength(0);
	});

	it("adds nothing and returns null when the template is missing its buttons", () => {
		const template = document.createElement("template");
		template.innerHTML = `<div class="municipio-chat__feedback"></div>`;
		const factory = new FeedbackFactory({ updateMessage: jest.fn() } as any, template, { postStat: jest.fn() } as any);
		const message = createMessage();

		expect(factory.create(message)).toBeNull();
		expect(message.container.children).toHaveLength(0);
	});
});
