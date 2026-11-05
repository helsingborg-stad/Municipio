import PuzzelConfig from "./puzzelConfig";

class PuzzelFactory implements ChatIntegrationFactory {
    private static createPuzzel: CreatePuzzelFunction | null = null;
    private puzzelConfig: PuzzelConfig | null = null;

    public async create(
        chat: any,
        uiManager: ChatUiManagerInterface,
        name: string
    ): Promise<ChatIntegrationInterface|false> {
        this.puzzelConfig = this.createConfig(chat, name);

        if (!this.puzzelConfig) {
            return false;
        }

        if (!PuzzelFactory.createPuzzel) {
            const { createPuzzel } = await import("./createPuzzel");
            PuzzelFactory.createPuzzel = createPuzzel;
        }

        return PuzzelFactory.createPuzzel!(chat, uiManager, this.puzzelConfig, name);
    }

    private createConfig(chat: any, name: string): PuzzelConfig|null {
        const attributePrefix = `data-js-chat-${name}`;
        const customerId = chat.getElement().getAttribute(`${attributePrefix}-customer-id`);
        const queueuKey = chat.getElement().getAttribute(`${attributePrefix}-queue-key`);
        const visualQueueId = chat.getElement().getAttribute(`${attributePrefix}-visual-queue-id`);
        const timeId = chat.getElement().getAttribute(`${attributePrefix}-time-id`);
        const clientId = chat.getElement().getAttribute(`${attributePrefix}-client-id`);
        const assistantName = chat.getElement().getAttribute(`${attributePrefix}-name`);

        if (!customerId || !queueuKey || !clientId || !visualQueueId || !timeId || !assistantName) {
            console.error("Missing required Puzzel configuration attributes");
            return null;
        }

        return new PuzzelConfig(customerId, queueuKey, clientId, visualQueueId, timeId, assistantName);
    }
}

export default PuzzelFactory;