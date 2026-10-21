class PuzzelChatStatusHandler implements PuzzelChatStatusHandlerInterface {
    public getStatus(statuses: PuzzelConversationStatusResponse[]): string|null {
        if (!statuses || statuses.length === 0) {
            return null;
        }
        const currentStatus = statuses[statuses.length - 1];
        return currentStatus.status;
    }
}

export default PuzzelChatStatusHandler;