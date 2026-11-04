class EneoFactory implements ChatIntegrationFactory {
    private static createEneo: CreateIntegrationFunction | null = null;

    public async create(chat: any, actionFactory: ChatActionFactoryInterface, name: string): Promise<ChatIntegrationInterface|false> {
        if (!this.canRun()) {
            return false;
        }

        if (!EneoFactory.createEneo) {
            const { createEneo } = await import("./createEneo");
            EneoFactory.createEneo = createEneo;
        }

        return EneoFactory.createEneo(chat, actionFactory, name);
    }

    public canRun(): boolean {
        return true;
    }
}

export default EneoFactory;
