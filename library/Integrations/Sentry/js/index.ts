export type WebVitalsConfig = {
	dsn: string;
	environment: string;
	release: string;
	sampleRate: number;
	tracesSampleRate: number;
	interactionTargets: Record<string, string>;
};

declare global {
	interface Window {
		municipioSentryWebVitalsConfig?: WebVitalsConfig;
	}
}

const config = window.municipioSentryWebVitalsConfig;

if (config?.dsn && Math.random() < config.sampleRate) {
	const start = () => {
		import("./sentryClient").then(({ initializeSentryWebVitals }) =>
			initializeSentryWebVitals(config),
		);
	};

	if (document.readyState === "complete") {
		window.requestIdleCallback?.(start, { timeout: 3000 }) ??
			window.setTimeout(start, 0);
	} else {
		window.addEventListener(
			"load",
			() =>
				window.requestIdleCallback?.(start, { timeout: 3000 }) ??
				window.setTimeout(start, 0),
			{ once: true },
		);
	}
}
