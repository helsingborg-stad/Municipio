export const attachInput = (
	askFn: (message: string) => Promise<void>,
	inputField: HTMLInputElement | HTMLTextAreaElement,
	sendButton: HTMLButtonElement,
): void => {
	const submit = () => {
		const msg = inputField.value;
		inputField.value = "";
		askFn(msg).catch((error) => {
			console.error("Chat error:", error);
		});
	};

	sendButton.addEventListener("click", (event) => {
		event.preventDefault();
		submit();
	});

	inputField.addEventListener("keypress", (ev) => {
		const event = ev as KeyboardEvent;
		if (event.key === "Enter" && !event.shiftKey) {
			event.preventDefault();
			submit();
		}
	});
};
