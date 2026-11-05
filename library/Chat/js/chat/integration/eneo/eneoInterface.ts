interface EneoInterface extends ChatIntegrationInterface {
	createNewEneoSession(): void;
}

type CreateEneoFunction = (
    chat: unknown,
    uiManager: ChatUiManagerInterface,
    name: string,
) => ChatIntegrationInterface;
