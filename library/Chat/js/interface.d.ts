interface ChatConfiguration {
	chatAssistant: string | null;
	feedbackTemplate: HTMLTemplateElement | null;
	greetingsPhrase: string | null;
	isPersistentChat: boolean;
	newChatButtonElement: HTMLElement | null;
}

interface ChatServices {
	chatSessionFactory: ChatSessionFactory;
	feedbackApi: FeedbackApi;
	feedbackFactory: FeedbackFactory;
}
