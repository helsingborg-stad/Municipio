class PuzzelAgentJoinedHandler implements MetaDataHandler {
	public constructor(
		private chat: any,
	) {
	}

	public handleMetaDataEvent(data: PuzzelMetaDataEvent): void {
		if (!data || data.senderRole !== "agent" || data.type !== "participantJoinEvent") {
            return;
        }

		// TODO: Translation
		this.chat.addInfo("Agent has joined the chat.");
	}
}

export default PuzzelAgentJoinedHandler;