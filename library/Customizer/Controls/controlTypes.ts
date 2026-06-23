export type CustomizerSetting = {
	get: () => unknown;
	set: (value: string) => void;
};

export type WordPressCustomizer = {
	(settingName: string): CustomizerSetting | undefined;
};

export type WordPressI18n = {
	__: (text: string, domain?: string) => string;
};

export type WordPressGlobals = {
	customize?: WordPressCustomizer;
	i18n?: WordPressI18n;
};

export type JQueryCollection = {
	wpColorPicker?: (options: {
		change?: () => void;
		clear?: () => void;
	}) => void;
	sortable?: (options: { handle: string; update: () => void }) => void;
};

export type JQueryFactory = {
	(element: Element): JQueryCollection;
	fn?: {
		wpColorPicker?: unknown;
		sortable?: unknown;
	};
};

declare global {
	interface Window {
		jQuery?: JQueryFactory;
		wp?: WordPressGlobals;
	}

	const jQuery: JQueryFactory | undefined;
}

export type JsonObject = Record<string, unknown>;

export function getJQuery(): JQueryFactory | null {
	if (window.jQuery) {
		return window.jQuery;
	}

	return typeof jQuery !== "undefined" ? jQuery : null;
}

export function translate(text: string): string {
	return window.wp?.i18n?.__(text, "municipio") ?? text;
}

export function readJsonObject(value: string | null | undefined): JsonObject {
	if (!value) {
		return {};
	}

	try {
		const parsedValue = JSON.parse(value);
		return parsedValue !== null && typeof parsedValue === "object" && !Array.isArray(parsedValue)
			? parsedValue
			: {};
	} catch {
		return {};
	}
}

export function dispatchCustomizerChange(input: HTMLInputElement): void {
	input.dispatchEvent(new Event("change", { bubbles: true }));
}