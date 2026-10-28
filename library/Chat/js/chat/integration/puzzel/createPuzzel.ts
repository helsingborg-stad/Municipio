import GetConversation from "./api/getConversation";
import GetConversationStatus from "./api/getConversationStatus";
import GetMetaData from "./api/getMetaData";
import SendMetadata from "./api/sendMetadata";
import SendUserMessage from "./api/sendUserMessage";
import StartConversation from "./api/startConversation";
import ConversationStorageManager from "./authentication/conversationStorageManager";
import PuzzelAuthenticator from "./authentication/puzzelAuthenticator";
import ChatHandler from "./handler/chatHandler";
import PuzzelAgentJoinedHandler from "./handler/puzzelAgentJoinedHandler";
import PuzzelAgentLeftHandler from "./handler/puzzelAgentLeftHandler";
import PuzzelChatHistoryHandler from "./handler/puzzelChatHistoryHandler";
import PuzzelChatStatusHandler from "./handler/puzzelChatStatusHandler";
import PuzzelIncomingMessageHandler from "./handler/puzzelIncomingMessageHandler";
import PuzzelOutgoingChatHandler from "./handler/puzzelOutgoingChatHandler";
import PuzzelTypingIndicatorHandler from "./handler/puzzelTypingIndicatorHandler";
import Puzzel from "./puzzel";
import PuzzelConfig from "./puzzelConfig";
import PuzzelConversationManager from "./puzzelConversationManager";

export function createPuzzel(
    chat: any,
    actionFactory: ChatActionFactoryInterface
): ChatIntegrationInterface {
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

    return new Puzzel(
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
        chatHandler,
        actionFactory
    );
}