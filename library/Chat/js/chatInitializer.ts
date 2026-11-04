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

		if (name !== null) {
			const assistantName = name.toLowerCase();

			if (!this.availableAssistants[assistantName]) {
				return false;
			}

			return this.initializeAssistant(assistantName, actionFactory);
		}

		for (const assistantName of Object.keys(this.availableAssistants)) {
			const initialized = await this.initializeAssistant(assistantName, actionFactory);
			if (initialized) {
				return true;
			}
		}

		return false;
	}

	private async initializeAssistant(
		assistantName: string,
		actionFactory: ChatActionFactoryInterface,
	): Promise<boolean> {
		if (assistantName === this.activeIntegration) {
			return true;
		}

		actionFactory.clearActions();
		let isInitialized = false;

		const integration = this.availableAssistants[assistantName];
		try {
			const created = await this.tryCreateIntegration(integration, this.chat, actionFactory, assistantName);

			if (!created) {
				return false;
			}

			const initialized = await created.init();
			if (!initialized) {
				return false;
			}

			this.createdIntegrations[assistantName] = created;
			this.activeIntegration = assistantName;
			isInitialized = true;

			return true;
		} catch (error) {
			console.warn(`Chat integration "${integration}" failed to initialize`, error);
			return false;
		} finally {
			if (!isInitialized) {
				actionFactory.clearActions();
			}
		}
	}

	private async tryCreateIntegration(
		integration: string,
		chat: any,
		actionFactory: ChatActionFactoryInterface,
		assistantName: string,
	): Promise<ChatIntegrationInterface|false> {
		try {
			const factory = (
				await import(`./chat/integration/${integration}/${integration}Factory.ts`)
			).default;

			const instance = new factory();
			const created: ChatIntegrationInterface|false = await instance.create(chat, actionFactory, assistantName);
			console.log(`Created integration for ${integration}:`, created);
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