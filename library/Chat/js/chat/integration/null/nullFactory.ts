import Null from "./null";

class NullFactory implements ChatIntegrationFactory {
	public create(chat: any, _actionFactory: ChatActionFactoryInterface): Promise<ChatIntegrationInterface|false> {
		return Promise.resolve(new Null(chat));
	}

	public canRun(): boolean {
		return true;
	}
}

export default NullFactory;
