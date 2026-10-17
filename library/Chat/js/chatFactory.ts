import Chat from "./chat/chat";
import FeedbackFactory from "./chat/feedbackFactory";
import GreetingPhrase from "./chat/greetingPhrase";
import NewChatSessionButton from "./chat/newChatSessionButton";
import AiChatIntegration from "./integrations/ai/AiChatIntegration";
import AiFeedbackApi from "./integrations/ai/AiFeedbackApi";
import { AiChatSessionFactory } from "./integrations/ai/AiChatSessionFactory";
import { createMarkdownRenderer } from "./shared/markdownRenderer";

class ChatFactory {
	public init(chat: any): void {
		const chatElement = chat.getElement() as HTMLElement;
		const greetingsPhrase = chatElement.dataset.jsChatGreetingsPhrase || null;
		const feedbackTemplate = chatElement.querySelector("[data-js-chat-feedback]") as HTMLTemplateElement | null;
		const chatAssistant = chatElement.dataset.jsChatAssistant || null;
		const persistentAttribute = chatElement.getAttribute("data-js-chat-persistent");
		const newChatButtonElement = chatElement.querySelector("[data-js-chat-new]") as HTMLElement | null;
		const markdownRenderer = createMarkdownRenderer();
		const chatSessionFactory = new AiChatSessionFactory(wpApiSettings.root);
		const feedbackApi = new AiFeedbackApi(wpApiSettings.root);
		const feedbackFactory = new FeedbackFactory(chat, feedbackTemplate as HTMLTemplateElement, feedbackApi);
		const chatSurface = new Chat(chat, feedbackFactory);
		const chatUiApi = chatSurface.getUiApi();

		chatUiApi.getMessages().forEach((message: any, index: number) => {
			if (!message.getIsReply()) {
				return;
			}

			if (index === 0 && greetingsPhrase === message.getContent()) {
				return;
			}

			chatUiApi.attachFeedback(message);
		});

		if (greetingsPhrase) {
			new GreetingPhrase(chat, greetingsPhrase);
		}

		const aiIntegration = new AiChatIntegration({
			subscribeToUserMessages: (handler) => chatSurface.onUserMessage(handler),
			uiApi: chatUiApi,
			sessionFactory: chatSessionFactory,
			feedbackApi,
			renderMarkdown: markdownRenderer,
			assistantName: chatAssistant,
			persistSession:
				persistentAttribute !== null && persistentAttribute !== "false",
			errorMessage: municipioChatLocale.errorMessage,
		});

		if (newChatButtonElement) {
			new NewChatSessionButton(newChatButtonElement, chatUiApi, () =>
				aiIntegration.createNewChatSession(),
			);
		}

		aiIntegration.init();
	}
}

export default ChatFactory;