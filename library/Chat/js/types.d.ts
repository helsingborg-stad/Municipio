export {};

declare global {
	const municipioChatLocale: {
		errorMessage: string;
	};

	interface WpApiSettings {
		root: string;
	}

	const wpApiSettings: WpApiSettings;
}