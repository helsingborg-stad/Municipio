import { scrubHexValue } from "../utils/scrubHexValue";
import { isRemoteMediaFile } from "../utils/isRemoteMediaFile";
import { themeIdIsValid, getRemoteSiteDesignData, getSettings, resetSettingsToDefault, migrateCustomFonts, migrateRemoteMediaFile, updateKirkiImageControl, showErrorNotification } from "./designShareUtils";
import { replaceRemoteFilesWithLocalInString } from "../utils/replaceRemoteFilesWithLocalInString";

async function handleLoadSettingChange(loadDesignSetting:any, id:any) {
    
    const incompatibleKeyStack: string[] = [];
    
    if( !themeIdIsValid(id) ) {
        showErrorNotification(loadDesignSetting, 'loadDesignError', 'The selected theme id is not valid')
        return
    }
    
    const apiResponse = await getRemoteSiteDesignData(id);
    
    try {
        const dataUrl = new URL(apiResponse.website)
        const sanitizedCss = await replaceRemoteFilesWithLocalInString(apiResponse.css ?? '', dataUrl.origin)
        wp.customize.control('custom_css').setting.set(sanitizedCss);
    } catch (error) {
        showErrorNotification(loadDesignSetting, 'loadDesignError', 'Failes migrating css from source.')
    }
    
    if( Object.keys(apiResponse.mods).length < 1 ) {
        showErrorNotification(loadDesignSetting, 'loadDesignError', 'This theme seems to be empty, please select another one.')
        return
    }
    
    const settings = getSettings()
    resetSettingsToDefault(settings)
    
    for (const [key, value] of Object.entries(apiResponse.mods)) {
        
        const control = wp.customize.control(key);
        
        if ('custom_fonts' === key) {
            
            await migrateCustomFonts(value as {[key:string]: string})

        } else if (typeof control !== 'undefined') {
            
            if (isRemoteMediaFile(value)) {
                
                await migrateRemoteMediaFile(value, control)
                updateKirkiImageControl(control, value);
                
            } else {
                const scrubbedValue = scrubHexValue(value);
                control.setting.set(scrubbedValue);
            }
            
        } else {
            if (!key.startsWith('archive_')) {
                incompatibleKeyStack.push(key);
            }
        }
    }
    
    if (incompatibleKeyStack.length > 0) {
        const errorMessage = `
        The selected theme may be incompatible with this version 
        of the theme customizer. Some settings (${incompatibleKeyStack.join(', ')}) may be missing.`
        showErrorNotification(loadDesignSetting, 'loadDesignError', errorMessage)
        return
    }
    
}

export default (() => {
    if(!wp.customize) return
    
    wp.customize.bind('ready', () => {
        wp.customize('load_design', (loadDesignSetting:any) => {
            loadDesignSetting.bind((id:any) => handleLoadSettingChange(loadDesignSetting, id))
        });
    });
})();