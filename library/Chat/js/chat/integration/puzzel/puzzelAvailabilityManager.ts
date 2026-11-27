class PuzzelAvailabilityManager implements PuzzelAvailabilityManagerInterface {
	public constructor(
		private getChatOpenApi: GetChatOpenInterface,
		private getQueueInfoApi: GetQueueInfoInterface,
	) {
	}

	public async isAvailable(): Promise<boolean> {
		const [isChatOpen, hasLoggedInAgents] = await Promise.all([
			this.getChatOpenApi.execute(),
			this.getQueueInfoApi.execute(),
		]);


		return (isChatOpen ?? true) && hasLoggedInAgents;
	}
}

export default PuzzelAvailabilityManager;