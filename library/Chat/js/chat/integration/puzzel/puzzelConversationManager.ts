class PuzzelConversationManager implements PuzzelConversationManagerInterface {
    private conversationId: string | undefined = undefined;

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
        private startConversationApi: StartConversationInterface,
        private sendUserJoinedConversationApi: SendUserJoinedConversationInterface,
        private listenForMetaDataApi: ListenForMetaDataInterface,
        private listenForConversationApi: ListenForConversationInterface,
        private sendUserMessageApi: SendUserMessageInterface,
    ) {
    }

    public getConversationId(): string {
        this.conversationId ??= crypto.randomUUID();
        return this.conversationId;
    }

    public async sendUserJoinEventToConversation(): Promise<void> {
        await this.sendUserJoinedConversationApi.execute(this.getConversationId());
    }

    public async startConversation(): Promise<RouteConversationResponse> {
        return this.startConversationApi.execute(this.getConversationId());
    }

    public async sendUserMessage(message: string): Promise<void> {
        await this.sendUserMessageApi.execute(this.getConversationId(), message);
    }

    public async listenForMetaData(callback: (data: any) => void): Promise<void> {
        await this.listenForMetaDataApi.listen(this.getConversationId(), callback);
    }

    public async listenForConversation(callback: (data: any) => void): Promise<void> {
        await this.listenForConversationApi.listen(this.getConversationId(), callback);
    }
}

export default PuzzelConversationManager;