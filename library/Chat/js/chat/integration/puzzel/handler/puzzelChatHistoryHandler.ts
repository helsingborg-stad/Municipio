class PuzzelChatHistoryHandler implements PuzzelChatHistoryHandlerInterface {
    public constructor(private chatHandler: ChatHandlerInterface) {}

    public load(conversationEvents: PuzzelConversationEvent[], metaDataEvents: PuzzelMetaDataEvent[]): void {
        const filteredMetaDataEvents = this.filterMetaDataEvents(metaDataEvents);

        const allEventsSorted = [...conversationEvents, ...filteredMetaDataEvents].sort((a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime());
        allEventsSorted.forEach(event => {
            if (event.type === 'chat') {
                const message = typeof event.message === 'string' ? event.message : '';

                this.chatHandler.addMessage(
                    message,
                    event.senderRole === 'agent' ? true : false,
                );
            } else {
                this.chatHandler.addInfoFromType(event.type, event.senderRole)
            }
        });
    }

    private filterMetaDataEvents(metaDataEvents: PuzzelMetaDataEvent[]): PuzzelMetaDataEvent[] {
        const filteredMetaDataEvents = metaDataEvents.filter(
            (event) => event.senderRole !== "visitor" && event.type !== "typingEvent",
        );

        return filteredMetaDataEvents;
    }
}

export default PuzzelChatHistoryHandler;