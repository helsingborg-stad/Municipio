import ActionFactory from "./chat/feature/actions/actionFactory";

class ChatInitializer implements ChatInitializerInterface {
	private activeIntegration: string | null = null;
	private availableAssistants: AvailableAssistants = {};
	private actionsTemplate: HTMLTemplateElement | null = null;
	private actionsContainer: HTMLElement | null = null;
	private createdIntegrations: {[key: string]: ChatIntegrationInterface} = {};

	public constructor(private chat: any) {
		this.availableAssistants = this.getAvailableAssistants(chat);
		this.actionsTemplate = this.chat.getElement().querySelector('[data-js-municipio-chat-actions]');
		this.actionsContainer = this.chat.getElement().querySelector('[data-js-chat-actions]');
	}

	public async init(name: string|null = null): Promise<boolean> {
		if (!this.actionsContainer || !this.actionsTemplate) {
			return false;
		}

		const actionFactory = new ActionFactory(this.actionsContainer, this.actionsTemplate, this.chat);

		console.log(this.availableAssistants)

		for (const name of Object.keys(this.availableAssistants)) {
			if (name === this.activeIntegration) return true;

			const integration = this.availableAssistants[name];
			const created = await this.tryCreateIntegration(integration, this.chat, actionFactory);

			if (created) {
				const initialized = await created.init();
				if (initialized) {
					this.createdIntegrations[name] = created;
					this.activeIntegration = name;
					return true;
				}
			}
		}

		return false;
	}

	private async tryCreateIntegration(
		integration: string,
		chat: any,
		actionFactory: ChatActionFactoryInterface,
	): Promise<ChatIntegrationInterface|false> {
		try {
			const factory = (
				await import(`./chat/integration/${integration}/${integration}Factory`)
			).default;

			const instance = new factory();
			const created: ChatIntegrationInterface|false = await instance.create(chat, actionFactory);
			
			return created;
		} catch (error) {
			console.warn(`Chat integration "${integration}" could not be loaded`, error);
			return false;
		}
	}

	private getAvailableAssistants(chat: any): AvailableAssistants {
		const jsonAssistants = chat.getElement().dataset.jsChatAssistants ?? null;
		const assistants = JSON.parse(jsonAssistants || "[]");

		assistants.null = 'null';

		return assistants;
	}
}

export default ChatInitializer;