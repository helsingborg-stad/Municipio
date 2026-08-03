import type { MediaSideloadArgs } from "../restApi/endpoints/mediaSideload";
import { mediaSideload } from "../restApi/endpoints/mediaSideload";
import { isRemoteMediaFile } from "../utils/isRemoteMediaFile";
import { scrubHexValue } from "../utils/scrubHexValue";

type CustomizerSetting = {
	id: string;
	selector: string;
	params: {
		default?: unknown;
		id: string;
		section: string;
		type?: string;
		value?: unknown;
	};
	setting: {
		set: (value: unknown) => void;
	};
	notifications: {
		add: (code: string, notification: unknown) => void;
	};
};

type CustomizerMods = Record<string, unknown>;

type RemoteDesignConfig = {
	dbVersion: number;
	website?: string;
	allowedSettingKeys?: string[];
	allowedSettingKeyPrefixes?: string[];
	mods: CustomizerMods;
	css?: string;
};

const IMPORT_EPHEMERAL_SETTING_IDS = ["load_design_site_url"];
const COMPATIBILITY_ERROR_MESSAGE =
	"We cannot import from this site. It must run the current version of Municipio or newer.";
const REMOTE_FETCH_ERROR_MESSAGE =
	"Unable to fetch design data from this site via import proxy. Ensure the URL is correct and publicly reachable.";

type DesignShareConfig = {
	allowedSettingKeys?: string[];
	allowedSettingKeyPrefixes?: string[];
};

function getAllowedSettingConfig(): DesignShareConfig {
	return (window as Window & {
		municipioDesignShareConfig?: DesignShareConfig;
	}).municipioDesignShareConfig ?? {};
}

function getAllowedExactKeys(): string[] {
	const configKeys = getAllowedSettingConfig().allowedSettingKeys;

	if (!Array.isArray(configKeys)) {
		return [];
	}

	return configKeys.filter((key): key is string => typeof key === "string");
}

function getAllowedPrefixes(): string[] {
	const configPrefixes = getAllowedSettingConfig().allowedSettingKeyPrefixes;

	if (!Array.isArray(configPrefixes)) {
		return [];
	}

	return configPrefixes.filter(
		(prefix): prefix is string => typeof prefix === "string",
	);
}

function isAllowedImportSettingKey(key: string): boolean {
	if (key === "") {
		return false;
	}

	const allowedExactKeys = getAllowedExactKeys();
	if (allowedExactKeys.includes(key)) {
		return true;
	}

	const allowedPrefixes = getAllowedPrefixes();
	return allowedPrefixes.some((prefix) => key.startsWith(prefix));
}

function hasOwn(object: object, property: string): boolean {
	return Object.hasOwn(object, property);
}

export async function handleMediaSideload(args: MediaSideloadArgs) {
	return mediaSideload.call(args).catch((error) => {
		console.error(error);
		return null;
	});
}

function getTrimmedUrl(value: unknown): string {
	if (typeof value !== "string") {
		return "";
	}

	return value.trim();
}

function ensureProtocol(url: string): string {
	if (url.startsWith("http://") || url.startsWith("https://")) {
		return url;
	}

	return `https://${url}`;
}

function normalizeSiteUrl(rawUrl: unknown): string {
	const trimmedUrl = getTrimmedUrl(rawUrl);

	if (trimmedUrl === "") {
		throw new Error("Please enter a Municipio site URL before importing.");
	}

	const url = new URL(ensureProtocol(trimmedUrl));
	return url.toString();
}

function buildImportProxyEndpointUrl(rawSiteUrl: unknown): string {
	const normalizedSiteUrl = normalizeSiteUrl(rawSiteUrl);
	const cacheBust = `${Date.now()}-${Math.random().toString(36).slice(2)}`;
	const endpointUrl = new URL(
		"/wp-json/municipio/v1/design-library/import",
		window.location.origin,
	);

	endpointUrl.searchParams.set("source", normalizedSiteUrl);
	endpointUrl.searchParams.set("cache-bust", cacheBust);

	return endpointUrl.toString();
}

function isValidRemoteDesignConfig(payload: unknown): payload is RemoteDesignConfig {
	if (payload === null || typeof payload !== "object") {
		return false;
	}

	if (!hasOwn(payload, "mods") || !hasOwn(payload, "dbVersion")) {
		return false;
	}

	const typedPayload = payload as Record<string, unknown>;
	return (
		typedPayload.mods !== null &&
		typeof typedPayload.mods === "object" &&
		Number.isFinite(Number(typedPayload.dbVersion))
	);
}

