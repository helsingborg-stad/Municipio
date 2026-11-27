class PuzzelChatHistoryHandler implements PuzzelChatHistoryHandlerInterface {
    public constructor(
        private chatHandler: ChatHandlerInterface,
        private infoDataManager: InfoDataManagerInterface,
    ) {}

    public load(conversationEvents: PuzzelConversationEvent[], metaDataEvents: PuzzelMetaDataEvent[]): void {
        const filteredMetaDataEvents = this.filterMetaDataEvents(metaDataEvents);

        if (filteredMetaDataEvents.some(event => event.type === 'participantLeaveEvent')) {
            this.chatHandler.endSession();
        }

        const allEventsSorted = [...conversationEvents, ...filteredMetaDataEvents].sort((a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime());
        allEventsSorted.forEach(event => {
            if (event.type === 'chat') {
                const message = typeof event.message === 'string' ? event.message : '';

                this.chatHandler.addMessage(
                    message,
                    event.senderRole === 'agent' ? true : false,
                );
            } else {
                const info = this.infoDataManager.getInfoContentFromType(event.type, event.senderRole);

                if (info) {
                    this.chatHandler.addInfo(info);
                }
            }
        });

    }

    private filterMetaDataEvents(metaDataEvents: PuzzelMetaDataEvent[]): PuzzelMetaDataEvent[] {
        const filteredMetaDataEvents = metaDataEvents.filter(
            (event) => event.type !== "typingEvent"
                && (event.senderRole !== "visitor" || event.type === "participantLeaveEvent"),
        );

        return filteredMetaDataEvents;
    }
}

export default PuzzelChatHistoryHandler;