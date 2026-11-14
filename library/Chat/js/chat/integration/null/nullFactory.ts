import Null from "./null";

class NullFactory implements ChatIntegrationFactory {
	public create(chat: any, uiManager: ChatUiManagerInterface, _name: string): Promise<ChatIntegrationInterface|false> {
		return Promise.resolve(new Null(chat, uiManager));
	}

	public canRun(): boolean {
		return true;
	}
}

export default NullFactory;
