import { themeIdIsValid, getRemoteSiteDesignData, getSettings, resetSettingsToDefault, showNotification, getExcludedSettingIds, getFormattedMods, importSettings } from "./designShareUtils";
import { replaceRemoteFilesWithLocalInString } from "../utils/replaceRemoteFilesWithLocalInString";

async function handleLoadSettingChange(id:any) {
    
    const apiResponse = await getRemoteSiteDesignData(id);
    
    try {
        const dataUrl = new URL(apiResponse.website)
        const sanitizedCss = await replaceRemoteFilesWithLocalInString(apiResponse.css ?? '', dataUrl.origin)
        wp.customize.control('custom_css').setting.set(sanitizedCss);
    } catch (error) {
        throw new Error("Failed migrating css from source.")
    }
    
    if( Object.keys(apiResponse.mods).length < 1 ) {
        throw new Error("The selected theme seems to be empty, please select another one.")
    }
    
    const settings = getSettings()
    const excludedSettings = getExcludedSettingIds();
    const formattedMods = await getFormattedMods(apiResponse.mods)
    resetSettingsToDefault(settings)
    importSettings(formattedMods, excludedSettings)
}

export default (() => {
    if(!wp.customize) return
    
    wp.customize.bind('ready', () => {
        wp.customize('load_design', (loadDesignSetting:any) => {
            loadDesignSetting.bind((id:any) => {
                
                if( !themeIdIsValid(id) ) {
                    return
                }

                handleLoadSettingChange(id)
                .catch(error => {
                    showNotification({
                        setting: loadDesignSetting,
                        code: "loadDesignError",
                        message: error.message,
                        type: 'error'
                    })
                    console.error(error.message)
                })
            })
        });
    });
})();