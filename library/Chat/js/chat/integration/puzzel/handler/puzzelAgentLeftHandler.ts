class PuzzelAgentLeftHandler implements MetaDataHandler {
	public constructor(
		private chatHandler: ChatHandlerInterface,
		private bannerDataManager: BannerDataManagerInterface,
		private infoDataManager: InfoDataManagerInterface,
	) {
	}

	public handleMetaDataEvent(data: PuzzelMetaDataEvent): boolean {
		if (!data || data.senderRole !== "agent" || data.type !== "participantLeaveEvent") {
            return false;
        }

		this.chatHandler.setBannerContent(this.bannerDataManager.getAgentLeftContent());
		this.chatHandler.displayBanner(true);
		this.chatHandler.addInfo(this.infoDataManager.getAgentLeftInfoContent());
		this.chatHandler.endSession();
        return true;
	}
}

export default PuzzelAgentLeftHandler;