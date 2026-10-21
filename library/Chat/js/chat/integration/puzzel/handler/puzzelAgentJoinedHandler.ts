class PuzzelAgentJoinedHandler implements MetaDataHandler {
	public constructor(
		private chatHandler: ChatHandlerInterface
	) {
	}

	public handleMetaDataEvent(data: PuzzelMetaDataEvent): boolean {
		if (!data || data.senderRole !== "agent" || data.type !== "participantJoinEvent") {
            return false;
        }

		this.chatHandler.addAgentJoinedInfo();
        return true;
	}
}

export default PuzzelAgentJoinedHandler;