class PuzzelConversationManager implements PuzzelConversationManagerInterface {
    private conversationId: string | undefined = undefined;

    public constructor(
        private startConversationApi: StartConversationInterface,
        private sendMetaDataApi: SendMetadataInterface,
        private listenForMetaDataApi: ListenForMetaDataInterface,
        private listenForConversationApi: ListenForConversationInterface,
        private sendUserMessageApi: SendUserMessageInterface,
        private getConversationStatusApi: GetConversationStatusInterface,
        private conversationStorage: ConversationStorageManagerInterface,
    ) {
    }

    public getConversationId(): string {
        this.conversationId ??= this.conversationStorage.getConversationId() ?? crypto.randomUUID();

        return this.conversationId;
    }

    public resetConversationId(): void {
        this.conversationId = undefined;
    }

    public stopListening(): void {
        this.stopListeningForMetaData();
        this.stopListeningForConversation();
    }

    public stopListeningForMetaData(): void {
        this.listenForMetaDataApi.stopListening(this.getConversationId());
    }

    public stopListeningForConversation(): void {
        this.listenForConversationApi.stopListening(this.getConversationId());
    }

    public async getConversationStatus(): Promise<PuzzelConversationStatusResponse[]> {
        return await this.getConversationStatusApi.execute(this.getConversationId());
    }

    public async getCurrentConversation(): Promise<PuzzelConversationEvent[]> {
        return await this.listenForConversationApi.execute(this.getConversationId());
    }

    public async getCurrentMetaData(): Promise<PuzzelMetaDataEvent[]> {
        return await this.listenForMetaDataApi.execute(this.getConversationId());
    }

    public async sendUserJoinEventToConversation(): Promise<void> {
        await this.sendMetaDataApi.execute(this.getConversationId(), "participantJoinEvent");
    }

    public async sendUserTypingEvent(): Promise<void> {
        await this.sendMetaDataApi.execute(this.getConversationId(), "typingEvent");
    }

    public async startConversation(): Promise<RouteConversationResponse> {
        const conversationResponse = await this.startConversationApi.execute(this.getConversationId());

        return conversationResponse;
    }

    public async sendUserMessage(message: string): Promise<void> {
        await this.sendUserMessageApi.execute(this.getConversationId(), message);
    }

    public async listenForMetaData(callback: (data: PuzzelMetaDataEvent) => void): Promise<void> {
        await this.listenForMetaDataApi.listen(this.getConversationId(), callback);
    }

    public async listenForConversation(callback: (data: PuzzelConversationEvent) => void): Promise<void> {
        await this.listenForConversationApi.listen(this.getConversationId(), callback);
    }
}

export default PuzzelConversationManager;