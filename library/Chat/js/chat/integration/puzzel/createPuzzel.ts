import GetConversation from "./api/getConversation";
import GetChatOpen from "./api/getChatOpen";
import GetQueueInfo from "./api/getQueueInfo";
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
import PuzzelAvailabilityManager from "./puzzelAvailabilityManager";
import PuzzelConversationManager from "./puzzelConversationManager";

export function createPuzzel(
    chat: any,
    actionFactory: ChatActionFactoryInterface,
    puzzelConfig: PuzzelConfigInterface,
    _name: string,
): ChatIntegrationInterface {
    const conversationStorageManager = new ConversationStorageManager();
    const puzzelAuthenticator = new PuzzelAuthenticator(puzzelConfig, conversationStorageManager);

    const puzzelAvailabilityManager = new PuzzelAvailabilityManager(
        new GetChatOpen(puzzelConfig, puzzelAuthenticator),
        new GetQueueInfo(puzzelConfig, puzzelAuthenticator),
    );

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
        puzzelAvailabilityManager,
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