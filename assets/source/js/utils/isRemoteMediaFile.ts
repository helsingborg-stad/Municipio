import { isValidFileExtensionFromUrl } from "./isValidFileExtensionFromUrl";
import { isValidUrl } from "./isValidUrl";

export const isRemoteMediaFile = (url:any):url is string => {
    if( typeof url !== 'string' ) return false;
    if(!isValidUrl(url)) return false;
    if(!isValidFileExtensionFromUrl(url, ['svg'])) return false
    
    return true;
}