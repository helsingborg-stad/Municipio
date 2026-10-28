class PuzzelFactory implements ChatIntegrationFactory {
    private static createPuzzel: CreateIntegrationFunction | null = null;

    public async create(chat: any, actionFactory: ChatActionFactoryInterface): Promise<ChatIntegrationInterface|false> {
        if (!this.canRun()) {
            return false;
        }

        if (!PuzzelFactory.createPuzzel) {
            const { createPuzzel } = await import("./createPuzzel");
            PuzzelFactory.createPuzzel = createPuzzel;
        }

        return PuzzelFactory.createPuzzel(chat, actionFactory);
    }

    public canRun(): boolean {
        return true;
    }
}

export default PuzzelFactory;