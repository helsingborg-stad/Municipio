class PuzzelChatStatusHandler implements PuzzelChatStatusHandlerInterface {
    public getCurrentStatus(statuses: PuzzelConversationStatusResponse[]): string|null {
        return this.getStatus(statuses)?.status ?? null;
    }

    public getQueuePosition(statuses: PuzzelConversationStatusResponse[]): number|null {
        return this.getStatus(statuses)?.queuePosition ?? null;
    }

    public getStatus(statuses: PuzzelConversationStatusResponse[]): PuzzelConversationStatusResponse|null {
        if (!statuses || statuses.length === 0) {
            return null;
        }
        const lastStatus = statuses[statuses.length - 1];
        return lastStatus;
    }
}

export default PuzzelChatStatusHandler;