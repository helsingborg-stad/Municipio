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

function hasOwn(object: object, property: string): boolean {
	return Object.hasOwn(object, property);
}

export async function handleMediaSideload(args: MediaSideloadArgs) {
	return mediaSideload.call(args).catch((error) => {
		console.error(error);
		return null;
	});
}

function getExcludedSections(): string[] {
	const defaultSections = ["municipio_customizer_panel_design_module"];
	const excludedSections = wp.customize
		.control("exclude_load_design")
		.setting.get() as string[];
	return [...defaultSections, ...excludedSections];
}

export function getExcludedSettingIds(): string[] {
	const excludedSections = getExcludedSections();
	return Object.entries(wp.customize.settings.controls)
		.map(([key]) => wp.customize.control(key))
		.filter((setting) => setting !== undefined)
		.filter((setting) => hasOwn(setting, "params"))
		.filter((setting) => excludedSections.includes(setting.params.section))
		.map((setting) => setting.id);
}

export function getSettingsWithDefaultSetting() {
	const excludedSettingsIds = getExcludedSettingIds();
	return Object.entries(wp.customize.settings.settings)
		.map(([key]) => wp.customize.control(key))
		.filter((setting) => setting !== undefined)
		.filter((setting) => hasOwn(setting, "params"))
		.filter(
			(setting) =>
				hasOwn(setting.params, "default") && hasOwn(setting.params, "value"),
		)
		.filter((setting) => setting.params.type !== "custom")
		.filter((setting) => !excludedSettingsIds.includes(setting.params.id));
}

export function resetSettingsToDefault(settings: CustomizerSetting[]) {
	settings.forEach((setting) => {
		wp.customize.control(setting.id).setting.set(setting.params.default);
	});
}

export function themeIdIsValid(id: unknown): id is string {
	return typeof id === "string" && id.length === 32;
}

export async function getRemoteSiteDesignData(id: string) {
	return fetch(`https://customizer.municipio.tech/id/${id}`)
		.then((response) => response.json())
		.catch((error) => {
			alert(error);
		});
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
	excludedSettings: string[],
) {
	const formattedMods: CustomizerMods = {};

	for (const [key, value] of Object.entries(mods)) {
		if (excludedSettings.includes(key)) {
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
	excludedSettings: string[],
) {
	for (const [key, rawValue] of Object.entries(formattedMods)) {
		const control = wp.customize.control(key);
		const value = Array.isArray(rawValue)
			? rawValue.filter((el) => el !== null)
			: rawValue;

		if (excludedSettings.includes(key)) {
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
