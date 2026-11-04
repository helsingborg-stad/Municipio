import PuzzelConfig from "./puzzelConfig";

class PuzzelFactory implements ChatIntegrationFactory {
    private static createPuzzel: CreateIntegrationFunction | null = null;
    private puzzelConfig: PuzzelConfig | null = null;

    public async create(
        chat: any,
        actionFactory: ChatActionFactoryInterface,
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

        return PuzzelFactory.createPuzzel(chat, actionFactory, this.puzzelConfig, name);
    }

    private createConfig(chat: any, name: string): PuzzelConfig|null {
        const attributePrefix = `data-js-chat-${name}`;
        const customerId = chat.getElement().getAttribute(`${attributePrefix}-customer-id`);
        const queueuKey = chat.getElement().getAttribute(`${attributePrefix}-queue-key`);
        const visualQueueId = chat.getElement().getAttribute(`${attributePrefix}-visual-queue-id`);
        const timeId = chat.getElement().getAttribute(`${attributePrefix}-time-id`);
        const clientId = chat.getElement().getAttribute(`${attributePrefix}-client-id`);

        if (!customerId || !queueuKey || !clientId || !visualQueueId || !timeId) {
            console.error("Missing required Puzzel configuration attributes");
            return null;
        }

        return new PuzzelConfig(customerId, queueuKey, clientId, visualQueueId, timeId);
    }
}

export default PuzzelFactory;