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
		let contentAdded = false;

		for await (const event of this.session.ask(message)) {
			switch (event.type) {
				case "text":
					console.log("Received text:", event.content);
					this.chat.editMessage(event.content, pendingMessage);
					contentAdded = true;
					break;
				case "tool_call":
					console.log("Using tools...", event);
					contentAdded = true;
					break;
				case "done":
					console.log("Done");
					this.chat.enableSend();
					if (!contentAdded) {
						//TODO: Handle errors
						this.chat.deleteMessage(pendingMessage);
					}

					break;
			}
		}
	}
}

export default Chat;
