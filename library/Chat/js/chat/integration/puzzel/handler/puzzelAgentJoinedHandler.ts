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
		this.chatHandler.displayBanner(false);
		this.chatHandler.enable();
        return true;
	}
}

export default PuzzelAgentJoinedHandler;