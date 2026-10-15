class Puzzel {
    private authenticated: AuthenticationResponse | undefined;
    private isTyping = false;
    private pendingMessage: any = null;
    private typingTimeoutId: ReturnType<typeof setTimeout> | null = null;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
        private puzzelConversationManager: PuzzelConversationManagerInterface,
        private chat: any,
        private isPersistent: boolean,
    ) {
    }

    public async init(): Promise<void> {
        this.authenticated = await this.puzzelAuthenticator.authenticate();

        if (!this.authenticated) {
            return;
        }

        const data = await this.puzzelConversationManager.startConversation();
        await this.puzzelConversationManager.sendUserJoinEventToConversation();

        this.chat.getElement().addEventListener("chat:message-added", (e: any) => {
			const message = e.detail;

			if (message.getIsReply()) {
				return;
			}

            if (message.getContent() && message.getContent().trim() !== "") {
                this.puzzelConversationManager.sendUserMessage(message.getContent());
            }
		});

        this.chat.getElement().addEventListener("chat:typing", (e: any) => {
            this.puzzelConversationManager.sendUserTypingEvent();
        });

        await this.puzzelConversationManager.listenForMetaData((data) => {
            if (data && 
                data.senderRole === "agent" &&
                data.type === "typingEvent"
            ) {
                this.isTyping = true;
                if (!this.pendingMessage) {
                    this.pendingMessage = this.chat.addPendingMessage();
                }

                this.resetTypingTimeout();
            }

            console.log("MetaData received:", data);
        });

        await this.puzzelConversationManager.listenForConversation((data) => {
            console.log("Conversation received:", data);
            if (
                data && 
                data.type === "chat" && 
                data.senderRole === "agent" && 
                data.message && 
                data.message.trim() !== ""
            ) {
                if (this.pendingMessage) {
                    this.chat.editMessage(this.pendingMessage, data.message);
                    this.pendingMessage = null;
                    this.clearTypingState();
                } else {
                    this.clearTypingState();
                    this.chat.addMessage(data.message, true);
                }
            }
        });
    }

    private resetTypingTimeout(): void {
        if (this.typingTimeoutId) {
            clearTimeout(this.typingTimeoutId);
        }

        this.typingTimeoutId = setTimeout(() => {
            if (this.pendingMessage) {
                this.chat.deleteMessage(this.pendingMessage);
            }

            this.pendingMessage = null;
            this.typingTimeoutId = null;
            this.isTyping = false;
        }, 7000);
    }

    private clearTypingState(): void {
        if (this.typingTimeoutId) {
            clearTimeout(this.typingTimeoutId);
            this.typingTimeoutId = null;
        }

        if (this.pendingMessage) {
            this.chat.deleteMessage(this.pendingMessage);
            this.pendingMessage = null;
        }

        this.isTyping = false;
    }
}

export default Puzzel;