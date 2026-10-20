import MarkdownIt from "markdown-it";
import { ChatSessionFactory } from "./chat/ChatSessionFactory";
import Chat from "./chat/chat";
import FeedbackApi from "./chat/feature/feedback/feedbackApi";
import FeedbackFactory from "./chat/feature/feedback/feedbackFactory";
import GreetingPhrase from "./chat/feature/greetingsPhrase/greetingPhrase";
import NewChatSessionButton from "./chat/feature/newChat/newChatSessionButton";
import Puzzel from "./chat/integration/puzzel/puzzel";
import PuzzelConfig from "./chat/integration/puzzel/puzzelConfig";
import PuzzelAuthenticator from "./chat/integration/puzzel/authentication/puzzelAuthenticator";
import PuzzelConversationManager from "./chat/integration/puzzel/puzzelConversationManager";
import StartConversation from "./chat/integration/puzzel/api/startConversation";
import SendMetadata from "./chat/integration/puzzel/api/sendMetadata";
import ListenForMetaData from "./chat/integration/puzzel/api/listenForMetaData";
import ListenForConversation from "./chat/integration/puzzel/api/listenForConversation";
import SendUserMessage from "./chat/integration/puzzel/api/sendUserMessage";
import PuzzelOutgoingChatHandler from "./chat/integration/puzzel/handler/puzzelOutgoingChatHandler";
import PuzzelIncomingMessageHandler from "./chat/integration/puzzel/handler/puzzelIncomingMessageHandler";
import PuzzelTypingIndicatorHandler from "./chat/integration/puzzel/handler/puzzelTypingIndicatorHandler";
import PuzzelAgentLeftHandler from "./chat/integration/puzzel/handler/puzzelAgentLeftHandler";
import PuzzelAgentJoinedHandler from "./chat/integration/puzzel/handler/puzzelAgentJoined";
import ConversationStorageManager from "./chat/integration/puzzel/authentication/conversationStorageManager";

function createMarkdownParser(): MarkdownIt {
	const parser = new MarkdownIt({ html: false, linkify: false, typographer: false });

	parser.validateLink = (url: string): boolean => /^(https?:|mailto:|tel:|\/|#)/i.test(url);

	return parser;
}

class ChatFactory {
	public init(chat: any): void {
		const puzzelConfig = new PuzzelConfig();
		const conversationStorageManager = new ConversationStorageManager();
		const puzzelAuthenticator = new PuzzelAuthenticator(puzzelConfig, conversationStorageManager);
		const puzzelConversationManager = new PuzzelConversationManager(
			new StartConversation(puzzelConfig, puzzelAuthenticator),
			new SendMetadata(puzzelConfig, puzzelAuthenticator),
			new ListenForMetaData(puzzelConfig, puzzelAuthenticator),
			new ListenForConversation(puzzelConfig, puzzelAuthenticator),
			new SendUserMessage(puzzelConfig, puzzelAuthenticator),
			conversationStorageManager,
		);

		const puzzelTypingIndicatorHandler = new PuzzelTypingIndicatorHandler(chat);

		new Puzzel(
			puzzelConfig,
			puzzelAuthenticator,
			puzzelConversationManager,
			conversationStorageManager,
			puzzelTypingIndicatorHandler,
			new PuzzelIncomingMessageHandler(chat, puzzelTypingIndicatorHandler),
			new PuzzelOutgoingChatHandler(puzzelConversationManager, chat),
			new PuzzelAgentLeftHandler(chat),
			new PuzzelAgentJoinedHandler(chat)
		).init();

		return;
		const chatElement = chat.getElement() as HTMLElement;
		const greetingsPhrase = chatElement.dataset.jsChatGreetingsPhrase || null;
		const feedbackTemplate = chatElement.querySelector("[data-js-chat-feedback]") as HTMLTemplateElement | null;
		const chatAssistant = chatElement.dataset.jsChatAssistant || null;
		const newChatButtonElement = chatElement.querySelector("[data-js-chat-new]") as HTMLElement | null;
		const markdownParser = createMarkdownParser();
		const chatSessionFactory = new ChatSessionFactory(wpApiSettings.root);
		const feedbackApi = new FeedbackApi(wpApiSettings.root);
		const feedbackFactory = new FeedbackFactory(chat, feedbackTemplate as HTMLTemplateElement, feedbackApi);

		chat.getMessages().forEach((message: any, index: number) => {
			if (!message.getIsReply()) {
				return;
			}

			if (index === 0 && greetingsPhrase === message.getContent()) {
				return;
			}

			feedbackFactory.create(message);
		});

		if (greetingsPhrase !== null) {
			new GreetingPhrase(chat, greetingsPhrase);
		}

		const chatInstance = new Chat(
			chatSessionFactory,
			chat,
			markdownParser,
			feedbackFactory,
			feedbackApi,
			chatAssistant,
			this.isPersistent(chat),
		);
		
		if (newChatButtonElement !== null) {
			new NewChatSessionButton(newChatButtonElement, chatInstance, chat);
		}

		chatInstance.init();
	}

	private isPersistent(chat: any): boolean {
		const persistentAttribute = chat.getElement().getAttribute("data-js-chat-persistent");

		return persistentAttribute !== null
			&& persistentAttribute.toLowerCase() !== "false";
	}
}

export default ChatFactory;