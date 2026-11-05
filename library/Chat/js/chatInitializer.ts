import ActionFactory from "./chat/feature/actions/actionFactory";
import ChatUiManager from "./chat/feature/ui/chatUiManager";

class ChatInitializer implements ChatInitializerInterface {
	private activeIntegration: string | null = null;
	private availableAssistants: AvailableAssistants = {};
	private actionsTemplate: HTMLTemplateElement | null = null;
	private actionsContainer: HTMLElement | null = null;
	private chatTitleElement: HTMLElement | null = null;
	private chatAvatarElement: HTMLElement | null = null;
	private createdIntegrations: {[key: string]: ChatIntegrationInterface} = {};

	public constructor(private chat: any) {
		this.availableAssistants = this.getAvailableAssistants(chat);
		this.actionsTemplate = this.chat.getElement().querySelector('[data-js-municipio-chat-actions]');
		this.actionsContainer = this.chat.getElement().querySelector('[data-js-chat-actions]');
		this.chatTitleElement = this.chat.getElement().querySelector('[data-js-chat-title]');
		this.chatAvatarElement = this.chat.getElement().querySelector('[data-js-chat-avatar]');
	}

	public async init(name: string|null = null): Promise<boolean> {
		if (!this.actionsContainer || !this.actionsTemplate) {
			return false;
		}

		const actionFactory = new ActionFactory(this.actionsContainer, this.actionsTemplate, this.chat);
		const chatUiManager = new ChatUiManager(actionFactory, this.actionsContainer, this.chatTitleElement, this.chatAvatarElement);

		if (name !== null) {
			const assistantName = name.toLowerCase();

			if (!this.availableAssistants[assistantName]) {
				return false;
			}

			return this.initializeAssistant(assistantName, chatUiManager);
		}

		for (const assistantName of Object.keys(this.availableAssistants)) {
			const initialized = await this.initializeAssistant(assistantName, chatUiManager);
			if (initialized) {
				return true;
			}
		}

		return false;
	}

	private async initializeAssistant(
		assistantName: string,
		chatUiManager: ChatUiManagerInterface,
	): Promise<boolean> {
		if (assistantName === this.activeIntegration) {
			return true;
		}

		const integration = this.availableAssistants[assistantName];
		try {
			const created = await this.tryCreateIntegration(integration, this.chat, chatUiManager, assistantName);

			if (!created) {
				return false;
			}

			const initialized = await created.init();
			if (!initialized) {
				return false;
			}

			this.createdIntegrations[assistantName] = created;
			this.activeIntegration = assistantName;

			return true;
		} catch (error) {
			console.warn(`Chat integration "${integration}" failed to initialize`, error);
			return false;
		}
	}

	private async tryCreateIntegration(
		integration: string,
		chat: any,
		chatUiManager: ChatUiManagerInterface,
		assistantName: string,
	): Promise<ChatIntegrationInterface|false> {
		try {
			const factory = (
				await import(`./chat/integration/${integration}/${integration}Factory.ts`)
			).default;

			const instance = new factory();
			const created: ChatIntegrationInterface|false = await instance.create(chat, chatUiManager, assistantName);
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