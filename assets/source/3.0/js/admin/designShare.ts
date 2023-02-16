import { scrubHexValue } from "../utils/scrubHexValue";
import { isRemoteMediaFile } from "../utils/isRemoteMediaFile";
import { themeIdIsValid, getRemoteSiteDesignData, getSettings, resetSettingsToDefault, migrateRemoteMediaFile, updateKirkiImageControl, showNotification, handleMediaSideload, getExcludedSettingIds } from "./designShareUtils";
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
    
    const keysNotFound:string[] = []
    const settings = getSettings()
    const excludedSettings = getExcludedSettingIds();
    resetSettingsToDefault(settings)
    
    let formattedMods:Record<string,any> = {}
    
    for (const [key, value] of Object.entries(apiResponse.mods)) {
        
        if( value !== null && typeof value === 'object' && !Array.isArray(value) ) {
            
            for (const [subKey, subValue] of Object.entries(value)) {
                formattedMods[`${key}[${subKey}]`] = subValue
            }
            
        } else {
            formattedMods[key] = value
        }
    }
    
    for (const [key, rawValue] of Object.entries(formattedMods)) {
        
        const control = wp.customize.control(key);
        const value = Array.isArray(rawValue) ? rawValue.filter(el => el !== null) : rawValue

        if( excludedSettings.includes(key) ) {
            continue;
        }
        
        if( value === null ) {
            continue;
        }
        
        if( typeof control === 'undefined' && !key.startsWith('archive_') ) {
            keysNotFound.push(key)
        }
        
        if (key.startsWith('custom_fonts')) {
            const fontName = key.match(/\[(.+)\]$/)
            if( fontName === null ) continue;
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

    if( keysNotFound.length > 0 ) {
        console.warn('The selected theme may be incompatible with this version of the theme customizer.', `Missing settings: ${keysNotFound.join(', ')}`)
    }
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