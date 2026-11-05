interface CloseConfirmationControllerInterface {
	initialize(): void;
	requestConfirmation(onConfirm: () => void, onCancel?: () => void): void;
	remove(): void;
}
