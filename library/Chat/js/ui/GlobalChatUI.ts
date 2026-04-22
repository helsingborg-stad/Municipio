import { attachInput } from "./attachInput";

export const initGlobalChat = ({
	utils,
	sessionFactory,
	apiRoot,
	strings,
}: ChatUIDependencies): void => {
	const chatRoot = utils.unsafeGetElement<HTMLElement>("chat-global-root");
	if (!chatRoot) {
		return;
	}

	// TODO: temp hack
	const panel = utils.safeQueryElement<HTMLElement>(".c-fab__panel", chatRoot);
	if (panel) {
		panel.style.maxWidth = "500px";
		panel.style.width = "500px";
	}

	const userMessageTemplate = utils.safeQueryElement<HTMLTemplateElement>(
		"[data-js-chat-template-user]",
		chatRoot,
	);
	const assistantMessageTemplate = utils.safeQueryElement<HTMLTemplateElement>(
		"[data-js-chat-template-assistant]",
		chatRoot,
	);
	const messagesRoot = utils.safeQueryElement<HTMLElement>(
		"[data-js-chat-messages]",
		chatRoot,
	);
	const inputForm = utils.safeQueryElement<HTMLFormElement>(
		"form[data-js-chat-form]",
		chatRoot,
	);
	const inputField = utils.safeQueryElement<
		HTMLInputElement | HTMLTextAreaElement
	>("textarea", inputForm);
	const sendButton = utils.safeQueryElement<HTMLButtonElement>(
		"button",
		inputForm,
	);
	const origSendButtonText = sendButton.textContent || "";

	let lastMessageElement: Element | null = null;

	const scrollLast = () => {
		lastMessageElement?.scrollIntoView({ behavior: "smooth" });
	};

	const { ask } = sessionFactory({
		assistantId: null,
		apiRoot,
		strings,
		utils,
		events: {
			onMessageAdded: (role) => {
				lastMessageElement = utils.appendMessageElement(
					role,
					messagesRoot,
					userMessageTemplate,
					assistantMessageTemplate,
				);
				lastMessageElement.textContent = "";
				scrollLast();
			},
			onUserMessageText: (text) => {
				if (!lastMessageElement) return;
				lastMessageElement.textContent = text;
				scrollLast();
			},
			onAssistantMessageText: (html) => {
				if (!lastMessageElement) return;
				lastMessageElement.innerHTML = html;
				scrollLast();
			},
			onStateChanged: ({ isMessagePending }) => {
				sendButton.disabled = isMessagePending;
				inputField.disabled = isMessagePending;
			},
			onStatusTextChanged: (status) => {
				sendButton.textContent = status ?? origSendButtonText;
			},
		},
	});

	attachInput(ask, inputField, sendButton);
};
