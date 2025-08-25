import { mediaSideload, MediaSideloadArgs } from "../restApi/endpoints/mediaSideload";
import { isRemoteMediaFile } from "../utils/isRemoteMediaFile";
import { scrubHexValue } from "../utils/scrubHexValue";

export async function handleMediaSideload(args: MediaSideloadArgs) {
    return mediaSideload
        .call(args)
        .catch(error => {
            console.error(error);
            return null;
        });
}

function getExcludedSections(): string[] {
    const defaultSections = ['municipio_customizer_panel_design_module']
    const excludedSections = wp.customize.control('exclude_load_design').setting.get() as string[]
    return [...defaultSections, ...excludedSections]
}

export function getExcludedSettingIds(): string[] {
    const excludedSections = getExcludedSections()
    return Object
    .entries(wp.customize.settings.controls)
    .map(([key]) => wp.customize.control(key))
    .filter(setting => setting !== undefined)
    .filter(setting => setting.hasOwnProperty("params"))
    .filter(setting => excludedSections.includes(setting.params.section))
    .map(setting => setting.id)
}

export function getSettingsWithDefaultSetting() {
    const excludedSettingsIds = getExcludedSettingIds();
    return Object
        .entries(wp.customize.settings.settings)
        .map(([key]) => wp.customize.control(key))
        .filter(setting => setting !== undefined)
        .filter(setting => setting.hasOwnProperty("params"))
        .filter(setting => setting.params.hasOwnProperty("default") && setting.params.hasOwnProperty("value"))
        .filter(setting => setting.params.type !== "kirki-custom")
        .filter(setting => !excludedSettingsIds.includes(setting.params.id))
}

export function resetSettingsToDefault(settings: any[]) {
    settings.forEach(setting => {
        wp.customize.control(setting.id).setting.set(setting.params.default);
    });
}

export function themeIdIsValid(id: any): id is string {
    return typeof id === 'string' && id.length === 32;
}

export async function getRemoteSiteDesignData(id: string) {
    return fetch(`https://customizer.municipio.tech/id/${id}`)
        .then(response => response.json())
        .catch(error => {
            alert(error);
        });
}

export async function migrateRemoteMediaFile(value: string, control: any = null) {
    const sideloadedMedia = await handleMediaSideload({ url: value, return: 'src' });

    if (control && sideloadedMedia !== null) {
        control.setting.set(sideloadedMedia);
    }

    return sideloadedMedia
}

export function updateKirkiImageControl(control: any, value: string) {
    const img = document.querySelector(`.control-section-kirki-default ${control.selector} .attachment-thumb`);
    if (img !== null) {
        img.setAttribute('src', value);
    }
}

interface CustomizerNotificationProps {
    setting: any,
    code: string,
    message: string,
    type?: 'error' | 'warning' | 'notice'
}

export function showNotification(args: CustomizerNotificationProps) {
    const notification = new wp.customize.Notification(args.code, { message: args.message, type: args.type ?? 'notice', dismissible: true });
    args.setting.notifications.add(args.code, notification);
}

export async function getFormattedMods(mods: any, excludedSettings:string[]) {
    let formattedMods: Record<string, any> = {}


    for (const [key, value] of Object.entries(mods)) {

        if( excludedSettings.includes(key) ) {
            continue
        }

        if (value !== null && typeof value === 'object' && !Array.isArray(value)) {

            for (const [subKey, subValue] of Object.entries(value)) {
                formattedMods[`${key}[${subKey}]`] = subValue
            }

        } else {
            formattedMods[key] = value
        }
    }

    return formattedMods
}

export async function importSettings(formattedMods: Record<string, any>, excludedSettings: string[]) {
    for (const [key, rawValue] of Object.entries(formattedMods)) {

        const control = wp.customize.control(key);
        const value = Array.isArray(rawValue) ? rawValue.filter(el => el !== null) : rawValue

        if (excludedSettings.includes(key)) {
            continue;
        }

        if (value === null) {
            continue;
        }

        if (key.startsWith('custom_fonts')) {
            const fontName = key.match(/\[(.+)\]$/)
            if (fontName === null) continue;
            await handleMediaSideload({ url: value, description: fontName[1], return: 'id' })
        } else if (typeof control !== 'undefined') {

            if (isRemoteMediaFile(value)) {

                await migrateRemoteMediaFile(value, control)
                updateKirkiImageControl(control, value);

            } else {
                const scrubbedValue = scrubHexValue(value);
                control.setting.set(scrubbedValue)
            }
        }
    }
}