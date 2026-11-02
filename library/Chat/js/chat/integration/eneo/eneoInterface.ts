interface EneoInterface extends ChatIntegrationInterface {
	createNewEneoSession(): void;
}

type CreateEneoFunction = (
    chat: unknown,
    actionFactory: ChatActionFactoryInterface,
) => ChatIntegrationInterface;
