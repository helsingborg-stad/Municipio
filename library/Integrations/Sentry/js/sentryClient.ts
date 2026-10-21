import * as Sentry from "@sentry/browser";
import { onCLS, onFCP, onINP, onLCP, onTTFB } from "web-vitals/attribution";
import type { WebVitalsConfig } from "./index";

type MetricName = "CLS" | "FCP" | "INP" | "LCP" | "TTFB";

export const initializeSentryWebVitals = (config: WebVitalsConfig) => {
	Sentry.init({
		dsn: config.dsn,
		environment: config.environment || undefined,
		release: config.release || undefined,
		integrations: [Sentry.browserTracingIntegration()],
		tracesSampleRate: config.tracesSampleRate,
		maxBreadcrumbs: 0,
		tracePropagationTargets: [window.location.origin],
	});

	const baseAttributes = () => ({
		page: window.location.pathname,
		navigation_type:
			(
				performance.getEntriesByType("navigation")[0] as
					| PerformanceNavigationTiming
					| undefined
			)?.type ?? "unknown",
	});

	const report = (
		name: MetricName,
		value: number,
		attributes: Record<string, string | number>,
	) => {
		Sentry.metrics.distribution(`web_vital.${name.toLowerCase()}`, value, {
			unit: name === "CLS" ? "none" : "millisecond",
			attributes: {
				...baseAttributes(),
				...attributes,
			},
		});
	};

	const interactionTarget = (target: Node | null): string => {
		if (!(target instanceof Element)) {
			return "unknown";
		}

		for (const [selector, name] of Object.entries(config.interactionTargets)) {
			try {
				if (target.closest(selector)) {
					return name;
				}
			} catch {
				// An invalid user-supplied selector must not affect page interactions.
			}
		}

		return "other";
	};

	onCLS((metric) => report("CLS", metric.value, { rating: metric.rating }));
	onFCP((metric) => report("FCP", metric.value, { rating: metric.rating }));
	onLCP((metric) => report("LCP", metric.value, { rating: metric.rating }));
	onTTFB((metric) => report("TTFB", metric.value, { rating: metric.rating }));
	onINP(
		(metric) => {
			const attribution = metric.attribution;

			report("INP", metric.value, {
				rating: metric.rating,
				interaction: interactionTarget(metric.entries[0]?.target ?? null),
				interaction_type: attribution.interactionType,
				input_delay: Math.round(attribution.inputDelay),
				processing_duration: Math.round(attribution.processingDuration),
				presentation_delay: Math.round(attribution.presentationDelay),
			});
		},
		{ reportAllChanges: false },
	);
};
