class PuzzelAgentJoinedHandler implements MetaDataHandler {
	public constructor(
		private chatHandler: ChatHandlerInterface
	) {
	}

	public handleMetaDataEvent(data: PuzzelMetaDataEvent): void {
		if (!data || data.senderRole !== "agent" || data.type !== "participantJoinEvent") {
            return;
        }

		this.chatHandler.addAgentJoinedInfo();
	}
}

export default PuzzelAgentJoinedHandler;