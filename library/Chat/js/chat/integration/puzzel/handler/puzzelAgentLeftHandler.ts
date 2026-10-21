class PuzzelAgentLeftHandler implements MetaDataHandler {
	public constructor(
		private chatHandler: ChatHandlerInterface,
	) {
	}

	public handleMetaDataEvent(data: PuzzelMetaDataEvent): boolean {
		if (!data || data.senderRole !== "agent" || data.type !== "participantLeaveEvent") {
            return false;
        }

		this.chatHandler.addAgentLeftInfo();
		this.chatHandler.disable();
        return true;
	}
}

export default PuzzelAgentLeftHandler;