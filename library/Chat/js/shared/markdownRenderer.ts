import MarkdownIt from "markdown-it";

export type MarkdownRenderer = (content: string) => string;

export function createMarkdownRenderer(): MarkdownRenderer {
	const parser = new MarkdownIt({ html: false, linkify: false, typographer: false });

	parser.validateLink = (url: string): boolean => /^(https?:|mailto:|tel:|\/|#)/i.test(url);

	return (content: string): string => {
		try {
			return parser.render(content);
		} catch (error) {
			console.error(
				"[MarkdownRenderer] Failed to render markdown, falling back to escaped text.",
				error,
			);
			return `<p>${parser.utils.escapeHtml(content)}</p>`;
		}
	};
}
