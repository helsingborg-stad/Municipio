class SendPersistentState {
    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public execute(conversationId: string, state: Record<string, unknown>): Promise<void> {

        return Promise.resolve();
    }
}