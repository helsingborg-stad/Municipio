import { attachInput } from "./attachInput";

const initPageModuleChat = (
	chatEl: HTMLElement,
	{ utils, sessionFactory, apiRoot, strings }: ChatUIDependencies,
): void => {
	const userMessageTemplate = utils.safeQueryElement<HTMLTemplateElement>(
		"[data-js-chat-template-user]",
		chatEl,
	);
	const assistantMessageTemplate = utils.safeQueryElement<HTMLTemplateElement>(
		"[data-js-chat-template-assistant]",
		chatEl,
	);
	const messagesRoot = utils.safeQueryElement<HTMLElement>(
		"[data-js-chat-messages]",
		chatEl,
	);

	const initialGroup = utils.safeQueryElement<HTMLElement>(
		"[data-js-chat-initial-group]",
		chatEl,
	);
	const initialField = utils.safeQueryElement<
		HTMLInputElement | HTMLTextAreaElement
	>("input", initialGroup);
	const initialButton = utils.safeQueryElement<HTMLButtonElement>(
		"button",
		initialGroup,
	);

	const mainGroup = utils.safeQueryElement<HTMLElement>(
		"[data-js-chat-main-group]",
		chatEl,
	);
	const mainInputField = utils.safeQueryElement<
		HTMLInputElement | HTMLTextAreaElement
	>("input", mainGroup);
	const mainSendButton = utils.safeQueryElement<HTMLButtonElement>(
		"button[data-js-chat-send-button]",
		mainGroup,
	);
	const closeButton = utils.safeQueryElement<HTMLButtonElement>(
		"button[data-js-chat-close-button]",
		chatEl,
	);

	const assistantId = chatEl.dataset.jsChatAssistant || null;
	const origSendButtonText = mainSendButton.textContent || "";

	mainGroup.style.visibility = "hidden";

	let lastMessageElement: Element | null = null;

	const scrollLast = () => {
		lastMessageElement?.scrollIntoView({ behavior: "smooth" });
	};

	const { ask, clear } = sessionFactory({
		assistantId,
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
				mainSendButton.disabled = isMessagePending;
				mainInputField.disabled = isMessagePending;
			},
			onStatusTextChanged: (status) => {
				mainSendButton.textContent = status ?? origSendButtonText;
			},
		},
	});

	const askAndReveal = (message: string): Promise<void> => {
		initialGroup.style.visibility = "hidden";
		mainGroup.style.visibility = "visible";
		mainInputField.focus();
		return ask(message);
	};

	attachInput(askAndReveal, initialField, initialButton);
	attachInput(askAndReveal, mainInputField, mainSendButton);

	closeButton.addEventListener("click", () => {
		mainGroup.style.visibility = "hidden";
		initialGroup.style.visibility = "visible";
		messagesRoot.innerHTML = "";
		lastMessageElement = null;
		clear();
		mainInputField.value = "";
		initialField.value = "";
		initialField.focus();
	});
};

export const initPageModuleChats = (deps: ChatUIDependencies): void => {
	document
		.querySelectorAll<HTMLElement>("[data-js-chat-module]")
		.forEach((chatEl) => { initPageModuleChat(chatEl, deps); });
};
