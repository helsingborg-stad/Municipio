class Chat {
	private session: ChatSession | null = null;

	constructor(
		private readonly sessionFactory: ChatSessionFactory,
		private readonly chat: any,
	) {}

	public init(): void {
		this.session = this.sessionFactory.create(null);
        this.subscribeToUserMessages();
	}

    private subscribeToUserMessages(): void {
        this.chat.subscribeToUserMessages((message: any) => {
            const content = message.getContent();
            this.sendMessage(content);
        });
    }

	public async sendMessage(message: string): Promise<void> {
		if (!this.session) return;

		const pendingMessage = this.chat.addPendingMessage();
		this.chat.disableSend();
		for await (const event of this.session.ask(message)) {
			switch (event.type) {
				case "text":
					this.chat.editMessage(event.content, pendingMessage);
					break;
				case "tool_call":
					console.log("Using tools...");
					break;
				case "done":
					console.log("Done");
					this.chat.enableSend();
					break;
			}
		}
	}
}

export default Chat;
