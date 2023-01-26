import { scrubHexValue } from "../utils/scrubHexValue";
import { isRemoteMediaFile } from "../utils/isRemoteMediaFile";
import { themeIdIsValid, getRemoteSiteDesignData, getSettings, resetSettingsToDefault, migrateCustomFonts, migrateRemoteMediaFile, updateKirkiImageControl, showNotification } from "./designShareUtils";
import { replaceRemoteFilesWithLocalInString } from "../utils/replaceRemoteFilesWithLocalInString";

async function handleLoadSettingChange(loadDesignSetting:any, id:any) {
    
    const incompatibleKeyStack: string[] = [];
    
    if( !themeIdIsValid(id) ) {
        showNotification({
            setting: loadDesignSetting,
            code: "loadDesignError",
            message: "The selected theme id is not valid",
            type: 'error'
        })
        return
    }
    
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
        
        if( value === null ) {
            continue;
        }
        
        if( typeof control === 'undefined' && !key.startsWith('archive_') ) {
            const message = `
            The selected theme may be incompatible with this version of the theme customizer.
            Setting: "(${key})" may be missing.`
            console.warn(message)
        }
        if ('custom_fonts' === key) {
            
            await migrateCustomFonts(value as {[key:string]: string})

        } else if (typeof control !== 'undefined') {
            
            if (isRemoteMediaFile(value)) {
                
                await migrateRemoteMediaFile(value, control)
                updateKirkiImageControl(control, value);
                
            } else {
                const scrubbedValue = scrubHexValue(value);
                control.setting.set(scrubbedValue)
            }
            
        } else {
            if (!key.startsWith('archive_')) {
                incompatibleKeyStack.push(key);
            }
        }
    }
    
    if (incompatibleKeyStack.length > 0) {
        const message = `
        The selected theme may be incompatible with this version 
        of the theme customizer. Some settings (${incompatibleKeyStack.join(', ')}) may be missing.`
        console.warn(message)
    }
    
}

export default (() => {
    if(!wp.customize) return
    
    wp.customize.bind('ready', () => {
        wp.customize('load_design', (loadDesignSetting:any) => {
            loadDesignSetting.bind((id:any) => {
                handleLoadSettingChange(loadDesignSetting, id).catch(error => {
                    showNotification({
                        setting: loadDesignSetting,
                        code: "loadDesignError",
                        message: error.message,
                        type: 'error'
                    })
                })
            })
        });
    });
})();