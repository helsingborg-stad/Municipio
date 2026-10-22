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
import GetMetaData from "./chat/integration/puzzel/api/getMetaData";
import GetConversation from "./chat/integration/puzzel/api/getConversation";
import SendUserMessage from "./chat/integration/puzzel/api/sendUserMessage";
import PuzzelOutgoingChatHandler from "./chat/integration/puzzel/handler/puzzelOutgoingChatHandler";
import PuzzelIncomingMessageHandler from "./chat/integration/puzzel/handler/puzzelIncomingMessageHandler";
import PuzzelTypingIndicatorHandler from "./chat/integration/puzzel/handler/puzzelTypingIndicatorHandler";
import PuzzelAgentLeftHandler from "./chat/integration/puzzel/handler/puzzelAgentLeftHandler";
import PuzzelAgentJoinedHandler from "./chat/integration/puzzel/handler/puzzelAgentJoinedHandler";
import ConversationStorageManager from "./chat/integration/puzzel/authentication/conversationStorageManager";
import PuzzelChatHistoryHandler from "./chat/integration/puzzel/handler/puzzelChatHistoryHandler";
import ChatHandler from "./chat/integration/puzzel/handler/chatHandler";
import GetConversationStatus from "./chat/integration/puzzel/api/getConversationStatus";
import PuzzelChatStatusHandler from "./chat/integration/puzzel/handler/puzzelChatStatusHandler";


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
			new GetMetaData(puzzelConfig, puzzelAuthenticator),
			new GetConversation(puzzelConfig, puzzelAuthenticator),
			new SendUserMessage(puzzelConfig, puzzelAuthenticator),
			new GetConversationStatus(puzzelConfig, puzzelAuthenticator),
			conversationStorageManager,
		);

		const chatHandler = new ChatHandler(chat);
		const puzzelTypingIndicatorHandler = new PuzzelTypingIndicatorHandler(chatHandler);

		new Puzzel(
			puzzelConfig,
			puzzelAuthenticator,
			puzzelConversationManager,
			conversationStorageManager,
			puzzelTypingIndicatorHandler,
			new PuzzelIncomingMessageHandler(chatHandler, puzzelTypingIndicatorHandler),
			new PuzzelOutgoingChatHandler(chatHandler, puzzelConversationManager),
			new PuzzelAgentLeftHandler(chatHandler),
			new PuzzelAgentJoinedHandler(chatHandler),
			new PuzzelChatHistoryHandler(chatHandler),
			new PuzzelChatStatusHandler(chatHandler, puzzelConversationManager),
			chatHandler
		).init();

		return;
	}
}

export default ChatFactory;