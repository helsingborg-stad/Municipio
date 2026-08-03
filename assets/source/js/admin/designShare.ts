import {
    getRemoteSiteDesignData,
    getSettingsWithDefaultSetting,
    resetSettingsToDefault,
    showNotification,
    getFormattedMods,
    importSettings,
} from "./designShareUtils";
import { replaceRemoteFilesWithLocalInString } from "../utils/replaceRemoteFilesWithLocalInString";

type DesignShareConfig = {
    minimumSupportedDbVersion?: number;
};

function getMinimumSupportedDbVersion(): number {
    const config = (window as Window & {
        municipioDesignShareConfig?: DesignShareConfig;
    }).municipioDesignShareConfig;

    return Number(config?.minimumSupportedDbVersion ?? 0);
}

async function handleLoadSettingChange(siteUrl: unknown) {
    const apiResponse = await getRemoteSiteDesignData(
        siteUrl,
        getMinimumSupportedDbVersion(),
    );

    if (Object.keys(apiResponse.mods).length < 1) {
        throw new Error(
            "The source site does not contain importable design settings.",
        );
    }

    const settingsWithDefaultSetting = getSettingsWithDefaultSetting();
    const formattedMods = await getFormattedMods(apiResponse.mods);
    resetSettingsToDefault(settingsWithDefaultSetting);
    await importSettings(formattedMods);

    try {
        const dataUrl = new URL(apiResponse.website ?? String(siteUrl));
        const sanitizedCss = await replaceRemoteFilesWithLocalInString(
            apiResponse.css ?? "",
            dataUrl.origin,
        );

        const customCssControl = wp.customize.control("custom_css");
        if (customCssControl?.setting) {
            customCssControl.setting.set(sanitizedCss);
        }
    } catch (error) {
        throw new Error("Failed to migrate CSS from the source site.");
    }

    if (wp.customize.previewer) {
        wp.customize.previewer.refresh();
    }
}

export default (() => {
    if (!wp.customize) return;

    wp.customize.bind("ready", () => {
        wp.customize("load_design_site_url", (loadDesignSiteUrlSetting: any) => {
            let debounceTimeoutId: ReturnType<typeof setTimeout> | null = null;
            let latestImportedUrl = "";

            loadDesignSiteUrlSetting.bind((siteUrl: unknown) => {
                if (debounceTimeoutId !== null) {
                    clearTimeout(debounceTimeoutId);
                }

                debounceTimeoutId = setTimeout(() => {
                    const normalizedSiteUrl =
                        typeof siteUrl === "string" ? siteUrl.trim() : "";
                    if (normalizedSiteUrl === "") {
                        return;
                    }

                    if (normalizedSiteUrl === latestImportedUrl) {
                        return;
                    }

                    handleLoadSettingChange(normalizedSiteUrl)
                        .then(() => {
                            latestImportedUrl = normalizedSiteUrl;
                            showNotification({
                                setting: loadDesignSiteUrlSetting,
                                code: "loadDesignSuccess",
                                message:
                                    "Design imported into preview. Review the result and click Publish when ready.",
                                type: "notice",
                            });
                        })
                        .catch((error) => {
                            showNotification({
                                setting: loadDesignSiteUrlSetting,
                                code: "loadDesignError",
                                message: error.message,
                                type: "error",
                            });
                            console.error(error.message);
                        });
                }, 700);
            });
        });
    });
})();