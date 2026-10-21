class PuzzelAgentLeftHandler implements MetaDataHandler {
	public constructor(
		private chatHandler: ChatHandlerInterface,
	) {
	}

	public handleMetaDataEvent(data: PuzzelMetaDataEvent): void {
		if (!data || data.senderRole !== "agent" || data.type !== "participantLeaveEvent") {
            return;
        }

		this.chatHandler.addAgentLeftInfo();
		this.chatHandler.disable();
	}
}

export default PuzzelAgentLeftHandler;