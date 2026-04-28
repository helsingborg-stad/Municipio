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

		for await (const event of this.session.ask(message)) {
			console.log(event.type, event);
			switch (event.type) {
				case "text":
					this.chat.editMessage(event.content, pendingMessage);
					break;
				case "tool_call":
					console.log("Using tools...");
					break;
				case "done":
					console.log("Done");
					break;
			}
		}
	}
}

export default Chat;
