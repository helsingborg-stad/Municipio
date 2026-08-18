type CustomizeSetting = {
	get: () => unknown;
	set: (value: string) => void;
};

type CustomizeControlsApi = {
	(settingId: string, callback: (setting: CustomizeSetting) => void): void;
	bind: (eventName: string, callback: () => void) => void;
	state?: (id: string) => {
		get: () => unknown;
	};
	previewer: {
		bind: (eventName: string, callback: (value: string) => void) => void;
	};
};

declare const wp: {
	customize: CustomizeControlsApi;
};

declare global {
	interface Window {
		__municipioHeaderLogoScrollAspectRatioDebug?: Record<string, unknown>;
	}
}

function getCustomizeApi(): CustomizeControlsApi | null {
	return typeof wp !== "undefined" && wp.customize ? wp.customize : null;
}

function setDebugState(key: string, value: Record<string, unknown>): void {
	if (typeof window === "undefined") {
		return;
	}

	window.__municipioHeaderLogoScrollAspectRatioDebug = {
		...(window.__municipioHeaderLogoScrollAspectRatioDebug ?? {}),
		[key]: value,
	};

	console.debug("[Municipio] header logo scroll aspect ratio", key, value);
}

export const aspectRatioSettingId = "header_logo_scroll_aspect_ratio";
export const aspectRatioUpdateEvent =
	"municipio:headerLogoScrollAspectRatio:update";

function hasBoundPreviewerEvent(customizeApi: CustomizeControlsApi): boolean {
	return (
		(customizeApi as CustomizeControlsApi & {
			__municipioHeaderLogoScrollAspectRatioBound?: boolean;
		}).__municipioHeaderLogoScrollAspectRatioBound ?? false
	);
}

function setHasBoundPreviewerEvent(
	customizeApi: CustomizeControlsApi,
	value: boolean,
): void {
	(
		customizeApi as CustomizeControlsApi & {
			__municipioHeaderLogoScrollAspectRatioBound?: boolean;
		}
	).__municipioHeaderLogoScrollAspectRatioBound = value;
}

export function applyHeaderLogoScrollAspectRatioValue(value: string): void {
	const customizeApi = getCustomizeApi();

	if (!customizeApi) {
		setDebugState("controls-apply", {
			status: "missing-customize-api",
			value,
		});
		return;
	}

	customizeApi(aspectRatioSettingId, (setting: CustomizeSetting) => {
		const previousValue = setting.get();

		setDebugState("controls-apply", {
			status: previousValue === value ? "unchanged" : "updated",
			previousValue,
			value,
		});

		if (setting.get() === value) {
			return;
		}

		setting.set(value);
	});
}

export function initializeHeaderLogoScrollAspectRatioSync(): void {
	const customizeApi = getCustomizeApi();

	if (!customizeApi) {
		setDebugState("controls-init", {
			status: "missing-customize-api",
		});
		return;
	}

	const bindPreviewerEvent = (): void => {
		if (hasBoundPreviewerEvent(customizeApi)) {
			return;
		}

		setHasBoundPreviewerEvent(customizeApi, true);

		setDebugState("controls-init", {
			status: "bound",
			event: aspectRatioUpdateEvent,
			settingId: aspectRatioSettingId,
		});

		customizeApi.previewer.bind(aspectRatioUpdateEvent, (value: string) => {
			setDebugState("controls-receive", {
				status: "received",
				value,
			});

			applyHeaderLogoScrollAspectRatioValue(value);
		});
	};

	const isReady = customizeApi.state?.("ready")?.get() === true;

	if (isReady) {
		bindPreviewerEvent();
		return;
	}

	customizeApi.bind("ready", () => {
		setDebugState("controls-init", {
			status: "ready-event",
		});

		bindPreviewerEvent();
	});
}

initializeHeaderLogoScrollAspectRatioSync();
