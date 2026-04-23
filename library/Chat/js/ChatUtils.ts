const escapeAttribute = (value: string): string =>
	value.replaceAll("&", "&amp;").replaceAll('"', "&quot;");

const normalizeHttpUrl = (url: string): string | null => {
	try {
		const parsed = new URL(url.replaceAll("&amp;", "&"));
		if (parsed.protocol !== "http:" && parsed.protocol !== "https:") {
			return null;
		}
		return parsed.toString();
	} catch {
		return null;
	}
};

export const createChatUtils = (): ChatUtilsApi => ({
	unsafeGetElement<T extends HTMLElement = HTMLElement>(id: string): T | null {
		return document.getElementById(id) as T | null;
	},

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

	appendMessageElement(
		role: ChatRole,
		parent: Element,
		userTemplate: HTMLTemplateElement,
		assistantTemplate: HTMLTemplateElement,
	): Element {
		const template = role === "user" ? userTemplate : assistantTemplate;
		const fragment = template.content.cloneNode(true) as DocumentFragment;

		// TODO: replace with a query to a data-js-* attribute
		const msgEl = this.safeQueryElement(
			".c-comment__bubble--inner",
			fragment,
		);

		parent.appendChild(fragment);
		msgEl.scrollIntoView({ behavior: "smooth" });
		return msgEl;
	},

	/**
	 * Minimal markdown-to-HTML converter for assistant output.
	 * Escapes raw HTML, validates link URLs, and escapes href attribute values.
	 */
	renderMarkdown(text: string): string {
		return text
			.replaceAll("&", "&amp;")
			.replaceAll("<", "&lt;")
			.replaceAll(
				/\[([^\]]+)\]\((https?:\/\/[^)]+)\)/g,
				(_match, label: string, url: string) => {
					const normalized = normalizeHttpUrl(url);
					if (!normalized) {
						return label;
					}
					return `<a href="${escapeAttribute(normalized)}" target="_blank" rel="noopener">${label}</a>`;
				},
			)
			.replaceAll(/\*\*(.+?)\*\*/g, "<strong>$1</strong>")
			.replaceAll(/\*(.+?)\*/g, "<em>$1</em>")
			.replaceAll("\n", "<br>");
	},
});
