class EneoFactory implements ChatIntegrationFactory {
    private static createEneo: CreateEneoFunction | null = null;

    public async create(chat: any, uiManager: ChatUiManagerInterface, name: string): Promise<ChatIntegrationInterface|false> {
        const attributePrefix = `data-js-chat-${name}`;
        const assistantName = chat.getElement().getAttribute(`${attributePrefix}-name`);
        const greetingPhrase = chat.getElement().getAttribute(`${attributePrefix}-greeting-phrase`);

        if (!EneoFactory.createEneo) {
            const { createEneo } = await import("./createEneo");
            EneoFactory.createEneo = createEneo;
        }

        return EneoFactory.createEneo!(chat, uiManager, assistantName, greetingPhrase);
    }
}

export default EneoFactory;
