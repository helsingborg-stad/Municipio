import ActionFactory from "./chat/feature/actions/actionFactory";

class ChatInitializer implements ChatInitializerInterface {
	private activeIntegration: {[key: string]: ChatIntegrationInterface} = {};
	public async init(chat: any, mode: string|null = null): Promise<boolean> {
		const actionsTemplate = chat.getElement().querySelector('[data-js-municipio-chat-actions]');
		const actionsContainer = chat.getElement().querySelector('[data-js-chat-actions]');
		const actionFactory = new ActionFactory(actionsContainer, actionsTemplate, chat);

		const modes = this.getModes(chat, mode);

		for (const mode of modes) {
			// TODO: Allow switch
			if (this.activeIntegration[mode]) {
				return true;
			}

			const created = await this.tryCreateIntegration(mode, chat, actionFactory);

			if (created) {
				const initialized = await created.init();
				if (initialized) {
					this.activeIntegration[mode] = created;
					return true;
				}
			}
		}

		return false;
	}

	private async tryCreateIntegration(
		mode: string,
		chat: any,
		actionFactory: ChatActionFactoryInterface,
	): Promise<ChatIntegrationInterface|false> {
		try {
			const factory = (
				await import(`./chat/integration/${mode}/${mode}Factory`)
			).default;

			const instance = new factory();
			const created: ChatIntegrationInterface|false = await instance.create(chat, actionFactory);
			
			return created;
		} catch (error) {
			console.warn(`Chat integration "${mode}" could not be loaded`, error);
			return false;
		}
	}

	private getModes(chat: any, mode: string|null = null): string[] {
		return mode ? [mode] : (chat.getElement().dataset.jsChatModes ?? "")
			.split(",")
			.map((mode: string) => mode.trim())
			.filter((mode: string) => mode.length > 0);
	}
}

export default ChatInitializer;