export function getSettingsWithDefaultSetting() {
	return Object.entries(wp.customize.settings.settings)
		.map(([key]) => wp.customize.control(key))
		.filter((setting) => setting !== undefined)
		.filter((setting) => hasOwn(setting, "params"))
		.filter(
			(setting) =>
				hasOwn(setting.params, "default") && hasOwn(setting.params, "value"),
		)
		.filter((setting) => setting.params.type !== "custom")
		.filter(
			(setting) => !IMPORT_EPHEMERAL_SETTING_IDS.includes(setting.params.id),
		);
}

export function resetSettingsToDefault(settings: CustomizerSetting[]) {
	settings.forEach((setting) => {
		wp.customize.control(setting.id).setting.set(setting.params.default);
	});
}

export async function getRemoteSiteDesignData(
	siteUrl: unknown,
	minimumSupportedDbVersion: number,
) {
	const endpointUrl = buildImportProxyEndpointUrl(siteUrl);
	let response: Response;

	try {
		response = await fetch(endpointUrl, {
			headers: {
				Accept: "application/json",
			},
		});
	} catch (error) {
		throw new Error(REMOTE_FETCH_ERROR_MESSAGE);
	}

	if (!response.ok) {
		throw new Error(COMPATIBILITY_ERROR_MESSAGE);
	}

	const data: unknown = await response.json();

	if (!isValidRemoteDesignConfig(data)) {
		throw new Error(COMPATIBILITY_ERROR_MESSAGE);
	}

	if (Number(data.dbVersion) < minimumSupportedDbVersion) {
		throw new Error(COMPATIBILITY_ERROR_MESSAGE);
	}

	return data;
}

export async function migrateRemoteMediaFile(
	value: string,
	control: CustomizerSetting | null = null,
) {
	const sideloadedMedia = await handleMediaSideload({
		url: value,
		return: "src",
	});

	if (control && sideloadedMedia !== null) {
		control.setting.set(sideloadedMedia);
	}

	return sideloadedMedia;
}

export function updateCustomizerImageControl(
	control: CustomizerSetting,
	value: string,
) {
	const img = document.querySelector(
		`${control.selector} .attachment-thumb, ${control.selector} img`,
	);
	if (img !== null) {
		img.setAttribute("src", value);
	}
}

interface CustomizerNotificationProps {
	setting: CustomizerSetting;
	code: string;
	message: string;
	type?: "error" | "warning" | "notice";
}

export function showNotification(args: CustomizerNotificationProps) {
	const notification = new wp.customize.Notification(args.code, {
		message: args.message,
		type: args.type ?? "notice",
		dismissible: true,
	});
	args.setting.notifications.add(args.code, notification);
}

export async function getFormattedMods(
	mods: CustomizerMods,
) {
	const formattedMods: CustomizerMods = {};

	for (const [key, value] of Object.entries(mods)) {
		if (!isAllowedImportSettingKey(key)) {
			continue;
		}

		if (IMPORT_EPHEMERAL_SETTING_IDS.includes(key)) {
			continue;
		}

		if (value !== null && typeof value === "object" && !Array.isArray(value)) {
			for (const [subKey, subValue] of Object.entries(value)) {
				formattedMods[`${key}[${subKey}]`] = subValue;
			}
		} else {
			formattedMods[key] = value;
		}
	}

	return formattedMods;
}

export async function importSettings(
	formattedMods: CustomizerMods,
) {
	for (const [key, rawValue] of Object.entries(formattedMods)) {
		if (!isAllowedImportSettingKey(key)) {
			continue;
		}

		const control = wp.customize.control(key);
		const value = Array.isArray(rawValue)
			? rawValue.filter((el) => el !== null)
			: rawValue;

		if (IMPORT_EPHEMERAL_SETTING_IDS.includes(key)) {
			continue;
		}

		if (value === null) {
			continue;
		}

		if (key.startsWith("custom_fonts") && typeof value === "string") {
			const fontName = key.match(/\[(.+)\]$/);
			if (fontName === null) continue;
			await handleMediaSideload({
				url: value,
				description: fontName[1],
				return: "id",
			});
		} else if (typeof control !== "undefined") {
			if (typeof value === "string" && isRemoteMediaFile(value)) {
				await migrateRemoteMediaFile(value, control);
				updateCustomizerImageControl(control, value);
			} else {
				const scrubbedValue = scrubHexValue(value);
				control.setting.set(scrubbedValue);
			}
		}
	}
}
