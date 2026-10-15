import PuzzelIncomingMessageHandler from "./handler/puzzelIncomingMessageHandler";
import PuzzelOutgoingChatHandler from "./handler/puzzelOutgoingChatHandler";
import PuzzelTypingIndicatorHandler from "./handler/puzzelTypingIndicatorHandler";

class Puzzel {
    private authenticated: AuthenticationResponse | undefined;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
        private puzzelConversationManager: PuzzelConversationManagerInterface,
        private typingIndicatorHandler: PuzzelTypingIndicatorHandlerInterface,
        private incomingMessageHandler: PuzzelIncomingMessageHandlerInterface,
        private outgoingChatHandler: PuzzelOutgoingChatHandlerInterface,
        private chat: any,
        private isPersistent: boolean,
    ) {
    }

    public async init(): Promise<void> {
        this.authenticated = await this.puzzelAuthenticator.authenticate();

        if (!this.authenticated) {
            return;
        }

        await this.puzzelConversationManager.startConversation();
        await this.puzzelConversationManager.sendUserJoinEventToConversation();

        this.outgoingChatHandler.register();

        await this.puzzelConversationManager.listenForMetaData((data) => {
            this.typingIndicatorHandler.handleMetaDataEvent(data);

            console.log("MetaData received:", data);
        });

        await this.puzzelConversationManager.listenForConversation((data) => {
            console.log("Conversation received:", data);
            this.incomingMessageHandler.handleConversationEvent(data);
        });
    }
}

export default Puzzel;