import { PUZZEL_CHAT_STATUS } from "../enums";

class PuzzelChatStatusHandler implements PuzzelChatStatusHandlerInterface {
    private latestStatus: PuzzelConversationStatusResponse|null = null;

    public constructor(
        private chatHandler: ChatHandlerInterface,
        private puzzelConversationManager: PuzzelConversationManagerInterface
    ) {
    }

    public getCurrentStatus(statuses: PuzzelConversationStatusResponse[]): string|null {
        return this.getStatus(statuses)?.status ?? null;
    }

    public getQueuePosition(statuses: PuzzelConversationStatusResponse[]): number|null {
        return this.getCurrentStatus(statuses) === PUZZEL_CHAT_STATUS.IN_QUEUE && this.getStatus(statuses)?.queuePosition ? this.getStatus(statuses)!.queuePosition : null;
    }

    public getStatus(statuses: PuzzelConversationStatusResponse[]): PuzzelConversationStatusResponse|null {
        if (!statuses || statuses.length === 0) {
            return null;
        }

        this.latestStatus = statuses[statuses.length - 1];
        return this.latestStatus;
    }

    private handleDisconnected(): void {
        this.chatHandler.disable();
    }

    private handleInQueue(): void {
        this.chatHandler.enable();
    }

    public handleStatusEvent(status: PuzzelConversationStatusResponse): void {
        if (!status) {
            return;
        }

        switch (status.status) {
            case PUZZEL_CHAT_STATUS.DISCONNECTED:
                this.handleDisconnected();
                break;
            case PUZZEL_CHAT_STATUS.IN_QUEUE:
                this.handleInQueue();
                break;
        }
    }
}

export default PuzzelChatStatusHandler;