interface WpApiSettings {
	root: string;
}

declare const wpApiSettings: WpApiSettings;

const ChatUtils = {
	safeGetElement<T extends HTMLElement = HTMLElement>(id: string): T {
		const el = document.getElementById(id);
		if (!el) {
			throw new Error(`Element with id "${id}" not found`);
		}
		return el as T;
	},

	safeQueryElement<T extends Element = HTMLElement>(
		selector: string,
		parent: Element | DocumentFragment | null = null,
	): T {
		const el = parent
			? parent.querySelector(selector)
			: document.querySelector(selector);
		if (!el) {
			throw new Error(`Element with selector "${selector}" not found`);
		}
		return el as T;
	},

	appendMessageElement(role: "user" | "assistant", parent: Element, userTemplate: HTMLTemplateElement, assistantTemplate: HTMLTemplateElement): Element {
		const template = role === "user" ? userTemplate : assistantTemplate;

		const newMessage = template.content.cloneNode(true) as DocumentFragment;
		const msgEl = ChatUtils.safeQueryElement(
			".c-comment__bubble--inner",
			newMessage,
		);
		parent.appendChild(newMessage);
		msgEl.scrollIntoView({
			behavior: "smooth",
		});

		return msgEl;
	},

	/**
	 * Basic markdown-to-HTML converter. Supports links, bold, italics, and line breaks.
	 *
	 * @param {string} text - The markdown text to render.
	 * @returns {string} The rendered HTML string.
	 */
	renderMarkdown(text: string): string {
		return text
			.replace(/&/g, "&amp;")
			.replace(/</g, "&lt;")
			.replace(
				/\[([^\]]+)\]\((https?:\/\/[^)]+)\)/g,
				'<a href="$2" target="_blank" rel="noopener">$1</a>',
			)
			.replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>")
			.replace(/\*(.+?)\*/g, "<em>$1</em>")
			.replace(/\n/g, "<br>");
	},
};

class Chat {
	#apiRoot: string = "";

	initialize() {
		console.log("Chat initialize");

		this.#apiRoot = wpApiSettings.root;

		this.#initGlobalChat();
		this.#initPageModuleChats();
	}

