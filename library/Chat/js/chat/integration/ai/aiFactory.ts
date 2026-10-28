class AiFactory implements ChatIntegrationFactory {
    private static createAi: CreateIntegrationFunction | null = null;

	public async create(chat: any, actionFactory: ChatActionFactoryInterface): Promise<ChatIntegrationInterface|false> {
        if (!this.canRun()) {
            return false;
        }

        if (!AiFactory.createAi) {
            const { createAi } = await import("./createAi");
            AiFactory.createAi = createAi;
        }

        return AiFactory.createAi(chat, actionFactory);
    }

	public canRun(): boolean {
        return true;
    }
}

export default AiFactory;