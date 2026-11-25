class PuzzelAgentLeftHandler implements MetaDataHandler {
	public constructor(
		private chatHandler: ChatHandlerInterface,
		private bannerDataManager: BannerDataManagerInterface,
	) {
	}

	public handleMetaDataEvent(data: PuzzelMetaDataEvent): boolean {
		if (!data || data.senderRole !== "agent" || data.type !== "participantLeaveEvent") {
            return false;
        }

		this.chatHandler.setBannerContent(this.bannerDataManager.getChatInactiveBannerContent());
		this.chatHandler.displayBanner(true);
		this.chatHandler.addAgentLeftInfo();
		this.chatHandler.endSession();
        return true;
	}
}

export default PuzzelAgentLeftHandler;