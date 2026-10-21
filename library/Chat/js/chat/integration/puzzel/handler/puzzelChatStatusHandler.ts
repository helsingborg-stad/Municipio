class PuzzelChatStatusHandler implements PuzzelChatStatusHandlerInterface {
    public getStatus(statuses: PuzzelConversationStatusResponse[]): string {
        const currentStatus = statuses[statuses.length - 1];

        return currentStatus.status;
    }
}

export default PuzzelChatStatusHandler;