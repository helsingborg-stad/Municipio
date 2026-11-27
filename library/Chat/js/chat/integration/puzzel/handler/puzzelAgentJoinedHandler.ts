class PuzzelAgentJoinedHandler implements MetaDataHandler {
	public constructor(
		private chatHandler: ChatHandlerInterface,
		private infoDataManager: InfoDataManagerInterface,
	) {
	}

	public handleMetaDataEvent(data: PuzzelMetaDataEvent): boolean {
		if (!data || data.senderRole !== "agent" || data.type !== "participantJoinEvent") {
            return false;
        }

		this.chatHandler.displayBanner(false);
		this.chatHandler.addInfo(this.infoDataManager.getAgentJoinedInfoContent());
		this.chatHandler.enable();
        return true;
	}
}

export default PuzzelAgentJoinedHandler;