	#useChat({
		assistantId,
		onMessageAdded,
		onMessageTextChanged,
		onStateChanged,
		onStatusTextChanged,
	}: {
		assistantId: string | null,
		onMessageAdded: (role: "user" | "assistant") => void,
		onMessageTextChanged: (newText: string) => void,
		onStateChanged: (state: { isMessagePending: boolean }) => void,
		onStatusTextChanged: (status: string | null) => void,
	}): {
		ask(message: string): Promise<void>;
		clear(): void;
	} {
		const apiRoot = this.#apiRoot;
		let sessionId: string | null = null;

		return {
			async ask(message: string): Promise<void> {
				try {
					const messageText = message.trim();

					if (!messageText || messageText.length === 0) {
						return;
					}

					onStateChanged({ isMessagePending: true });
					onMessageAdded("user");
					onMessageTextChanged(messageText);
					onStatusTextChanged("Skickar...");

					onMessageAdded("assistant");

					const res = await fetch(`${apiRoot}municipio/v1/chat`, {
						method: "POST",
						headers: {
							"Content-Type": "application/json",
						},
						body: JSON.stringify({
							message: messageText,
							session_id: sessionId,
							assistant_id: assistantId,
						}),
					});

					if (!res.ok || !res.body) {
						throw new Error(`Network response was not ok: ${res.status}`);
					}

					// Stream response
					const reader = res.body.getReader();
					const decoder = new TextDecoder("utf-8");
					let buffer: string = "";
					let accum: string = "";

					while (true) {
						const { done, value } = await reader.read();
						if (done) break;

						buffer += decoder.decode(value, {
							stream: true,
						});

						let error: string | null = null;
						try {
							error = JSON.parse(buffer);
						} catch (_) { }
						if (error && error.length > 0) {
							throw new Error(error);
						}

						const lines = buffer.split("\n");
						buffer = lines.pop() ?? "";

						let eventType = "";
						for (const line of lines) {
							if (line.trim() === "") continue;
							const dataPart = line.slice(6).trim();
							if (line.startsWith("event: ")) {
								eventType = line.slice(7).trim();
							} else if (line.startsWith("data: ")) {
								try {
									const data = JSON.parse(dataPart);
									switch (eventType) {
										case "first_chunk":
											sessionId = data.session_id;
											onStatusTextChanged("Skriver...");
											break;
										case "text":
											accum += data.answer;
											onMessageTextChanged(ChatUtils.renderMarkdown(accum));
											onStatusTextChanged("Skriver...");
											break;
										case "tool_call":
											onStatusTextChanged("Verktyg används...");
											break;
										case "error":
											throw new Error(`Chat error: ${dataPart}`);
									}
								} catch (e) {
									throw new Error(`Failed to parse line data as JSON: ${dataPart}. Error: ${e}`);
								}
							} else {
								throw new Error(`Unexpected data line: ${line}`);
							}
						}
					}

					onStatusTextChanged(null);
				} catch (error) {
					console.error("Error during chat", error);
					onMessageTextChanged("Ett fel uppstod. Försök igen senare.");
				} finally {
					onStateChanged({ isMessagePending: false });
				}
			},

			clear() {
				sessionId = null;
				onMessageTextChanged("");
				onStatusTextChanged(null);
				onStateChanged({ isMessagePending: false });
			}
		};
	}

	#initGlobalChat() {
		console.log("Init global chat");

		// TODO: temp hack
		const chatRoot = ChatUtils.safeGetElement<HTMLElement>("chat-global-root");
		const panel = ChatUtils.safeQueryElement(".c-fab__panel", chatRoot);
		if (panel) {
			panel.style.maxWidth = "500px";
			panel.style.width = "500px";
		}

		const userMessageTemplate = ChatUtils.safeQueryElement<HTMLTemplateElement>("[data-chat-template-user]", chatRoot);
		const assistantMessageTemplate = ChatUtils.safeQueryElement<HTMLTemplateElement>("[data-chat-template-assistant]", chatRoot);
		const messagesRoot = ChatUtils.safeQueryElement<HTMLElement>("[data-chat-messages]", chatRoot);
		const inputForm = ChatUtils.safeQueryElement<HTMLFormElement>("form[data-chat-form]", chatRoot);
		const inputField = ChatUtils.safeQueryElement<HTMLInputElement | HTMLTextAreaElement>("textarea", inputForm);
		const sendButton = ChatUtils.safeQueryElement<HTMLButtonElement>("button", inputForm);
		const origSendButtonText = sendButton.textContent || "";

		let lastMessageElement: Element | null = null;

		const { ask } = this.#useChat({
			assistantId: null,
			onMessageAdded: (role) => {
				lastMessageElement = ChatUtils.appendMessageElement(role, messagesRoot, userMessageTemplate, assistantMessageTemplate);
				lastMessageElement.textContent = "";
				lastMessageElement.scrollIntoView({
					behavior: "smooth",
				});
			},
			onMessageTextChanged: (newText) => {
				if (!lastMessageElement) return;
				lastMessageElement.innerHTML = newText;
				lastMessageElement.scrollIntoView({
					behavior: "smooth",
				});
			},
			onStateChanged: ({ isMessagePending }) => {
				sendButton.disabled = isMessagePending;
				inputField.disabled = isMessagePending;
			},
			onStatusTextChanged: (status) => {
				sendButton.textContent = status ? status : origSendButtonText;
			},
		});

		this.#addListeners(ask, inputField, sendButton);
	}

	#addListeners(
		askFn: (message: string) => Promise<void>,
		inputField: HTMLInputElement | HTMLTextAreaElement,
		sendButton: HTMLButtonElement
	) {
		sendButton.addEventListener("click", (event) => {
			event.preventDefault();
			const msg = inputField.value;
			inputField.value = "";
			askFn(msg).catch((error) => {
				console.error("Chat error:", error);
			});
		});
		inputField.addEventListener("keypress", (ev) => {
			const event = ev as KeyboardEvent;
			if (event.key === "Enter" && !event.shiftKey) {
				event.preventDefault();
				const msg = inputField.value;
				inputField.value = "";
				askFn(msg).catch((error) => {
					console.error("Chat error:", error);
				});
			}
		});
	}

	#initPageModuleChats() {
		const chatElements = document.querySelectorAll<HTMLElement>('[data-chat-assistant]');

		chatElements.forEach((chatEl) => {
			const userMessageTemplate = ChatUtils.safeQueryElement<HTMLTemplateElement>("[data-chat-template-user]", chatEl);
			const assistantMessageTemplate = ChatUtils.safeQueryElement<HTMLTemplateElement>("[data-chat-template-assistant]", chatEl);
			const messagesRoot = ChatUtils.safeQueryElement<HTMLElement>("[data-chat-messages]", chatEl);

			const initialGroup = ChatUtils.safeQueryElement<HTMLElement>("[data-chat-initial-group]", chatEl);
			const initialField = ChatUtils.safeQueryElement<HTMLInputElement | HTMLTextAreaElement>("input", initialGroup);
			const initialButton = ChatUtils.safeQueryElement<HTMLButtonElement>("button", initialGroup);

			const mainGroup = ChatUtils.safeQueryElement<HTMLElement>("[data-chat-main-group]", chatEl);
			const mainInputField = ChatUtils.safeQueryElement<HTMLInputElement | HTMLTextAreaElement>("input", mainGroup);
			const mainSendButton = ChatUtils.safeQueryElement<HTMLButtonElement>("button[data-chat-send-button]", mainGroup);
			const closeButton = ChatUtils.safeQueryElement<HTMLButtonElement>("button[data-chat-close-button]", chatEl);

			const origSendButtonText = mainSendButton.textContent || "";

			mainGroup.style.visibility = "hidden";

			let lastMessageElement: Element | null = null;

			const { ask, clear } = this.#useChat({
				assistantId: null,
				onMessageAdded: (role) => {
					lastMessageElement = ChatUtils.appendMessageElement(role, messagesRoot, userMessageTemplate, assistantMessageTemplate);
					lastMessageElement.textContent = "";
					lastMessageElement.scrollIntoView({
						behavior: "smooth",
					});
				},
				onMessageTextChanged: (newText) => {
					if (!lastMessageElement) return;
					lastMessageElement.innerHTML = newText;
					lastMessageElement.scrollIntoView({
						behavior: "smooth",
					});
				},
				onStateChanged: ({ isMessagePending }) => {
					mainSendButton.disabled = isMessagePending;
					mainInputField.disabled = isMessagePending;
				},
				onStatusTextChanged: (status) => {
					mainSendButton.textContent = status ? status : origSendButtonText;
				},
			});

			const askEx = (message: string) => new Promise<void>((res) => {
				initialGroup.style.visibility = "hidden";
				mainGroup.style.visibility = "visible";
				mainInputField.focus();
				res();
			}).then(() => ask(message));

			this.#addListeners(askEx, initialField, initialButton);
			this.#addListeners(askEx, mainInputField, mainSendButton);
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
		});
	}
}

document.addEventListener("DOMContentLoaded", () => {
	new Chat().initialize();
});
