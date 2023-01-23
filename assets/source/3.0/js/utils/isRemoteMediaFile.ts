import { isValidFileExtensionFromUrl } from "./isValidFileExtensionFromUrl";
import { isValidUrl } from "./isValidUrl";

export const isRemoteMediaFile = (url:string):boolean => {
    if(!isValidUrl(url)) return false;
    if(!isValidFileExtensionFromUrl(url, ['svg'])) return false

    return true;
}