class PuzzelAgentLeftHandler implements MetaDataHandler {
	public constructor(
		private chat: any,
	) {
	}

	public handleMetaDataEvent(data: PuzzelMetaDataEvent): void {
		if (!data || data.senderRole !== "agent" || data.type !== "participantLeaveEvent") {
            return;
        }

		// TODO: Translation
		this.chat.addInfo("Agent has left the chat.");
		this.chat.disable();
	}
}

export default PuzzelAgentLeftHandler;