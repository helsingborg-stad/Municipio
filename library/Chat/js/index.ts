interface WpApiSettings {
	root: string;
}

declare const wpApiSettings: WpApiSettings;

// Log a message when the DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
	console.log("Chat init");

	function safeGetElement<T extends HTMLElement = HTMLElement>(id: string): T {
		const el = document.getElementById(id);
		if (!el) {
			throw new Error(`Element with id "${id}" not found`);
		}
		return el as T;
	}

	function safeQueryElement<T extends Element = HTMLElement>(
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
	}

	const chatRoot = safeGetElement("chat-root");

	const apiRoot = wpApiSettings.root;

	if (!apiRoot) {
		console.error("No api root found - disabling chat");
		chatRoot.style.display = "none";
		return;
	}

	// TODO: temp hack
	const panel = safeQueryElement(".c-fab__panel", chatRoot);
	if (panel) {
		panel.style.maxWidth = "500px";
		panel.style.width = "500px";
	}

	let sessionId: string | null = null;

	/**
	 * Appends a new message to the chat messages container.
	 *
	 * @param {string} role - The role of the message sender ('user' or 'assistant').
	 * @param {string} text - The text content of the message.
	 * @returns {Element} The newly created message element.
	 */
	function appendMessage(role: "user" | "assistant", text: string): Element {
		// Create a new chat message element
		const template = safeGetElement<HTMLTemplateElement>(
			`chat-message-template-${role}`,
		);

		const newMessage = template.content.cloneNode(true) as DocumentFragment;
		const msgEl = safeQueryElement(".c-comment__bubble--inner", newMessage);

		msgEl.textContent = text;

		// Append the new message to the chat messages container
		const messagesRoot = safeGetElement("chat-messages");
		messagesRoot.appendChild(newMessage);

		msgEl.scrollIntoView({
			behavior: "smooth",
		});

		return msgEl;
	}

	/**
	 * Basic markdown-to-HTML converter. Supports links, bold, italics, and line breaks.
	 *
	 * @param {string} text - The markdown text to render.
	 * @returns {string} The rendered HTML string.
	 */
	function renderMarkdown(text: string): string {
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
	}

	async function ask() {
		const inputField = safeGetElement<HTMLInputElement>("input_chat-input");
		const messageText = inputField.value.trim();

		if (!messageText || messageText.length === 0) {
			return;
		}

		console.log("sessionId", sessionId);

		appendMessage("user", messageText);
		const answerEl = appendMessage("assistant", "");

		const submitButton = safeGetElement<HTMLButtonElement>("chat-submit");
		const submitButtonOriginalText = submitButton.textContent;
		submitButton.textContent = "Skickar...";
		submitButton.disabled = true;

		inputField.value = "";
		inputField.disabled = true;

		const res = await fetch(`${apiRoot}municipio/v1/chat`, {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify({
				message: messageText,
				session_id: sessionId,
			}),
		});

		if (!res.ok || !res.body) {
			answerEl.textContent = "Ett fel uppstod. Försök igen senare.";
			return;
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

			const lines = buffer.split("\n");
			buffer = lines.pop() ?? "";

			let eventType = "";
			for (const line of lines) {
				if (line.startsWith("event: ")) {
					eventType = line.slice(7).trim();
				} else if (line.startsWith("data: ") && line.slice(6).trim()) {
					try {
						const data = JSON.parse(line.slice(6));
						switch (eventType) {
							case "first_chunk":
								sessionId = data.session_id;
								submitButton.textContent = "Skriver...";
								break;
							case "text":
								accum += data.answer;
								answerEl.innerHTML = renderMarkdown(accum);
								answerEl.scrollIntoView({
									behavior: "smooth",
								});
								submitButton.textContent = "Skriver...";
								break;
							case "tool_call":
								console.log("Tool call:", data);
								submitButton.textContent = "Verktyg används...";
								break;
						}
					} catch (e) {
						console.error("Error parsing chat response:", e);
					}
				}
			}
		}

		submitButton.disabled = false;
		submitButton.textContent = submitButtonOriginalText;
		inputField.disabled = false;
	}

	const submitButton = safeGetElement<HTMLButtonElement>("chat-submit");
	submitButton.addEventListener("click", (event) => {
		event.preventDefault();
		ask().catch((error) => {
			console.error("Chat error:", error);
		});
	});

	const inputField = safeGetElement<HTMLInputElement>("input_chat-input");
	inputField.addEventListener("keypress", (event) => {
		if (event.key === "Enter" && !event.shiftKey) {
			event.preventDefault();
			ask().catch((error) => {
				console.error("Chat error:", error);
			});
		}
	});
});
