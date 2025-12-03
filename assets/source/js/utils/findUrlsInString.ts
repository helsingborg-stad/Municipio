import { isValidUrl } from "./isValidUrl";


export function findUrlsInString(string: string, origin?:string): string[] {
    const urlRegex = /(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#/%=~_|$?!:,.]*\)|[-A-Z0-9+&@#/%=~_|$?!:,.])*(?:\([-A-Z0-9+&@#/%=~_|$?!:,.]*\)|[A-Z0-9+&@#/%=~_|$])/gi;
    const foundFromRegex = string.match(urlRegex);
    
    if (foundFromRegex === null) {
        return [];
    }

    const validUrls = foundFromRegex.filter(isValidUrl);

    if( !origin ) {
        return validUrls
    }

    const urlsMatchingOrigin = validUrls.filter(urlString => {
        const url = new URL(urlString)
        return origin === url.origin
    })

    return urlsMatchingOrigin

}