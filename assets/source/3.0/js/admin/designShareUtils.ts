import { mediaSideload, MediaSideloadArgs } from "../restApi/endpoints/mediaSideload";

export async function handleMediaSideload(args: MediaSideloadArgs) {
    return mediaSideload
        .call(args)
        .catch(error => {
            console.warn(error);
            return null;
        });
}

export function getSettings() {
    return Object
        .entries(wp.customize.settings.settings)
        .map(([key]) => wp.customize.control(key))
        .filter(setting => setting !== undefined)
        .filter(setting => setting.hasOwnProperty("params"))
        .filter(setting => setting.params.hasOwnProperty("default") && setting.params.hasOwnProperty("value"))
        .filter(setting => setting.params.type !== "kirki-custom")
        .filter(setting => setting.params.id !== "load_design");
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
    return fetch(`https://customizer.helsingborg.io/id/${id}`)
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
    setting:any,
    code:string,
    message: string,
    type?: 'error'|'warning'|'notice'
}

export function showNotification(args: CustomizerNotificationProps) {
    const notification = new wp.customize.Notification( args.code, {message: args.message, type:args.type ?? 'notice', dismissible: true} );
    args.setting.notifications.add( args.code, notification );
}